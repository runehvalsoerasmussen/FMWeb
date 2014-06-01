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
#$allowedExts = array("gif", "jpeg", "jpg", "png");
#$temp = explode(".", $_FILES["file"]["name"]);
#$extension = end($temp);
if ($_FILES["file"]["size"] < 1000000)
#((($_FILES["file"]["type"] == "image/gif")
#|| ($_FILES["file"]["type"] == "image/jpeg")
#|| ($_FILES["file"]["type"] == "image/jpg")
#|| ($_FILES["file"]["type"] == "image/pjpeg")
#|| ($_FILES["file"]["type"] == "image/x-png")
#|| ($_FILES["file"]["type"] == "image/png"))
#&& ($_FILES["file"]["size"] < 20000)
#&& in_array($extension, $allowedExts))
  {
  if ($_FILES["file"]["error"] > 0)
    {
    echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
    }
  else
    {
    echo "Upload: " . $_FILES["file"]["name"] . "<br>";
    echo "Type: " . $_FILES["file"]["type"] . "<br>";
    echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
    echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br>";

    if (file_exists("../images/" . $_FILES["file"]["name"]))
      {
      echo $_FILES["file"]["name"] . " already exists. ";
      }
    else
      {
      move_uploaded_file($_FILES["file"]["tmp_name"],
      "../images/" . $_FILES["file"]["name"]);
      echo "Stored in: " . "images/" . $_FILES["file"]["name"];
      }
    }
  } else {
     printf("File size too big, max is 1MB, current is %s.", $_FILES["file"]["size"]);
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
