<?php
	include 'header.php';

	// unauthorised users get kicked back to home page
	if (!isset($_SESSION['userId']) || $_SESSION['userRank'] == 1)
	{
		echo '<script>window.location.replace("http://localhost/news/");
</script>';
	}


	if(!isset($_POST['id']))
	{
		$title = $subtitle = $content = $cf_caption = $video = "";
		$genre_id = null;
		$cf_type = -1;
		$tn = $mtn = $image = "Choose file";
		$priority = 1;
		$sources = array();
		$redir = "submit_story.inc.php";
		$id = $cfID = $tnID = $mtnID = null;
		$r = " required";

		$author_id = $_SESSION['userId'];

		echo '<script>document.title = "Submit Story - localnews";</script>';
	}
	else
	{
		$id = $_POST['id'];
		$sql = "SELECT *, iCF.images_path AS cf, iCF.images_id AS cfID, iTN.images_path as tn, iTN.images_id as tnID, iMTN.images_path as mtn, iMTN.images_id as mtnID
						FROM stories
						JOIN users
							ON stories.stories_auth_id = users.users_id
						JOIN genres
							ON stories.stories_genre = genres.genres_id
						LEFT JOIN images iCF
							ON stories.stories_cf = iCF.images_id
						LEFT JOIN images iTN
							ON stories.stories_thumbnail = iTN.images_id
						LEFT JOIN images iMTN
							ON stories.stories_minithumbnail = iMTN.images_id
						WHERE stories.stories_id = ".$id.";";
		$result = mysqli_query($conn, $sql);
		if($row = mysqli_fetch_assoc($result))
		{
			$r = "";
			$title = $row['stories_title']; //
			$subtitle = $row['stories_subtitle']; //
			$content = $row['stories_content']; //
			$author_id = $row['stories_auth_id']; //
			$genre_id = $row['genres_id']; //
			$priority = $row['stories_priority']; //
			$video = $row['stories_cf']; //
			$image = $row['cf']; //
			$cf_caption = $row['stories_cf_caption']; //
			$cf_type = $row['stories_cf_type']; //
			$tn = $row['tn']; //
			$mtn = $row['mtn']; //
			$sources_raw = $row['stories_sources']; //
			$sources = json_decode($sources_raw, true); //
			$id = $row['stories_id'];

			$cfID = $row['cfID'];
			$tnID = $row['tnID'];
			$mtnID = $row['mtnID'];

			$redir = "edit_story.inc.php";

			if($video == 4)
			{
				$video = "";
			}
		}

		echo '<script>document.title = "Edit Story - localnews";</script>';
	}

?>

