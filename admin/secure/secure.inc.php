<?
const FILE_NAME = ".htpasswd";


// Генерирует хеш пароля
function getHash($password){ 
    $hash = password_hash($password, PASSWORD_BCRYPT); 
    return $hash; 
}

// Проверяет пароль
function checkHash($password, $hash){ 
    return password_verify(trim($password), trim($hash)); 
}

// Создает новую запись в файле пользователей
function saveUser($login, $hash){ 
    $str = "$login:$hash\n"; 
    if(file_put_contents(FILE_NAME, $str, FILE_APPEND)) 
        return true; 
    else 
        return false; 
}

// Проверяет наличие пользователя в списке
function userExists($login){ 
    if(!is_file(FILE_NAME)) 
        return false; 
    $users = file(FILE_NAME); 
    foreach($users as $user){ 
        if(strpos($user, $login.':') !== false) 
            return $user; 
    } 
    return false; 
}

// Завершает сеанс пользователя
function logOut(){ 
    session_destroy(); 
    header('Location: secure/login.php'); 
    exit; 
}