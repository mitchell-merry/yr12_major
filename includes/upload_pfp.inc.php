<?php
if(isset($_POST['submit']))
{
  include 'func.inc.php';
  require 'dbh.inc.php';

  $id = uploadImage($_FILES['file'], "profiles");

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
    else
    {
      $sql = "UPDATE users SET users_pfp=? WHERE users_uid=?";
    	$stmt = mysqli_stmt_init($conn);
    	if (!mysqli_stmt_prepare($stmt, $sql))
    	{
    		header("Location: http://localhost/news/profile/".$_POST['username']."?pfp_err=sql3");
    		exit();
    	}
    	else
    	{
    		mysqli_stmt_bind_param($stmt, "is", $id, $_POST['username']);
    		mysqli_stmt_execute($stmt);
        header("Location: http://localhost/news/profile/".$_POST['username']);
      }
    }
  }


  // $sql = "SELECT images_path FROM users JOIN images ON images_id = users_pfp WHERE users_uid=?";
	// $stmt = mysqli_stmt_init($conn);
	// if (!mysqli_stmt_prepare($stmt, $sql))
	// {
	// 	header("Location: http://localhost/news/profile/".$_POST['username']."?pfp_err=sql1");
	// 	exit();
	// }
	// else
	// {
	// 	mysqli_stmt_bind_param($stmt, "s", $_POST['username']);
	// 	mysqli_stmt_execute($stmt);
  //   $result = mysqli_stmt_get_result($stmt);
	// 	if($row = mysqli_fetch_assoc($result))
	// 	{
  //     if($row['users_pfp'] != 'default_avatar.png')
  //     {
  //       unlink("../imgs/".$row['images_path']);
  //     }
  //   }
  //   else
  //   {
  // 			header("Location: http://localhost/news/profile/".$_POST['username']."?pfp_err=sql2");
  //   }
  // }



  // $file = $_FILES['file'];
  //
  // $fileName = $file['name'];
  // $fileTmpName = $file['tmp_name'];
  // $fileSize = $file['size'];
  // $fileError = $file['error'];
  //
  // $fileExt = explode('.', $fileName);
  // $fileActualExt = strtolower(end($fileExt));
  //
  // $allowed = array('jpg', 'jpeg', 'png');
  // if (!in_array($fileActualExt, $allowed))
  // {
  //   header("Location: http://localhost/news/profile/".$_POST['username']."?pfp_err=ext");
  //   exit();
  // }
  //
  // if ($fileError !== 0)
  // {
  //   header("Location: http://localhost/news/profile/".$_POST['username']."?pfp_err=error");
  //   exit();
  // }
  //
  // if($fileSize < 256000) // 256kb
  // {
  //   $fileNameNew = uniqid('', true).".".$fileActualExt;
  //   $fileDestination = "../imgs/profiles/".$fileNameNew;
  //
  //   require 'dbh.inc.php';
  //   include 'func.inc.php';
  //
  //   // Crop the image and upload it
  //   $image = cropImage($fileTmpName, $fileDestination, $fileActualExt);
  //
  //
  //
  //   $sql = "SELECT users_pfp FROM users WHERE users_uid=?";
	// 	$stmt = mysqli_stmt_init($conn);
	// 	if (!mysqli_stmt_prepare($stmt, $sql))
	// 	{
	// 		header("Location: http://localhost/news/profile/".$_POST['username']."?pfp_err=sql1");
	// 		exit();
	// 	}
	// 	else
	// 	{
	// 		mysqli_stmt_bind_param($stmt, "s", $_POST['username']);
	// 		mysqli_stmt_execute($stmt);
  //     $result = mysqli_stmt_get_result($stmt);
	// 		if($row = mysqli_fetch_assoc($result))
	// 		{
  //       if($row['users_pfp'] != 'default_avatar.png')
  //       {
  //         unlink("../imgs/".$row['users_pfp']);
  //       }
  //     }
  //     else
  //     {
  //   			header("Location: http://localhost/news/profile/".$_POST['username']."?pfp_err=sql2");
  //     }
  //   }
  //
  //   $sql = "UPDATE users SET users_pfp=? WHERE users_uid=?";
	// 	$stmt = mysqli_stmt_init($conn);
	// 	if (!mysqli_stmt_prepare($stmt, $sql))
	// 	{
	// 		header("Location: http://localhost/news/profile/".$_POST['username']."?pfp_err=sql3");
	// 		exit();
	// 	}
	// 	else
	// 	{
  //     $path = "profiles/".$fileNameNew;
	// 		mysqli_stmt_bind_param($stmt, "ss", $path, $_POST['username']);
	// 		mysqli_stmt_execute($stmt);
  //     header("Location: http://localhost/news/profile/".$_POST['username']);
  //   }
  // }
  // else
  // {
  //   header("Location: http://localhost/news/profile/".$_POST['username']."?pfp_err=b2b");
  //   exit();
  // }
}
else
{
  // header("Location: index.php");
}
