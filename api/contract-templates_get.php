<?php
include_once '../header/json.php';
include_once '../utils/files.php';

$data = list_dir('../assets/contracts');

echo json_encode($data, JSON_THROW_ON_ERROR);
