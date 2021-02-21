<?php
include_once "../header/204.php";
include_once "../utils/string.php";
include_once "../utils/sql.php";
include_once "../utils/security.php";
include_once "../utils/files.php";

const hash_algo = "sha3-512";

$mysqli = createMysqlConnection();
$json = read_body_json();
// JSON as individual variables
$insert_user = $json["user"];
$insert_pwd = $json["pwd"];
$contract_filename = $json["contractType"];
$contract_dueDate = $json["dueDate"];
$person_array = $json["persons"];

if (str_contains($contract_filename, "\\") || str_contains($contract_filename, "/"))
    die("Illegal filename " . $contract_filename);

// Check if user is allowed to insert
if (!checkUserInsert($mysqli, $insert_user, $insert_pwd)) {
    die("Wrong PWD");
}

// Load file from disk

$contract_data = file_get_contents("../assets/contracts/" . $contract_filename);
if ($contract_data === false)
    die("Contract not found");
$contract_hash = hash(hash_algo, $contract_data);


$contract_dbid = insert_contract($mysqli, $contract_data, $contract_dueDate, $contract_hash);

$inserted_permissions = insert_permissions($mysqli, $person_array, $contract_dbid);

if (count($person_array) !== count($inserted_permissions))
    die("Die Anzahl an Permissions ist ungleich der angeforderten");

//TODO email senden

/**
 * Insert contract into DB
 * @param mysqli $mysqli
 * @param string $contract_data
 * @param mixed $contract_dueDate
 * @param string $contract_hash
 * @return int|string
 */
function insert_contract(mysqli $mysqli, string $contract_data, mixed $contract_dueDate, string $contract_hash): int|string
{
    $insert_contract_statement = $mysqli->prepare("INSERT INTO contract_data(markdown,due_date,hash_algo,hash_value) VALUES (?,?,?,?)");
    $insert_contract_statement->bind_param("ssss", $contract_data, $contract_dueDate, hash_algo, $contract_hash);
    if (!$insert_contract_statement->execute()) {
        die("Cannot insert contract");
    }
    return $mysqli->insert_id;
}

/**
 * Insert permissions
 * @param mysqli $mysqli
 * @param mixed $persons
 * @param int|string $contract_dbid
 * @return void
 */
function insert_permissions(mysqli $mysqli, array $persons, int|string $contract_dbid): array
{
    $contract_access_statement = $mysqli->prepare("INSERT INTO contract_access(contract_id ,email,firstname,lastname,birthday) VALUES (?,?,?,?,?)");
    foreach ($persons as $person) {
        $contract_access_statement->bind_param("issss", $contract_dbid, $person["email"], $person["firstName"], $person["lastName"], $person["birthday"]);
        if (!$contract_access_statement->execute())
            die("Could not insert access permission");
    }
    $permission_result = $mysqli->query("SELECT email,access_key FROM  contract_access WHERE contract_id = " . $contract_dbid);
    if ($permission_result === false)
        die("No permissions found");
    return $permission_result->fetch_all(MYSQLI_ASSOC);
}
