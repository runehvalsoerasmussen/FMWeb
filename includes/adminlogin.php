<?php
include_once 'db_connect.php';
include_once 'fmweb_func.php';

sec_session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Administrator side</title>
    </head>
    <body>

<?php
if (isset($_POST['email'],
          $_POST['p'])) {
    $email = $_POST['email'];
    $pwd = $_POST['p']; // The hashed password.
    $slvl = login($email, $pwd, $mysqli);
    if ($slvl == 1) {
       // Login success
       // slvl :: 1 = admin, 2 = user
       $TestID = "";
       $TestNavn = "";
       echo "<p>Welcome\n";
       echo htmlentities($_SESSION['username']);
       echo "!</p>\n";

       echo "<h2>Her kan du &#230;ndre i en eksisterende test</h2>\n";
       echo "<form action=\"change_test.php\" method=\"post\" name=\"testname\">\n";
       echo "<select name=\"testname\" />\n";
       echo "   <option value=\"1000\">Tilf&#248;j en test</option>\n";

       try{
          $db = new PDO('mysql:host=danskerne.se.mysql;dbname=danskerne_se', 'danskerne_se', $password);
          $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          $statement = $db->query("SELECT DISTINCT TestID, TestNavn FROM UdTest ORDER BY TestID ASC");
          while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
             $TestID=$row['TestID'];
             $TestNavn=$row['TestNavn'];
             printf("   <option value=\"%s\">%s</option>\n", $TestID, $TestNavn);
          }
       echo "</select>\n";
       echo "<input type=\"submit\" value=\"Start editering\">\n";
       echo "</form>\n";
       } catch (PDOException $e) {
          printf("<br>Huston 2 we had a problem: %s\n",$e->getMessage());
       }

       echo "<h2>Her kan du oprette et token til en bruger, s&#229; de kan gennemf&#248;re en test</h2>\n";
       echo "<form action=\"create_test_token.php\" method=\"post\">\n";
       echo "e-mail: <input type=\"text\" name=\"email\"/>\n";
       echo "<select name=\"TestID\" />\n";
       try{
          $db = new PDO('mysql:host=danskerne.se.mysql;dbname=danskerne_se', 'danskerne_se', $password);
          $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          $statement = $db->query("SELECT DISTINCT TestID, TestNavn FROM UdTest ORDER BY TestID ASC");
          while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
             $TestID=$row['TestID'];
             $TestNavn=$row['TestNavn'];
             printf("   <option value=\"%s\">%s</option>\n", $TestID, $TestNavn);
          }
       echo "</select>\n";
       } catch (PDOException $e) {
          printf("<br>Huston 2 we had a problem: %s\n",$e->getMessage());
       }
       echo "<input type=\"submit\" value=\"Lav token\">\n";
       echo "</form>\n";

       echo "<h2>Her kan du up-loade en fil til serveren</h2>";
       echo "<form action=\"upload_file.php\" method=\"post\" enctype=\"multipart/form-data\">\n";
       echo "<label for=\"file\">Filename:</label>\n";
       echo "<input type=\"file\" name=\"file\" id=\"file\"><br>\n";
       echo "<input type=\"submit\" name=\"submit\" value=\"Upload\">\n";
       echo "</form>\n";

    } else {
       echo "<p>Welcome\n";
       echo htmlentities($_SESSION['username']);
       echo "<br>Du skal have admin rettigheder for at kunne bruge denne service.</p>\n";
    }
} else {
// The correct POST variables were not sent to this page.
echo 'Invalid Request';
}
?>

    </body>
</html>
