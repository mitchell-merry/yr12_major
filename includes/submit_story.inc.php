<?php
if(isset($_POST['story_submit']))
{
  include_once 'dbh.inc.php';
  include 'func.inc.php';

  $tmp = array_keys($_POST);
  $key = $tmp[count($tmp) - 10];
  $sourceCount = (int)substr($key, -1);

  // source handling
  $sourceArray = array();
  for ($i=0; $i < $sourceCount; $i++)
  {
  	$tempArray = array();
  	$tempArray["link"] = $_POST["link-" . ($i+1)];
  	$tempArray["name"] = $_POST["name-" . ($i+1)];
  	$tempArray["credit"] = $_POST["credit-" . ($i+1)];
  	array_push($sourceArray, $tempArray);
  }

  $sources = json_encode($sourceArray);

  if($_POST['story_genre'] == "null" || $_POST['story_content-frame'] == "null")
  {
    header("Location: ../submit.php?suberr=edd"); // TODO add the rest of the fields FUTURE EDIT: lol?? no
    die();
  }

  $sql = "INSERT INTO stories (stories_title, stories_subtitle, stories_auth_id, stories_content, stories_genre, stories_priority, stories_cf, stories_cf_caption, stories_cf_type, stories_thumbnail, stories_minithumbnail, stories_date, stories_sources)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURTIME(), ?);";

  $stmt = mysqli_stmt_init($conn);
  if(!mysqli_stmt_prepare($stmt, $sql))
  {
    header("Location: ../submit.php?suberr=sql");
    exit();
  }
  else
  {
    $cf = $_POST['story_content-frame'];
    $story_cf = "";
    $cfFD = "";
    if($cf == "1")
    {
      // $cfImage = $_FILES['story_content-image'];
      //
      // $cfFN = $cfImage['name'];
      // $cfTN = $cfImage['tmp_name'];
      // $cfError = $cfImage['error'];
      // $fileExt = explode('.', $fileName);;
      //
      // $cfFD = uniqid('', true).".".strtolower(end($fileExt));
      // $fileDestination = "../imgs/cf/".$cfFD;
      //
      // copy($cfTN, $fileDestination);
      $story_cf = uploadImage($_FILES['story_content-image'], "cf", false, 100000000);
    }
    else if($cf == "0")
    {
      $story_cf = $_POST['story_video-link'];
    }

    print_r($_FILES);
    echo '<br>';

    // $tnImage = $_FILES['story_thumbnail'];
    // $tnExt = explode('.', $tnImage['name']);
    // $tnFN = uniqid('', true).".".strtolower(end($tnExt));
    // $tnFileDest = "../imgs/thumbnails/".$tnFN;
    //
    // cropImage($tnImage['tmp_name'], $tnFileDest, $tnExt);

    $tnId = uploadImage($_FILES['story_thumbnail'], "thumbnails");
    if(gettype($tnId) != "integer")
    {
      // TODO: ERROR HANDLING
    }

    $mtnId = uploadImage($_FILES['story_mini-thumbnail'], "mini_thumbnail");
    if(gettype($mtnId) != "integer")
    {
      // TODO: ERROR HANDLING
    }

    // $mtnImage = $_FILES['story_mini-thumbnail'];
    // $mtnExt = explode('.', $mtnImage['name']);
    // $mtnFN = uniqid('', true).".".strtolower(end($mtnExt));
    // $mtnFileDest = "../imgs/mini_thumbnail/".$mtnFN;
    //
    // cropImage($mtnImage['tmp_name'], $mtnFileDest, $mtnExt);

    mysqli_stmt_bind_param($stmt, "ssssssssssss", $_POST['story_title'], $_POST['story_subtitle'], $_POST['story_auth_id'], $_POST['story_content'], $_POST['story_genre'], $_POST['story_priority'], $story_cf, $_POST['story_cf_caption'], $_POST['story_content-frame'], $tnId, $mtnId, json_encode($sourceArray));
    mysqli_stmt_execute($stmt);
    echo mysqli_stmt_error($stmt);
    echo '<br>'.$tnId.' '.$mtnId;
    header("Location: ../index.php?story=success");
    exit();
  }

  print_r($_POST);
}
else {
  // header("Location: ../");
}
