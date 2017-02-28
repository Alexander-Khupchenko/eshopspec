<?php
/* Параметры базы данных */
const DB_HOST = "localhost";
const DB_LOGIN = "root";
const DB_PASSWORD = "";
const DB_NAME = "eshop";
/* Соединение с БД */
$link = mysqli_connect(DB_HOST, DB_LOGIN, DB_PASSWORD, DB_NAME);
/* Файл с данными пользователя */
const ORDERS_LOG = "orders.log";
/* Корзина пользователя */
$basket = [];
/* Кол-во товара в корзине пользователя */
$count = 0;
/* Создание или чтение корзины пользователя */
basketInit();

// Отслеживаем ошибки при соединении
if(!$link) {
    echo 'Ошибка: '. mysqli_connect_errno() . ':' . mysqli_connect_error();
}

