CREATE TABLE referral_info
(
    referrer        VARCHAR(128) NOT NULL,
    referred_person VARCHAR(128) PRIMARY KEY
);

CREATE TABLE referral_values
(
    id      VARCHAR(50) PRIMARY KEY,
    display VARCHAR(128) NOT NULL,
    value   SMALLINT     NOT NULL
);

INSERT INTO referral_values (id, display, value)
VALUES ('manual_thx_100', 'Manuelle Änderung +100', 100);
INSERT INTO referral_values (id, display, value)
VALUES ('perk_more-people', 'Shooting mit mehreren Personen (ausgenommen Pärchen)', -50);
INSERT INTO referral_values (id, display, value)
VALUES ('perk_no-public', 'Shooting ohne verpflichtende Veröffentlichung von Bildern', -100);
INSERT INTO referral_values (id, display, value)
VALUES ('perk_pics_25+', 'Mehr als 25 Bilder (< 35 Bilder) bei einem Shooting', -25);
INSERT INTO referral_values (id, display, value)
VALUES ('shooting_bad', 'Negative Shootingerfahrung', -100);
INSERT INTO referral_values (id, display, value)
VALUES ('shooting_good', 'Positive Shootingerfahrung', 25);
INSERT INTO referral_values (id, display, value)
VALUES ('waitlist_register', 'Registrierung einer Person via Referral Link', 25);
INSERT INTO referral_values (id, display, value)
VALUES ('waitlist_register_self', 'Bonus für die Registrierung', 25);

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


CREATE TABLE referral_values
(
    id      VARCHAR(50)  NOT NULL
        PRIMARY KEY,
    display VARCHAR(128) NOT NULL,
    value   SMALLINT     NOT NULL
);
