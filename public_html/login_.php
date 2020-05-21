<?php
if($_SERVER['REQUEST_METHOD']!=="POST" OR !isset($_POST['email'],$_POST['password'])){
    http_response_code(400);
    exit();
}
require './core/conn.php';
http_response_code(200);
$email=htmlspecialchars($_POST['email']);
$pass=htmlspecialchars($_POST['password']);
$nums=$conn->prepare("SELECT id,password FROM login_id WHERE email=? LIMIT 1");
$nums->bind_param("s",$email);
if(!$nums->execute()){
    $nums->close();mysqli_close($conn);
    echo '{"status":"error"}';
    exit();
}
$nums->bind_result($Sno,$password);$nums->fetch();
$nums->close();
if(!password_verify($pass,$password)){
    mysqli_close($conn);
    echo '{"status":"invalid"}';
    exit();
}
$token=bin2hex(openssl_random_pseudo_bytes(64,$cstrong));
$sha=sha1($token);
$nums=$conn->prepare("DELETE FROM token_generater WHERE id=?");
$nums->bind_param("i",$Sno);
$nums->execute();
$nums->close();
$nums=$conn->prepare("INSERT INTO token_generater (id,token_text) VALUE (?,?)");
$nums->bind_param("is",$Sno,$sha);
if(!$nums->execute()){
    $nums->close();mysqli_close($conn);
    echo '{"status":"error"}';
    exit();
}
$nums->close();
echo '{"status":"success","token":"'.$sha.'"}';