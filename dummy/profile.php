<?php
	include 'header.php';
?>
<div class="content row">
	<div class="col-2 ad-wrapper">adspace</div>
	<div class="center col-7">
		<?php include 'includes/login_message.inc.php' ?>
		<div class="profile-wrapper pt-1 w-100 text-light">
				<?php
					if(isset($_GET['uid']))
					{
						$sql = "SELECT * FROM users WHERE username=?;";
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
								echo $row['username'].'<br>';
								echo $row['firstname'].'<br>';
								echo $row['lastname'].'<br>';
								echo $row['email'].'<br>';
								echo $row['id'].'<br>';
								if($row['id'] == $_SESSION['userId'])
								{
									echo $row['pwd'].'<br>';
									echo $row['salt'].'<br>';
								}
							}
							else
							{
								echo '<div class="bg-danger w-100 mb-3 p-2 text-light rounded text-center">User not found.</div>';
							}
						}
					}
				?>
		</div>
	</div>
	<div class="col-3">minor story space</div>
</div>
<?php
	include 'end.php';
?>
