<?php

function checkUserInsert(PDO $connection, string $user, string $pwd): bool
{
    // Get salt
    $statement = $connection->prepare("SELECT salt, pwd FROM contract_permissions WHERE USER = :user");
    $statement->bindParam("user", $user);
    $statement->execute();

    $user = $statement->fetch();
    // $newPwd = password_hash($pwd . $user["salt"], PASSWORD_BCRYPT, array("cost" => 10));

    return password_verify(
        $pwd . $user["salt"],
        $user["pwd"]
    );
}
