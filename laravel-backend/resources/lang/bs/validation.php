<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute polje mora biti prihvaćeno.',
    'accepted_if' => ':attribute polje mora biti prihvaćeno kada je :other :value.',
    'active_url' => ':attribute polje mora biti validan URL.',
    'after' => ':attribute polje mora biti datum nakon :date.',
    'after_or_equal' => ':attribute polje mora biti datum nakon ili jednak :date.',
    'alpha' => ':attribute polje može sadržavati samo slova.',
    'alpha_dash' => ':attribute polje može sadržavati samo slova, brojeve, crtice i donje crte.',
    'alpha_num' => ':attribute polje može sadržavati samo slova i brojeve.',
    'any_of' => ':attribute polje je nevažeće.',
    'array' => ':attribute polje mora biti niz.',
    'ascii' => ':attribute polje može sadržavati samo jednobajtne alfanumeričke znakove i simbole.',
    'before' => ':attribute polje mora biti datum prije :date.',
    'before_or_equal' => ':attribute polje mora biti datum prije ili jednak :date.',
    'between' => [
        'array' => ':attribute polje mora imati između :min i :max stavki.',
        'file' => ':attribute polje mora biti između :min i :max kilobajta.',
        'numeric' => ':attribute polje mora biti između :min i :max.',
        'string' => ':attribute polje mora biti između :min i :max karaktera.',
    ],
    'boolean' => ':attribute polje mora biti tačno ili netačno.',
    'can' => ':attribute polje sadrži neovlaštenu vrijednost.',
    'confirmed' => 'Potvrda :attribute polja se ne podudara.',
    'contains' => ':attribute polje nedostaje obavezna vrijednost.',
    'current_password' => 'Lozinka je netačna.',
    'date' => ':attribute polje mora biti validan datum.',
    'date_equals' => ':attribute polje mora biti datum jednak :date.',
    'date_format' => ':attribute polje mora odgovarati formatu :format.',
    'decimal' => ':attribute polje mora imati :decimal decimalnih mjesta.',
    'declined' => ':attribute polje mora biti odbijeno.',
    'declined_if' => ':attribute polje mora biti odbijeno kada je :other :value.',
    'different' => ':attribute polje i :other moraju biti različiti.',
    'digits' => ':attribute polje mora imati :digits cifara.',
    'digits_between' => ':attribute polje mora biti između :min i :max cifara.',
    'dimensions' => ':attribute polje ima nevažeće dimenzije slike.',
    'distinct' => ':attribute polje ima duplu vrijednost.',
    'doesnt_end_with' => ':attribute polje ne smije završavati sa jednim od sljedećih: :values.',
    'doesnt_start_with' => ':attribute polje ne smije počinjati sa jednim od sljedećih: :values.',
    'email' => ':attribute polje mora biti validna email adresa.',
    'ends_with' => ':attribute polje mora završavati sa jednim od sljedećih: :values.',
    'enum' => 'Odabrani :attribute je nevažeći.',
    'exists' => 'Odabrani :attribute je nevažeći.',
    'extensions' => ':attribute polje mora imati jednu od sljedećih ekstenzija: :values.',
    'file' => ':attribute polje mora biti fajl.',
    'filled' => ':attribute polje mora imati vrijednost.',
    'gt' => [
        'array' => ':attribute polje mora imati više od :value stavki.',
        'file' => ':attribute polje mora biti veći od :value kilobajta.',
        'numeric' => ':attribute polje mora biti veći od :value.',
        'string' => ':attribute polje mora biti duže od :value karaktera.',
    ],
    'gte' => [
        'array' => ':attribute polje mora imati :value stavki ili više.',
        'file' => ':attribute polje mora biti veći ili jednak :value kilobajta.',
        'numeric' => ':attribute polje mora biti veći ili jednak :value.',
        'string' => ':attribute polje mora biti duže ili jednako :value karaktera.',
    ],
    'hex_color' => ':attribute polje mora biti validna heksadecimalna boja.',
    'image' => ':attribute polje mora biti slika.',
    'in' => 'Odabrani :attribute je nevažeći.',
    'in_array' => ':attribute polje mora postojati u :other.',
    'integer' => ':attribute polje mora biti cijeli broj.',
    'ip' => ':attribute polje mora biti validna IP adresa.',
    'ipv4' => ':attribute polje mora biti validna IPv4 adresa.',
    'ipv6' => ':attribute polje mora biti validna IPv6 adresa.',
    'json' => ':attribute polje mora biti validan JSON string.',
    'list' => ':attribute polje mora biti lista.',
    'lowercase' => ':attribute polje mora biti malim slovima.',
    'lt' => [
        'array' => ':attribute polje mora imati manje od :value stavki.',
        'file' => ':attribute polje mora biti manji od :value kilobajta.',
        'numeric' => ':attribute polje mora biti manji od :value.',
        'string' => ':attribute polje mora biti kraće od :value karaktera.',
    ],
    'lte' => [
        'array' => ':attribute polje ne smije imati više od :value stavki.',
        'file' => ':attribute polje mora biti manji ili jednak :value kilobajta.',
        'numeric' => ':attribute polje mora biti manji ili jednak :value.',
        'string' => ':attribute polje mora biti kraće ili jednako :value karaktera.',
    ],
    'mac_address' => ':attribute polje mora biti validna MAC adresa.',
    'max' => [
        'array' => ':attribute polje ne smije imati više od :max stavki.',
        'file' => ':attribute polje ne smije biti veći od :max kilobajta.',
        'numeric' => ':attribute polje ne smije biti veći od :max.',
        'string' => ':attribute polje ne smije biti duže od :max karaktera.',
    ],
    'max_digits' => ':attribute polje ne smije imati više od :max cifara.',
    'mimes' => ':attribute polje mora biti fajl tipa: :values.',
    'mimetypes' => ':attribute polje mora biti fajl tipa: :values.',
    'min' => [
        'array' => ':attribute polje mora imati najmanje :min stavki.',
        'file' => ':attribute polje mora biti najmanje :min kilobajta.',
        'numeric' => ':attribute polje mora biti najmanje :min.',
        'string' => ':attribute polje mora biti najmanje :min karaktera.',
    ],
    'min_digits' => ':attribute polje mora imati najmanje :min cifara.',
    'missing' => ':attribute polje mora biti nedostajuće.',
    'missing_if' => ':attribute polje mora biti nedostajuće kada je :other :value.',
    'missing_unless' => ':attribute polje mora biti nedostajuće osim ako :other nije :value.',
    'missing_with' => ':attribute polje mora biti nedostajuće kada je :values prisutno.',
    'missing_with_all' => ':attribute polje mora biti nedostajuće kada su :values prisutni.',
    'multiple_of' => ':attribute polje mora biti višekratnik od :value.',
    'not_in' => 'Odabrani :attribute je nevažeći.',
    'not_regex' => 'Format :attribute polja je nevažeći.',
    'numeric' => ':attribute polje mora biti broj.',
    'password' => [
        'letters' => ':attribute polje mora sadržavati najmanje jedno slovo.',
        'mixed' => ':attribute polje mora sadržavati najmanje jedno veliko i jedno malo slovo.',
        'numbers' => ':attribute polje mora sadržavati najmanje jedan broj.',
        'symbols' => ':attribute polje mora sadržavati najmanje jedan simbol.',
        'uncompromised' => 'Dati :attribute se pojavio u curenju podataka. Molimo odaberite drugi :attribute.',
    ],
    'present' => ':attribute polje mora biti prisutno.',
    'present_if' => ':attribute polje mora biti prisutno kada je :other :value.',
    'present_unless' => ':attribute polje mora biti prisutno osim ako :other nije :value.',
    'present_with' => ':attribute polje mora biti prisutno kada je :values prisutno.',
    'present_with_all' => ':attribute polje mora biti prisutno kada su :values prisutni.',
    'prohibited' => ':attribute polje je zabranjeno.',
    'prohibited_if' => ':attribute polje je zabranjeno kada je :other :value.',
    'prohibited_unless' => ':attribute polje je zabranjeno osim ako :other nije u :values.',
    'prohibits' => ':attribute polje zabranjuje :other da bude prisutan.',
    'regex' => 'Format :attribute polja je nevažeći.',
    'required' => ':attribute polje je obavezno.',
    'required_array_keys' => ':attribute polje mora sadržavati unose za: :values.',
    'required_if' => ':attribute polje je obavezno kada je :other :value.',
    'required_if_accepted' => ':attribute polje je obavezno kada je :other prihvaćeno.',
    'required_if_declined' => ':attribute polje je obavezno kada je :other odbijeno.',
    'required_unless' => ':attribute polje je obavezno osim ako :other nije u :values.',
    'required_with' => ':attribute polje je obavezno kada je :values prisutno.',
    'required_with_all' => ':attribute polje je obavezno kada su :values prisutni.',
    'required_without' => ':attribute polje je obavezno kada :values nije prisutno.',
    'required_without_all' => ':attribute polje je obavezno kada nijedan od :values nije prisutan.',
    'same' => ':attribute polje mora odgovarati :other.',
    'size' => [
        'array' => ':attribute polje mora sadržavati :size stavki.',
        'file' => ':attribute polje mora biti :size kilobajta.',
        'numeric' => ':attribute polje mora biti :size.',
        'string' => ':attribute polje mora biti :size karaktera.',
    ],
    'starts_with' => ':attribute polje mora počinjati sa jednim od sljedećih: :values.',
    'string' => ':attribute polje mora biti string.',
    'timezone' => ':attribute polje mora biti validna vremenska zona.',
    'unique' => ':attribute je već zauzet.',
    'uploaded' => ':attribute nije uspio upload.',
    'uppercase' => ':attribute polje mora biti velikim slovima.',
    'url' => ':attribute polje mora biti validan URL.',
    'ulid' => ':attribute polje mora biti validan ULID.',
    'uuid' => ':attribute polje mora biti validan UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "rule.attribute". This makes it quick to specify a specific
    | custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'name' => 'ime',
        'email' => 'email',
        'password' => 'lozinka',
        'password_confirmation' => 'potvrda lozinke',
        'current_password' => 'trenutna lozinka',
        'organization_name' => 'naziv organizacije',
        'description' => 'opis',
        'sport' => 'sport',
        'league_name' => 'naziv lige',
        'start_date' => 'datum početka',
        'end_date' => 'datum završetka',
        'player_name' => 'ime igrača',
        'date_of_birth' => 'datum rođenja',
        'position' => 'pozicija',
        'jersey_number' => 'broj dresa',
        'home_score' => 'poeni domaćina',
        'away_score' => 'poeni gosta',
        'scheduled_at' => 'zakazano vrijeme',
        'status' => 'status',
        'role' => 'uloga',
        'subject' => 'naslov',
        'message' => 'poruka',
        'feedback_type' => 'tip povratne informacije',
    ],

];