<?php

if(!isset($_GET["type"])) die;

$type = $_GET["type"];
if(!in_array($type, ["spotify", "youtube"])) die;

//set id of the payment to search for
if($type === "spotify"){
    $type = 1;
    $startMonth = 8;
}else{ //$type === "youtube"
    $type = 2;
    $startMonth = 12;
}

require "include/admin.php";
require "include/database.php";

//Load types
$typesResult = $connection->query("SELECT * FROM subscription_types");
$types = [];
$subscriptions = [];
while($row = $typesResult->fetch_assoc()){
    $id = $row["id"];
    $types[$id] = $row;
    $subscriptions[$id] = [];
}

//Load subscriptions
$subscriptionsResult = $connection->query(
    "SELECT subscriptions.*, users.username
    FROM subscriptions
    JOIN users ON subscriptions.user = users.id
    WHERE subscription_type=$type"
);
$currentDate = strtotime(date("m/d/Y"));
$paid = [];
while($row = $subscriptionsResult->fetch_assoc()){
    $userId = $row["user"];
    $row["from"] = strtotime($row["from"]);
    $row["until"] = $row["until"] === null ? $currentDate : strtotime($row["until"]);
    $subscriptions[$row["subscription_type"]][$userId] = $row;
    if($userId != 1) $paid[$userId] = [];
}

//Load payments
$result = $connection->query(
    "SELECT users.id, users.username, subscription_types.name AS `service`, payments.payment_for, 'zaplaceno' AS `state`
    FROM payments
    JOIN users ON users.id = payments.user
    JOIN subscription_types ON subscription_types.id = payments.subscription_type
    WHERE subscription_type=$type"
);

if(!$result){
    echo json_encode(["error" => "Query failed: " . $connection->error]);
    die;
}

$data = [];

while($row = $result->fetch_assoc()){
    $row["action"] = "";
    $data[] = $row;
    $userId = $row["id"];
    $paid[$userId][] = $row["payment_for"];
}

$date = strtotime("$startMonth/01/2023");
$current = time();
while($date <= $current){
    $index = date("y/m", $date);
    foreach($paid as $userId => $dates){
        $user = $subscriptions[$type][$userId];
        if(!in_array($index, $dates) && ($date <= $user["until"] && $date >= $user["from"])){
            $row = [
                "username" => $user["username"],
                "service" => $types[$type]["name"],
                "payment_for" => $index,
                "state" => "nezaplaceno",
            ];
            if($admin){
                $row["action"] =
                    "<button
                        class='btn btn-sm btn-primary pay-button'
                        data-service='$type'
                        data-user-id='$userId'
                        data-payment-for='$index'
                    >cinkačka🤑</button>";
            }
            $data[] = $row;
        }
    }
    $date = strtotime("+1 month", $date);
}

$result->free();
$connection->close();

header("Content-Type: application/json");
echo json_encode(["data" => $data]);