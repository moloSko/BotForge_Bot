<?php

try {
    $dbh = new PDO('mysql:host=АДРЕС_БД;dbname=название;charset=utf8mb4', 'логин', 'пароль');
} catch (PDOException $e) {
    print "Ошибка!: " . $e->getMessage() . "<br/>";
    die();
}