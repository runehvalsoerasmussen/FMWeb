<?php
include_once 'includes/db_connect.php';
include_once 'includes/fmweb_func.php';

sec_session_start();

if (login_check($mysqli) == true) {
    $logged = 'in';
} else {
    $logged = 'out';
}
?>
<!DOCTYPE html>
<html>
<head>
   <link rel="stylesheet" href="styles/Brandtest.css" />
   <script type="text/JavaScript" src="js/sha512.js"></script>
   <script type="text/JavaScript" src="js/forms.js"></script>
   <meta charset="UTF-8">
   <title>FM - Web Facility Management</title>
   <style>
      body {
            margin:5px;
         }
      header {
            background-color:#eee;
            padding:5px;
        }

      div.box2 {
         width: 400px;
         height: 200px;
         padding: 10px;
         border: 5px solid #eee;
         margin: 15px;
         }

   </style>
</head>
<header>
      <div id='topnav'>
	<div style="margin-top:10px;width:600px;height:25px;padding:10px;border-radius:10px;border:10px solid #EE872A;font-size:120%;">
      		<img style="display: block; float: left; margin: 0 15px 15px 10; border: none;" title="FM Web" src="http://www.danskerne.se/FMWeb/logo.png" alt="FM Web" width="99" height="99" />
      		<div style='float:left;word-spacing:0;'>
      			<a class='topnav' target='_top' href='/index.html'>Forside</a><span style='letter-spacing:14px;'> |</span>
			<a class='topnav' target='_top' href='/index.html'>Om os</a><span style='letter-spacing:14px;'> |</span>
      			<a class='topnav' target='_top' href='/index.html'>Vores tjenester</a><span style='letter-spacing:14px;'> |</span>
			<a class='topnav' target='_top' href='/index.html'>Kontakt os</a>
      		</div>
      	</div>
      </div>
</header>
      <body>

<div style='float:left' class="box2">


        <?php
        if (isset($_GET['error'])) {
            echo '<p class="error">Error Logging In!</p>';
        }
        ?>
        <form action="includes/readtest.php" method="post" name="login_form">
            Email: <input type="text" name="email" />
            <br>Password: <input type="password"
                             name="password"
                             id="password"/>
            <input type="button"
                   value="Login"
                   onclick="formhash(this.form, this.form.password);" />
            <INPUT type="hidden" name="TestID" value="1001">
            <INPUT type="hidden" name="PageID" value="1">
            <INPUT type="hidden" name="CorrectAnswers" value="0">
        </form>
        <p>If you don't have a login, please <a href="register.php">register</a></p>
        <p>If you are done, please <a href="includes/logout.php">log out</a>.</p>
        <p>You are currently logged <?php echo $logged ?>.</p>

</div>

</body>
</html>

