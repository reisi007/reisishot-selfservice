<?php
include_once "sql.php";

function safeArray(array $array, string $key)
{
    if (array_key_exists($key, $array)) {
        return $array[$key];
    }
    return false;
}

function isAuthed(): bool
{
    $headers = getallheaders();

    $user = safeArray($headers, "Email");
    if (!$user) {
        $user = $_GET["email"];
    }

    $pwd = safeArray($headers, 'Accesskey');
    if (!$pwd) {
        $pwd = safeArray($_GET, "auth");
    }
    if (!$pwd) {
        $pwd = safeArray($_GET, "accesskey");
    }

    $user = trim($user);
    $pwd = trim($pwd);

    $pdo = createMysqlConnection();
    $pdo->beginTransaction();

    $statement = $pdo->prepare("UPDATE permission_session SET last_used = CURRENT_TIMESTAMP WHERE user_id = :user AND hash = :hash");
    $statement->bindParam("user", $user);
    $statement->bindParam("hash", $pwd);
    $statement->execute();

    if ($statement->rowCount() !== 1) {
        $same = $pdo->prepare("SELECT COUNT(*) FROM permission_session WHERE user_id =:user AND hash = :hash");
        $same->bindParam("user", $user);
        $same->bindParam("hash", $pwd);
        $same->execute();
        $count = $same->fetchColumn();
        $isValid = $count === '1';
        if ($isValid) {
            $pdo->commit();
        } else {
            $pdo->rollBack();
        }
        return $isValid;
    }
    $pdo->commit();
    return true;
}
