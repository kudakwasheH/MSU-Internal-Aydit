CREATE DATABASE IF NOT EXISTS internal_audit CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'internal_audit'@'localhost' IDENTIFIED BY 'InternalAudit@2026';
GRANT ALL PRIVILEGES ON internal_audit.* TO 'internal_audit'@'localhost';
FLUSH PRIVILEGES;
