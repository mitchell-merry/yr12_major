<?php
if(isset($_POST['signup-submit']))
{
	include 'dbh.inc.php';
	include 'func.inc.php';

	$username = $_POST['uid'];
	$email = $_POST['email'];
	$email_conf = $_POST['email-conf'];
	$firstname = $_POST['firstname'];
	$lastname = $_POST['lastname'];
	$pwd = $_POST['pwd'];
	$pwd_conf = $_POST['pwd-conf'];
  $salt = bin2hex(random_bytes(32));

	if(empty($username) || empty($email) || empty($email_conf) || empty($firstname) || empty($lastname) || empty($pwd) || empty($pwd_conf)) // If any field is empty
	{
		header("Location: ../index.php?serror=efs&uid=".$username."&email=".$email."&fn=".$firstname."&ln=".$lastname);
		exit();
	}
	else if(!filter_var($email, FILTER_VALIDATE_EMAIL) && !preg_match("/^[a-zA-Z0-9_]*$/", $username)) // Invalid email and username
	{
		header("Location: ../index.php?serror=invemailuid&fn=".$firstname."&ln=".$lastname);
		exit();
	}
	else if(!filter_var($email, FILTER_VALIDATE_EMAIL) || $email == 'localnewsresetpwd@gmail.com') // Not a valid email, or is equal to our support email, which is a no-go
	{
		header("Location: ../index.php?serror=invemail&uid=".$username."&fn=".$firstname."&ln=".$lastname);
		exit();
	}
	else if(!preg_match("/^[a-zA-Z0-9_]*$/", $username)) // Invalid username
	{
		header("Location: ../index.php?serror=invuid&email=".$email."&fn=".$firstname."&ln=".$lastname);
		exit();
	}
	else if ($pwd !== $pwd_conf) // Password and Confirm Password are not equal
	{
		header("Location: ../index.php?serror=pwd&uid=".$username."&email=".$email."&fn=".$firstname."&ln=".$lastname);
		exit();
	}
	else if ($email !== $email_conf) // Email and Confirm Email are not equal
	{
		header("Location: ../index.php?serror=email&uid=".$username."&email=".$email."&fn=".$firstname."&ln=".$lastname);
		exit();
	}
	else if (!isset($_POST['tac'])) // User has not agreed to the Terms and Conditions as well as the Privacy Policy
	{
		header("Location: ../index.php?serror=tac&uid=".$username."&email=".$email."&fn=".$firstname."&ln=".$lastname);
		exit();
	}
	else
	{
		$sql = "SELECT users_uid FROM users WHERE users_uid = ?";
		$stmt = mysqli_stmt_init($conn);
		if(!mysqli_stmt_prepare($stmt, $sql))
		{
			header("Location: ../index.php?serror=sql1");
			exit();
		}
		else
		{
			mysqli_stmt_bind_param($stmt, "s", $username);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_store_result($stmt);
			$resultCheck = mysqli_stmt_num_rows($stmt);

			if($resultCheck > 0)
			{
				header("Location: ../index.php?serror=usertaken&uid=".$username."&email=".$email."&fn=".$firstname."&ln=".$lastname);
				exit();
			}
			else
			{
				$sql = "SELECT users_uid FROM users WHERE users_email = ?";
				$stmt = mysqli_stmt_init($conn);
				if(!mysqli_stmt_prepare($stmt, $sql))
				{
					header("Location: ../index.php?serror=sql2");
					exit();
				}
				else
				{
					mysqli_stmt_bind_param($stmt, "s", $email);
					mysqli_stmt_execute($stmt);
					mysqli_stmt_store_result($stmt);
					$resultCheck = mysqli_stmt_num_rows($stmt);

					if($resultCheck > 0)
					{
						header("Location: ../index.php?serror=emailtaken&uid=".$username."&email=".$email."&fn=".$firstname."&ln=".$lastname);
						exit();
					}
					else
					{
						$pfp = 1;
						$sql = "INSERT INTO users (`users_uid`, `users_rank_id`, `users_first`, `users_last`, `users_email`, `users_pfp`, `users_pwd`, `users_salt`) VALUES (?, ?, ?, ?, ?, ?, ?, ?);";
						$stmt = mysqli_stmt_init($conn);
						if(!mysqli_stmt_prepare($stmt, $sql))
						{
							header("Location: ../index.php?serror=sql3");
							exit();
						}
						else
						{
							$hashed_pwd = password_hash($pwd.$salt, PASSWORD_DEFAULT);
							$rank = 1;
							mysqli_stmt_bind_param($stmt, "ssssssss", $username, $rank, $firstname, $lastname, $email, $pfp, $hashed_pwd, $salt);
							mysqli_stmt_execute($stmt);
							header("Location: ../index.php?signup=success");
							exit();
						}
					}
				}
			}
		}

	}
	mysqli_stmt_close($stmt);
	mysqli_close($conn);
}
else
{
	header("Location: ../index.php");
	exit();
}
