<?php
	include 'header.php';
?>

<?php
	$id = '';
	if(isset($_GET['id']))
	{
		$id = intval($_GET['id']);
	}
	else
	{
		echo '<script>window.location.replace("http://localhost/news/");
</script>';
	}


	#https://stackoverflow.com/questions/10195451/sql-inner-join-with-3-tables
	$sql = "SELECT *
					FROM stories
					JOIN users
						ON stories.stories_auth_id = users.users_id
					JOIN genres
						ON stories.stories_genre = genres.genres_id
					LEFT JOIN images
						ON stories.stories_cf = images.images_id
					WHERE stories.stories_id = ".$id.";";
	$result = mysqli_query($conn, $sql);
	if($row = mysqli_fetch_assoc($result))
	{
		mysqli_query($conn, "UPDATE stories SET stories_views = stories_views + 1 WHERE stories.stories_id = ".$id.";");

		$title = $row['stories_title'];
		$subtitle = $row['stories_subtitle'];
		$content = $row['stories_content'];

		$author_id = $row['stories_auth_id'];
		$firstname = $row['users_first'];
		$lastname = $row['users_last'];
		$name = $firstname." ".$lastname;
		$username = $row['users_uid'];

		$genre = $row['genres_name'];
		$genre_id = $row['genres_id'];
		$priority = $row['stories_priority'];
		$status = $row['stories_status'];

		$video = $row['stories_cf'];
		$image = $row['images_path'];
		$cf_caption = $row['stories_cf_caption'];
		$cf_type = $row['stories_cf_type'];

		$thumbnail = $row['stories_thumbnail'];
		$mini_thumb = $row['stories_minithumbnail'];

		$date = $row['stories_date'];
		$sources_raw = $row['stories_sources'];
		$sources = json_decode($sources_raw, true);

		$views = $row['stories_views'] + 1;
	}
	else
	{
		echo '<br><br>a<br>';
		// ini_set('display_errors', 'On');
		echo mysqli_num_rows($result);
		echo '<br>a';
	}

	echo '<script>document.title = "'.$title.' - localnews";</script>';

if(isset($_SESSION['userRank']) && $_SESSION['userRank'] > 2)
{
?>

<div class="w-100 text-center mt-2"><div class="inline wsnw">
	<?php
		if($status != 2)
		{
	?>
	<form class="ilblck" action="includes/pending-handle.inc.php" method="post">
		<input type="hidden" name="action" value="2">
		<button class="btn btn-success btn-sm text-light" name="id" value="<?php echo $id; ?>">Accept Story</button>
	</form>
	<?php } ?>
	<form class="ilblck" action="/news/submit" method="post">
		<button class="btn btn-primary btn-sm text-light" name="id" value="<?php echo $id; ?>">Edit Story</button>
	</form>
	<form class="ilblck" action="/news/includes/pending-handle.inc.php" method="post">
		<input type="hidden" name="action" value="0">
		<button class="btn btn-danger btn-sm text-light" name="id" value="<?php echo $id; ?>">Remove Story</button>
	</form>
</div></div>
<?php } ?>

<h3 class="text-light my-3"><?php echo $title; ?></h3>
<p  class="text-light mb-3"><i><?php echo $subtitle; ?></i></p>

<div class="text-light mb-3 w-100 row p-0 m-0">
	<?php
		echo '<div class="col-4 align-self-center" style="white-space: nowrap;"> Written by: <i><a href="/news/profile/'.strtolower($username).'" class="">'.$name.'</a></i><br>Views: '.$views.'</div>';
		// 3rd of March, 2019 at 10:05am
		$d = strtotime($date);
		$d1 = date("jS", $d);
		$d2 = date("F, Y", $d);
		$d3 = date("g:ia", $d);
		echo '<div class="col-8 text-right" style="">'.$d1.' of '.$d2.', at '.$d3;
		echo '<div class="text-white"><i><a href="/news/?g='.$genre_id.'" class="text-light">'.$genre.'</a></i></div></div>';
	?>
</div>

<div class="cf-container w-100" id="cf-con">
	<div class="cf-cap-container" id="cf">
		<?php
			if(!isset($cf_type))
			{
				echo 'There\'s been an error!';
			}
			else if($cf_type == 0)
			{
				echo '<div class="yt-container">';
				echo '<iframe class="yt-video" src="https://www.youtube.com/embed/'.$video.'" frameborder="0" allow="encrypted-media;" allowfullscreen></iframe>';
				echo '<div class="yt-video-border"></div>';
				echo '</div>';
			}
			else if($cf_type == 1)
			{
				echo '<div class="img-container">';
				echo '<img src="/news/imgs/'.$image.'" class="w-100">';
				echo '</div>';
			}
		?>
		<div class="cf-cap align-self-center">
			<?php echo $cf_caption; ?>
		</div>
	</div>
</div>

<div class="text-light mt-3">
	<?php echo str_replace("\n", "<br>", $content); ?>

	<h4 class="mt-4">Sources</h4>
	<ol>
		<?php
			foreach ($sources as $source) {
				echo '<li><a href="'.$source['link'].'" class="story-source-link text-light">'.$source['name'].'</a> - '.$source['credit'].'</li>';
			}
		?>
	</ol>
</div>

<?php
	include 'end.php';
?>
