<?php
	require "inc/lib.inc.php";
	require "inc/config.inc.php";

    $n = $_POST['name'];
    $e = $_POST['email'];
    $p = $_POST['phone'];
    $a = $_POST['address'];
    $dt = time();
    $oid = $basket['orderid'];
    $order = "$n|$e|$p|$a|$dt|$oid\n";
    file_put_contents("admin/".ORDERS_LOG, $order, FILE_APPEND);
    saveOrder($dt);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Сохранение данных заказа</title>
</head>
<body>
	<p>Ваш заказ принят.</p>
	<p><a href="catalog.php">Вернуться в каталог товаров</a></p>
</body>
</html>