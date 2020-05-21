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
if($status==NULL){
    echo '{"status":"empty"}';
}else{
    $nums=$conn->prepare("SELECT lobbyname FROM lobbylist WHERE Sno=? LIMIT 1");
    $nums->bind_param("i",$status);
    $nums->execute();
    $nums->bind_result($lname);$nums->fetch();$nums->close();
    echo '{"status":"'.$status.'","lobbyname":"'.$lname.'"}';
}