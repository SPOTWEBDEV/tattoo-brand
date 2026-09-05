-- Bare Skin Studio — database schema
-- Run this once against a fresh database, e.g.:
--   mysql -u root -p bare_skin_studio < schema.sql

CREATE TABLE IF NOT EXISTS orders (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ref              VARCHAR(20)  NOT NULL UNIQUE,
    customer_name    VARCHAR(120) NOT NULL,
    email            VARCHAR(160) NOT NULL,
    phone            VARCHAR(40)  NOT NULL,
    tattoo_title     VARCHAR(160) NOT NULL,
    size_label       VARCHAR(40)  NOT NULL,
    service_type     ENUM('shop','home') NOT NULL DEFAULT 'shop',
    service_location VARCHAR(255) DEFAULT '',
    item_total       DECIMAL(10,2) NOT NULL DEFAULT 0,
    fee              DECIMAL(10,2) NOT NULL DEFAULT 0,
    deposit          DECIMAL(10,2) NOT NULL DEFAULT 0,
    balance          DECIMAL(10,2) NOT NULL DEFAULT 0,
    total            DECIMAL(10,2) NOT NULL DEFAULT 0,
    appointment_at   DATETIME NULL,
    notes            TEXT,
    status           ENUM('upcoming','completed','cancelled') NOT NULL DEFAULT 'upcoming',
    created_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS messages (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    msg_ref     VARCHAR(20)  NOT NULL UNIQUE,
    name        VARCHAR(120) NOT NULL,
    email       VARCHAR(160) NOT NULL,
    placement   VARCHAR(160) DEFAULT '',
    message     TEXT NOT NULL,
    is_read     TINYINT(1)   NOT NULL DEFAULT 0,
    replied_at  TIMESTAMP NULL,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_read (is_read),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
