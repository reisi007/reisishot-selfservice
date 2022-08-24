CREATE TABLE review_pictures_access
(
    person BIGINT   NOT NULL,
    folder CHAR(36) NOT NULL,
    PRIMARY KEY (folder, person),
    INDEX (person),
    FOREIGN KEY review_access (person) REFERENCES waitlist_person (id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

CREATE TABLE review_pictures_comment
(
    person   BIGINT       NOT NULL,
    folder   CHAR(36)     NOT NULL,
    filename VARCHAR(128) NOT NULL,
    comment  TEXT         NOT NULL,
    PRIMARY KEY (folder, person, filename),
    FOREIGN KEY (person, folder) REFERENCES review_pictures_access (person, folder)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

CREATE TABLE review_pictures_rating
(
    person   BIGINT        NOT NULL,
    folder   CHAR(36)      NOT NULL,
    filename VARCHAR(128)  NOT NULL,
    rating   DECIMAL(2, 1) NOT NULL,
    PRIMARY KEY (person, folder, filename),
    FOREIGN KEY (person, folder) REFERENCES review_pictures_access (person, folder)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);
