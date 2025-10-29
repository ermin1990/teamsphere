-- Fix Organization Access Issue
-- This script ensures user has proper access to their organizations

-- Check current state
SELECT 'Current organization_user:' as info;
SELECT * FROM organization_user;

-- Add user to organization if not already a member
-- User ID 1 (ermin1990@gmail.com) should be member of Organization ID 1 (Asee d.o.o.)
INSERT INTO organization_user (organization_id, user_id, role, created_at, updated_at)
SELECT 1, 1, 'admin', NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM organization_user 
    WHERE organization_id = 1 AND user_id = 1
);

-- Add user to organization if not already a member
-- User ID 1 should be member of Organization ID 5 (Test Organization)
INSERT INTO organization_user (organization_id, user_id, role, created_at, updated_at)
SELECT 5, 1, 'admin', NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM organization_user 
    WHERE organization_id = 5 AND user_id = 1
);

-- Verify the fix
SELECT 'Updated organization_user:' as info;
SELECT ou.*, u.email, o.name as organization_name 
FROM organization_user ou
JOIN users u ON ou.user_id = u.id
JOIN organizations o ON ou.organization_id = o.id;

-- Show organizations and their owners
SELECT 'Organizations and owners:' as info;
SELECT o.id, o.name, o.user_id as owner_id, u.email as owner_email
FROM organizations o
JOIN users u ON o.user_id = u.id;
