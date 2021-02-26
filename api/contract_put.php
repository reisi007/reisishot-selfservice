<?php
include_once "../header/required.php";
include_once "../utils/security.php";
include_once "../utils/string.php";
include_once "../utils/sql.php";
include_once "../utils/mail.php";
include_once "../utils/files.php";

const hash_algo = "sha3-512";

$pdo = createMysqlConnection();
$pdo->beginTransaction();

$json = read_body_json();
// JSON as individual variables
$insert_user = $json["user"];
$insert_pwd = $json["pwd"];
$contract_filename = $json["contractType"];
$additionalText = $json["text"];
$contract_dueDate = $json["dueDate"];
$person_array = $json["persons"];
$base_url = $json["baseUrl"];

if (!isEmailUnique($person_array))
    die();

if (strpos($contract_filename, "\\") !== false || strpos($contract_filename, "/") !== false)
    die("Illegal filename " . $contract_filename);

// Check if user is allowed to insert
if (!checkUserInsert($pdo, $insert_user, $insert_pwd)) {
    die("Wrong PWD");
}

// Load file from disk

$contract_data = file_get_contents("../assets/contracts/" . $contract_filename);
if ($contract_data === false)
    die("Contract not found");

$contract_dbid = insertContract($pdo, $contract_data, $additionalText, $contract_dueDate);

insertPermissions($pdo, $person_array, $contract_dbid, $contract_dueDate, $base_url);

$pdo->commit();

function isEmailUnique($persons): bool
{
    $array = array();
    foreach ($persons as $person) {
        array_push($array, $person["email"]);
    }
    return no_dupes($array);
}

function no_dupes(array $input_array): bool
{
    return count($input_array) === count(array_flip($input_array));
}

/**
 * Insert contract into DB
 * @param PDO $pdo
 * @param string $contraxtData
 * @param string $additionalText
 * @param string $dueDate
 * @return string
 */
function insertContract(PDO $pdo, string $contraxtData, string $additionalText, string $dueDate): string
{
    $contractHash = hash(hash_algo, $contraxtData);
    $fullHash = hash(hash_algo, combineMd($contraxtData, $additionalText));
    $id = insertContractData($pdo, $contractHash, $contraxtData);

    $stmt = $pdo->prepare("INSERT INTO contract_instances(contract_id, additional_text, hash_algo, hash_value, due_date) VALUES (:contractId,:text,:algo,:hash,:dueDate)");
    $stmt->bindParam("contractId", $id);
    $stmt->bindParam("text", $additionalText);
    $algo = hash_algo;
    $stmt->bindParam("algo", $algo);
    $stmt->bindParam("hash", $fullHash);
    $stmt->bindParam("dueDate", $dueDate);
    $stmt->execute();
    return $pdo->lastInsertId();
}

/**
 * @param PDO $pdo
 * @param string $hashedData
 * @param string $contraxtData
 * @return int
 */
function insertContractData(PDO $pdo, string $hashedData, string $contraxtData): int
{
    $hash_algo = hash_algo;
    // Check if contract is already there
    $find = $pdo->prepare("SELECT id FROM contract_data WHERE hash_algo = :algo AND hash_value = :hash");
    $find->bindParam("algo", $hash_algo);
    $find->bindParam("hash", $hashedData);
    $find->execute();
    $column = $find->fetchColumn(0);
    if ($column !== false)
        return $column;

    $statement = $pdo->prepare("INSERT INTO contract_data(markdown,hash_algo,hash_value) VALUES (:md,:algo,:hash)");
    $statement->bindParam("md", $contraxtData);
    $statement->bindParam("algo", $hash_algo);
    $statement->bindParam("hash", $hashedData);
    $statement->execute();

    return $pdo->lastInsertId();
}

/**
 * Insert permissions
 * @param PDO $pdo
 * @param mixed $persons
 * @param string $id
 * @param string $dueDate
 * @param string $base_url
 * @return void
 */
function insertPermissions(PDO $pdo, array $persons, string $id, string $dueDate, string $base_url): void
{
    $insert = $pdo->prepare("INSERT INTO contract_access(contract_id,access_key ,email,firstname,lastname,birthday) VALUES (:id,:key,:email,:first,:last,:birthday)");
    foreach ($persons as $key => $person) {
        $uuid = uuid($pdo);
        $insert->bindParam("id", $id);
        $insert->bindParam("key", $uuid);
        $insert->bindParam("email", $person["email"]);
        $insert->bindParam("first", $person["firstName"]);
        $insert->bindParam("last", $person["lastName"]);
        $insert->bindParam("birthday", $person["birthday"]);
        $insert->execute();

        sendMailInternal($person, $dueDate, $base_url, $uuid);
    }
}

function sendMailInternal(array $person, string $endDate, string $baseUrl, string $access_key): void
{
    $formattedDate = date("d.m.Y h:i", strtotime($endDate));

    $to = $person["email"];
    sendMail(
        "contracts@reisishot.pictures",
        $to,
        "Zugriff zu deinem Vertrag",
        "
<h1>Zugriff zu deinem Vertrag</h1>
 <p>
  Bitte benutze den folgenden Link, um zu deinem Vertrag zu kommen: <a href='$baseUrl/contracts/$to/$access_key'>Link zum Vertrag</a>
</p><p>
Der Vertrag kann bis spätestens $formattedDate unterschrieben werden!
</p>
"
    );
}
