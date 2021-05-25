<?php
if(isset($_POST['rs-btn']) && $_POST['userRank'] > 2) // kick the user out if they didnt get here via the button, they aren't logged in, or they aren't of sufficient rank
{
  include 'dbh.inc.php';
  include 'func.inc.php';

  $id = $_POST['rs-btn']; // id of the story

  $sql = "SELECT *, i1.images_id as tni, i2.images_id AS mtni, i1.images_path as tnp, i2.images_path AS mtnp FROM stories
          LEFT JOIN images AS i1 ON stories.stories_thumbnail = i1.images_id
          LEFT JOIN images AS i2 ON stories.stories_minithumbnail = i2.images_id
          WHERE stories_id = ".$id;
  $result = mysqli_query($conn, $sql);
  if($row = mysqli_fetch_assoc($result))
  {
    if(!deleteImage($row['tni'], $row['tnp']) || !deleteImage($row['mtni'], $row['mtnp']))
    {
      echo "why though";
    }
    else
    {
      $sql = "DELETE FROM stories WHERE stories.stories_id = ?;"; // sql statement to delete the row with the story
      $stmt = mysqli_stmt_init($conn);
      if(!mysqli_stmt_prepare($stmt, $sql))
      {
        header("Location: ../index.php?rserr=sql");
        exit();
      }
      else
      {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        header("Location: ../index.php");
        exit();
      }
    }
  }
}
else
{
  var_dump($_SESSION);
  // header("Location: ../index.php");
  // exit();
}
