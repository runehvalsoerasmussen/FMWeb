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
          $_POST["TotalPages"],
          $_POST["TestNavn"])) {
   $CorrectAnswers = 0;
   $TestID = $_POST["TestID"];
   $TotalPages = $_POST["TotalPages"];
   $TestNavn = $_POST["TestNavn"];
   $CurrentTestPage = 1;
   $ExplainText="";
   $QuestionText="";
   $Q1Text="";
   $Q2Text="";
   $Q3Text="";
   $QSvar=1;
   $PictName="";


      try {
         $db = new PDO('mysql:host=danskerne.se.mysql;dbname=danskerne_se', 'danskerne_se', $password);
         $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
         for ($i=1;$i<=$TotalPages;$i++) {
            $update_txt = "('" . $TestID . "', '" . $i . "', '" . $TotalPages . "', 'Missing Explain Text', 'Missing Question Text', 'Missing Q1 Text', 'Missing Q2 Text', 'Missing Q3 Text', '1', 'Test.jpg', '" . $TestNavn . "')" ;
printf("<p>i=%s</p>\n",$update_txt);
            $statement = $db->query("INSERT INTO UdTest (TestID, PageID, TotalID, ExplainTXT, QuestionTXT, Q1, Q2, Q3, QSvar, Pict, TestNavn) VALUES $update_txt ");
         }
      } catch (PDOException $e) {
         printf("<br>Huston 0 we had a problem: %s\n",$e->getMessage());
      }

    try{
       $db = new PDO('mysql:host=danskerne.se.mysql;dbname=danskerne_se', 'danskerne_se', $password);
       $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
       $statement = $db->query("SELECT * FROM UdTest WHERE TestID=$TestID AND PageID=1");
       $row = $statement->fetch(PDO::FETCH_ASSOC);
       $ExplainText=$row['ExplainTXT'];
       $QuestionText=$row['QuestionTXT'];
       $Q1Text=$row['Q1'];
       $Q2Text=$row['Q2'];
       $Q3Text=$row['Q3'];
       $QSvar=$row['QSvar'];
       $TotalPages=$row['TotalID'];
       $PictName=$row['Pict'];

printf("<br>Edit: %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s.\n",$CurrentTestPage, $TotalPages, $TestID, $ExplainText, $PictName, $QuestionText, $QSvar, $CorrectAnswers, $Q1Text, $Q2Text, $Q3Text);
       write_edit_form($CurrentTestPage, $TotalPages, $TestID, $ExplainText, $PictName, $QuestionText, $QSvar, $CorrectAnswers, $Q1Text, $Q2Text, $Q3Text);
    } catch (PDOException $e) {
       printf("<br>Huston 2 we had a problem: %s\n",$e->getMessage());
    }

   } else {
      // The correct POST variables were not sent to this page.
      echo 'Invalid Request in add_test.php';
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
