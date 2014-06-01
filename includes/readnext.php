<?php
include_once 'db_connect.php';
include_once 'fmweb_func.php';

sec_session_start();
?>
<!DOCTYPE html>
<html lang="da">
    <head>
        <title>Brandtest</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width">
        <link rel="stylesheet" href="../styles/Brandtest.css" type="text/css"/>
   <script>
      function enableNext()
      {
      document.getElementById("frame7").innerHTML = "<INPUT type=\"image\" id=\"myButton\" src=\"..\/green.png\" width=\"50\" height=\"50\">\n";
      }
   </script>
    </head>
    <body>
<?php if (login_check($mysqli) == true) : ?>
<?php
   echo "<p>Welcome\n";
   echo htmlentities($_SESSION['username']);
   echo "!</p>\n";
   $CorrectAnswers = 0;
   $UserID = $_POST["UserID"];
   $TestID = $_POST["TestID"];
   $CurrentTestPage = $_POST["PageID"];
   $CorrectAnswers = $_POST["CorrectAnswers"];

   $ExplainText="";
   $QuestionText="";
   $Q1Text="";
   $Q2Text="";
   $Q3Text="";
   $QSvar=0;
   $NextPage=0;
   $TotalPages = 0;
   $PictName="";

   if ($CurrentTestPage == 0) {
      try{
         $db = new PDO('mysql:host=danskerne.se.mysql;dbname=danskerne_se', 'danskerne_se', $password);
         $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
         $statement = $db->query("SELECT * FROM UdTest WHERE TestID=$TestID");
         $row = $statement->fetch(PDO::FETCH_ASSOC);
         $TotalPages=$row['TotalID'];
      }
      catch (PDOException $e) {
         printf("<br>Huston 1 we had a problem: %s\n",$e->getMessage());
      }
   printf("<h1>Fantastisk - Du er faerdig, du fik %s rigtige svar ud af %s</h1>\n", $CorrectAnswers, $TotalPages);
   } else {
      try{
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
         }
         catch (PDOException $e) {
            printf("<br>Huston 2 we had a problem: %s\n",$e->getMessage());
         }
      echo "<div id=\"frame1\">\n";
      echo "<form action=\"readnext.php\" method=\"post\">\n";
      if ($CurrentTestPage < $TotalPages) {
         printf("<INPUT type=\"hidden\" name=\"PageID\" value=\"%s\">\n", $CurrentTestPage+1);
      } else {
         if ($CurrentTestPage == $TotalPages) {
            printf("<INPUT type=\"hidden\" name=\"PageID\" value=\"%s\">\n", 0);
         }
      }
      printf("<INPUT type=\"hidden\" name=\"UserID\" value=\"%s\">\n", $UserID);
      printf("<INPUT type=\"hidden\" name=\"TestID\" value=\"%s\">\n", $TestID);
      printf("<p>Side %s af totalt %s\n", $CurrentTestPage, $TotalPages);
      echo "<div id=\"frame2\">\n";
      echo htmlentities($ExplainText);
      echo "</div>\n";
      echo "<div id=\"frame3\">\n";
      printf("<img src=\"..\/%s\" width=\"%s\" height=\"%s\" />\n", $PictName, "100%", "100%");
      echo "</div>\n";
      echo "<div id=\"frame3b\">\n";
      echo htmlentities($QuestionText);
      echo "</div>\n";
      echo "<div id=\"frame4\">\n";
      echo "<input type=\"radio\" onclick=\"enableNext()\" name=\"CorrectAnswers\"";
      if ($QSvar == 1){
         printf("value=\"%s\">\n", ($CorrectAnswers + 1));
      } else {
         printf("value=\"%s\">\n", $CorrectAnswers);
      }
      echo htmlentities($Q1Text);
      echo "</div>\n";
      echo "<div id=\"frame5\">\n";
      echo "<input type=\"radio\" onclick=\"enableNext()\" name=\"CorrectAnswers\"";
      if ($QSvar == 2){
         printf("value=\"%s\">\n", ($CorrectAnswers + 1));
      } else {
         printf("value=\"%s\">\n", $CorrectAnswers);
      }
      echo htmlentities($Q2Text);
      echo "<br>\n";
      echo "</div>\n";
      echo "<div id=\"frame6\">\n";
      echo "<input type=\"radio\" onclick=\"enableNext()\" name=\"CorrectAnswers\"";
      if ($QSvar == 3){
         printf("value=\"%s\">\n", ($CorrectAnswers + 1));
      } else {
         printf("value=\"%s\">\n", $CorrectAnswers);
      }
      echo htmlentities($Q3Text);
      echo "<br>\n";
      echo "</div>\n";
      echo "<div id=\"frame7\">\n";
      echo "</div>\n";
      echo "</form>\n";
      echo "</div>\n";
   }

?>
        <?php else : ?>
            <p>
                <span class="error">You are not authorized to access this page.</span> Please <a href="index.php">login</a>.
            </p>
        <?php endif; ?>

    </body>
</html>
