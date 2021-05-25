<?php
	require_once 'C:\xampp\htdocs\news\random_compat\lib\random.php';
	require 'dbh.inc.php';

	function str_limit($str, $limit)
	{
		if(strlen($str) >= $limit)
		{
			$str = substr($str, 0, $limit) . "...";
		}
		return $str;
	}

	function last_pos($str, $find)
	{
		$str = strrev($str);
		return strlen($str) - strpos($str, " ")-1;
	}

	function starts_with($str, $starts)
	{
		return strpos($str, $starts) === 0;
	}

	function ends_with($str, $ends)
	{
		return starts_with(strrev($str), strrev($ends));
	}

	function throw_error($prefix, $error, $additionalUrl)
	{
		header("Location: ../index.php?".$prefix."error=".$error.$additionalUrl);
	}

	function return_home()
	{
		header("Location: ../index.php");
	}

	function time_convert_w_m($message, $time) // Used for time remaining on login attempts (when locked out)
	{
		$time_measure = " second(s).";
		if ($time >= 60)
		{
			$time /= 60;
			$time = (int)$time;
			$time_measure = " minute(s).";
		}
		else if($time <= 0)
		{
			return '<div class="bg-success w-100 mb-3 p-2 text-light rounded">You are able to login again! Feel free to attempt to log in once more.';
		}
		return '<div class="bg-danger w-100 mb-3 p-2 text-light rounded">Error: '.$message.$time.$time_measure;
	}

	function ci ($name, $image)
	{
		list($w, $h) = getimagesize($name);

		if ($w < $h) // then keep the width and scale the height
		{
			return imagecrop($image, array("x" => 0, "y" => ($w - $h) / 2, "width" => $w,"height" => $w));
		}
		else if ($h < $w) // then keep the height and scale the width
		{
			return imagecrop($image, array("x" => ($w - $h) / 2, "y" => 0, "width" => $h, "height" => $h));
		}
		else
		{
			return $image;
		}
	}

	function cropImage($fileTmpName, $fileDestination, $fileActualExt)
	{
		$image = '';
		switch ($fileActualExt)
		{
			case 'png':
				$image = ci($fileTmpName, imagecreatefrompng($fileTmpName));
				imagepng($image, $fileDestination);
				break;
			case 'jpg':
			case 'jpeg':
				$image = ci($fileTmpName, imagecreatefromjpeg($fileTmpName));
				imagejpeg($image, $fileDestination);
				break;
		}

		return $image;
	}


	function uploadImage($file, $folder, $doCrop = true, $fileSizeLimit = 1024000) // the file to be uploaded, its dest folder, and the file size limit (defaults to 256kb)
	{
	  $fileName = $file['name'];
	  $fileTmpName = $file['tmp_name'];
	  $fileSize = $file['size'];
	  $fileError = $file['error'];

	  $fileExt = explode('.', $fileName); // file.Png -> ['file', 'Png']
	  $fileActualExt = strtolower(end($fileExt)); // ['file', 'Png'] -> 'png'

	  $allowed = array('jpg', 'jpeg', 'png', 'gif', 'webp'); // the file types allowed to be uploaded
	  if(!in_array($fileActualExt, $allowed))
	  {
	    return 'ext'; // Returns a file extension error if the file isn't an allowed type
	  }
	  else if($fileError !== 0)
	  {
	    return 'fue -'. $fileError; // Returns a file upload error if it wasn't able to be submitted or something. Essentially, not my fault if this happens
	  }
	  else if($fileSize > $fileSizeLimit)
	  {
	      return 'fse - '.$fileSize; // Returns a file size error if the file size is larger than the given limit
	  }
	  else
	  {
	    // Attempt to upload the image
	    $fileNameNew = uniqid('', true).'.'.$fileActualExt; // Give the image a unique filename
	    $fileDestination = "../imgs/".$folder."/".$fileNameNew; // The file's final destination, with parsed in folder

	    // include database connection
			require 'dbh.inc.php';

	    // Upload the image
			if($doCrop)
			{
				cropImage($fileTmpName, $fileDestination, $fileActualExt); // TODO! Check if broken
			}
			else
			{
				copy($fileTmpName, $fileDestination); // copy the image directly without cropping
			}

	    $sql = "INSERT INTO images (images_path) VALUES (?);"; // SQL to insert destination into images table
	    $stmt = mysqli_stmt_init($conn);
	    if(!mysqli_stmt_prepare($stmt, $sql))
	    {
	      return "sqe"; // Returns an SQL error if something happens here
	    }
	    else
	    {
	      // Generate the file path inserted into the database
	      $path = $folder."/".$fileNameNew;
				mysqli_stmt_bind_param($stmt, "s", $path);
				mysqli_stmt_execute($stmt); // Insert it
				return mysqli_insert_id($conn); // Returns the last inserted ID for use
	    }
	  }
	}

	function deleteImage($id, $path)
	{
		if($id != 1 && $id != null)
		{
			require 'dbh.inc.php';
			$sql = "DELETE FROM images WHERE images_id = ".$id.";";
			$result = mysqli_query($conn, $sql);
			// if($row = mysqli_fetch_assoc($result))
			// {
			if(!unlink("../imgs/".$path))
			{
				echo "Unlink error! ID: " . $id;
			}
			// }
			// else
			// {
			// 	echo mysqli_errno($conn);
			// 	echo "d";
			// 	return false;
			// }
		}
		return true;
	}

	function days_ago($date)
	{
		$today = date("Y-m-d");
		$diff = strtotime($today) - strtotime(date("Y-m-d", strtotime($date)));
		$days = (int)($diff/(60*60*24));

		return $days;
	}

	function english_days($days) // HAHA THIS CODE IS SO FUCKING UGLY LOL; 1 hour to write, 2 hours to decipher
	{
		if($days == 0){return "Today";}
		else if($days == 1){return "Yesterday";}
		else if($days < 7){return $days." days ago";}
		else if($days < 31){return (int)($days/4)." week".(((int)($days/4) == 1)?"":"s")." ago";}
		else if($days < 365){return (int)($days/31)." month".(((int)($days/31) == 1)?"":"s")." ago";}
		return (int)($days/365)." year".(((int)($days/365) == 1)?"":"s")." ago";
	}
?>
