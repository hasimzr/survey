<?php
    session_start();
    //$db = new PDO('mysql:host=localhost; dbname=survey; charset=utf8', 'root', '');
    $db = new PDO('mysql:host=localhost; dbname=ozerhasi_survey; charset=utf8', 'ozerhasi_parzemis', 'P;pCt1T2TMKp');


    if($_POST){
    

        if(isset($_POST["userName"])){
            if($_POST["userName"] == "admin" && $_POST["passwords"] == "1234"){


                $_SESSION["admin"] = "admin";
                header("location:admin.php");


            }else{


                echo "Şifre veya parola hatalı";


            }
        }
        if(isset($_POST["surveyName"])){


            $surveyName    = addslashes(strip_tags(htmlspecialchars(trim($_POST["surveyName"]))));
            $surveyDate    = addslashes(strip_tags(htmlspecialchars(trim($_POST["surveyDate"]))));
            $survey        = $db->query("INSERT INTO `survey` (`id`, `name`, `date`, `vote`) VALUES (NULL, '$surveyName', '$surveyDate', '0');", PDO::FETCH_OBJ)->fetch();
            $optionNumber2 = $_POST["optionNumber2"];

            
            $surveys = $db->query("SELECT * FROM `survey`", PDO::FETCH_OBJ)->fetchAll();
            $id = 0;
            foreach($surveys as $survey){
                $id++;
            }

            for($i=1; $i<=$optionNumber2; $i++){

                $optionName  = addslashes(strip_tags(htmlspecialchars(trim($_POST["optionName".$i]))));
                $option      = $db->query("INSERT INTO `option` (`id`, `optionName`, `vote`, `surveyİd`) VALUES (NULL, '$optionName', '0', '$id');", PDO::FETCH_OBJ)->fetch();
                   
              }

              header("location:admin.php");

            
        }
        if(isset($_POST["surveyNameUpdate"])){

    
            $surveyNameUpdate = addslashes(strip_tags(htmlspecialchars(trim($_POST["surveyNameUpdate"]))));
            $surveyDate       = addslashes(strip_tags(htmlspecialchars(trim($_POST["surveyDate"]))));
            $surveyİdUpdate   = addslashes(strip_tags(htmlspecialchars(trim($_POST["surveyİdUpdate"]))));
            $survey           = $db->query("UPDATE `survey` SET `name` = '$surveyNameUpdate', `date` = '$surveyDate' WHERE `survey`.`id` = $surveyİdUpdate;", PDO::FETCH_OBJ)->fetch(); 

            
            $options = $db->query("SELECT * FROM `option` WHERE `surveyİd` = $surveyİdUpdate", PDO::FETCH_OBJ)->fetchAll();
            foreach($options as $option){
                $optionValue = addslashes(strip_tags(htmlspecialchars(trim($_POST["optionName".$option->id]))));
                $survey = $db->query("UPDATE `option` SET `optionName` = '$optionValue' WHERE `option`.`id` = $option->id;", PDO::FETCH_OBJ)->fetch();     
            }

               header("location:admin.php");

            
        }




    }




    
    if(isset($_SESSION["admin"])){


        if(!$_GET){
            ?>
                

                <style>
                    th,td{
                        padding: 8px;
                    }
                </style>
                
                <div align="center">


                    <a href="admin.php?add-survey=1">Anket Oluştur</a>
                    <br><br>

                    <table>

                        <tr>

                           <th>Sıra</th>
                           <th>Anket Adı<th>
                           <th>Anket Süresi</th>
                           <th>Oy Kullanım Sayısı</th>

                        </tr>


                        <?php


                             $surveys = $db->query("SELECT * FROM `survey`", PDO::FETCH_OBJ)->fetchAll();
                             foreach($surveys as $survey){
                                 ?>


                                     <tr>


                                        <td><?php echo $survey->id;   ?></td>
                                        <td><?php echo $survey->name; ?><td>
                                        <td><?php echo $survey->date; ?></td>
                                        <td><?php echo $survey->vote; ?></td>
                                        <td><a href="show-survey.php?survey=<?php echo $survey->id; ?>">Ankete Git</a></td>
                                        <td><a href="admin.php?answer-id=<?php echo $survey->id; ?>">Sonuçları Göster</a></td>
                                        <td><a href="admin.php?del-id=<?php echo $survey->id; ?>">Sil</a></td>
                                        <td><a href="admin.php?update-id=<?php echo $survey->id; ?>">Güncelle</a></td>


                                     </tr>
                                 <?php
                             }

                        
                        ?>                        


                    </table>


                </div>


            <?php
        }

        if(isset($_GET["add-survey"])){

            
            ?>
            
               <div align="center">


                   <a href="admin.php">Yönetim paneline geri dön</a>

                   <form action="" method="get">


                       <br>
                       <input type="number" name="optionNumber" placeholder="Kaç Adet Seçenek Olacak?" >
                       <input type="submit" value="Bir Sonraki Adıma Geç">


                   </form>


               </div>

            <?php

        }

        if(isset($_GET["optionNumber"])){

            
            ?>
            
               <div align="center">


                   <a href="admin.php?add-survey=1">Bir Önceki Adıma Geri Dön.</a>

                   <form action="" method="post">
                    

                       <br>
                       <input type="text" name="surveyName" placeholder="Anket Adını girin.">
                       <br>

                       <?php


                          for($i=1; $i<=$_GET["optionNumber"]; $i++){

                            echo "<br><input type='text' name='optionName$i' placeholder='Seçeneğin Adını Girin.'>";
                               
                          }


                       ?>
                       <br><br>
                       <input type="hidden" name="optionNumber2" value="<?php echo $_GET["optionNumber"]; ?>">
                       <br>
                       Bu Tarihe kadar Geçerli: <input type="date" name="surveyDate" />
                       <br><br>
                       <input type="submit" value="Bir Sonraki Adıma Geç">


                   </form>


               </div>

            <?php

        }

        if(isset($_GET["answer-id"])){

            
            $surveyId    = addslashes(strip_tags(htmlspecialchars(trim($_GET["answer-id"]))));
            $options = $db->query("SELECT * FROM `option` WHERE `surveyİd` = $surveyId", PDO::FETCH_OBJ)->fetchAll();

            $r = 0;
            $totalVote = 0;
            foreach($options as $option){
                $totalVote += $option->vote;
            }
            foreach($options as $option){
                $r++;

                $optionName[$r] = $option->optionName;
                $vote[$r]       = $option->vote;
                
                if($option->vote == 0){
                    $optinDer[$r]   = 0;
                }else{
                    $optinDer[$r]   = (100 / ($totalVote / $option->vote));
                }



            }
            ?>
               

               <div align="center">
               <?php
               
                   $survey = $db->query("SELECT * FROM `survey` WHERE `id` = $surveyId", PDO::FETCH_OBJ)->fetch();
               
               ?>

               <h1><?php echo  $survey->name; ?></h1>
               <a href="admin.php">Admin Sayfasına Dön</a>
               <br><br>

               
               <div style="background-color: grey; padding: 5px;">Toplam: <?php echo $totalVote; ?> Oy Kullanıldı</div>
               </div>
               <br>

               <div style="width: 500px;  border: 2px solid black;    margin-left: auto;    margin-right: auto;    border-top: none;    border-right: none;    padding-top: 18px;">
               <?php
                  
                  for($c=1; $c<=$r; $c++){

                      ?>


                        <div style="width: 100% height: 16px;"><div style="background-color: red; padding: 5px;  height: 16px; width: <?php echo $optinDer[$c]; ?>%;"><div style="width: 500px; color: black; text-shadow: 0.5px 0.5px white;"><?php echo $optionName[$c]; ?> Seçeğine: <?php echo $vote[$c]; ?> Oy Kullanıldı ------- <?php echo $optinDer[$c]; ?>%</div></div></div>
                        <br>


                      <?php

                  }
               
               ?>
               </div>
               



            <?php


        }

        if(isset($_GET["del-id"])){


            $surveyId    = addslashes(strip_tags(htmlspecialchars(trim($_GET["del-id"]))));
            $options = $db->query("SELECT * FROM `option` WHERE `surveyİd` = $surveyId", PDO::FETCH_OBJ)->fetchAll();

            foreach($options as $option){
                $options = $db->query("DELETE FROM `option` WHERE `option`.`id` = $option->id;", PDO::FETCH_OBJ)->fetch();
            }

            $survey = $db->query("DELETE FROM `survey` WHERE `survey`.`id` = $surveyId;", PDO::FETCH_OBJ)->fetchAll();

            header("location:admin.php");


        }

        if(isset($_GET["update-id"])){

            $surveyId    = addslashes(strip_tags(htmlspecialchars(trim($_GET["update-id"]))));
            $optionxs = $db->query("SELECT * FROM `option` WHERE `surveyİd` = $surveyId", PDO::FETCH_OBJ)->fetchAll();


            ?>

              
                <form align="center" action="" method="post">
                    
                      <a href="admin.php">Yönetici Sayfasına Git</a><br><br>
                    
                      <?php
               
                          $survey = $db->query("SELECT * FROM `survey` WHERE `id` = $surveyId", PDO::FETCH_OBJ)->fetch();
               
                       ?>

                       <br>
                       <input type="hidden" name="surveyİdUpdate" value="<?php echo $survey->id; ?>">
                       <input type="text" name="surveyNameUpdate" value="<?php echo $survey->name; ?>" placeholder="Anket Adını girin.">
                       <br>
                            
                       <?php

                          foreach($optionxs as $option){

                            echo "<br><input type='text' name='optionName".$option->id."' value='".$option->optionName."' placeholder='Seçeneğin Adını Girin.'>";
                              
                          }


                       ?>
                       <br><br>
                       <br>
                       Bu Tarihe kadar Geçerli: <input type="date" name="surveyDate" />
                       <br><br>
                       <input type="submit" value="Formu Güncelle">


                   </form>
            
            <?php
        }


    }else{
        ?>
            
            <form align="center" action="" method="post">

                <input type="text" name="userName" value="admin"/><br>
                <input type="password" name="passwords" value="1234" /><br>
                <input type="submit" value="Giriş Yap" />


            </form>

        <?php
    }


?>