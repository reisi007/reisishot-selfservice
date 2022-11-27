CREATE TABLE waitlist_item
(
    id             BIGINT       NOT NULL PRIMARY KEY AUTO_INCREMENT,
    short          TEXT(10)     NOT NULL,
    image_id       TEXT         NOT NULL,
    title          TEXT         NOT NULL,
    description    TEXT         NOT NULL,
    available_from DATE         NOT NULL,
    available_to   DATE,
    max_waiting    INT UNSIGNED,
    sort_index     INT UNSIGNED NOT NULL DEFAULT 0,
    UNIQUE (short(10))
);

CREATE TABLE waitlist_person
(
    id           BIGINT       NOT NULL PRIMARY KEY AUTO_INCREMENT,
    email        VARCHAR(128) NOT NULL UNIQUE,
    url          VARCHAR(128) NULL,
    firstname    VARCHAR(200) NOT NULL,
    lastname     VARCHAR(200) NOT NULL,
    birthday     DATE         NOT NULL,
    availability TEXT         NOT NULL,
    phone_number VARCHAR(32)  NOT NULL,
    website      VARCHAR(200),
    access_key   TEXT(36)     NOT NULL,
    ignore_until DATE
);

CREATE TABLE waitlist_entry
(
    item_id       BIGINT NOT NULL,
    person        BIGINT NOT NULL,
    text          TEXT,
    date_assigned BIT    NOT NULL DEFAULT 0,
    FOREIGN KEY (person) REFERENCES waitlist_person (id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES waitlist_item (id)
        ON DELETE CASCADE
);

CREATE UNIQUE INDEX waitlist_entry_item_unique ON waitlist_entry (item_id, person);
