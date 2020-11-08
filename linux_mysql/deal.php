<?php
header("Access-Control-Allow-Origin: *");

$sqlInfo = isset($_POST["sqlInfo"]) ? $_POST["sqlInfo"] : '';
$choice = isset($_POST["choice"]) ? $_POST["choice"] : '';

include './DB.php';

/**
 * @description  根据不同的选择值调用不同的函数
 * 2: 表示是事务操作
 * 4: 表示select操作
 * 1: 表示其他操作
 * 6: 表示索引操作
 */
if ($choice) {
    $DB_operation = new DB();
    /**
     * @description  @choice 4：表示select操作 1：表示其他
     */
    if ($choice == "4") {
        $DB_operation->select_sql($sqlInfo);
        //$DB_operation->test();
    }
    else if ($choice == "1"||$choice == "6") {
        $DB_operation->other($sqlInfo);
    }
    else if($choice == "2")
    {
        $DB_operation->transaction_operation($sqlInfo);
        //$DB_operation->split_sql($sqlInfo);
    }

}

?>