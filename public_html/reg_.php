<?php
if($_SERVER['REQUEST_METHOD']!=="POST" OR !isset($_POST['username'],$_POST['email'],$_POST['password'],$_POST['c_password'])){
    http_response_code(400);
    return;
}
require './core/conn.php';
http_response_code(200);
//sanitize
$username=htmlspecialchars($_POST['username']);
$nums=$conn->prepare("SELECT username FROM login_id WHERE username=? LIMIT 1");
$nums->bind_param("s",$username);
if(!$nums->execute()){
    $nums->close();mysqli_close($conn);
    echo '{"status":"error"}';
    exit();
}
$nums->store_result();
if($nums->num_rows){
    $nums->close();mysqli_close($conn);
    echo '{"status":"user_already"}';
    exit();
}
$nums->close();
$email=htmlspecialchars($_POST['email']);
$nums=$conn->prepare("SELECT username FROM login_id WHERE email=? LIMIT 1");
$nums->bind_param("s",$email);
if(!$nums->execute()){
    $nums->close();mysqli_close($conn);
    echo '{"status":"error"}';
    exit();
}
$nums->store_result();
if($nums->num_rows){
    $nums->close();mysqli_close($conn);
    echo '{"status":"email_already"}';
    exit();
}
$nums->close();
$pass=htmlspecialchars($_POST['password']);
if($pass!==$_POST['password']){
    mysqli_close($conn);
    echo '{"status":"pass_invalid"}';
    exit();
}
$cpass=htmlspecialchars($_POST['c_password']);
if($pass!==$cpass){
    mysqli_close($conn);
    echo '{"status":"pass_not_same"}';
    exit();
}
$pass=password_hash($pass,PASSWORD_DEFAULT,array('cost'=>10));
mysqli_autocommit($conn,false);
$qsuccess=1;
$nums=$conn->prepare("INSERT INTO login_id (username,email,password) VALUE (?,?,?)");
$nums->bind_param("sss",$username,$email,$pass);
if(!$nums->execute()){
    $qsuccess=0;
}
if($qsuccess){
    $Sno=$nums->insert_id;
    $nums->close();
    $token=bin2hex(openssl_random_pseudo_bytes(64,$cstrong));
    $sha=sha1($token);
    $nums=$conn->prepare("INSERT INTO token_generater (id,token_text) VALUE (?,?)");
    $nums->bind_param("is",$Sno,$sha);
    if(!$nums->execute()){
        $qsuccess=0;
    }
    $nums->close();
}else{
    $nums->close();
}
if($qsuccess){
    mysqli_commit($conn);
    echo '{"status":"success","token":"'.$sha.'"}';
}else{
    mysqli_rollback($conn);
    echo '{"status":"error"}';
}