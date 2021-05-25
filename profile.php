<?php
	include 'header.php';
?>
<div class="profile-wrapper pt-1 w-100 text-light">
<?php
	if(isset($_GET['uid']))
	{
		$sql = "SELECT * FROM users
						JOIN ranks ON ranks_id = users_rank_id
						LEFT JOIN images ON users_pfp = images_id
						WHERE users_uid = ?;";
		$stmt = mysqli_stmt_init($conn);
		if (!mysqli_stmt_prepare($stmt, $sql))
		{
			header("Location: ../index.php");
			exit();
		}
		else
		{
			mysqli_stmt_bind_param($stmt, "s", $_GET['uid']);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			if($row = mysqli_fetch_assoc($result))
			{
				echo '<script>document.title = "'. $row['users_uid'].'\'s profile - localnews";</script>';
				?>
				<div class="w-100 pt-4">
					<div class="media border rounded bg-secondary profilebox" style="padding: 15px 0 15px 15px;">
						<?php
							if(isset($_SESSION['userId']) && $row['users_id'] == $_SESSION['userId'])
							{
								echo '<a href="#" data-toggle="modal" data-target="#pfpModal">';
								echo '<div class="pfpIMG">';
								echo '	<img src="http://localhost/news/imgs/'.$row['images_path'].'" class="align-middle pfpIMGimg" style="width: 120px">';
								echo '<div class="pfpFG">📷<br><p class="pfpFGtext">Change profile picture</p></div></div></a>';
							}
							else
							{
								echo '	<img src="http://localhost/news/imgs/'.$row['images_path'].'" class="align-middle" style="width: 120px">';
							}

							if(isset($_SESSION['userRank']))
							{
								$userRank = $row['users_rank_id'];
								$curUserRank = $_SESSION['userRank'];
								$sa = 5; // super admin
								$a = 4; // admin

							}

							echo '<div class="media-body">';
							echo '	<h3 style="padding-top: 10px;" class="w-100 pl-4">'.$row['users_uid'].'</h3>';
							echo '	<h6 class="pl-4">';
							echo '		<span class="badge badge-'. $row['ranks_colour'] .'">'.$row['ranks_name'].'</span>';
							echo 			'&nbsp;&nbsp;'.$row['users_first'].' '. $row['users_last'];
							if(isset($_SESSION['userRank'])
							&& $curUserRank > 2 // journalists can't do shit mk
							&& ($curUserRank == $sa // if user is a superadmin
							||	$curUserRank > $userRank // if user has a higher rank than the promotee
							|| ($curUserRank == $a && $userRank != $sa))) // if the user is an admin and the promotee isnt a superadmin
							{
								echo '		<div class="float-right pr-4">';
								echo '			<button class="btn btn-primary text-light btn-sm" data-toggle="modal" data-target="#promoteModal">Promote User</button>';
								echo '		</div>';
							}
							echo '	</h6>';
							echo '</div>';
						?>
					</div>
				</div>

				<!-- PFP Modal -->
				<div class="modal fade text-dark" id="pfpModal">
				  <div class="modal-dialog">
				    <div class="modal-content">

				      <!-- Modal Header -->
				      <div class="modal-header">
				        <h4 class="modal-title">Change Profile Picture</h4>
				        <button type="button" class="close" data-dismiss="modal">&times;</button>
				      </div>

				      <!-- Modal body -->
				      <div class="modal-body">
								<?php
								if(isset($_GET['pfp_err']))
								{
									$error = $_GET['pfp_err'];

									echo '<div class="bg-danger w-100 mb-3 p-2 text-light rounded">Error: ';

									switch ($error) {
										case 'b2b':
											echo 'Filesize is too large! Images must be under 256kb in size.';
											break;
										case 'ext':
											echo 'The file\'s extension is unfortunately not allowed. Allowed extensions: JPEG, JPG, GIF, PNG.';
											break;
										case 'error':
											echo 'An unexpected error occured with the file uploading! Please try again.';
											break;
										case 'sql1':
										case 'sql2':
										case 'sql3':
											echo 'SQL error. '.$error;
											break;
									}
									echo '</div>';
								}

								?>
								<form action="http://localhost/news/includes/upload_pfp.inc.php" method="post" enctype="multipart/form-data">
									<input type="hidden" value=<?php echo '"'.$row['users_uid'].'"' ?> name="username">
									<div class="custom-file mb-3">
										<input type="file" class="custom-file-input" name="file" id="customFile"
											onchange="document.getElementById('img_label').innerHTML = this.files[0].name;" accept="image/gif, image/jpeg, image/png">
										<label class="custom-file-label" for="customFile" id="img_label">Choose File</label>
									</div>
									<button class="btn btn-success" type="submit" name="submit">Upload</button>
								</form>
								<p class="mt-3">...or...</p>
								<form action="../includes/reset_pfp.inc.php" method="post" enctype="multipart/form-data">
									<input type="hidden" value=<?php echo '"'.$row['users_uid'].'"' ?> name="username">
									<button class="btn btn-success" type="submit" name="submit_reset">Reset Profile Picture</button>
								</form>
				      </div>

							<script>
								function onChangeFile()
								{
									var file1 = document.getElementById('customFile');
									document.getElementById('img_label').innerHTML = file1.files[0].name;
								}
							</script>

				      <!-- Modal footer -->
				      <div class="modal-footer">
				        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				      </div>

				    </div>
				  </div>
				</div>

				<div class="modal fade text-dark" id="promoteModal">
				  <div class="modal-dialog">
				    <div class="modal-content">

				      <!-- Modal Header -->
				      <div class="modal-header">
				        <h4 class="modal-title">Promote User</h4>
				        <button type="button" class="close" data-dismiss="modal">&times;</button>
				      </div>

				      <!-- Modal body -->
				      <div class="modal-body">
									Change <?php echo $row['users_uid']; ?>'s role:
									<form class="promoteForm formcontrol my-2" action="/news/includes/promote.inc.php" method="post">
										<select class="custom-select" name="promoteSelect">
											<?php
											$sql = "SELECT * FROM ranks;";
											$result = mysqli_query($conn, $sql);
											while($rank_row = mysqli_fetch_assoc($result))
											{
												if($_SESSION['userRank'] == 5 || $rank_row['ranks_id'] < $_SESSION['userRank'])
												{
													$k = "";
													if($rank_row['ranks_id'] == $row['users_rank_id'])
													{
														$k = " selected";
													}
													echo '<option value="'.$rank_row['ranks_id'].'"'.$k.'>'.$rank_row['ranks_name'].'</option>';
												}
											}
											?>
										</select>
										<input type="hidden" value="<?php echo $row['users_uid'] ?>" name="promoteUid">
										<button type="submit" class="mt-3 btn btn-primary" name="promoteSubmit" value="yea">Submit</button>
									</form>
				      </div>

				      <!-- Modal footer -->
				      <div class="modal-footer">
				        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				      </div>

				    </div>
				  </div>
				</div>

				<?php
				$isUserPage = (isset($_SESSION['userId']) && $row['users_id'] == $_SESSION['userId']);
				if($row['users_bio'] != '' || $isUserPage)
				{
				?>
				<div class="w-100 border rounded mt-4 bg-secondary profilebox">
					<h3>Bio
					<?php
					if($isUserPage)
					{
						echo '<button class="btn btn-primary my-2 text-light btn-sm" data-toggle="modal" data-target="#bioModal">Edit Bio</button>';
					}
					?></h3>
					<p><?php echo $row['users_bio'] ?></p>
				</div>

				<!-- Bio Modal -->
				<div class="modal fade text-dark" id="bioModal">
					<div class="modal-dialog">
						<div class="modal-content">

							<!-- Modal Header -->
							<div class="modal-header">
								<h4 class="modal-title">Edit Bio</h4>
								<button type="button" class="close" data-dismiss="modal">&times;</button>
							</div>

							<!-- Modal body -->
							<div class="modal-body w-100">
									Edit your bio!:
									<form class="formcontrol my-2 w-100" action="/news/includes/edit_bio.inc.php" method="post">
										<textarea name="bioBio" class="form-control w-100 mb-2" rows="8"><?php echo $row['users_bio']; ?></textarea>
										<input type="hidden" required value="<?php echo $row['users_uid'] ?>" name="bioUid">
										<button type="submit" class="mt-3 btn btn-primary" name="bioSubmit" value="yea">Submit</button>
									</form>
							</div>

							<!-- Modal footer -->
							<div class="modal-footer">
								<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
							</div>

						</div>
					</div>
				</div>


				<?php
				}
				?>
				<!-- <div class="w-100 border rounded mt-4 bg-secondary profilebox">
					<h3>Details</h3>
					<p>to be decided what goes in here still.<?php //echo $row['users_bio'] ?></p>
				</div> -->
				<?php
				if($row['users_rank_id'] > 1)
				{
					echo '<div class="w-100 border rounded mt-4 bg-secondary profilebox" style="min-height: 150px; padding: 15px;">
									<h3>Stories</h3>
									<div class="small-article-wrapper text-light">';
					$sql = "SELECT * FROM stories
					 					LEFT JOIN images ON images_id = stories_thumbnail
										WHERE stories_auth_id = ".$row['users_id']."
											AND stories_priority < 3
											AND stories_status = 2
										ORDER BY stories_date DESC;";

					$result = mysqli_query($conn, $sql);
					while ($row = mysqli_fetch_assoc($result))
					{
						echo '<a class="article-link text-light" href="http://localhost/news/story/'.$row['stories_id'].'">';
						echo '	<div class="small-article media">';
						if($row['stories_thumbnail'] != "")
						{
							echo '		<img src="/news/imgs/'.$row['images_path'].'" class="mr-3 align-self-center" style="height: 130px;">';
						}
						else
						{
							echo '		<div class="mr-3 bg-danger align-self-center" style="height: 100px; width: 100px;"></div>';
						}

						$breaking = "";
						if($row['stories_priority'] > 1)
						{
							$breaking = '<span class="badge badge-danger mr-2">BREAKING</span>';
						}
						else if($row['stories_priority'] == 0)
						{
							$breaking = '<span class="badge badge-info mr-2">Low</span>';
						}

						echo '		<div class="media-body" style="text-decoration: none;">';
						echo '			<h5>' . $breaking . $row['stories_title'] . '</h5>';
						echo '			<i>Posted on '.date_format(date_create($row['stories_date']), "d/m/Y").'</i>';
						echo '			<p>' . $row['stories_subtitle'] . "</p>";
						echo '		</div>';
						echo '	</div>';
						echo '</a>';
					}
					echo '</div></div>';
				}
			}
			else
			{
				echo '<script>document.title = User not found - localnews";</script>';
				echo '<div class="bg-danger w-100 mb-3 p-2 text-light rounded text-center">User not found.</div>';
				echo '<div class="bg-light w-100 mb-3 p-2 text-secondary rounded text-center">DEBUG<br>';
				echo '</div>';
			}
		}
	}
?>

<script>

</script>

</div>



<?php
	if(isset($_GET['pfp_err']))
	{
		echo '<script>$("#pfpModal").modal("show");</script>';
	}
?>

<?php
	include 'end.php';
?>
