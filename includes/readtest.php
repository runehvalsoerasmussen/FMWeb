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

<?php
if (isset($_POST['email'],
          $_POST['p'],
          $_POST["TestID"],
          $_POST["PageID"],
          $_POST["CorrectAnswers"])) {
    $email = $_POST['email'];
    $password = $_POST['p']; // The hashed password.
    $slvl = login($email, $password, $mysqli);
    if ($slvl > 0) {
       // Login success
       // slvl :: 1 = admin, 2 = user
       echo "<p>Welcome\n";
       echo htmlentities($_SESSION['username']);
       echo "!</p>\n";
       $CorrectAnswers = 0;
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
             // No need to test slvl - there is 5 pages, so it cannot be the last one
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
printf("<br>slvl=%s.\n",$slvl);
             if ($slvl == 2) {
printf("<br>Static: %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s.\n",$CurrentTestPage, $TotalPages, $TestID, $ExplainText, $PictName, $QuestionText, $QSvar, $CorrectAnswers, $Q1Text, $Q2Text, $Q3Text);
                write_static_form($CurrentTestPage, $TotalPages, $TestID, $ExplainText, $PictName, $QuestionText, $QSvar, $CorrectAnswers, $Q1Text, $Q2Text, $Q3Text);
             } else {
                // admin
printf("<br>Edit: %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s.\n",$CurrentTestPage, $TotalPages, $TestID, $ExplainText, $PictName, $QuestionText, $QSvar, $CorrectAnswers, $Q1Text, $Q2Text, $Q3Text);
                write_edit_form($CurrentTestPage, $TotalPages, $TestID, $ExplainText, $PictName, $QuestionText, $QSvar, $CorrectAnswers, $Q1Text, $Q2Text, $Q3Text);
             }
          }
       } else {
        // Login failed
        header('Location: ../index.php?error=1');
       }
    } else {
       // The correct POST variables were not sent to this page.
       echo 'Invalid Request';
printf("<br>%s, %s, %s, %s, %s.\n",$_POST['email'], $_POST['p'], $_POST["TestID"], $_POST["PageID"], $_POST["CorrectAnswers"]);
    }
?>

    </body>
</html>
