<?php
if($_SERVER['REQUEST_METHOD']!=="POST"){
    http_response_code(400);
    exit();
}
require './core/conn.php';
http_response_code(200);
$lobby=mysqli_query($conn,"SELECT Sno,pjoined,lobbyname FROM lobbylist");
$list=[];
while ($fetch=mysqli_fetch_array($lobby)){
    $list[]=array('lobbyid'=>$fetch['Sno'],'lobbyname'=>$fetch['lobbyname'],'joined'=>$fetch['pjoined']);
}
echo json_encode($list);