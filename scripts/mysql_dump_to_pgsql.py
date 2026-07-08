#!/usr/bin/env python3
"""
Konvertuje phpMyAdmin/MySQL dump (samo PODATKE) u PostgreSQL-kompatibilan SQL.

Schemu u Laravel projektu grade migracije (`php artisan migrate`), pa ovaj
skript NE dira strukturu tabela - izvlaci samo INSERT redove iz MySQL dumpa,
konvertuje ih u Postgres sintaksu i dodaje reset sekvenci na kraju.

Koristenje:
    python scripts/mysql_dump_to_pgsql.py infinit4_testteamsphere.sql database/legacy_data.pgsql
"""
import re
import sys

# Framework / prolazne tabele koje NE importujemo:
#  - migrations: puni ih `artisan migrate`
#  - cache/cache_locks/sessions/jobs/...: prolazni podaci
SKIP_TABLES = {
    "migrations",
    "cache",
    "cache_locks",
    "sessions",
    "jobs",
    "job_batches",
    "failed_jobs",
    "password_reset_tokens",
}

# Kolone koje su u Postgres schemi `boolean` (iz `$table->boolean(...)` migracija),
# a u MySQL dumpu su tinyint(1) ili varchar '0'/'1'. Emitujemo ih kao TRUE/FALSE
# da ne zavisimo od Postgres string->bool coercion-a.
BOOLEAN_COLUMNS = {
    "is_active", "active", "is_double_round", "is_team_based", "is_public",
    "manual_knockout_selection", "has_tiebreak", "is_bye", "is_completed",
    "must_win_by_two",
}


def parse_create_tables(sql):
    """Vrati {tabela: {kolona: tip}} iz CREATE TABLE blokova."""
    tables = {}
    for m in re.finditer(
        r"CREATE TABLE `(\w+)` \((.*?)\)\s*ENGINE", sql, re.DOTALL
    ):
        name = m.group(1)
        body = m.group(2)
        cols = {}
        for line in body.splitlines():
            line = line.strip()
            cm = re.match(r"`(\w+)`\s+([^\s,]+)", line)
            if cm:
                cols[cm.group(1)] = cm.group(2).lower()
        tables[name] = cols
    return tables


def decode_mysql_string(s):
    """Dekodira MySQL string literal (bez okolnih navodnika) u stvarnu vrijednost."""
    out = []
    i = 0
    n = len(s)
    escapes = {
        "0": "\x00", "b": "\b", "n": "\n", "r": "\r",
        "t": "\t", "Z": "\x1a", "\\": "\\", "'": "'", '"': '"',
    }
    while i < n:
        c = s[i]
        if c == "\\" and i + 1 < n:
            nxt = s[i + 1]
            out.append(escapes.get(nxt, nxt))
            i += 2
        elif c == "'" and i + 1 < n and s[i + 1] == "'":
            out.append("'")
            i += 2
        else:
            out.append(c)
            i += 1
    return "".join(out)


def pg_quote(value):
    """Postgres string literal (standard_conforming_strings=on): udvostruci ' i izbaci NUL."""
    value = value.replace("\x00", "")
    return "'" + value.replace("'", "''") + "'"


def tokenize_values(text, start):
    """
    Parsira niz VALUES tuple-ova pocev od pozicije `start` (na '(').
    Vraca (lista_redova, pozicija_nakon_zavrsnog_tacka-zareza).
    Svaki red je lista tokena: ('null'|'num'|'str', vrijednost).
    """
    rows = []
    i = start
    n = len(text)
    while i < n:
        while i < n and text[i] in " \t\r\n,":
            i += 1
        if i >= n or text[i] == ";":
            break
        assert text[i] == "(", f"ocekivano '(' na {i}, dobijeno {text[i]!r}"
        i += 1
        row = []
        while True:
            while i < n and text[i] in " \t\r\n":
                i += 1
            c = text[i]
            if c == "'":
                # string literal - citaj do neescape-ovanog '
                i += 1
                buf = []
                while i < n:
                    ch = text[i]
                    if ch == "\\":
                        buf.append(ch)
                        buf.append(text[i + 1])
                        i += 2
                    elif ch == "'":
                        if i + 1 < n and text[i + 1] == "'":
                            buf.append("''")
                            i += 2
                        else:
                            i += 1
                            break
                    else:
                        buf.append(ch)
                        i += 1
                row.append(("str", decode_mysql_string("".join(buf))))
            else:
                # NULL ili broj/token do , ili )
                j = i
                while i < n and text[i] not in ",)":
                    i += 1
                tok = text[j:i].strip()
                if tok.upper() == "NULL":
                    row.append(("null", None))
                else:
                    row.append(("num", tok))
            while i < n and text[i] in " \t\r\n":
                i += 1
            if text[i] == ",":
                i += 1
                continue
            if text[i] == ")":
                i += 1
                break
        rows.append(row)
    # preskoci do ;
    while i < n and text[i] != ";":
        i += 1
    return rows, i + 1


