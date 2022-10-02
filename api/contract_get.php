<?php
include_once "../db/fetch_single_secured.php";
include_once '../header/json.php';
include_once '../utils/string.php';

$result = select("
SELECT access_key,
       email,
       firstname,
       lastname,
       birthday,
       cd.markdown,
       additional_text,
       dd.markdown AS dsgvo_markdown,
       ci.hash_algo,
       ci.hash_value,
       due_date
FROM contract_access ca
         JOIN contract_instances ci ON ci.id = ca.contract_id
         JOIN contract_data cd ON cd.id = ci.contract_id
JOIN dsgvo_data dd ON ci.dsgvo_id = dd.id
WHERE email = :email
  AND access_key = :access_key
", createMysqlConnection());

$result['markdown'] = combineMd($result['markdown'], $result['additional_text']);

$result['dsgvo_markdown'] = trimToNull($result['dsgvo_markdown']);

$result = array_diff_key($result, ['additional_text' => 0]);
echo json_encode($result, JSON_THROW_ON_ERROR);

function trimToNull(string $s): string|null
{
    $trimmd = trim($s);
    if (empty($trimmd)) {
        return null;
    }

    return $trimmd;
}
