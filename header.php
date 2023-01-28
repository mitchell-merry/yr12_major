<?php
	session_start();
	if (isset($_COOKIE['remember'])) // Retrieve remembered user data
	{
		setcookie("remember", true, time() + (86400 * 30), "/"); // Expires after one month
		setcookie("id", $_COOKIE['id'], time() + (86400 * 30), "/"); // We're resetting all variables here
		setcookie("username", $_COOKIE['username'], time() + (86400 * 30), "/");
		setcookie("firstname", $_COOKIE['firstname'], time() + (86400 * 30), "/");
		setcookie("lastname", $_COOKIE['lastname'], time() + (86400 * 30), "/");
		setcookie("rank", $_COOKIE['rank'], time() + (86400 * 30), "/");

		$_SESSION['userId'] = $_COOKIE['id'];
		$_SESSION['userUsername'] = $_COOKIE['username'];
		$_SESSION['userFirst'] = $_COOKIE['firstname'];
		$_SESSION['userLast'] = $_COOKIE['lastname'];
		$_SESSION['userRank'] = $_COOKIE['rank'];
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>afghbkijsghfkjsfhjksfd</title>
	<meta charset="utf-8">
	<meta name="description" content="Trustworthy news source!">

	<!-- Bootstrap, JQuery -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>

	<link rel="stylesheet" href="/news/style.css">
</head>
<body onresize="onResize();" onscroll="onScroll();" onclick="bodyClick();">
	<?php
		include_once 'includes/dbh.inc.php';
		include 'includes/func.inc.php';
		include 'includes/sql.inc.php';
	?>

	<div class="container-fluid">
		<nav class="row navbar navbar-expand-sm fixed-top navbar-dark bg-dark shadow" id="nav">
			<ul class="navbar-nav col-9" id="navbar-genres">
				<li class="nav-item">
					<a class="navbar-brand pl-3" style="" href="http://localhost/news">localnews</a>
				</li>
				<li class="nav-item"><a href="/news/about" class="nav-link pl-3" style="white-space: nowrap;">About Us</a></li>
				<?php
					$sql = "SELECT * FROM genres;";

					$result = mysqli_query($conn, $sql);
					$count = 1;
					while ($row = mysqli_fetch_assoc($result))
					{
						if($count > 4)
						{
							break;
						}
						$count++;
						echo '<li class="nav-item nav-genre">';
						echo '<a class="nav-link" href="http://localhost/news?g='.$row['genres_id'].'">' . $row['genres_name'] . '</a>';
						echo '</li>';
					}
				?>

		    <!-- Dropdown -->
		    <li class="nav-item dropdown">
		    	<a class="nav-link dropdown-toggle pl-3" href="#" data-toggle="dropdown" style="white-space: nowrap;">
		    		All Genres
		    	</a>
		    	<div class="dropdown-menu">
					<?php
						$result = mysqli_query($conn, $sql);
						while ($row = mysqli_fetch_assoc($result))
						{
							echo '<a class="dropdown-item" href="http://localhost/news?g='.$row['genres_id'].'">';
							echo $row['genres_name'];
							echo '</a>';
						}

						if(isset($_SESSION['userRank']) && $_SESSION['userRank'] > 2)
						{
							echo '<div class="dropdown-divider"></div>';
							echo '<a class="dropdown-item" href="#" data-toggle="modal" data-target="#genreAdd">Add a genre</a>';
						}
					?>
		    	</div>
		    </li>

			</ul>

			<div class="text-right col-3" id="navbar-login" style="white-space: nowrap;">
				<?php
					if(isset($_SESSION['userId']))
					{
						echo '<form action="http://localhost/news/includes/logout.inc.php" method="post">';
						echo '	<label class="text-light"> Hello, <a href="http://localhost/news/profile/'.strtolower($_SESSION['userUsername']).'">'.$_SESSION['userFirst'].'</a></label>';
						echo '	<button type="submit" class="btn btn-danger ml-2 text-right" name="logout-submit" id="logout">Logout</button>';
						echo '</form>';
					}
					else
					{
						echo '<a class="login-link mr-2" href="#" data-toggle="modal" data-target="#mainModal">Login / Signup</a>';
					}
				?>

			</div>
		</nav>
	</div>

	<!-- The Modal -->
	<div class="modal fade" id="mainModal" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
			  <!-- Modal Header -->
				<div class="modal-header">
		    	<h4 class="modal-title">Login / Signup</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
		    </div>

		    <!-- Login body -->
		    <div class="modal-body">
			   	<h5 class="pb-2 pt-0">Login</h5>
					<!-- Error Handling -->
					<?php
						if(isset($_GET['lerror']))
						{
							$error = $_GET['lerror'];
							if(isset($_COOKIE['locked'])) { $time = (int)$_COOKIE['locked'] - date("U"); }
							else { $time = 0; }

							$text = '<div class="bg-danger w-100 mb-3 p-2 text-light rounded">Error: ';
							if($error !== 'maxattempts' && $error !== 'makick') { echo $text; }

							switch ($error) {
								case 'efl':
									echo 'One or more empty fields!';
									break;
								case 'usrnotfound':
									echo 'Could not find anyone with that username / email registered.';
									break;
								case 'wrongpwd':
									echo 'Wrong password entered! Attempts: '.$_SESSION['loginattempts'].'/3';
									break;
								case 'maxattempts':
									echo time_convert_w_m('Too many failed login attempts. Try and log back in in ', $time);
									break;
								case 'makick':
									echo time_convert_w_m('You\'re currently locked out from logging in for having too many login attempts. Come back in ', $time);
									break;
								case 'wrongpwdbutweird':
									echo 'This code should never run! How did you do this???';
									break;
								case 'sqlerrl':
									echo ':)';
									break;
							}
							echo '</div>';
						}
					?>
					<!-- Login Form -->
		    	<form action="/news/includes/login.inc.php" method="post">
			   		<input type="text" class="form-control mb-3" placeholder="Username / Email" name="mailuid">
			   		<input type="password" class="form-control mb-3" placeholder="Password" name="pwd">
			  		<div class="custom-control custom-checkbox mb-3">
	    				<input type="checkbox" class="custom-control-input" id="rememberMe" name="remember">
	   					<label class="custom-control-label" for="rememberMe">Remember me</label>
						</div>
			   		<button type="submit" class="btn btn-success" name="login-submit">Log In</button>
		    	</form>
				</div>

		    <!-- Separator -->
		    <div class='col-lg-12'><hr></div>

	    	<!-- Signup body -->
				<div class="modal-body">
		    	<h5 class="pb-2">Create an Account</h5>
					<!-- Error Handling -->
					<?php
						if(isset($_GET['serror']))
						{
							$error = $_GET['serror'];
							echo '<div class="bg-danger w-100 mb-3 p-2 text-light rounded">Error: ';
							switch ($error) {
								case 'efs':
									echo 'One or more empty fields!';
									break;
								case 'invemailuid':
									echo 'Invalid email and username (alphanumeric and underscore characters only)!';
									break;
								case 'invemail':
									echo 'Invalid email!';
									break;
								case 'invuid':
									echo 'Invalid username (alphanumeric and underscore characters only)!';
									break;
								case 'pwd':
									echo 'Password and Confirm password fields do not match!';
									break;
								case 'email':
									echo 'Email and Confirm email fields do not match!';
									break;
								case 'usertaken':
									echo 'Username already taken!';
									break;
								case 'emailtaken':
									echo 'Email already taken!';
									break;
								case 'tac':
									echo 'You need to agree to the T&C and the Privacy Policy in order to sign up.';
									break;
								case 'sql1':
								case 'sql2':
								case 'sql3':
									echo ':)';
									break;
							}
							echo '</div>';
						}
					?>
					<!-- Signup Form -->
	    		<form action="includes/signup.inc.php" method="POST">
		    		<input type="text" class="form-control mb-3" placeholder="Username" name="uid" <?php if(isset($_GET['uid'])) { echo ' value="'.$_GET['uid'].'"'; } ?>>
		    		<input type="text" class="form-control mb-3" placeholder="First Name" name="firstname" <?php if(isset($_GET['fn'])) { echo ' value="'.$_GET['fn'].'"'; } ?>>
		    		<input type="text" class="form-control mb-3" placeholder="Last Name" name="lastname" <?php if(isset($_GET['ln'])) { echo ' value="'.$_GET['ln'].'"'; } ?>>
		    		<input type="password" class="form-control mb-3" placeholder="Password" name="pwd">
		    		<input type="password" class="form-control mb-3" placeholder="Confirm Password" name="pwd-conf">
		    		<input type="text" class="form-control mb-3" placeholder="Email" name="email" <?php if(isset($_GET['email'])) { echo ' value="'.$_GET['email'].'"'; } ?>>
		    		<input type="text" class="form-control mb-3" placeholder="Confirm Email" name="email-conf">
						<div class="custom-control custom-checkbox mb-3">
	    				<input type="checkbox" class="custom-control-input" id="tac" name="tac">
	   					<label class="custom-control-label" for="tac">I have read and agreed to the <a data-toggle="collapse" href="#tacCollapse">Terms and Conditions and the Privacy Policy.</a></label>
						</div>
						<div id="tacCollapse" class="collapse"><div class="card p-2">
							<h4>Terms and Conditions</h4>
							<h5>Introduction</h5>
							<p>These don't mean anything and are just a placeholder. You're free to ignore all of this; these were procedurally generated (link below).</p>
							<p>These Website Standard Terms and Conditions written on this webpage shall manage your use of our website, localnews accessible at localhost/news.</p>
							<p>These Terms will be applied fully and affect to your use of this Website. By using this Website, you agreed to accept all terms and conditions written in here. You must not use this Website if you disagree with any of these Website Standard Terms and Conditions. These Terms and Conditions have been generated with the help of the <a href="https://termsandcondiitionssample.com">Terms And Conditions Template</a> and the <a href="https://privacy-policy-template.com">Privacy Policy Template</a>.</p>

							<h5>Intellectual Property Rights</h5>
							<p>Other than the content you own, under these Terms, localnews and/or its licensors own all the intellectual property rights and materials contained in this Website.</p>
							<p>You are granted limited license only for purposes of viewing the material contained on this Website.</p>

							<h5>Restrictions</h5>
							<p>You are specifically restricted from all of the following:</p>
							<ul>
							    <li>publishing any Website material in any other media;</li>
							    <li>selling, sublicensing and/or otherwise commercializing any Website material;</li>
							    <li>publicly performing and/or showing any Website material;</li>
							    <li>using this Website in any way that is or may be damaging to this Website;</li>
							    <li>using this Website in any way that impacts user access to this Website;</li>
							    <li>using this Website contrary to applicable laws and regulations, or in any way may cause harm to the Website, or to any person or business entity;</li>
							    <li>engaging in any data mining, data harvesting, data extracting or any other similar activity in relation to this Website;</li>
							    <li>using this Website to engage in any advertising or marketing.</li>
							</ul>
							<p>Certain areas of this Website are restricted from being access by you and localnews may further restrict access by you to any areas of this Website, at any time, in absolute discretion. Any user ID and password you may have for this Website are confidential and you must maintain confidentiality as well.</p>

							<h5>Your Content</h5>
							<p>In these Website Standard Terms and Conditions, "Your Content" shall mean any audio, video text, images or other material you choose to display on this Website. By displaying Your Content, you grant localnews a non-exclusive, worldwide irrevocable, sub licensable license to use, reproduce, adapt, publish, translate and distribute it in any and all media.</p>
							<p>Your Content must be your own and must not be invading any third-party’s rights. localnews reserves the right to remove any of Your Content from this Website at any time without notice.</p>

							<h5>Your Privacy</h5>
							<p>We will not share or sell any of your personal information with any third party.</p>

							<h5>No warranties</h5>
							<p>This Website is provided "as is," with all faults, and localnews express no representations or warranties, of any kind related to this Website or the materials contained on this Website. Also, nothing contained on this Website shall be interpreted as advising you.</p>

							<h5>Limitation of liability</h5>
							<p>In no event shall localnews, nor any of its officers, directors and employees, shall be held liable for anything arising out of or in any way connected with your use of this Website whether such liability is under contract.  localnews, including its officers, directors and employees shall not be held liable for any indirect, consequential or special liability arising out of or in any way related to your use of this Website.</p>

							<h5>Indemnification</h5>
							<p>You hereby indemnify to the fullest extent localnews from and against any and/or all liabilities, costs, demands, causes of action, damages and expenses arising in any way related to your breach of any of the provisions of these Terms.</p>

							<h5>Severability</h5>
							<p>If any provision of these Terms is found to be invalid under any applicable law, such provisions shall be deleted without affecting the remaining provisions herein.</p>

							<h5>Variation of Terms</h5>
							<p>localnews is permitted to revise these Terms at any time as it sees fit, and by using this Website you are expected to review these Terms on a regular basis.</p>

							<h5>Assignment</h5>
							<p>The localnews is allowed to assign, transfer, and subcontract its rights and/or obligations under these Terms without any notification. However, you are not allowed to assign, transfer, or subcontract any of your rights and/or obligations under these Terms.</p>

							<h5>Entire Agreement</h5>
							<p>These Terms constitute the entire agreement between localnews and you in relation to your use of this Website, and supersede all prior agreements and understandings.</p>

							<h5>Governing Law & Jurisdiction</h5>
							<p>These Terms will be governed by and interpreted in accordance with the laws of NSW in Australia, and you submit to the non-exclusive jurisdiction of the state and federal courts located in Australia for the resolution of any disputes.</p>

							<a data-toggle="collapse" href="#tacCollapse">Hide these terms and conditions</a>
							</div></div>
		    		<button type="submit" class="btn btn-success mt-3" name="signup-submit">Sign Up</button>
	    		</form>
	    	</div>

				<!-- Separator -->
				<div class='col-lg-12'><hr></div>

				<!-- Forgot Password Body -->
				<div class="modal-body">
		    	<h5 class="pb-2">Forgotten Password</h5>
					<p>An e-mail will be sent to you with instructions on how to reset your password.</p>
					<form action="includes/reset.inc.php" method="post">
						<?php
						if(isset($_GET['fperror']))
						{
							$error = $_GET['fperror'];

							echo '<div class="bg-danger w-100 mb-3 p-2 text-light rounded">Error: ';

							switch ($error) {
								case 'ef':
									echo 'Enter an email address.';
									break;
								case 'emailinv':
									echo 'Invalid email.';
									break;
								case 'emailnf':
									echo 'This email isn\'t registered.';
									break;
								default:
									echo 'An error has occured.';
									break;
							}
							echo '</div>';
						}

						?>
						<input type="text" class="form-control mb-3" name="email" placeholder="Enter your email address...">
						<button type="submit" name="reset-submit" class="btn btn-success">Recieve new password by email</button>
					</form>
				</div>

			   	<!-- Modal footer -->
			 	<div class="modal-footer">
			 		<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			 	</div>
			</div>
		</div>
	</div>

	<!-- Password reset modal -->
	<div class="modal fade" id="pwdReset" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<!-- Modal Header -->
				<div class="modal-header">
		    	<h4 class="modal-title">Reset your password</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
		    </div>

				<!-- Modal body -->
				<div class="modal-body">
					<?php
						$selector = $_GET["selector"];
						$validator = $_GET["validator"];

						if(empty($selector) || empty($validator))
						{
							echo "Could not validate your request.";
						}
						else
						{
							if(ctype_xdigit($selector) !== false && ctype_xdigit($validator) !== false)
							{
									if(isset($_GET['rperror']))
									{
										$error = $_GET['rperror'];

										echo '<div class="bg-danger w-100 mb-3 p-2 text-light rounded">Error: ';
										switch ($error) {
											case 'pwdnm':
												echo 'Entered passwords do not match.';
												break;
											case 'efnewpwd':
												echo 'One or more empty fields.';
												break;
											case 'rsm':
												echo 'An unexpected error has occured; you will need to resubmit your request. If this doesn\'t work, please contact our support team at localnewssupport@gmail.com (note: fake email).';
												break;
											case 'sqlerrnp':
												echo ':)';
												break;
										}
										echo '</div>';
									}
								?>

									<form class="" action="includes/reset-pwd.inc.php" method="post">
										<input type="hidden" name="selector" value="<?php echo $selector; ?>">
										<input type="hidden" name="validator" value="<?php echo $validator; ?>">
										<input type="password" class="form-control mb-3" name="pwd" placeholder="Enter a new password">
										<input type="password" class="form-control mb-3" name="pwd_conf" placeholder="Confirm new password">
										<button type="submit" class="btn btn-success" name="reset-pwd-submit">Reset password</button>
									</form>

								<?php
							}
							else
							{
								echo '<div class="bg-danger w-100 mb-3 p-2 text-light rounded">Error: Invalid URL.</div>';
							}
						}
					?>
				</div>
			</div>
		</div>
	</div>

	<!-- Add genre modal -->
	<div class="modal fade" id="genreAdd" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<!-- Modal Header -->
				<div class="modal-header">
		    	<h4 class="modal-title">Add a genre</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
		    </div>

				<!-- Modal body -->
				<div class="modal-body">
					Name of Genre:
					<form class="add-genre-form" action="/news/includes/addgenre.inc.php" method="post">
						<input class="form-control mb-3" type="text" name="genreName">
						<button class="btn btn-primary" type="submit" name="genreSubmit">Add Genre</button>
					</form>
				</div>

				<div class="modal-footer">
			 		<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			 	</div>
			</div>
		</div>
	</div>

	<?php
		if(isset($_GET['lerror']) || isset($_GET['serror']))
		{
			echo '<script>$("#mainModal").modal("show");</script>';
		}
		else if(isset($_GET['fperror']))
		{
			echo '<script>';
			echo '	var modal_e = document.getElementById("mainModal");'; // get modal element
			echo '	modal_e.classList.toggle("fade");'; // remove animation
			echo '	$("#mainModal").modal("show");'; // show the modal
			echo '	modal_e.scrollTop = modal_e.scrollHeight;'; // scroll to the bottom
			echo '	modal_e.classList.toggle("fade");'; // add animation (reason we remove the animation is because it screws with scrolling)
			echo '</script>';
		}
		else if(isset($_GET['selector']) && isset($_GET['validator']))
		{
			echo '<script>$("#pwdReset").modal("show");</script>';
		}
	?>

	<div class="row content bg-secondary">
		<div class="ad-wrapper col-2 pr-0 text-center" id="left" style="">
			<img src="/news/imgs/ad.png" id="ad">
		</div>
		<div class="center col-7 pb-4" id="center"><div id="center-wrapper">
			<?php include 'includes/login_message.inc.php' ?>
