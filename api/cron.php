<?php
include_once "../header/required.php";
include_once "../utils/sql.php";

header('Content-Type: text/plain');

println("Starte Job");
println();

$pdo = createMysqlConnection();
$pdo->beginTransaction();
$pdo->commit();

$deleted_sessions = $pdo->exec("DELETE FROM permission_session WHERE last_used < (CURRENT_TIMESTAMP - INTERVAL 10 DAY ) ");

println("Deleted $deleted_sessions sessions....");

println();
println("Job erfolgreich ausgeführt!");


function println(string $text = "")
{
    echo $text;
    echo "\n";
}
