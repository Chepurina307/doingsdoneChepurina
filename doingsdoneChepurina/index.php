<?php


$con = mysqli_connect("localhost", "root", "root", "schema");
if(!$con){
	print("Error connected: " . mysqli_connect_error());
}else{

mysqli_set_charset($con, "utf8");


//ВАЖНО:  ФИЛЬТРАЦИИ
    function esc($str) {
        $text = htmlspecialchars($str);
        return $text;
    }



    $user_id = intval(1);


    $show_complete_tasks = 0; //Показ выполненных здч
    if (isset($_GET['show_completed'])) {
        $show_complete_tasks = intval($_GET['show_completed']);
    }


    //Получение проекта
    $sql = "SELECT ID, Name, Autor, (SELECT COUNT(*) FROM `task` WHERE `Category` = `project`.ID) AS `Amount`  FROM `project` WHERE `Autor` = ".$user_id;
    $result = mysqli_query($con, $sql);
    $projectz = mysqli_fetch_all($result, MYSQLI_ASSOC);


    if (isset($_GET['project'])) {
        $prjnm = esc($_GET['project']);

        //Проверка проекта
        $sql = "SELECT ID FROM `project` WHERE `Autor` = ".$user_id." AND `Name` = '".strval($prjnm)."'";
        $result = mysqli_query($con, $sql);
        $idprjnm =  mysqli_fetch_all($result, MYSQLI_ASSOC);

        if(count($idprjnm)!=0){
            foreach ($idprjnm as $fdfd => $key){
                $idprj = $key['ID'];
            };
            //Получение задач
            $sql = "SELECT * FROM `task` WHERE `Autor` = ".$user_id." AND `Category` = ".$idprj;
            $result = mysqli_query($con, $sql);
            $taskz = mysqli_fetch_all($result, MYSQLI_ASSOC);
        }else{

            http_response_code(404);

            //Получение задачей
            $sql = "SELECT * FROM `task` WHERE `Autor` = " . $user_id;
            $result = mysqli_query($con, $sql);
            $taskz = mysqli_fetch_all($result, MYSQLI_ASSOC);
        }


    }else {
        //Получение задачей
        $sql = "SELECT * FROM `task` WHERE `Autor` = " . $user_id;
        $result = mysqli_query($con, $sql);
        $taskz = mysqli_fetch_all($result, MYSQLI_ASSOC);
    };







require_once("helpers.php");

$page_content = include_template('main.php',
    ['projectz' => $projectz,'taskz' => $taskz, 'show_complete_tasks' => $show_complete_tasks]);

$layout_content = include_template('layout.php', [
'content' => $page_content,
'title' => 'Дела в порядке'
]);
print($layout_content);
}



?>
