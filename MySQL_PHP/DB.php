<?php

/**
 * @description Trait ResponseJson 封装后的接口
 */
trait ResponseJson
{
    /**
     * @description  当接口业务出现异常时的返回
     * @param $code
     * @param $message
     * @param $data
     * @return false|string
     */
    public function jsonExceptionData($code,$message,$data)
    {
        return $this->jsonResponse($code,$message,$data);
    }

    /**
     * @description 接口调用请求成功时的返回
     * @param array $data
     * @return false|string
     */
    public function  jsonSuccessData($data=[])
    {
        return $this->jsonResponse(200,'success',$data);
    }

    /**
     * @description 返回一个json
     * @param $code
     * @param $message
     * @param $data
     * @return false|string
     */
    public function jsonResponse($code,$message,$data)
    {
        $content = [
            'code'=>$code,
            'msg'=>$message,
            'data'=>$data
        ];

        return json_encode($content,JSON_UNESCAPED_UNICODE);
    }
}

/**
 * @description Class DB 数据库操作类
 */
class DB
{
    use ResponseJson;

    /**
     * @var string
     * 服务器名
     */
    private  $servername;
    /**
     * @var string
     * 登录的用户名
     */
    private  $username;
    /**
     * @var string
     * 登录密码
     */
    private  $password;
    /**
     * @var string
     * 选择的数据库名
     */
    private  $db_name;
    /**
     * @var mysqli
     * 连接数据库的实例
     */
    private  $conn;

    /**
     * DB constructor
     * 初始化参数
     */
    public function __construct()
    {
        $this->servername = "localhost";
        $this->username = "root";
        $this->password = "1234";
        $this->db_name = "data";
    }

    /**
     * @param string $db_name
     * @description  用于修改连接的数据库
     */
    public function setDbName($db_name)
    {
        $this->db_name = $db_name;
    }

    /**
     * @return mysqli 数据库连接函数
     */
    public function  connect_db()
    {

        // 创建连接
        $conn = new mysqli($this->servername, $this->username, $this->password, $this->db_name);
        // 检测连接
        if ($conn->connect_error) {
            die("连接失败: " . $conn->connect_error);
        }
        return $conn;
    }

    /**
     * @description 需要对输入的sql语句进行判断
     * @param $sqlInfo
     */
    public function select_sql($sqlInfo)
    {
        $conn = $this->connect_db();

        //$sql = "select * from stu";
        $sql = $sqlInfo;

        $result = $conn->query($sql);

        $res_temp = array();
        $ans = array();

        if ($result->num_rows > 0) {
            header("Content-Type: application/json; charset=UTF-8");

            // 获取所有字段的信息
            $fieldInfo = mysqli_fetch_fields($result);

            foreach ($fieldInfo as $val) {
                $res_temp[] = $val->name;
            }
            $ans[] = $res_temp;

            //获取数据信息
            while ($row = $result->fetch_array()) {
                // $row = $result->fetch_object();

                $res_temp = array();
                for ($i = 0; $i < count($row); $i++) {
                    if ($row[$i]) {
                        $res_temp[] = $row[$i];
                    }
                }
                $ans[] = $res_temp;

            }
            // 释放结果集
            mysqli_free_result($result);
        }
        $conn->close();

        $json = $this->jsonSuccessData($ans);

        //$json = json_encode($ans, JSON_UNESCAPED_UNICODE);
        echo $json;
    }

    /**
     * @description  其他数据库操作
     * @param $sqlInfo
     */
    public function other($sqlInfo)
    {
        $conn = $this->connect_db();

        $sql = $sqlInfo;
        $result = false;

        if ($sql != '') {
            $result = $conn->query($sql);
        }

        if ($result == TRUE) {
            echo $this->jsonSuccessData();
        } else {

            $info = "Error:" . $sql . "<br>" . $conn->error;
            echo $this->jsonExceptionData(400,'operation fail',$info);
        }
        $conn->close();

    }

    /**
     * @param $sqlInfo
     * @description  事务操作函数
     */
    public function transaction_operation($sqlInfo)
    {
        $conn  = $this->connect_db();

        $conn->autocommit(false);

        $sql = $this->split_sql($sqlInfo);
        for($i=0;$i<count($sql);$i++)
        {
            $result = $conn->query($sql[$i]);
            if(!$result)
            {
                $conn->rollback();
                echo $this->jsonExceptionData(401,'transaction error',$sql);
                return;
            }
        }

        $conn->commit();
        echo $this->jsonSuccessData($sql);

        $conn->close();
    }

    /**
     * @description 分割sql语句的字符串
     * @param $sqlInfo
     * @return array
     */
    public function split_sql($sqlInfo)
    {
        $res = array();
        $token = strtok($sqlInfo,";");
        while($token!=false)
        {
            $res[] = $token;
            $token = strtok(";");
        }

        return $res;
    }

}