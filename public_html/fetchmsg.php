<?php
if($_SERVER['REQUEST_METHOD']!=="POST" OR !isset($_POST['lobby_id'])){
    http_response_code(400);
    exit();
}
require './core/conn.php';
http_response_code(200);
$lobbyid=(int)$_POST['lobby_id'];
$nums=$conn->prepare("SELECT user_msg,userid,datemsg FROM message WHERE lobbyid=?");
$nums->bind_param("i",$lobbyid);
if(!$nums->execute()){
    $nums->close();mysqli_close($conn);
    echo '{"status":"error"}';
    exit();
}
$nums->store_result();
if(!$nums->num_rows()){
    $nums->close();mysqli_close($conn);
    echo '{"status":"empty"}';
    exit();
}
$arrmsg=array();
$nums->bind_result($user_msg,$userid,$datemsg);
while ($nums->fetch()){
    $extra=$conn->prepare("SELECT l.username,t.token_text FROM login_id l,token_generater t WHERE l.id=? AND l.id=t.id LIMIT 1");
    $extra->bind_param("i",$userid);
    $extra->execute();
    $extra->bind_result($msgusername,$token_text);$extra->fetch();
    $extra->close();
    $arrmsg[]=array('msg'=>$user_msg,'username'=>$msgusername,'token'=>$token_text,'dateadded'=>$datemsg);
}
$nums->close();
echo json_encode($arrmsg);
