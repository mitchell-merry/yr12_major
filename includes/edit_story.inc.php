<?php
if(isset($_POST['story_id']))
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

    $cfType = intval($_POST['story_content-frame']); // 1 -> image 0 -> video

    $tnSQL = $mtnSQL = "";

    if($_FILES['story_thumbnail']['error'] != 4)
    {
      deleteImage($_POST['story_old_tn_id'], $_POST['story_old_tn']);
      $newTNid  = intval(uploadImage($_FILES['story_thumbnail'], "thumbnails"));
      $tnSQL = 'stories_thumbnail = '.$newTNid.',';
    }
    if($_FILES['story_mini-thumbnail']['error'] != 4)
    {
      deleteImage($_POST['story_old_mtn_id'], $_POST['story_old_mtn']);
      $newMTNid = intval(uploadImage($_FILES['story_mini-thumbnail'], "mini_thumbnail"));
      $mtnSQL = 'stories_minithumbnail = '.$newMTNid.',';
    }

    if($cfType == 1 && $_FILES['story_content-image']['error'] != 4)
    {
      deleteImage($_POST['story_old_cf_id'], $_POST['story_old_cf']);
      $cf = uploadImage($_FILES['story_content-image'], "cf", false, 5*1024000);
    }
    else {
      $cf = $_POST['story_video-link'];
    }

    $id = intval($_POST['story_id']);
    $title = mysqli_real_escape_string($conn, $_POST['story_title']);
    $subtitle = mysqli_real_escape_string($conn, $_POST['story_subtitle']);
    $content = mysqli_real_escape_string($conn, $_POST['story_content']);
    $genre = intval($_POST['story_genre']);
    $priority = intval($_POST['story_priority']);
    $cf_caption = mysqli_real_escape_string($conn, $_POST['story_cf_caption']);

    $sql = "UPDATE stories SET
              stories_title = '$title',
              stories_subtitle = '$subtitle',
              stories_content = '$content',
              stories_genre = $genre,
              stories_priority = $priority,
              stories_cf = '$cf',
              stories_cf_caption = '$cf_caption',
              stories_cf_type = $cfType,
              $tnSQL $mtnSQL
              stories_date = CURTIME(),
              stories_sources = '$sources'
            WHERE stories_id = $id;";

    mysqli_query($conn, $sql);
    $err = mysqli_errno($conn);

    // $err=1; // TODO debug

    if($err == 0)
    {
      header("Location: /news/story/".$id);
    }
    else
    {
      echo '<br>'.$err;
      print_r($_POST);
      print_r($_FILES);
      echo '<br><br>'.$sql;
      echo '<br><a href="/news/story/'.$id.'">link to story</a>';
      echo '<br><a href="/news/pending">back</a>';
    }
}
else
{
  header("Location: /news/");
}
?>
