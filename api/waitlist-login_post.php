<?php
include_once "../header/json.php";
include_once "../utils/sql.php";
include_once "../utils/mail.php";
include_once "../utils/files.php";
include_once "../utils/uuid.php";
include_once "../feature/referral/index.php";

$json = read_body_json();

$referrer = isset($json['referrer']) ? trim($json['referrer']) : null;
$email = trim($json['email']);

$reset = isset($json["reset"]) && string2boolean($json["reset"]);

$pdo = createMysqlConnection();
$pdo->beginTransaction();

if ($reset) {
    $access_key = uuid();
    $statement = $pdo->prepare("UPDATE waitlist_person SET access_key = :access_key WHERE email = :email");
    $statement->bindParam("email", $email);
    $statement->bindParam("access_key", $access_key);

    $statement->execute();

    if ($statement->rowCount() != 1) {
        throw new Exception("Could not update access key");
    }
} else {
    $statement = $pdo->prepare("SELECT access_key FROM waitlist_person WHERE email = :email");
    $statement->bindParam("email", $email);
    $statement->execute();
    if ($statement->rowCount() != 1) {
        throw new Exception('Could not find person');
    }
    $access_key = $statement->fetchColumn();
}

/** @noinspection PhpUnhandledExceptionInspection */
setReferral($pdo, $referrer, $email);

$pdo->commit();

$url = "https://service.reisinger.pictures/waitlist/$email/$access_key";

sendMail("waitlist@reisinger.pictures", $email, "florian@reisinger.pictures", "Zugang zur Warteliste", "
<h1>Zugang zur Warteliste</h1>
" . button($url, 'Bitte klicke hier, um dich für ein Shooting anzumelden') . "
<p>Wenn du hier keinen Link siehst, schick mir bitte eine Nachricht und ich lasse dir den Link per SMS zukommen</p>
");

/**
 * @param mixed $string
 * @return mixed
 */
function string2boolean(mixed $string): mixed
{
    return json_decode(strtolower($string));
}
