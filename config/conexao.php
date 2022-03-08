<?php

$username = 'postgres';
$password = '';
$database = 'sistema_login_crud';
$host = 'localhost';

try {
    $conn = new PDO('pgsql:host='.$host.';port=5432;dbname='.$database, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch(PDOException $e) {
      echo 'ERROR: ' . $e->getMessage();
  }
