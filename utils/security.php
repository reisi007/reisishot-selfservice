<?php

function checkUserInsert(mysqli $connection, string $user, string $pwd): bool
{
    // Get salt
    $statement = $connection->prepare("SELECT salt FROM contract_permissions WHERE USER = ?");
    $statement->bind_param("s", $user);
    if (!$statement->execute())
        return false;
    $mysqli_result = $statement->get_result();
    if ($mysqli_result === false)
        return false;
    $salt = $mysqli_result->fetch_row()[0];
    if ($salt === false)
        return false;

    $db_pwd = password_hash(
        $pwd,
        PASSWORD_BCRYPT,
        array(
            "cost" => 20,
            "salt" => $salt
        )
    );

    $check_statement = $connection->prepare("SELECT USER FROM contract_permissions WHERE pwd = ?");
    $check_statement->bind_param("s", $db_pwd);
    if ($check_statement->execute())
        return false;
    $check_result = $check_statement->get_result();
    if ($check_result === false)
        return false;

    return $check_result->fetch_row() !== null;
}
