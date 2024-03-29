--
-- Create Tables
--

-- Stores the important data of the contracts
CREATE TABLE contract_data
(
    id         BIGINT       NOT NULL PRIMARY KEY AUTO_INCREMENT,
    markdown   TEXT         NOT NULL, -- Markdown of the contract
    hash_algo  VARCHAR(64)  NOT NULL, -- Used algorithm for hashing
    hash_value VARCHAR(128) NOT NULL, -- Hash value for field markdown as calculated by hash_algo
    UNIQUE (hash_algo, hash_value)
);

CREATE TABLE dsgvo_data
(
    id       BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    markdown TEXT   NOT NULL -- Markdown of the contract
);

CREATE TABLE contract_instances
(
    id              BIGINT        NOT NULL PRIMARY KEY AUTO_INCREMENT,
    contract_id     BIGINT        NOT NULL,
    dsgvo_id        BIGINT        NOT NULL,
    additional_text VARCHAR(1024) NOT NULL,
    hash_algo       VARCHAR(64)   NOT NULL, -- Used algorithm for hashing
    hash_value      VARCHAR(128)  NOT NULL, -- Hash value for field markdown as calculated by hash_algo
    due_date        DATETIME      NOT NULL, -- Datum + Uhrzeit, bis zu der unterschrieben werden kann
    FOREIGN KEY (contract_id) REFERENCES contract_data (id) ON DELETE CASCADE,
    FOREIGN KEY (dsgvo_id) REFERENCES dsgvo_data (id) ON DELETE CASCADE
);

-- Stores the access "secret"
CREATE TABLE contract_access
(
    access_key  TEXT(36)     NOT NULL,
    email       VARCHAR(128) NOT NULL,
    contract_id BIGINT       NOT NULL,
    firstname   VARCHAR(200) NOT NULL,
    lastname    VARCHAR(200) NOT NULL,
    birthday    DATE         NOT NULL,

    PRIMARY KEY (access_key(36), email(128)),
    FOREIGN KEY (contract_id) REFERENCES contract_instances (id)
        ON DELETE CASCADE
);

-- Stores all changes made by the user e.g SIGN "events"
CREATE TABLE contract_log
(
    contract_id BIGINT       NOT NULL,
    email       VARCHAR(128) NOT NULL,
    timestamp   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    log_type    TEXT(4)      NOT NULL,
    hash_value  VARCHAR(128) NOT NULL,

    PRIMARY KEY (email, contract_id, timestamp, log_type(4)),
    FOREIGN KEY (contract_id) REFERENCES contract_instances (id)
);

-- Access permissions
CREATE TABLE permissions
(
    user_id VARCHAR(255) PRIMARY KEY,
    salt    TEXT(36)     NOT NULL,
    pwd     VARCHAR(255) NOT NULL
);

CREATE TABLE permission_session
(
    user_id   VARCHAR(255) NOT NULL,
    hash      VARCHAR(128) NOT NULL,
    last_used DATETIME     NOT NULL,
    PRIMARY KEY (user_id, hash),
    FOREIGN KEY (user_id) REFERENCES permissions (user_id)
)
