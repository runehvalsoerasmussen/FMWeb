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
          $_POST["email"])) {
   $TestID = $_POST["TestID"];
   $email = $_POST["email"];
   $newToken=0;
//   if (! ereg("[a-z]+@[a-z]+\.[a-z]", $email)) {
//      echo $email;
//      echo "e-mail address is not valid";
//      exit;
//   }
   try {
      $row_count = 0;
      $db = new PDO('mysql:host=danskerne.se.mysql;dbname=danskerne_se', 'danskerne_se', $password);
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $find_count = "SELECT * FROM TokenDB2 WHERE email='" . $email . "' AND TestID='" . $TestID . "'" ;
      $statement = $db->query($find_count);

      while ($rowGetIt = $statement->fetch(PDO::FETCH_ASSOC)) {
         $oldToken = $rowGetIt['Token'];
         $row_count++;
      }
      if ($row_count == 1) {
         printf("<h2>Der er opstået en fejl email=%s, og testID=%s, findes allerede og Token=%s</h2>\n",$email,$TestID, $oldToken);
         $mailMessage = "Du modtager en token=" . $oldToken . ", fra FMWeb til brug i testen";
         mail($email, "Din token er klar", $mailMessage, "From: god@heaven.com\n");
      } else {
         $statement3 = $db->query("SELECT * FROM UdTest WHERE TestID=$TestID");
         $row = $statement3->fetch(PDO::FETCH_ASSOC);
         $TestNavn = $row['TestNavn'];
         $newToken=mt_rand(100000,999999);
         $update_txt = "('" . $email . "', '" . $newToken . "', '" . $TestID . "', '0')" ;
         $statement2 = $db->query("INSERT INTO TokenDB2 (email, Token, TestID, Resultat) VALUES $update_txt ");
         printf("<h2>Token lavet:%s, for email=%s, og testID=%s.</h2><p>Vi har sendt en mail til brugeren</p>\n", $newToken, $email, $TestID);
         $mailMessage = "Du modtager en token=" . $newToken . ", fra FMWeb til brug i testen " . $TestNavn . ".";
         mail($email, "Din token er klar", $mailMessage, "From: god@heaven.com\n");
      }
   } catch (PDOException $e) {
      printf("<br>rc=%s, Huston we had a problem: %s\n", $row_count, $e->getMessage());
   }
} else {
   // The correct POST variables were not sent to this page.
   echo 'Invalid Request in create_test_token.php';
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
