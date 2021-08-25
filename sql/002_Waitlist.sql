CREATE TABLE waitlist_item
(
    id             BIGINT       NOT NULL PRIMARY KEY AUTO_INCREMENT,
    image_id       TEXT         NOT NULL,
    title          TEXT         NOT NULL,
    description    TEXT         NOT NULL,
    available_from DATE         NOT NULL,
    available_to   DATE,
    max_waiting    INT UNSIGNED,
    sort_index     INT UNSIGNED NOT NULL DEFAULT 0
);

CREATE TABLE waitlist_entry
(
    item_id       BIGINT       NOT NULL,
    secret        TEXT         NOT NULL,
    email         VARCHAR(128) NOT NULL,
    firstname     VARCHAR(200) NOT NULL,
    lastname      VARCHAR(200) NOT NULL,
    birthday      DATE         NOT NULL,
    availability  TEXT         NOT NULL,
    phone_number  VARCHAR(32)  NOT NULL,
    website       VARCHAR(200),
    text          TEXT,
    done_customer BIT          NOT NULL DEFAULT 0,
    done_internal BIT          NOT NULL DEFAULT 0,
    UNIQUE (secret, email),
    FOREIGN KEY (item_id) REFERENCES waitlist_item (id)
        ON DELETE CASCADE
)
