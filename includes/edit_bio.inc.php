<?php
if(isset($_POST['bioSubmit']))
{
  include 'dbh.inc.php';
  include 'func.inc.php';

  $userUID = $_POST['bioUid'];
  $bioContents = mysqli_real_escape_string($conn, $_POST['bioBio']);
  print_r($_POST);

  $sql = "UPDATE users
          SET users_bio = '".$bioContents."'
          WHERE users_uid = '".$userUID."';
          ";

  $result = mysqli_query($conn, $sql);
  echo '<br><br>'.$sql;
  header("Location: /news/profile/".$userUID);
}
else
{
  die();
}
