<?php
include_once "../header/required.php";
include_once "../utils/sql.php";

header('Content-Type: text/plain');
// body
println();
println("=============");
println("Start Cleanup");
println("=============");

$pdo = createMysqlConnection();
$pdo->beginTransaction();

// Delete unused sessions
(function () use ($pdo) {
    $deleted_sessions = $pdo->exec("DELETE FROM permission_session WHERE last_used < (CURRENT_TIMESTAMP - INTERVAL 10 DAY ) ");

    println();
    println("Deleted $deleted_sessions sessions....");
})();

// Send birthday reminder emails
(function () use ($pdo) {
    $birthdayQuery = $pdo->query("SELECT firstname, lastname, email, YEAR(birthday) AS birthday
FROM contract_access
WHERE MONTH(birthday) = MONTH(NOW())
  AND DAY(birthday) = DAY(NOW())
GROUP BY email, birthday
");

    $foundBirthdays = $birthdayQuery->fetchAll(PDO::FETCH_ASSOC);
    $cntBirthdays = count($foundBirthdays);
    if ($cntBirthdays > 0) {
        include_once "../utils/mail.php";

        $body = "<h1>Personen, die heute Geburtstag haben</h1><ul>";

        foreach ($foundBirthdays as $person) {
            $first = $person['firstname'];
            $last = $person['lastname'];
            $email = $person['email'];
            $age = intval(date("Y")) - intval($person['birthday']);
            $body .= "<li>$first $last ($email) - $age Jahre</li>";
        }

        $body .= "</ul>";

        $subject = "🎂🎂🎂 ";
        if ($cntBirthdays === 1) {
            $subject .= "1 Person hat ";
        } else {
            $subject .= "$cntBirthdays Personen haben ";
        }
        $subject .= "heute Geburtstag!";

        println();
        println("Found $cntBirthdays birthdays....");
        sendMail("cron@reisishot.pictures", "florian@reisishot.pictures", null, $subject, $body);
    }
})();

$pdo->commit();

println();
println();
println("=============================");
println("Cleanup executed successfully!");
println("=============================");
println();

function println(string $text = ""): void
{
    echo $text;
    echo "\n";
}
