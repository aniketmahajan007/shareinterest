<?php
if($_SERVER['REQUEST_METHOD']!=="POST" OR !isset($_POST['token'])){
    http_response_code(400);
    exit();
}
require './core/conn.php';
http_response_code(200);
$token=htmlspecialchars($_POST['token']);
$nums=$conn->prepare("SELECT id FROM token_generater WHERE token_text=? LIMIT 1");
$nums->bind_param("s",$token);
if(!$nums->execute()) {
    $nums->close();mysqli_close($conn);
    echo '{"status":"error"}';
    exit();
}
$nums->store_result();
if($nums->num_rows()<1){
    $nums->close();mysqli_close($conn);
    echo '{"status":"error"}';
    exit();
}
$nums->bind_result($id);$nums->fetch();
$nums->close();
$nums=$conn->prepare("SELECT lobby_in FROM login_id WHERE id=? LIMIT 1");
$nums->bind_param("i",$id);
if(!$nums->execute()){
    $nums->close();mysqli_close($conn);
    echo '{"status":"error"}';
    exit();
}
$nums->bind_result($status);$nums->fetch();
$nums->close();
if($status!=NULL){
    $nums=$conn->prepare("UPDATE lobbylist SET pjoined=pjoined-1 WHERE Sno=? LIMIT 1");
    $nums->bind_param("i",$status);$nums->execute();$nums->close();
    $nums=$conn->prepare("UPDATE login_id SET lobby_in=NULL WHERE id=? LIMIT 1");
    $nums->bind_param("i",$id);$nums->execute();$nums->close();
}
$nums=$conn->prepare("DELETE FROM token_generater WHERE token_text=?");
$nums->bind_param("s",$token);
if($nums->execute()) {
    echo '{"status":"success"}';
}else{
    echo '{"status":"error"}';
}
$nums->close();
