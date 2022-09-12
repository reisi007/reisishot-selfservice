CREATE OR REPLACE VIEW contract_access_support AS
SELECT due_date                                                                        AS contract_due_date,
       CONCAT(firstname, ' ', lastname)                                                AS name,
       CONCAT('https://service.reisinger.pictures/contracts/', email, '/', access_key) AS url
FROM contract_access ca
         JOIN contract_instances ci ON ci.id = ca.contract_id
ORDER BY due_date DESC, lastname, firstname
