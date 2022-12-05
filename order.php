<?php
// Подключение библиотеки PDO
try {
    $pdo = new PDO('mysql:host=localhost;dbname=orders;charset=utf8;', 'root', 'root');
} catch (PDOException $e) {
    echo $e->getMessage();
}

// Присваивание переменным данные из формы
$address = implode(' ', [$_POST['street'], $_POST['home'], $_POST['part'], $_POST['appt'], $_POST['floor']]);
$email = $_POST['email'];
$userName = $_POST['name'];

//Проверка на объявление переменных
if($email) {
    // Запрос на выборку
    $query = $pdo->prepare("SELECT * FROM users WHERE `email` = :email");
    $query->execute(['email' => $email]);
    if (!$query) {
        echo "Запрос на поиск пользователя не выполнен";
        print_r($pdo->errorInfo()); die;
    }

    //Проверка на существование пользовавтеля
    $user = $query->fetch(PDO::FETCH_LAZY);
    if (!$user) {
        // Добавление нового пользователя с первым его заказом
        $queryIn = $pdo->prepare("INSERT INTO users (user_name, email, count_orders) values (:userName, :email, '1')");
        $queryIn->execute(['userName' => $userName, 'email' => $email]);
        if (!$queryIn) {
            echo "Запрос на создание пользователя не выполнен";
            print_r($pdo->errorInfo()); die;
        }
    } else {
        // Увеличение количества заказов пользователя
        $queryUp = $pdo->prepare("UPDATE users SET count_orders = count_orders + 1, user_name = :userName WHERE `email` = :email");
        $queryUp->execute(['userName' => $userName, 'email' => $email]);
        if (!$queryUp) {
            echo "Запрос на увеличение количества заказов не выполнен";
            print_r($pdo->errorInfo()); die;
        }
    }

    // Присваиваем переменным данные о пользователе и его заказе
    $query = $pdo->prepare("SELECT * FROM users WHERE `email` = :email");
    $query->execute(['email' => $email]);
    $userNew = $query->fetch(PDO::FETCH_LAZY);
    $userNumOrder = $userNew['count_orders'];
    $userId = $userNew['id'];

    // Пополняем таблицу заказов
    $orderAdd = $pdo->prepare("INSERT INTO `order` (user_id, `date`, address) values (:userId, NOW(), :address)");
    $orderAdd->execute(['userId' => $userId, 'address' => $address]);
    if (!$orderAdd) {
        echo "Запрос на дополнение таблицы заказов не выполнен";
        print_r($pdo->errorInfo()); die;
    }

    // Выбираем строку с данными последнего заказ
    $orderS = $pdo->prepare("SELECT * FROM `order` ORDER BY order_id DESC LIMIT 1");
    $orderS->execute();
    if (!$orderS) {
        echo "Запрос на выборку данных о заказе не выполнен";
        print_r($pdo->errorInfo()); die;
    }
    $orders = $orderS->fetch(PDO::FETCH_LAZY);

    // Выводим данные о заказе
    if ($orders) {
        echo "Спасибо, ваш заказ будет доставлен по адресу: " . $orders['address'] . "<br>";
        echo "Номер вашего заказа: " . $orders['order_id'] . "<br>";
        echo "Это ваш $userNumOrder-й заказ";
    }
}



