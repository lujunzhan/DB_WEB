<?php
header("Access-Control-Allow-Origin: *");

$sqlinfo = isset($_POST["sqlinfo"]) ? $_POST["sqlinfo"] : '';
$choice = isset($_POST["choice"])? $_POST["choice"] : '';

//需要对输入的sql语句进行判断
function select_sql($sqlinfo)
{
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
    $sql = $sqlinfo;

    $result = $conn->query($sql);

    $res_temp = array();
    $ans = array();

    if ($result->num_rows > 0) {
        header("Content-Type: application/json; charset=UTF-8");
        
        while($row = $result->fetch_array()) {
            // $row = $result->fetch_object();
            for($i=0;$i<count($row);$i++)
            {
                if($row[$i])
                {
                    $res_temp[]  = $row[$i];
                }
                
            }
            $ans[] = $res_temp;
            $res_temp = array();
         
        }
        
    }  
    $json = json_encode($ans,JSON_UNESCAPED_UNICODE);     
    $conn->close();
    echo $json;
}

function other($sqlinfo)
{   
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

    $sql = $sqlinfo;
	if($sql!='')
	{
		$result = $conn->query($sql);
	}


	if($result == TRUE)
	{
		echo "操作成功！";
	}
	else
	{
		echo "Error:".$sql."<br>".$conn->error;
	}
	
}
if($choice=="4")
{
	
}
select_sql($sqlinfo);

?>