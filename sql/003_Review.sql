CREATE TABLE reviews
(
    access_key     TEXT(36)     NOT NULL,
    email          VARCHAR(128),
    rating         INTEGER,
    name           VARCHAR(400) NOT NULL,
    creation_date  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    review_private TEXT,
    review_public  TEXT,

    PRIMARY KEY (access_key(36))
);

CREATE INDEX reviews_email USING HASH ON reviews (email(128)) ALGORITHM NOCOPY
