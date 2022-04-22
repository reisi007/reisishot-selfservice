<?php
include_once "../header/required.php";
include_once "../utils/sql.php";

header('Content-Type: text/plain');
// body
println();
println("=============");
println("Start Cleanup");
println("=============");
println();

$pdo = createMysqlConnection();
$pdo->beginTransaction();
$pdo->commit();

$deleted_sessions = $pdo->exec("DELETE FROM permission_session WHERE last_used < (CURRENT_TIMESTAMP - INTERVAL 10 DAY ) ");

println("Deleted $deleted_sessions sessions....");

println();
println();
println("=============================");
println("Cleanup executed successfully!");
println("=============================");
println();

function println(string $text = "")
{
    echo $text;
    echo "\n";
}
