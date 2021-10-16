CREATE TABLE referral_info
(
    referred_person VARCHAR(128) PRIMARY KEY,
    referrer        VARCHAR(128) NOT NULL
);

CREATE TABLE referral_values
(
    id      VARCHAR(50) PRIMARY KEY,
    display VARCHAR(128) NOT NULL,
    value   SMALLINT     NOT NULL
);

CREATE TABLE referral_points_raw
(
    referrer  VARCHAR(128) NOT NULL,
    type      VARCHAR(50)  NOT NULL,
    timestamp DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (type) REFERENCES referral_values (id)
);


CREATE OR REPLACE VIEW referral_points AS
SELECT referrer, SUM(value) AS points
FROM referral_points_raw rpr
         JOIN referral_values rv ON rpr.type = rv.id
GROUP BY referrer;

