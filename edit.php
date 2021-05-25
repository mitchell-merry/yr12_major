<?php
  if(!isset($_POST['id']) || !isset($_SESSION['userRank']) || $_SESSION['userRank'] < 2)
  {
    echo '<br> whyu thug';
    echo '<br> whyu thug';
    echo '<br> whyu thug';
    echo '<br>'. $_SESSION['userRank'];
    echo '<br>'. $_POST['id'];
    // header("Location: /news/");
  }

  $id = $_POST['id'];

  include 'submit.php';
?>
