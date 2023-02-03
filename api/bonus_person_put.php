<?php
include_once '../utils/mail.php';
include_once '../utils/files.php';
include_once '../utils/uuid.php';
include_once '../feature/referral/index.php';

$json = read_body_json();

$email = strtolower(trim($json['email']));
$firstname = trim($json['firstName']);
$lastname = trim($json['lastName']);
$birthday = trim($json['birthday']);
$tel = trim($json['tel']);
$pin = rand(100, 999);


$pdo = createMysqlConnection();
$pdo->beginTransaction();


$statement = $pdo->prepare('
INSERT INTO bonuscard(email, firstname, lastname, birthday,  tel, pin)
VALUES (:email, :firstname, :lastname, :birthday, :tel, :pin)
');
$statement->bindParam('email', $email);
$statement->bindParam('firstname', $firstname);
$statement->bindParam('lastname', $lastname);
$statement->bindParam('birthday', $birthday);
$statement->bindParam('tel', $tel);
$statement->bindParam('pin', $pin);

$result = $statement->execute();

if ($statement->rowCount() != 1) {
    throw new Exception('Could not insert new person');
}

$id = $pdo->lastInsertId();

$pdo->commit();

$url = "https://service.reisinger.pictures/bonus?id=$id&pin=$pin";
sendMail('bonusprogramm@reisinger.pictures', $email, 'florian@reisinger.pictures', 'Zugang zum Bonusprogramm', '
<h1>Zugang zum Bonusprogramm</h1>
' . button($url, 'Bitte klicke hier, um zum Bonusprogramm zu kommen')
);
