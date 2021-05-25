<?php
if(isset($_POST['submit_reset']))
{
  require 'dbh.inc.php';
  include 'func.inc.php';

  $sql = "SELECT users_pfp, images_path FROM users
            JOIN images ON images_id = users_pfp
            WHERE users_uid='".$_POST['username']."';";
  $result = mysqli_query($conn, $sql);
  if($row = mysqli_fetch_assoc($result))
  {
    $del = deleteImage($row['users_pfp'], $row['images_path']);
    if(!$del)
    {
      echo $id;
    }
  }

  $sql = "UPDATE users SET users_pfp=? WHERE users_uid=?";
  $stmt = mysqli_stmt_init($conn);
  if (!mysqli_stmt_prepare($stmt, $sql))
  {
    echo 'sql';
    exit();
  }
  else
  {
    $path=1;
    mysqli_stmt_bind_param($stmt, "ss", $path, $_POST['username']);
    mysqli_stmt_execute($stmt);

    header("Location: http://localhost/news/profile/".$_POST['username']);
    exit();

  }
}
