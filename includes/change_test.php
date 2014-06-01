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
if (isset($_POST["testname"])) {
    $TestID = $_POST["testname"];
    $NextID = $TestID;
    $CurrentTestPage = 1;
    $ExplainText="";
    $QuestionText="";
    $Q1Text="";
    $Q2Text="";
    $Q3Text="";
    $QSvar=0;
    $NextPage=0;
    $TotalPages = 0;
    $PictName="";

    if ($TestID == 1000) {
       // Create new test

       // Get next test ID
       try{
          $db = new PDO('mysql:host=danskerne.se.mysql;dbname=danskerne_se', 'danskerne_se', $password);
          $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          $statement = $db->query("SELECT * FROM UdTest ORDER BY TestID DESC");
          $row = $statement->fetch(PDO::FETCH_ASSOC);
          $TestID=$row['TestID'];
          if ($TestID > $NextID) { $NextID=$TestID; }
          $NextID = $NextID+1;

          echo "<form action=\"add_test.php\" method=\"post\">\n";
          echo "Antal sider: <input type=\"text\" name=\"TotalPages\">\n";
          echo "<br>Navn til test: <input type=\"text\" name=\"TestNavn\">\n";
          printf("<INPUT type=\"hidden\" name=\"TestID\" value=\"%s\">\n", $NextID);
          echo "<input type=\"submit\" value=\"Start editering\">\n";
          echo "</form>\n";
       } catch (PDOException $e) {
          printf("<br>Huston 2 we had a problem: %s\n",$e->getMessage());
       }
    } else {
       // Modify existing test
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

// printf("<br>Edit: %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s.\n",$CurrentTestPage, $TotalPages, $TestID, $ExplainText, $PictName, $QuestionText, $QSvar, $CorrectAnswers, $Q1Text, $Q2Text, $Q3Text);
          write_edit_form($CurrentTestPage, $TotalPages, $TestID, $ExplainText, $PictName, $QuestionText, $QSvar, $CorrectAnswers, $Q1Text, $Q2Text, $Q3Text);
       } catch (PDOException $e) {
          printf("<br>Huston 2 we had a problem: %s\n",$e->getMessage());
       }
    }
} else {
   // The correct POST variables were not sent to this page.
   echo 'Invalid Request';
   printf("<br>%s, %s, %s.\n",$_POST['email'], $_POST['p'], $_POST["testname"]);
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
