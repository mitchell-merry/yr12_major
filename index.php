<?php
	include 'header.php';
	echo '<script>document.title = "Home - localnews";</script>';

?>

<div class="jumbotron bg-dark text-light jt-border" id="jt">
	<button type="button" class="close text-light jt-close" onclick="document.getElementById('jt').classList.toggle('hidden');">&times;</button>
	<h3>Hello!</h3>
	<p>This is just a little instructional to tell you how to use the site. Click the x to remove this message.</p>
	<p>The rank hierarchy is Superadmin > Admin > Editor > Journalist > Reader.</p>
	<p>Here are some account details (passwords are the same as the username):</p>
	<ul>
		<li>superadmin</li>
		<li>admin</li>
		<li>journalist</li>
		<li>reader</li>
	</ul>
	<p>A journalist and above can submit stories to the website, whilst only an editor or above can approve them.</p>
	<p>I would recommend checking out the pending stories page, there are some stories there that are waiting to be accepted so the site can be tested.</p>
</div>


<div class="text-light display-4 text-left mb-2">News</div>
<?php
	if(isset($_SESSION['userId']))
	{
		if($_SESSION['userRank'] > 1)
		{
			echo '<a href="/news/submit" role="button" class="text-right btn btn-primary btn-sm text-light">Submit Story</a>';
		}

		if($_SESSION['userRank'] > 2)
		{
			echo '<a href="/news/pending" role="button" class=" ml-2 text-right btn btn-success btn-sm text-light">Pending Stories</a>';
		}
	}
?>

<div class="article-wrapper text-light">
	<?php
		$sql = "SELECT *, users_first, users_last, i1.images_path AS tn, i2.images_path AS mtn FROM stories
		LEFT JOIN users ON users.users_id = stories.stories_auth_id
		LEFT JOIN images AS i1 ON stories.stories_thumbnail = i1.images_id
		LEFT JOIN images AS i2 ON stories.stories_minithumbnail = i2.images_id
		WHERE stories.stories_priority > 0
			AND stories.stories_status = 2";
		if(isset($_GET['g']))
		{
			$sql .= " AND stories_genre = " . $_GET['g'];
		}
		$sql .= " ORDER BY stories_date DESC";
		$result = mysqli_query($conn, $sql);
		while($row = mysqli_fetch_assoc($result))
		{
			$urlString = substr(strtolower(urlencode($row['stories_title'])),0,40);
			$urlString = str_replace(".", "", $urlString);
			$urlString = str_replace("+", "-", $urlString);
			echo '<a class="article-link text-light" href="http://localhost/news/story/'.$row['stories_id'].'/'. $urlString .'">';
			echo '	<div class="article media">';

			if($row['stories_thumbnail'] != "")
			{
				echo '		<img src="/news/imgs/'.$row['tn'].'" class="mr-3 align-self-center" style="height: 130px;">';
			}
			else
			{
				echo '		<div class="mr-3 bg-danger align-self-center" style="height: 130px; width: 130px;"></div>';
			}

			$breaking = "";
			if($row['stories_priority'] > 1)
			{
				$breaking = '<span class="badge badge-danger mr-2">BREAKING</span>';
			}

			echo '		<div class="media-body" style="text-decoration: none;">';
			echo '			<h5>' . $breaking . $row['stories_title'] . '</h5>';
			echo '			<i>Posted on '.date_format(date_create($row['stories_date']), "d/m/Y").'</i>';
			echo '			<p>' . str_limit($row['stories_subtitle'], 120) . "</p>";

			if(isset($_SESSION['userId']) && $_SESSION['userRank'] > 2)
			{
				echo '<form action="includes/remove-story.inc.php" method="POST">
								<input type="hidden" name="userRank" value="'.$_SESSION['userRank'].'">
								<button class="home-s-btn rs-btn btn btn-sm btn-danger" type="submit" name="rs-btn" value="'.$row['stories_id'].'">Remove Story</button>
							</form>

							<form action="/news/submit" method="post">
								<button class="home-s-btn es-btn btn btn-sm btn-primary" name="id" value="'.$row['stories_id'].'">Edit Story</button>
							</form>';
			}

			echo '		</div>';
			echo '	</div>';
			echo '</a>';
		}
	?>
	<hr id="sp"></hr>
</div>
<?php
	// include 'includes/scrollpaddingtest.inc.php';
	include 'end.php';
?>