def is_bool(coltype, colname=""):
    return coltype == "tinyint(1)" or colname in BOOLEAN_COLUMNS


def to_bool_literal(val):
    """Mapira '0'/'1'/0/1 -> FALSE/TRUE."""
    return "TRUE" if str(val).strip().strip("'") == "1" else "FALSE"


def convert(src_path, out_path):
    with open(src_path, "r", encoding="utf-8") as f:
        sql = f.read()

    tables = parse_create_tables(sql)

    out = []
    out.append("-- Auto-generisano iz MySQL dumpa (samo podaci) za PostgreSQL.")
    out.append("-- Pokrenuti NAKON `php artisan migrate`.")
    out.append("BEGIN;")
    out.append("SET session_replication_role = replica;  -- iskljuci FK/triggere tokom uvoza")
    out.append("")

    seq_tables = []  # tabele sa 'id' kolonom -> reset sekvence

    insert_re = re.compile(
        r"INSERT INTO `(\w+)` \(([^)]*)\)\s*VALUES", re.IGNORECASE
    )
    pos = 0
    counts = {}
    for m in insert_re.finditer(sql):
        table = m.group(1)
        cols_raw = m.group(2)
        cols = [c.strip().strip("`") for c in cols_raw.split(",")]
        rows, _ = tokenize_values(sql, m.end())

        if table in SKIP_TABLES:
            continue
        if not rows:
            continue

        coltypes = tables.get(table, {})
        if "id" in coltypes and table not in seq_tables:
            seq_tables.append(table)

        col_list = ", ".join(f'"{c}"' for c in cols)
        counts[table] = counts.get(table, 0) + len(rows)

        # generisi INSERT-e u grupama od 500 redova
        for chunk_start in range(0, len(rows), 500):
            chunk = rows[chunk_start:chunk_start + 500]
            out.append(f'INSERT INTO "{table}" ({col_list}) VALUES')
            value_lines = []
            for row in chunk:
                vals = []
                for col, (kind, val) in zip(cols, row):
                    ctype = coltypes.get(col, "")
                    if kind == "null":
                        vals.append("NULL")
                    elif kind == "num":
                        if is_bool(ctype, col):
                            vals.append(to_bool_literal(val))
                        else:
                            vals.append(val)
                    else:  # str
                        if is_bool(ctype, col):
                            vals.append(to_bool_literal(val))
                        else:
                            vals.append(pg_quote(val))
                value_lines.append("  (" + ", ".join(vals) + ")")
            out.append(",\n".join(value_lines) + ";")
            out.append("")

    out.append("SET session_replication_role = DEFAULT;")
    out.append("")
    out.append("-- Reset auto-increment sekvenci na max(id)")
    for t in seq_tables:
        out.append(
            f"SELECT setval(pg_get_serial_sequence('\"{t}\"', 'id'), "
            f"COALESCE((SELECT MAX(id) FROM \"{t}\"), 1), "
            f"(SELECT MAX(id) FROM \"{t}\") IS NOT NULL);"
        )
    out.append("")
    out.append("COMMIT;")

    with open(out_path, "w", encoding="utf-8") as f:
        f.write("\n".join(out))

    print(f"Zapisano: {out_path}")
    print("Importovane tabele (redova):")
    for t, c in sorted(counts.items()):
        print(f"  {t}: {c}")


if __name__ == "__main__":
    src = sys.argv[1] if len(sys.argv) > 1 else "infinit4_testteamsphere.sql"
    out = sys.argv[2] if len(sys.argv) > 2 else "database/legacy_data.pgsql"
    convert(src, out)
