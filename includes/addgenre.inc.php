<?php
if(isset($_POST['genreName']))
{
  require 'dbh.inc.php';
  include 'func.inc.php';

  $sql = 'INSERT INTO genres (genres_name) VALUES (?);';

  $stmt = mysqli_stmt_init($conn);
  if(!mysqli_stmt_prepare($stmt, $sql))
  {
    header("Location: ../index.php?generr=sql");
    exit();
  }
  else
  {
    $gen = $_POST['genreName'];
    mysqli_stmt_bind_param($stmt, "s", $gen);
    mysqli_stmt_execute($stmt);
    // echo mysqli_stmt_error($stmt);
    header("Location: ../index.php?genre=success");
    exit();
  }
}
else
{
  header("Location: ../index.php");
  exit();
}
