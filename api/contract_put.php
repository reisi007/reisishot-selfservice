<?php
include_once "../header/required.php";
include_once "../utils/authed_only.php";
include_once "../utils/string.php";
include_once "../utils/mail.php";
include_once "../utils/files.php";
include_once "../utils/uuid.php";

const HASH_ALGO = "sha3-512";

$pdo = createMysqlConnection();
$pdo->beginTransaction();

$json = read_body_json();
// JSON as individual variables
$contract_filename = trim($json["contractType"]);
$additionalText = trim($json["text"]);
$contract_dueDate = trim($json["dueDate"]);
$person_array = $json["persons"];

if (str_contains($contract_filename, "/") || str_contains($contract_filename, "\\")) {
    throw new Exception("Illegal filename " . $contract_filename);
}

// Load file from disk

$contract_data = file_get_contents("../assets/contracts/" . $contract_filename);
if ($contract_data === false) {
    throw new Exception("Contract not found");
}
$contract_data = trim($contract_data);

$dsgvoData = file_get_contents('../assets/dsgvo.md');
if ($dsgvoData === false) {
    throw new Exception('Contract not found');
}
$dsgvoData = trim($dsgvoData);

$contract_dbid = insertContract(
    $pdo,
    $contract_data,
    $additionalText,
    $dsgvoData,
    $contract_dueDate
);

insertPermissions($pdo, $person_array, $contract_dbid, $contract_dueDate);

$pdo->commit();

/**
 * Insert contract into DB
 * @param PDO $pdo
 * @param string $contractData
 * @param string $additionalText
 * @param string $dueDate
 * @return string
 */
function insertContract(PDO $pdo, string $contractData, string $additionalText, string $dsgvo, string $dueDate): string
{
    $contractHash = hash(HASH_ALGO, $contractData);
    $fullHash = hash(HASH_ALGO, combineMd($contractData, $additionalText, $dsgvo));
    $id = insertContractData($pdo, $contractHash, $contractData);
    $dsgvoId = insertDsgvoData($pdo, $dsgvo);

    $stmt = $pdo->prepare("INSERT INTO contract_instances(contract_id ,dsgvo_id, additional_text, hash_algo, hash_value, due_date) VALUES (:contractId,:dsgvoId,:text,:algo,:hash,:dueDate)");
    $stmt->bindParam("contractId", $id);
    $stmt->bindParam("dsgvoId", $dsgvoId);
    $stmt->bindParam("text", $additionalText);
    $algo = HASH_ALGO;
    $stmt->bindParam("algo", $algo);
    $stmt->bindParam("hash", $fullHash);
    $stmt->bindParam("dueDate", $dueDate);
    $stmt->execute();
    return $pdo->lastInsertId();
}

/**
 * @param PDO $pdo
 * @param string $hashedData
 * @param string $contractData
 * @return int
 */
function insertContractData(PDO $pdo, string $hashedData, string $contractData): int
{
    $hash_algo = HASH_ALGO;
    // Check if contract is already there
    $find = $pdo->prepare("SELECT id FROM contract_data WHERE hash_algo = :algo AND hash_value = :hash");
    $find->bindParam("algo", $hash_algo);
    $find->bindParam("hash", $hashedData);
    $find->execute();
    $column = $find->fetchColumn(0);
    if ($column !== false) {
        return $column;
    }

    $statement = $pdo->prepare("INSERT INTO contract_data(markdown,hash_algo,hash_value) VALUES (:md,:algo,:hash)");
    $statement->bindParam("md", $contractData);
    $statement->bindParam("algo", $hash_algo);
    $statement->bindParam("hash", $hashedData);
    $statement->execute();

    return $pdo->lastInsertId();
}

/**
 * @param PDO $pdo
 * @param string $dsgvoData
 * @return int
 */
function insertDsgvoData(PDO $pdo, string $dsgvoData): int
{
    // Check if contract is already there
    $find = $pdo->prepare('SELECT id FROM dsgvo_data WHERE markdown = :md');
    $find->bindParam('md', $dsgvoData);
    $find->execute();
    $column = $find->fetchColumn();
    if ($column !== false) {
        return $column;
    }

    $statement = $pdo->prepare('INSERT INTO dsgvo_data(markdown) VALUES (:md)');
    $statement->bindParam('md', $dsgvoData);
    $statement->execute();

    return $pdo->lastInsertId();
}

/**
 * Insert permissions
 * @param PDO $pdo
 * @param mixed $persons
 * @param string $id
 * @param string $dueDate
 * @return void
 */
function insertPermissions(PDO $pdo, array $persons, string $id, string $dueDate): void
{
    $insert = $pdo->prepare("INSERT INTO contract_access(contract_id,access_key ,email,firstname,lastname,birthday) VALUES (:id,:key,:email,:first,:last,:birthday)");
    foreach ($persons as $key => $person) {

        $uuid = uuid();
        $email = strtolower(trim($person["email"]));
        $firstName = ucwords(trim($person["firstName"]));
        $lastName = ucwords(trim($person["lastName"]));
        $birthday = trim($person["birthday"]);
        $insert->bindParam("id", $id);
        $insert->bindParam("key", $uuid);
        $insert->bindParam("email", $email);
        $insert->bindParam("first", $firstName);
        $insert->bindParam("last", $lastName);
        $insert->bindParam("birthday", $birthday);
        $insert->execute();

        sendMailInternal($person, $dueDate, $uuid);
    }
}

function sendMailInternal(array $person, string $endDate, string $access_key): void
{
    $formattedDate = date("d.m.Y H:i", strtotime($endDate));

    $to = $person["email"];

    $url = "https://reisinger.pictures/contract?email=$to&accessKey=$access_key";

    sendMail(
        "contracts@reisinger.pictures",
        $to, null,
        "Zugriff zu deinem Vertrag",
        "<h1>Zugriff zu deinem Vertrag</h1>" .
        p("Bitte benutze den folgenden Link, um zu deinem Vertrag zu kommen:")
        . button($url, "Link zum Vertrag")
        . p("Der Vertrag kann bis spätestens $formattedDate unterschrieben werden!")
    );
}
