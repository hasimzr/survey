<?php
//$db = new PDO('mysql:host=localhost; dbname=survey; charset=utf8', 'root', '');
$db = new PDO('mysql:host=localhost; dbname=ozerhasi_survey; charset=utf8', 'ozerhasi_parzemis', 'P;pCt1T2TMKp');

if($_POST){
    $surveyİd = addslashes(strip_tags(htmlspecialchars(trim($_POST["survey"]))));
    $optiponİd = addslashes(strip_tags(htmlspecialchars(trim($_POST["optipon"]))));    
    $options = $db->query("SELECT * FROM `option` WHERE `id` = $optiponİd", PDO::FETCH_OBJ)->fetch();
    $survey = $db->query("SELECT * FROM `survey` WHERE `id` = $surveyİd", PDO::FETCH_OBJ)->fetch();
    $vote = $options->vote + 1;
    $vote2 = $survey->vote + 1;
    $options = $db->query("UPDATE `option` SET `vote` = '$vote' WHERE `option`.`id` = $optiponİd;", PDO::FETCH_OBJ)->fetch();
    $options = $db->query("UPDATE `survey` SET `vote` = '$vote2' WHERE `survey`.`id` = $surveyİd;", PDO::FETCH_OBJ)->fetch();
    $uservote = $db->query("INSERT INTO `uservote` (`id`, `Userİd`, `Surveyİd`) VALUES (NULL, '{$_COOKIE["userİd"]}', '$surveyİd');", PDO::FETCH_OBJ)->fetch();
}
if(isset($_COOKIE["userİd"])){
    $surveyİd = addslashes(strip_tags(htmlspecialchars(trim($_GET["survey"]))));
    $uservote = $db->query("SELECT * FROM `uservote` WHERE `surveyİd` = $surveyİd AND `Userİd` = {$_COOKIE["userİd"]}", PDO::FETCH_OBJ)->fetch();

    if($uservote){
        echo "<h1 align='center'>Bu anketi daha önce çözdünüz</h1>";
    }else{
        $bugün = date("Y-m-d");
        $surveyİd = addslashes(strip_tags(htmlspecialchars(trim($_GET["survey"]))));
        $surveyasfa = $db->query("SELECT * FROM `survey` WHERE `id` = $surveyİd", PDO::FETCH_OBJ)->fetch();
        if (strtotime($bugün) <= strtotime($surveyasfa->date)){
            ?>
           <form align="center" action="" method="post">
    
               <?php
                $surveyİd = addslashes(strip_tags(htmlspecialchars(trim($_GET["survey"]))));    
                $options = $db->query("SELECT * FROM `option` WHERE `surveyİd` = $surveyİd", PDO::FETCH_OBJ)->fetchAll();
                foreach($options as $option){
                    ?><input type="radio" name="optipon" value="<?php echo $option->id ?>"> <?php echo $option->optionName."<br><br>";
                }
               ?>
               <input type="hidden" name="survey" value="<?php echo $surveyİd; ?>">
               <input type="submit" value="Anketi Tamamla">
           </form>
            <?php  
        }else{
            echo "<h1 align='center'>Bu Anketin süresi doldu</h1>";
        }
        
    }

}else{


    $userAdd = $db->query("INSERT INTO `user` (`userİd`) VALUES (NULL);", PDO::FETCH_OBJ)->fetch();
    $user = $db->query("SELECT * FROM `user` ORDER BY `user`.`userİd` DESC", PDO::FETCH_OBJ)->fetch();

    setcookie("userİd", $user->userİd, time() + (86400 * 10800), "/");

    ?>
    <meta http-equiv="refresh" content="0;">
    <?php
}

?>