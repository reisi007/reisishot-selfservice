CREATE TABLE reviews
(
    access_key     TEXT(36)     NOT NULL,
    email          VARCHAR(128) NOT NULL,
    rating         INTEGER      NOT NULL,
    name           VARCHAR(400) NOT NULL,
    creation_date  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    review_private TEXT,
    review_public  TEXT         NOT NULL,

    PRIMARY KEY (access_key(36))
)
