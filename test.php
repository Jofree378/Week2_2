<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=orders', 'root', 'root');
} catch (PDOException $e) {
    echo $e->getMessage();
}

$orderAdd = $pdo->query("SELECT * FROM `order` where user_id = 10");
$user = $orderAdd->fetch(PDO::FETCH_LAZY);
var_dump($user);
//if (!$orderAdd) {
//    print_r($pdo->errorInfo()); die;
//}
//$orders = $orderAdd->fetch(PDO::FETCH_LAZY);
//
//$num = $pdo->lastInsertId();
////echo "Спасибо, ваш заказ будет доставлен по адресу: " . $orders['address'] . "<br>" ;
////echo "Номер вашего заказа: " . $orders['order_id'] . "<br>";
//echo "Это ваш $num-й заказ";