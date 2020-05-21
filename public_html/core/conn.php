<?php
$config = parse_ini_file('config.ini');
$conn = mysqli_connect($config['host'],$config['username'],$config['password'],$config['dbname']);
if($conn === false){
    header('Status: 400');
    exit();
}