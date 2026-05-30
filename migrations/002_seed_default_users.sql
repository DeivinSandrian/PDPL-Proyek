-- Seed/update default login accounts used by the current app.
-- Passwords are bcrypt hashes for: admin123 and cust123.

INSERT INTO users (email, username, password, role, email_verified_at, created_at, updated_at)
VALUES
    ('admin@travelgo.com', 'admin123', '$2b$12$kqBSKNhozEdrfQG2vP6rDeic2GrI2B7dyYOeFMFSWLWx85vLWw8.O', 'admin', NOW(), NOW(), NOW()),
    ('budi@example.com', 'budi', '$2b$12$BBJ3NMvHk4CAuHQ4CljFG.6oAn4OqqwDwYrP0iGu1qC4dHKyxGiXS', 'customer', NOW(), NOW(), NOW())
ON DUPLICATE KEY UPDATE
    username = VALUES(username),
    password = VALUES(password),
    role = VALUES(role),
    email_verified_at = COALESCE(email_verified_at, VALUES(email_verified_at)),
    updated_at = NOW();
