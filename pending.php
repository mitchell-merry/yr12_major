<?php
	include 'header.php';

	// unauthorised users get kicked back to home page
	if (!isset($_SESSION['userId']) || $_SESSION['userRank'] == 1) {
		echo '<script>window.location.replace("http://localhost/news/");
</script>';
	}

	echo '<script>document.title = "View pending stories - localnews";</script>';

?>

<h3 class="text-light text-left my-3">Pending Stories</h3>

<table class="table table-dark table-striped">
	<thead>
		<th>Title</th>
		<th>Date</th>>
		<th></th>
	</thead>
  <tbody>
		<?php
			$sql = "SELECT *, users_first, users_last, i1.images_path AS tn, i2.images_path AS mtn FROM stories
						LEFT JOIN users ON users.users_id = stories.stories_auth_id
						LEFT JOIN images AS i1 ON stories.stories_thumbnail = i1.images_id
						LEFT JOIN images AS i2 ON stories.stories_minithumbnail = i2.images_id
						WHERE stories.stories_status = 1
						ORDER BY stories_date";

			$result = mysqli_query($conn, $sql);
			while($row = mysqli_fetch_assoc($result))
			{
				$id = $row['stories_id'];
				echo '<tr>';
				echo '	<td><a href="/news/story/'.$row['stories_id'].'">'.$row['stories_title'].'</a></td>';
				echo '	<td style="width: 15%;">'.english_days(days_ago($row['stories_date'])).'</td>';
				echo '	<td class="pr-5">
									<div class="pending-button-wrapper" id="button-'.$id.'" onclick="pendingButton('.$id.');">
										<a href="#" class="pending-button text-light ns">⋮</a>
									</div>
									<div id="menu-'.$id.'" class="hidden pending-menu bg-dark border border-rounded mt-2 ns">
										<ul class="pending-menu-ul py-2">
											<li class="pending-menu-li ns">
												<form id="accept-'.$id.'" action="includes/pending-handle.inc.php" method="post">
													<input type="hidden" name="id" value="'.$id.'">
													<input type="hidden" name="action" value="2">
													<a class="text-success" href="#" onclick="document.getElementById(\'accept-'.$id.'\').submit();">Accept</a>
												</form>
											<li class="pending-menu-li ns">
												<form id="edit-'.$id.'" action="submit" method="post">
													<input type="hidden" name="id" value="'.$id.'">
													<a class="" href="#" onclick="document.getElementById(\'edit-'.$id.'\').submit();">Edit</a>
												</form>
											</li>
											<li class="pending-menu-li ns">
												<form id="reject-'.$id.'" action="includes/pending-handle.inc.php" method="post">
													<input type="hidden" name="id" value="'.$id.'">
													<input type="hidden" name="action" value="0">
													<a class="text-danger" href="#" onclick="document.getElementById(\'reject-'.$id.'\').submit();">Reject</a>
												</form>
											</li>
										</ul>
									</div>
								</td>';
				echo '</tr>';
			}
		?>
  </tbody>
	<script>
		function pendingButton(button)
		{
			menu = document.getElementById("menu-" + button);
			menu.classList.toggle("hidden");
			// button_ = document.getElementById("button-" + button);
			// var right = menu.getBoundingClientRect().x + menu.getBoundingClientRect().width;
			// if(right > document.body.clientWidth)
			// {
			// 	menu.style.class = (button_.getBoundingClientRect().x - menu.getBoundingClientRect().width) + "px";
			// }
			// else
			// {
			// 	menu.style.left = button_.getBoundingClientRect().x;
			// }
			// console.log(menu.getBoundingClientRect().width + " " + document.body.clientWidth + " " + menu.style.right);
		}
	</script>
</table>
<?php
	include 'end.php';
?>
