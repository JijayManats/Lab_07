-- Run this against your database (works the same for local MySQL/XAMPP
-- or an Aiven for MySQL service -- just make sure you're pointed at the
-- right database first, e.g. `USE defaultdb;` on Aiven).

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Required for DB-backed sessions (used so logins work correctly on
-- Vercel's serverless/ephemeral containers, and locally too).
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(128) PRIMARY KEY,
    data MEDIUMTEXT NOT NULL,
    last_activity INT NOT NULL
);

-- If you already have a `users` table from the original lab (no
-- created_at column, no UNIQUE username), run these instead:
-- ALTER TABLE users ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
-- ALTER TABLE users ADD UNIQUE (username);

-- Note: passwords are stored hashed (password_hash / password_verify).
-- Old plaintext-password rows from the original lab still work for login
-- once (login_process.php auto-detects and re-hashes them on first
-- successful login), but it's cleanest to just re-register fresh accounts.
