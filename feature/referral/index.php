<?php

function setReferral(\PDO $pdo, string|null $referrer, string $referredPerson)
{
    if ($referrer == null) {
        return;
    }

    $statement = $pdo->prepare("
INSERT INTO referral_info(referred_person, referrer)
VALUES (:rp, :r)
ON DUPLICATE KEY UPDATE referrer = :r
");
    $statement->bindParam("r", $referrer);
    $statement->bindParam("rp", $referredPerson);

    $statement->execute();

    if ($statement->rowCount() != 1) {
        throw new Exception("referral could not be set");
    }
}

/**
 * @param PDO $pdo
 * @param string $person
 * @param string $type
 * @throws Exception
 */
function addReferralPoints(\PDO $pdo, string $person, string $type)
{
    $check = $pdo->prepare("SELECT referrer FROM referral_info WHERE referred_person = :rp");
    $check->bindParam("rp", $person);
    $check->execute();
    $referrer = $check->fetchColumn();
    if ($referrer !== false) {
        addReferralPointsDirect($pdo, $referrer, $type);
    }
}

/**
 * @param PDO $pdo
 * @param mixed $referrer
 * @param string $type
 * @param $points
 * @throws Exception
 */
function addReferralPointsDirect(PDO $pdo, string $referrer, string $type): void
{
    $statement = $pdo->prepare("
INSERT INTO referral_points_raw(referrer, type)
VALUES (:r, :t)
");
    $statement->bindParam("r", $referrer);
    $statement->bindParam("t", $type);
    $statement->execute();

    if ($statement->rowCount() != 1) {
        throw new Exception("referral points could not be set");
    }
}
