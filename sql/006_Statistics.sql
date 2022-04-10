CREATE TABLE shooting_statistics
(
    item_id       BIGINT   NOT NULL,
    shooting_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    isminor       BIT      NOT NULL DEFAULT FALSE,
    isgroup       BIT      NOT NULL DEFAULT FALSE,
    FOREIGN KEY (item_id) REFERENCES waitlist_item (id)
        ON DELETE CASCADE
);

CREATE OR REPLACE VIEW shooting_statistics_aggregated AS
SELECT YEAR(shooting_date) AS year, item_id, COUNT(item_id) AS cnt, isminor, isgroup
FROM shooting_statistics
GROUP BY YEAR(shooting_date), item_id, isminor, isgroup

