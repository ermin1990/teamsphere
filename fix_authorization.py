#!/usr/bin/env python3
"""
Automatski zamjenjuje manual authorization checks sa policy-based authorization
"""

import re
import os
from pathlib import Path

# Mapiranje metoda na policy akcije
METHOD_TO_POLICY = {
    'index': 'view',
    'show': 'view',
    'create': 'update',
    'store': 'update',
    'edit': 'update',
    'update': 'update',
    'destroy': 'update',
    'delete': 'update',
}

def fix_controller(filepath):
    """Popravi jedan controller file"""
    print(f"\n📝 Processing: {filepath}")
    
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    original_content = content
    changes_made = 0
    
    # Pattern 1: if ($organization->user_id !== auth()->id()) { abort(403); }
    pattern1 = r'(\s+)// Ensure user owns this organization\n\s+if \(\$organization->user_id !== auth\(\)->id\(\)\) \{\n\s+abort\(403\);\n\s+\}'
    replacement1 = r'\1// Use policy for authorization\n\1$this->authorize(\'update\', $organization);'
    
    content, count1 = re.subn(pattern1, replacement1, content)
    changes_made += count1
    
    # Pattern 2: if ($organization->user_id !== Auth::id()) { abort(403); }
    pattern2 = r'(\s+)if \(\$organization->user_id !== Auth::id\(\)\) \{\n\s+abort\(403\);\n\s+\}'
    replacement2 = r'\1// Use policy for authorization\n\1$this->authorize(\'update\', $organization);'
    
    content, count2 = re.subn(pattern2, replacement2, content)
    changes_made += count2
    
    # Pattern 3: Simple inline checks without comment
    pattern3 = r'(\s+)if \(\$organization->user_id !== auth\(\)->id\(\)\) \{\n\s+abort\(403\);\n\s+\}'
    replacement3 = r'\1// Use policy for authorization\n\1$this->authorize(\'update\', $organization);'
    
    content, count3 = re.subn(pattern3, replacement3, content)
    changes_made += count3
    
    # Pattern 4: Multi-line authorization with player/referee checks (show methods)
    # Need to replace with policy + keep the variables
    pattern4 = r'''(\s+)// Allow access if user owns the organization OR is registered as a player in it OR is a referee
\s+\$isOwner = \$organization->user_id === auth\(\)->id\(\);
\s+\$isPlayer = \$organization->players\(\)->where\('user_id', auth\(\)->id\(\)\)->exists\(\);
\s+\$isReferee = auth\(\)->user\(\)->organizationUsers\(\)
\s+->where\('organization_id', \$organization->id\)
\s+->where\('role', 'referee'\)
\s+->exists\(\);

\s+if \(!\$isOwner && !\$isPlayer && !\$isReferee\) \{
\s+abort\(403\);
\s+\}'''
    
    replacement4 = r'''\1// Use the policy for authorization
\1$this->authorize('view', $organization);

\1// Set variables for the view
\1$isOwner = $organization->user_id === auth()->id();
\1$isPlayer = $organization->players()->where('user_id', auth()->id())->exists();
\1$isReferee = $organization->users()
\1    ->where('users.id', auth()->id())
\1    ->where('organization_user.role', 'referee')
\1    ->exists();'''
    
    content, count4 = re.subn(pattern4, replacement4, content, flags=re.MULTILINE)
    changes_made += count4
    
    if changes_made > 0:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"✅ Made {changes_made} changes")
        return True
    else:
        print(f"⏭️  No changes needed")
        return False

def main():
    """Main function"""
    controllers_dir = Path('app/Http/Controllers')
    
    # Controllers to fix
    controllers = [
        'CompetitionController.php',
        'PlayerController.php',
        'LeagueController.php',
        'OrganizationController.php',
        'OrganizationUserController.php',
        'TableController.php',
    ]
    
    total_fixed = 0
    
    print("🚀 Starting automatic authorization fix...\n")
    
    for controller in controllers:
        filepath = controllers_dir / controller
        if filepath.exists():
            if fix_controller(filepath):
                total_fixed += 1
        else:
            print(f"⚠️  Not found: {filepath}")
    
    print(f"\n\n✨ Done! Fixed {total_fixed} controllers")
    print("\n📋 Next steps:")
    print("1. Review changes with: git diff")
    print("2. Test locally")
    print("3. Commit: git add . && git commit -m 'Replace manual authorization with policy-based authorization'")
    print("4. Upload to production")

if __name__ == '__main__':
    main()