<h3 class="text-light mt-5">Submit Story:</h3>
<form class="" action="/news/includes/<?php echo $redir; ?>" method="post" enctype="multipart/form-data">
	<fieldset class="border p-2 border-dark border-2 rounded bg-secondary">
   <legend class="w-auto text-light">Information</legend>
	 <h6 class="text-light">Title</h6>
	 <input type="text" name="story_title" class="form-control w-100 mb-2" value="<?php echo $title; ?>" required>
	 <h6 class="text-light">Subtitle</h6>
	 <input type="text" name="story_subtitle" class="form-control w-100 mb-2" value="<?php echo $subtitle; ?>" required>
	 <h6 class="text-light">Genre</h6>
	 <select class="custom-select mb-2 w-auto" name="story_genre" value="<?php echo $genre_id; ?>" required>
		 <option value="null">Choose a genre</option>
		 <?php
			 $sql = "SELECT * FROM genres ORDER BY genres_name";
			 $result = mysqli_query($conn, $sql);

			 while($row = mysqli_fetch_assoc($result))
			 {
				 $selected = "";
				 if($row['genres_id'] == $genre_id)
				 {
					 $selected = " selected";
				 }
				 echo '<option value="' . $row['genres_id'] . '"'.$selected.'>' . $row['genres_name'] . '</option>';
			 }
		 ?>
	 </select>
	 <h6 class="text-light">Priority</h6>
	 <select class="custom-select mb-2 w-auto" name="story_priority" value="<?php echo $priority; ?>" required>
		<?php
			$types = ["Low", "Regular", "Breaking"];
			for ($i=0; $i < sizeof($types); $i++)
			{
				$selected = "";
				if($i == $priority)
				{
					$selected = " selected";
				}
				echo '<option value="'.$i.'"'.$selected.'>'.$types[$i].'</option>';
			}
		?>
	</select>
	 <h6 class="text-light">Content</h6>
	 <textarea name="story_content" rows="8" class="form-control w-100 mb-2" required><?php echo $content; ?></textarea>
	</fieldset>

	<fieldset class="border p-2 border-dark border-2 rounded bg-secondary">
  	<legend class="w-auto text-light">Images</legend>

		<h6 class="text-light">Thumbnail</h6>
		<div class="custom-file mb-2">
			<input type="file" class="custom-file-input" name="story_thumbnail" id="story_thumbnail"
			onchange="document.getElementById('tn_label').innerHTML = this.files[0].name;" />
		  	<label class="custom-file-label" for="story_thumbnail" id="tn_label"><?php echo $tn; ?></label>
		</div>

		<h6 class="text-light">Mini-thumbnail</h6>
		<div class="custom-file mb-2">
	   	<input type="file" class="custom-file-input" name="story_mini-thumbnail" id="story_mini-thumbnail"
			onchange="document.getElementById('mtn_label').innerHTML = this.files[0].name;" />
	   	<label class="custom-file-label" for="story_mini-thumbnail" id="mtn_label"><?php echo $mtn; ?></label>
		</div>

	 	<h6 class="text-light">Content frame:</h6>
	 	<select class="custom-select mb-2 w-auto" name="story_content-frame" value="<?php echo $cf_type; ?>" onchange="onCFChange(this);" id="cfcDD" required>
			<?php
			$s1 = "";
			$s2 = "";
			$s3 = "";
			if($cf_type == 1) {$s1 = " selected";}
			else if($cf_type == 0) {$s2 = " selected";}
			else {$s3 = " selected";}
			?>
			<option value="null" <?php echo $s3; ?>>Choose a content type</option>
		 	<option value="1" <?php echo $s1; ?>>Image</option>
		 	<option value="0" <?php echo $s2; ?>>Video link</option>
	 	</select>

		<div id="story_cf-ci-wrapper" class="hidden">
			<h6 class="text-light">Content image:</h6>
			<div class="custom-file mb-2">
		   	<input type="file" class="custom-file-input" name="story_content-image" id="story_content-image"
				onchange="document.getElementById('cf_label').innerHTML = this.files[0].name;">
		   	<label class="custom-file-label" for="story_content-image" id="cf_label"><?php echo $image; ?></label>
			</div>
		</div>

		<div id="story_cf-vl-wrapper" class="w-100 hidden mb-2">
	 	 	<h6 class="text-light">Video link (youtube):</h6>
			<div class="input-group">
				<div class="input-group-prepend">
					<span class="input-group-text" id="video-prepend">https://www.youtube.com/watch?v=</span>
			  </div>
				<input type="text" name="story_video-link" class="form-control" value="<?php echo $video; ?>" aria-label="video" aria-describedby="video-prepend">
			</div>
		</div>
		<script>
			if(<?php echo $cf_type; ?> == 1) document.getElementById("story_cf-ci-wrapper").classList.toggle("hidden");
			else if(<?php echo $cf_type; ?> == 0) document.getElementById("story_cf-vl-wrapper").classList.toggle("hidden");
		</script>
	 	<h6 class="text-light">Content frame caption:</h6>
 	 <input type="text" name="story_cf_caption" class="form-control w-100 mb-2" value="<?php echo $cf_caption; ?>" required>
	</fieldset>

	<!-- Hidden fields -->
	<input type="hidden" name="story_auth_id" value="<?php echo $author_id; ?>">

	<!-- PHP to generate the sources (on edit) -->
	<?php
		if(sizeof($sources) > 0) // if $sources == 0, then either the story has no sources or we're submitting
		{
			$link1 = $sources[0]["link"];
			$name1 = $sources[0]["name"];
			$credit1 = $sources[0]["credit"];
		}
		else {
			$link1 = $name1 = $credit1 = "";
		}
	?>

	<fieldset class="border p-2 border-dark border-2 rounded bg-secondary">
		<legend class="w-auto text-light">Sources</legend>
		<div id="sources"></div>
		<button onclick="return addSource();" class="btn btn-primary mt-2">Add Source</button>
	</fieldset>

	<input type="hidden" name="story_id" value="<?php echo $id; ?>">
	<input type="hidden" name="story_old_tn" value="<?php echo $tn; ?>">
	<input type="hidden" name="story_old_mtn" value="<?php echo $mtn; ?>">
	<input type="hidden" name="story_old_cf" value="<?php echo $image; ?>">
	<input type="hidden" name="story_old_tn_id" value="<?php echo $tnID; ?>">
	<input type="hidden" name="story_old_mtn_id" value="<?php echo $mtnID; ?>">
	<input type="hidden" name="story_old_cf_id" value="<?php echo $cfID; ?>">

	<!-- STORY SUBMIT BUTTON -->
	<input type="submit" name="story_submit" class="btn btn-success mt-3">
