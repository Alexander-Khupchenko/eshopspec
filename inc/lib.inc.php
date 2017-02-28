<?php
// Деббагинг кода
function debug($arr){
    echo '<pre>' . print_r($arr, true) . '</pre>';
}

// Сохранение нового товара в таблицу catalog
// Функция принимает в виде аргументов название, автора, год издания и цену товара 
function addItemToCatalog($title, $author, $pubyear, $price){
    global $link;
    $sql = 'INSERT INTO catalog (title, author, pubyear, price) VALUES (?, ?, ?, ?)';
    if (!$stmt = mysqli_prepare($link, $sql)) 
        return false; 
    mysqli_stmt_bind_param($stmt, "ssii", $title, $author, $pubyear, $price);
    mysqli_stmt_execute($stmt); 
    mysqli_stmt_close($stmt); 
    return true;
}

// Содержимое каталога товаров в виде ассоциативного массива
function selectAllItems(){
    global $link;
    $sql = 'SELECT id, title, author, pubyear, price FROM catalog';
    if(!$result = mysqli_query($link, $sql)) 
        return false; 
    $items = mysqli_fetch_all($result, MYSQLI_ASSOC); 
    mysqli_free_result($result); 
    return $items;
}

// Сохранение корзины с товарами в куки
function saveBasket(){
    global $basket, $count;
    $basket = base64_encode(serialize($basket));
    setcookie('basket', $basket, 0x7FFFFFFF);   
}

// Cоздает либо загружает в переменную $basket корзину с товарами, 
// либо создает новую корзину с идентификатором заказа 
function basketInit(){
    global $basket, $count;
    if(!isset($_COOKIE['basket'])){
        $basket = ['orderid' => uniqid()];
        saveBasket();
    }else{
        $basket = unserialize(base64_decode($_COOKIE['basket']));
        $count = count($basket) - 1; 
    }
}

// добавляет товар в корзину пользователя и  
// принимает к качестве аргумента идентификатор товара 
function add2Basket($id){
    global $basket;
    $basket[$id] = 1;
    saveBasket();   
}

// Возвращает всю пользовательскую корзину в виде ассоциативного массива 
function myBasket(){
    global $link, $basket;
    $goods = array_keys($basket);
    array_shift($goods);
    if(!$goods)
        return false;
    $ids = implode(",", $goods);
    $sql = "SELECT id, author, title, pubyear, price 
            FROM catalog WHERE id IN ($ids)";
    if(!$result = mysqli_query($link, $sql)) 
        return false;
    $items = result2Array($result);
    mysqli_free_result($result); 
    return $items;
}

// Принимает результат выполнения функции myBasket и
// возвращает ассоциативный массив товаров, дополненный их количеством 
function result2Array($data){
    global $basket;
    $arr = [];
    while($row = mysqli_fetch_assoc($data)){ 
        $row['quantity'] = $basket[$row['id']]; 
        $arr[] = $row; 
    } 
    return $arr;
}

//
function deleteItemFromBasket($id){
    global $basket;
    unset($basket[$id]);
    saveBasket();
}

// Пересохраняет товары из корзины в таблицу базы данных orders
function saveOrder($datetime){
    global $link, $basket; 
    $goods = myBasket(); 
    $stmt = mysqli_stmt_init($link); 
    $sql = 'INSERT INTO orders (
                                title, 
                                author, 
                                pubyear, 
                                price, 
                                quantity, 
                                orderid, 
                                datetime) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)'; 
    if (!mysqli_stmt_prepare($stmt, $sql)) 
        return false; 
    foreach($goods as $item){ 
        mysqli_stmt_bind_param($stmt, "ssiiisi", 
        $item['title'], $item['author'],
        $item['pubyear'], $item['price'],
        $item['quantity'], 
        $basket['orderid'], 
        $datetime);
        mysqli_stmt_execute($stmt); 
    } 
    mysqli_stmt_close($stmt);
    setcookie('basket', "", 1);
    return true;
}

// Возвращает многомерный массив с информацией 
// о всех заказах, включая персональные данные покупателя и список его товаров 
function getOrders(){
    global $link; 
    if(!is_file(ORDERS_LOG))
        return false;
    /* Получаем в виде массива персональные данные пользователей из файла */
    $orders = file(ORDERS_LOG);
    /* Массив, который будет возвращен функцией */ 
    $allorders = []; 
    foreach ($orders as $order) { 
        list($name, $email, $phone, $address, $date, $orderid) = explode("|", trim($order)); 
        /* Промежуточный массив для хранения информации о конкретном заказе */
        $orderinfo = []; 
        /* Сохранение информацию о конкретном пользователе */ 
        $orderinfo["name"] = $name; 
        $orderinfo["email"] = $email; 
        $orderinfo["phone"] = $phone; 
        $orderinfo["address"] = $address;  
        $orderinfo["date"] = $date;
        $orderinfo["orderid"] = $orderid;
        /* SQL-запрос на выборку из таблицы orders всех товаров для конкретного покупателя */ 
        $sql = "SELECT title, author, pubyear, price, quantity 
                        FROM orders 
                        WHERE orderid = '$orderid'";
        /* Получение результата выборки */ 
        if(!$result = mysqli_query($link, $sql)) 
            return false; 
        $items = mysqli_fetch_all($result, MYSQLI_ASSOC); 
        mysqli_free_result($result); 
        /* Сохранение результата в промежуточном массиве */ 
        $orderinfo["goods"] = $items; 
        /* Добавление промежуточного массива в возвращаемый массив */ 
        $allorders[] = $orderinfo; 
    } 
    return $allorders;
}

// Пересчет корзины
function getSum(){
    $sum += $item['price'] * $item['quantity']*2;
    return $sum;
}