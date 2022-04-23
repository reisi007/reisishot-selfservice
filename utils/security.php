<?php

include_once 'sql.php';

function checkIsAdmin(PDO $connection, string $userName, string $pwd): bool
{
    // Get salt
    $statement = $connection->prepare("SELECT salt, pwd FROM permissions WHERE user_id = :user");
    $statement->bindParam("user", $userName);
    $statement->execute();

    $user = $statement->fetch();

    return password_verify(
        $pwd . $user["salt"],
        $user["pwd"]
    );
}

function setPassword(PDO $connection, string $validUserName, $validPwd, string $userName, string $pwd)
{
    include_once "uuid.php";

    if (!checkIsAdmin($connection, $validUserName, $validPwd)) {
        throw new Exception("Invalid username / PWD");
    }

    $uuid = uuid();

    $newPwd = password_hash($pwd . $uuid, PASSWORD_BCRYPT, array("cost" => 10));

    // Try update
    $statement = $connection->prepare("UPDATE permissions SET salt = :salt, pwd = :pwd WHERE user_id = :user");

    $statement->bindParam("user", $userName);
    $statement->bindParam("salt", $uuid);
    $statement->bindParam("pwd", $newPwd);

    $statement->execute();

    $rowCount = $statement->rowCount();
    if ($rowCount > 1) {
        throw new Exception("Too many rows changed!");
    }
    if ($rowCount == 1) {
        return;
    }

    $statement = $connection->prepare("INSERT INTO permissions(user_id, salt, pwd) VALUES (:user,:salt,:pwd)");

    $statement->bindParam("user", $userName);
    $statement->bindParam("salt", $uuid);
    $statement->bindParam("pwd", $newPwd);

    $statement->execute();

    if ($statement->rowCount() != 1) {
        throw new Exception("Could not insert new user");
    }

}

