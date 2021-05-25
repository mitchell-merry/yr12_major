<?php
if(isset($_POST['login-submit']))
{
	include 'dbh.inc.php';

	$mailuid = $_POST['mailuid'];
	$pwd = $_POST['pwd'];
	session_start();
	// $_SESSION['loginattempts'] = 0;

	if(!isset($_COOKIE['locked']) && $_SESSION['loginattempts'] >= 3)
	{
		$_SESSION['loginattempts'] = 0;
	}

	if($_SESSION['loginattempts'] >= 3)
	{
		header("Location: ../index.php?lerror=makick");
		exit();
	}
	else if(empty($mailuid) || empty($pwd))
	{
		header("Location: ../index.php?lerror=efl");
		exit();
	}
	else
	{
		$sql = "SELECT * FROM users WHERE users_uid=? or users_email=?;";
		$stmt = mysqli_stmt_init($conn);
		if (!mysqli_stmt_prepare($stmt, $sql))
		{
			header("Location: ../index.php?lerror=sqlerrl");
			exit();
		}
		else
		{
			mysqli_stmt_bind_param($stmt, "ss", $mailuid, $mailuid);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			if($row = mysqli_fetch_assoc($result))
			{
				$pwdCheck = password_verify($pwd.$row['users_salt'], $row['users_pwd']);
				if($pwdCheck == false)
				{
					if(!isset($_SESSION['loginattempts']))
					{
						$_SESSION['loginattempts'] = 0;
					}
					else if($_SESSION['loginattempts'] >= 2)
					{
						$_SESSION['loginattempts']++;
						setcookie("locked", date("U") + 602, date("U") + 602, "/");
						header("Location: ../index.php?lerror=maxattempts");
						exit();
					}

					$_SESSION['loginattempts']++;

					header("Location: ../index.php?lerror=wrongpwd");
					exit();
				}
				else if($pwdCheck == true) // Log the user in; hashed passwords are equal
				{
					$_SESSION['userId'] = $row['users_id'];
					$_SESSION['userUsername'] = $row['users_uid'];
					$_SESSION['userFirst'] = $row['users_first'];
					$_SESSION['userLast'] = $row['users_last'];
					$_SESSION['userRank'] = $row['users_rank_id'];

					if(isset($_POST['remember']))
					{
						setcookie("remember", true, time() + (86400 * 30), "/"); // Expires after one month
						setcookie("id", $row['users_id'], time() + (86400 * 30), "/");
						setcookie("username", $row['users_uid'], time() + (86400 * 30), "/");
						setcookie("firstname", $row['users_first'], time() + (86400 * 30), "/");
						setcookie("lastname", $row['users_last'], time() + (86400 * 30), "/");
						setcookie("rank", $row['users_rank_id'], time() + (86400 * 30), "/");
					}

					header("Location: ../index.php?login=success");
					exit();
				}
				else
				{
					header("Location: ../index.php?lerror=wrongpwdbutweird");
					exit();
				}
			}
			else
			{
				header("Location: ../index.php?lerror=usrnotfound");
				exit();
			}
		}
	}
}
else
{
	header("Location: ../index.php");
	exit();
}
