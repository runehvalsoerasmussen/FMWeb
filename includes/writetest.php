<?php
include_once 'db_connect.php';
include_once 'fmweb_func.php';

sec_session_start();
?>
<?php
write_header_html();
?>
<?php if (login_check($mysqli) == true) : ?>
<?php
if (isset($_POST["TestID"],
      $_POST["PageID"])) {
   $CorrectAnswers = 0;
   $CurrentTestPage = $_POST["PageID"];
   $CorrectAnswers = $_POST["CorrectAnswers"];
   $ExplainText="";
   $QuestionText="";
   $Q1Text="";
   $Q2Text="";
   $Q3Text="";
   $QSvar=$_POST["QSvar"];
   $NextPage=0;
   $TotalPages = $_POST["TotalID"];
   $PictName="";

   try {
      $db = new PDO('mysql:host=danskerne.se.mysql;dbname=danskerne_se', 'danskerne_se', $password);
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $update_txt = "PageID='" . $_POST["PageID"] . "', TotalID='" . $_POST["TotalID"] . "', ExplainTXT='" . $_POST["ExplainText"] . "', QuestionTXT='" . $_POST["QuestionText"] . "', Q1='" . $_POST["Q1Text"] . "', Q2='" . $_POST["Q2Text"] . "', Q3='" . $_POST["Q3Text"] . "', QSvar='" . $QSvar . "', Pict='" . $_POST["PictName"] . "'";
      $test_txt = "TestID='" . $TestID . "' AND PageID='" . $CurrentTestPage . "'";
//printf("<p>update_txt=.%s.</p>\n",$update_txt);
//printf("<p>test_txt=.%s.</p>\n",$test_txt);
      $statement = $db->query("UPDATE UdTest SET $update_txt WHERE $test_txt");
   } catch (PDOException $e) {
      printf("<br>Huston 0 we had a problem: %s\n",$e->getMessage());
   }

   if ($CurrentTestPage == $TotalPages) {
         echo "<h1>Done - DB updated</h1>\n";
   } else {
      $CurrentTestPage = $CurrentTestPage+1;
      try {
         $db = new PDO('mysql:host=danskerne.se.mysql;dbname=danskerne_se', 'danskerne_se', $password);
         $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
         $statement = $db->query("SELECT * FROM UdTest WHERE TestID=$TestID AND PageID=$CurrentTestPage");
         $row = $statement->fetch(PDO::FETCH_ASSOC);
         $ExplainText=$row['ExplainTXT'];
         $QuestionText=$row['QuestionTXT'];
         $Q1Text=$row['Q1'];
         $Q2Text=$row['Q2'];
         $Q3Text=$row['Q3'];
         $QSvar=$row['QSvar'];
         $TotalPages=$row['TotalID'];
         $PictName=$row['Pict'];
         write_edit_form($CurrentTestPage, $TotalPages, $TestID, $ExplainText, $PictName, $QuestionText, $QSvar, $CorrectAnswers, $Q1Text, $Q2Text, $Q3Text);
      } catch (PDOException $e) {
         printf("<br>Huston 2 we had a problem: %s\n",$e->getMessage());
      }
   }


   } else {
      // The correct POST variables were not sent to this page.
      echo 'Invalid Request';
printf("<br>%s, %s, %s, %s, %s.\n",$_POST['email'], $_POST['p'], $_POST["TestID"], $_POST["PageID"], $_POST["CorrectAnswers"]);
   }
?>

        <?php else : ?>
            <p>
                <span class="error">You are not authorized to access this page.</span> Please <a href="index.php">login</a>.
            </p>
        <?php endif; ?>

<?php
write_footer_html();
?>
