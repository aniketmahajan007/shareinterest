<?php
if($_SERVER['REQUEST_METHOD']!=="POST" OR !isset($_POST['token'])){
    http_response_code(400);
    exit();
}
require './core/conn.php';
http_response_code(200);
$token=htmlspecialchars($_POST['token']);
$nums=$conn->prepare("SELECT login_id.username FROM token_generater,login_id WHERE token_text=? AND login_id.id=token_generater.id");
$nums->bind_param("s",$token);
if(!$nums->execute()) {
    $nums->close();mysqli_close($conn);
    echo '{"status":"error"}';exit();
}
$nums->store_result();
if(!$nums->num_rows()){
    $nums->close();mysqli_close($conn);
    echo '{"status":"error"}';exit();
}
$nums->bind_result($username);
$nums->fetch();$nums->close();mysqli_close($conn);
echo '{"username":"'.$username.'"}';