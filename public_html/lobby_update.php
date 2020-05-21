<?php
if($_SERVER['REQUEST_METHOD']!=="POST" OR !isset($_POST['token'],$_POST['lobby_id'])){
    http_response_code(400);
    exit();
}
require './core/conn.php';
http_response_code(200);
$token=htmlspecialchars($_POST['token']);
$lobbyid=(int)$_POST['lobby_id'];
if($lobbyid<0){
    mysqli_close($conn);
    echo '{"status":"error"}';
    exit();
}
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
$nums->bind_result($lobby_in);$nums->fetch();$nums->close();
if($lobby_in!=NULL && $lobbyid!=$lobby_in){
    $nums=$conn->prepare("SELECT lobbyname FROM lobbylist WHERE Sno=? LIMIT 1");
    $nums->bind_param("i",$lobby_in);
    $nums->execute();
    $nums->bind_result($lname);$nums->fetch();$nums->close();
    echo '{"status":"redirect","lobbyname":"'.$lname.'","lid":"'.$lobby_in.'"}';
    exit();
}
if($lobbyid==$lobby_in){
    echo '{"status":"success"}';
    exit();
}
mysqli_autocommit($conn,FALSE);$qsuccess=1;
$nums=$conn->prepare("UPDATE lobbylist SET pjoined=pjoined+1 WHERE Sno=? LIMIT 1");
$nums->bind_param("i",$lobbyid);
if(!$nums->execute()){
    $qsuccess=0;
}
$nums->close();
if($qsuccess){
    $nums=$conn->prepare("UPDATE login_id SET lobby_in=? WHERE id=? LIMIT 1");
    $nums->bind_param("ii",$lobbyid,$id);
    if(!$nums->execute()){
        $qsuccess=0;
    }
    $nums->close();
}
if($qsuccess){
    mysqli_commit($conn);
    echo '{"status":"success"}';
}else{
    mysqli_rollback($conn);
    echo '{"status":"error"}';
}