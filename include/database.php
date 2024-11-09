<?php

$env = parse_ini_file(".env");
$connection = new mysqli($env["DB_HOST"], $env["DB_USERNAME"], $env["DB_PASSWORD"], $env["DB_DATABASE"]);