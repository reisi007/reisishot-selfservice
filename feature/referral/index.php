<?php

function setReferral(\PDO $pdo, string|null $referrer, string $referredPerson)
{
    if ($referrer == null)
        return;

    $statement = $pdo->prepare("
INSERT INTO referral_info(referred_person, referrer)
VALUES (:rp, :r)
ON DUPLICATE KEY UPDATE referrer = :r
");
    $statement->bindParam("r", $referrer);
    $statement->bindParam("rp", $referredPerson);

    $statement->execute();

    if ($statement->rowCount() != 1)
        throw new Exception("referral could not be set");
}

function addReferralPoints(\PDO $pdo, string $referredPerson, string $type)
{
    $check = $pdo->prepare("SELECT referrer FROM referral_info WHERE referred_person = :rp");
    $check->bindParam("rp", $referredPerson);
    $check->execute();
    $referrer = $check->fetchColumn();
    if ($referrer === false)
        $referrer = $referredPerson;

    addReferralPointsDirect($pdo, $referrer, $type);
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
    switch ($type) {
        case "waitlist_register":
        case "shooting_good":
            $points = 25;
            break;
        case "shooting_bad":
            $points = -100;
            break;
        default:
            throw new Exception("Type $type is not known");
    }


    $statement = $pdo->prepare("
INSERT INTO referral_points_raw(referrer, type, value)
VALUES (:r, :t, :p)
");
    $statement->bindParam("r", $referrer);
    $statement->bindParam("t", $type);
    $statement->bindParam("p", $points);
    $statement->execute();

    if ($statement->rowCount() != 1)
        throw new Exception("referral points could not be set");
}

function setReferralAndAddPoints(\PDO $pdo, string|null $referrer, string $referredPerson, string $type)
{
    setReferral($pdo, $referrer, $referredPerson);
    addReferralPoints($pdo, $referredPerson, $type);
}
