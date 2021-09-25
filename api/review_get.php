<?php
include_once "../db/fetch_multiple_secured.php";

query("
SELECT * FROM review WHERE email = :email AND access_key = :access_key
");
