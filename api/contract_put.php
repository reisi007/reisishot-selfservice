<?php
include_once "../header/required.php";
include_once "../utils/security.php";
include_once "../utils/string.php";
include_once "../utils/sql.php";
include_once "../utils/mail.php";
include_once "../utils/files.php";

const hash_algo = "sha3-512";

$pdo = createMysqlConnection();
$json = read_body_json();
// JSON as individual variables
$insert_user = $json["user"];
$insert_pwd = $json["pwd"];
$contract_filename = $json["contractType"];
$contract_dueDate = $json["dueDate"];
$person_array = $json["persons"];
$base_url = $json["baseUrl"];

if (str_contains($contract_filename, "\\") || str_contains($contract_filename, "/"))
    die("Illegal filename " . $contract_filename);

// Check if user is allowed to insert
if (!checkUserInsert($pdo, $insert_user, $insert_pwd)) {
    die("Wrong PWD");
}

// Load file from disk

$contract_data = file_get_contents("../assets/contracts/" . $contract_filename);
if ($contract_data === false)
    die("Contract not found");
$contract_hash = hash(hash_algo, $contract_data);


$contract_dbid = insertContract($pdo, $contract_data, $contract_dueDate, $contract_hash);

insertPermissions($pdo, $person_array, $contract_dbid);


$pdo->commit();

foreach ($person_array as $person) {
    sendMailInternal($person, $base_url);
}

/**
 * Insert contract into DB
 * @param mysqli $pdo
 * @param string $contraxtData
 * @param mixed $dueDate
 * @param string $hashedData
 * @return int|string
 */
function insertContract(PDO $pdo, string $contraxtData, mixed $dueDate, string $hashedData): int|string
{
    $statement = $pdo->prepare("INSERT INTO contract_data(markdown,due_date,hash_algo,hash_value) VALUES (:md,:due,:algo,:hash)");
    $statement->bindParam("md", $contraxtData);
    $statement->bindParam("due", $dueDate);
    $hash_algo = hash_algo;
    $statement->bindParam("algo", $hash_algo);
    $statement->bindParam("hash", $hashedData);
    $statement->execute();
    return $pdo->lastInsertId();
}

/**
 * Insert permissions
 * @param PDO $pdo
 * @param mixed $persons
 * @param int|string $id
 * @return void
 */
function insertPermissions(PDO $pdo, array &$persons, int|string $id): void
{
    $insert = $pdo->prepare("INSERT INTO contract_access(contract_id ,email,firstname,lastname,birthday) VALUES (:id,:email,:first,:last,:birthday)");
    $access = $pdo->prepare("SELECT access_key FROM  contract_access WHERE contract_id = :id");
    foreach ($persons as $key => $person) {
        $insert->bindParam("id", $id);
        $insert->bindParam("email", $person["email"]);
        $insert->bindParam("first", $person["firstName"]);
        $insert->bindParam("last", $person["lastName"]);
        $insert->bindParam("birthday", $person["birthday"]);
        $insert->execute();

        $access->bindParam("id", $id);
        $access->execute();
        $persons[$key]["access_key"] = $access->fetchColumn(0);
    }
}

function sendMailInternal(array $person, string $baseUrl): void
{

    $access_key = $person["access_key"];
    $to = $person["email"];
    sendMail(
        "contracts@reisishot.pictures",
        $to,
        "Zugriff zu deinem Vertrag",
        "<h1>Zugriff zu deinem Vertrag</h1>
 <p>
  Bitte benutze den folgenden Link, um zu deinem Vertrag zu kommen: <a href='$baseUrl/contracts/$to/$access_key'>Link zum Vertrag</a>
</p>"
    );
}
