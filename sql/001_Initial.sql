-- Drop Tables if exists
DROP TABLE IF EXISTS contract_permissions;
DROP TABLE IF EXISTS contract_log;
DROP TABLE IF EXISTS contract_access;
DROP TABLE IF EXISTS contract_data;
--
-- Create Tables
--

-- Stores the important data of the contracts
CREATE TABLE contract_data
(
    id         BIGINT       NOT NULL PRIMARY KEY AUTO_INCREMENT,
    markdown   TEXT         NOT NULL, -- Markdown of the contract
    hash_algo  VARCHAR(64)  NOT NULL, -- Used algorithm for hashing
    hash_value VARCHAR(512) NOT NULL, -- Hash value for field markdown as calculated by hash_algo
    due_date   DATE         NOT NULL  -- Datum + Uhrzeit, bis zu der unterschrieben werden kann
);

-- Stores the access "secret"
CREATE TABLE contract_access
(
    access_key  TEXT(36)     NOT NULL DEFAULT UUID(),
    email       VARCHAR(512) NOT NULL,
    contract_id BIGINT       NOT NULL,
    firstname   VARCHAR(200) NOT NULL,
    lastname    VARCHAR(200) NOT NULL,
    birthday    DATE         NOT NULL,
    PRIMARY KEY (access_key(36)),
    UNIQUE (email, contract_id),
    FOREIGN KEY (contract_id) REFERENCES contract_data (id)
        ON DELETE CASCADE
);

-- Stores all changes made by the user e.g SIGN "events"
CREATE TABLE contract_log
(
    contract_id BIGINT        NOT NULL,
    email       VARCHAR(512)  NOT NULL,
    timestamp   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    log_type    TEXT(6)       NOT NULL,
    log_entry   VARCHAR(2048) NOT NULL,

    PRIMARY KEY (email, contract_id, timestamp),
    FOREIGN KEY (contract_id) REFERENCES contract_data (id)
);

-- Access permissions
CREATE TABLE contract_permissions
(
    user VARCHAR(255) PRIMARY KEY,
    salt TEXT(36)     NOT NULL,
    pwd  VARCHAR(255) NOT NULL
)
