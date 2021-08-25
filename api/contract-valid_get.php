<?php
include_once "../header/json.php";
include_once "../utils/sql.php";
include_once "../utils/string.php";

(function () {
    $pdo = createMysqlConnection();
    $headers = getallheaders();

    $email = $headers['email'];
    $accessKey = $headers['accessKey'];

    $getContractSql = "
 SELECT cd.markdown, ci.additional_text, ci.hash_algo, ci.hash_value, ci.id
 FROM contract_access ca
         JOIN contract_instances ci ON ca.contract_id = ci.id
         JOIN contract_data cd ON cd.id = ci.contract_id
 WHERE email = :email
  AND access_key = :access_key
";

    $getDataStatement = $pdo->prepare($getContractSql);
    $getDataStatement->bindParam("email", $email);
    $getDataStatement->bindParam("access_key", $accessKey);
    $getDataStatement->execute();
    $result = $getDataStatement->fetch();

    $hash_algo = $result["hash_algo"];
    $additional_text = $result["additional_text"];
    $markdown = $result["markdown"];
    $hash_value = $result["hash_value"];
    $contract_id = $result["id"];

    $calculatedHash = hash($hash_algo, combineMd($markdown, $additional_text));
    if ($calculatedHash != $hash_value) {
        echo '{"result":"0"}';
        return;
    }

    $checkSql = "
    SELECT COUNT(*) = 0 AS result
FROM contract_log
WHERE contract_id = :contractId
  AND hash_value != :hashValue
    ";

    $checkStatement = $pdo->prepare($checkSql);
    $checkStatement->bindParam("contractId", $contract_id);
    $checkStatement->bindParam("hashValue", $calculatedHash);
    $checkStatement->execute();

    echo json_encode($checkStatement->fetch(PDO::FETCH_ASSOC));
})();
