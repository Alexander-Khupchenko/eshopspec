<?php
	// подключение библиотек
	require "secure/session.inc.php";
    require "../inc/lib.inc.php";
    require "../inc/config.inc.php";		

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if (empty($_POST['title']) or empty($_POST['author']) or empty($_POST['pubyear']) or empty($_POST['price'])) 
        exit('Заполните все данные');
    else {
        $title = trim($_POST['title']);
        $author = trim($_POST['author']);
        $pubyear = (int) trim($_POST['pubyear']);
        $price = (int) trim($_POST['price']);
        if(!addItemToCatalog($title, $author, $pubyear, $price))
            echo 'Произошла ошибка при добавлении товара в каталог'; 
        else{ 
            header("Location: add2cat.php"); 
            exit; 
        }
    }    
}
