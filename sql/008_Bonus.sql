CREATE TABLE bonuscard
(
    id        BIGINT AUTO_INCREMENT
        PRIMARY KEY,
    email     VARCHAR(128)                            NOT NULL,
    firstname VARCHAR(200)                            NOT NULL,
    lastname  VARCHAR(200)                            NOT NULL,
    birthday  DATE                                    NOT NULL,
    tel       VARCHAR(32)                             NULL,
    pin       INT DEFAULT (FLOOR(RAND() * 900) + 100) NOT NULL,
    CONSTRAINT email
        UNIQUE (email)
);

CREATE TABLE bonuscard_entries_raw
(
    id        BIGINT AUTO_INCREMENT PRIMARY KEY,
    bonus     BIGINT        NOT NULL,
    text      TEXT          NOT NULL,
    value     DECIMAL(5, 2) NOT NULL,
    expire_at DATE          NOT NULL,
    used      DATE          NOT NULL,
    CONSTRAINT bonuscard_entries_raw_bonuscard_id_fk
        FOREIGN KEY (bonus) REFERENCES bonuscard (id)
            ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE OR REPLACE VIEW bonuscard_summed AS
SELECT b.*, IFNULL(ber.sum, 0) AS sum
FROM bonuscard b
         LEFT JOIN (SELECT bonus,
                           SUM(value
                               ) AS sum
                    FROM bonuscard_entries_raw
                    WHERE expire_at > NOW()
                      AND used IS NULL
                    GROUP BY bonus) ber
                   ON b.id = ber.bonus
