<!DOCTYPE html>
<html>
<head>
   <title>Contact us</title>
</head>
<body>
<?php
   $customermail = $_POST["customeremail"];
   $message = $_POST["message"];
   $replywanted = false;
   if (isset($_POST["replywanted"])) $replywanted = true;

   if (! ereg("[a-z]+@[a-z]+\.[a-z]", $_POST["customeremail"]) {
      echo "e-mail address is not valid";
      exit;
      }

   $text_to_display = "You have received a message from " . $customeremail . " :\n";
   $text_to_display = $text_to_display . $message . "\n";
   if ($replywanted)
      $text_to_display = $text_to_display . "A reply was requested";
   else
      $text_to_display = $text_to_display . "No reply was requested";


   mail("test@danskerne.se", "Contact us message", $text_to_display);

   echo "Thank you. Your message has been sent";
?>
</body>
</html>
