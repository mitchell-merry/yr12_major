<?php
if(isset($_POST['id']))
{
  include 'dbh.inc.php';

  $i = intval($_POST['id']);
  $s = intval($_POST['action']); // 2 is accept. 0 is reject.

  $sql = "UPDATE stories
            SET stories_status=".$s."
            WHERE stories_id = ".$i.";";
  mysqli_query($conn, $sql);
  header("Location: /news/pending");
}
