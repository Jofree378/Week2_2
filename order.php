<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=orders;charset=utf8;', 'root', 'root');
} catch (PDOException $e) {
    echo $e->getMessage();
}

$address = implode(' ', [$_POST['street'], $_POST['home'], $_POST['part'], $_POST['appt'], $_POST['floor']]);
$email = $_POST['email'];
$userName = $_POST['name'];

if($email) {
    $query = $pdo->prepare("SELECT * FROM users WHERE `email` = :email");
    $query->execute(['email' => $email]);
    if (!$query) {
        echo 1;
        print_r($pdo->errorInfo()); die;
    }
    $user = $query->fetch(PDO::FETCH_LAZY);

    if (!$user) {
        $queryIn = $pdo->prepare("INSERT INTO users (user_name, email, count_orders) values (:userName, :email, '1')");
        $queryIn->execute(['userName' => $userName, 'email' => $email]);
        if (!$queryIn) {
            echo 2;
            print_r($pdo->errorInfo()); die;
        }
    } else {
        $queryUp = $pdo->prepare("UPDATE users SET count_orders = count_orders + 1, user_name = :userName WHERE `email` = :email");
        $queryUp->execute(['userName' => $userName, 'email' => $email]);
        if (!$queryUp) {
            echo 3;
            print_r($pdo->errorInfo()); die;
        }
    }


    $query = $pdo->prepare("SELECT * FROM users WHERE `email` = :email");
    $query->execute(['email' => $email]);
    $userNew = $query->fetch(PDO::FETCH_LAZY);
    $userNumOrder = $userNew['count_orders'];
    $userId = $userNew['id'];


    $orderAdd = $pdo->prepare("INSERT INTO `order` (user_id, `date`, address) values (:userId, NOW(), :address)");
    $orderAdd->execute(['userId' => $userId, 'address' => $address]);
    if (!$orderAdd) {
        echo 4;
        print_r($pdo->errorInfo()); die;
    }
    $orderS = $pdo->prepare("SELECT * FROM `order` ORDER BY order_id DESC LIMIT 1");
    $orderS->execute();
    if (!$orderS) {
        echo 5;
        print_r($pdo->errorInfo()); die;
    }
    $orders = $orderS->fetch(PDO::FETCH_LAZY);
    if ($orders) {
        echo "Спасибо, ваш заказ будет доставлен по адресу: " . $orders['address'] . "<br>";
        echo "Номер вашего заказа: " . $orders['order_id'] . "<br>";
        echo "Это ваш $userNumOrder-й заказ";
    }
}



