<?php

require "include/admin.php";

if(!$admin){
	echo json_decode(0);
	exit;
}

if(!isset($_POST["service"], $_POST["userId"], $_POST["paymentFor"])){
	echo json_encode(0);
	exit;
}

require "include/database.php";

[
	"service" => $service,
	"userId" => $userId,
	"paymentFor" => $paymentFor,
] = $_POST;

$statement = $connection->prepare(
	"INSERT INTO `payments` (user, subscription_type, payment_for, date) VALUES (?, ?, ?, ?)"
);
$result = $statement->execute([$userId, $service, $paymentFor, date("Y-m-d")]);

echo json_decode($result);
exit(0);