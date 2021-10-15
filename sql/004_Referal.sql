CREATE TABLE referral_info
(
    referred_person VARCHAR(128) PRIMARY KEY,
    referrer        VARCHAR(128) NOT NULL
);

CREATE TABLE referral_points_raw
(
    referrer  VARCHAR(128) NOT NULL,
    type      TEXT(4)      NOT NULL,
    value     SMALLINT     NOT NULL,
    timestamp DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE OR REPLACE VIEW referral_points AS
SELECT referrer, SUM(value) AS points
FROM referral_points_raw
GROUP BY referrer;