</form>

<script type="text/javascript">
	var sourceCount = 0;

	function addSource(link="", name="", credit="", doRemove=true)
	{
		sourceCount++;
		var sourceBlock = document.createElement('fieldset');
		sourceBlock.setAttribute("class", "border p-2 border-dark border-2 rounded mb-2");
		sourceBlock.id = "source-" + sourceCount;
		var legend = '<legend class="w-auto text-light" id="legend-' + sourceCount + '">Source ' + sourceCount + '</legend>';
		var link = '<h6 class="text-light">Link</h6><input class="form-control w-100 mb-2" type="text" name="link-' + sourceCount + '" id="link-' + sourceCount + '" value="' + link.replace(/\"/g, "&#34;") + '">';
		var name = '<h6 class="text-light">Name</h6><input class="form-control w-100 mb-2" type="text" name="name-' + sourceCount + '" id="name-' + sourceCount + '" value="' + name.replace(/\"/g, "&#34;") + '">';
		var credit = '<h6 class="text-light">Credit</h6><input class="form-control w-100 mb-2" type="text" name="credit-' + sourceCount + '" id="credit-' + sourceCount + '" value="' + credit.replace(/\"/g, "&#34;") + '">';
		var removeButton = "";
		if(doRemove)
		{
			removeButton = '<div class="text-right"><button onclick="return removeSource(' + sourceCount + ');" class="btn btn-danger mt-2" id="button-' + sourceCount + '">Remove this source</button></div>';
		}

		sourceBlock.innerHTML = legend + link + name + credit + removeButton;
		document.getElementById('sources').appendChild(sourceBlock);
		return false;
	}

	addSource("<?php echo $link1.'", "'.$name1.'", "'.$credit1; ?>", false);

	function removeSource(sourceNumber)
	{
		var sourceBlock = document.getElementById("source-" + sourceNumber);
		sourceBlock.parentNode.removeChild(sourceBlock);

		if(sourceNumber != sourceCount)
		{
			for (var i = sourceNumber+1; i <= sourceCount; i++)
			{
				console.log(sourceCount + " " + sourceNumber + " " + i);
				var sourceI = document.getElementById("source-" + i);
				var legendI = document.getElementById("legend-" + i);
				var linkI = document.getElementById("link-" + i);
				var nameI = document.getElementById("name-" + i);
				var creditI = document.getElementById("credit-" + i);
				var buttonI = document.getElementById("button-" + i);

				sourceI.id = "source-" + (i-1);
				legendI.id = "legend-" + (i-1);
				linkI.id = "link-" + (i-1);
				nameI.id = "name-" + (i-1);
				creditI.id = "credit-" + (i-1);
				buttonI.id = "button-" + (i-1);
				legendI.innerHTML = "Source " + (i-1);
				linkI.setAttribute("name", "link-" + (i-1));
				nameI.setAttribute("name", "name-" + (i-1));
				creditI.setAttribute("name", "credit-" + (i-1));
				buttonI.setAttribute("onclick", "return removeSource(" + (i-1) + ");")
			}
		}

		sourceCount--;
		return false;
	}

	function onCFChange(select)
	{
		var ci_wrapper = document.getElementById("story_cf-ci-wrapper");
		var vl_wrapper = document.getElementById("story_cf-vl-wrapper");
		if(select.value == "1") {
			ci_wrapper.classList.remove("hidden");
			vl_wrapper.classList.add("hidden");
		}
		else if(select.value == "0") {
			vl_wrapper.classList.remove("hidden");
			ci_wrapper.classList.add("hidden");
		}
		else {
			vl_wrapper.classList.add("hidden");
			ci_wrapper.classList.add("hidden");
		}
	}
</script>

<?php
	// echo sizeof($sources);
	for ($i=1; $i < sizeof($sources); $i++)
	{
		$l = str_replace("\"", "\\\"", $sources[$i]["link"]);
		$n = str_replace("\"", "\\\"", $sources[$i]["name"]);
		$c = str_replace("\"", "\\\"", $sources[$i]["credit"]);
		if(ends_with($l, "\\") || ends_with($n, "\\") || ends_with($c, "\\"))
		{
			echo "Big oof <br>:" . $l . "<br>" . $n . "<br>" . $c . "<br>";
		}
		else
		{
			echo '<script>
							addSource("'.$l.'", "'.$n.'", "'.$c.'");
						</script>';
		}
	}
?>

<?php
	include 'end.php';
?>
