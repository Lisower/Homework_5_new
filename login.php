<link rel="stylesheet" href="style.css">
<?php

/**
 * Файл login.php для не авторизованного пользователя выводит форму логина.
 * При отправке формы проверяет логин/пароль и создает сессию,
 * записывает в нее логин и id пользователя.
 * После авторизации пользователь перенаправляется на главную страницу
 * для изменения ранее введенных данных.
 **/

// Отправляем браузеру правильную кодировку,
// файл login.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

// В суперглобальном массиве $_SESSION хранятся переменные сессии.
// Будем сохранять туда логин после успешной авторизации.
$session_started = false;
if ($_COOKIE[session_name()] && session_start()) {
  $session_started = true;
  if (!empty($_SESSION['login'])) {
    // Если есть логин в сессии, то пользователь уже авторизован.
    // TODO: Сделать выход (окончание сессии вызовом session_destroy()
    //при нажатии на кнопку Выход).
    // Делаем перенаправление на форму.
    header('Location: ./');
    exit();
  }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
?>

<form action="" method="post">
  <input name="login" />
  <input name="pass" />
  <input type="submit" value="Войти" />
</form>

<?php
}

else {
  include('credentials.php');
  $db = new PDO('mysql:host=localhost;dbname=u67447', $GLOBALS['user'], $GLOBALS['pass'],
    [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
  $stmt = $db->prepare("select id from Applications where login = ? and pass = ?");
  $stmt->execute([$_POST['login'],$_POST['pass']]);
  $row_count = $stmt->rowCount();
  if ($row_count <= 0) {
      header('Location: login.php');
      print('Пользователя с такими логином и паролем нет в базе данных!');
      exit();
  }
  
  if (!$session_started) {
    session_start();
  }

  $_SESSION['login'] = $_POST['login'];
  $_SESSION['uid'] = iniqid();

  header('Location: ./');
}
