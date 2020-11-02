<?php
    $servername = "localhost";
    $username = "root";
    $password = "1234";
    $db_name = "data";
     
    // 创建连接
    $conn = new mysqli($servername, $username, $password,$db_name);
    // 检测连接
    if ($conn->connect_error) 
    {
        die("连接失败: " . $conn->connect_error);
    } 

    $sql = "select * from stu";
    $result = $conn->query($sql);
    $res = array();

    if ($result->num_rows > 0) {
        header("Content-Type: application/json; charset=UTF-8");
        
        while($row = $result->fetch_array()) {
            // $row = $result->fetch_object();
            
            $res[]  = $row['uid'];
         
        }
        
    }  
    $json = json_encode($res,JSON_UNESCAPED_UNICODE);     
    $conn->close();
    echo $json;
?>