<?php
if(isset($_POST['promoteSubmit']))
{
  include 'dbh.inc.php';
  include 'func.inc.php';

  $rankId = intval($_POST['promoteSelect']);
  $userId = $_POST['promoteUid'];
  print_r($_POST);

  $sql = "UPDATE users
          SET users_rank_id = ".$rankId."
          WHERE users_uid = '".$userId."';
          ";

  $result = mysqli_query($conn, $sql);
  echo '<br><br>'.$sql;
  header("Location: /news/profile/".$userId);
}
else
{
  die();
}
