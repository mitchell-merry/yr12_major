<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include 'func.inc.php';

if(isset($_POST['reset-submit']))
{
  $userEmail = $_POST['email'];

  if(empty($userEmail))
  {
    throw_error('fp', 'ef', "");
    exit();
  }
  else if(!filter_var($userEmail, FILTER_VALIDATE_EMAIL) || $userEmail == 'localnewsresetpwd@gmail.com') // Not a valid email, or is equal to our support email, which is a no-go
	{
    throw_error('fp', 'emailinv', "");
    exit();
  }

  require 'dbh.inc.php';

  $sql = "SELECT users_email FROM users WHERE users_email=?;";
  $stmt = mysqli_stmt_init($conn);

  if(!mysqli_stmt_prepare($stmt, $sql))
  {
    echo 'stmt_prepare';
    exit();
  }

  mysqli_stmt_bind_param($stmt, "s", $userEmail);
  mysqli_stmt_execute($stmt);

  $result = mysqli_stmt_get_result($stmt);
  if(!$row = mysqli_fetch_assoc($result))
  {
    throw_error("fp", "emailnf", $urlAdditional);
    exit();
  }


  $selector = bin2hex(random_bytes(8));
  $token = random_bytes(32);

  $url = "http://localhost/news/index.php?selector=".$selector."&validator=".bin2hex($token);

  $expires = date("U") + 600; // Expires after 600 seconds (10 minutes)

  $sql = "DELETE FROM pwd_reset WHERE pwdr_email=?;";
  $stmt = mysqli_stmt_init($conn);

  if(!mysqli_stmt_prepare($stmt, $sql))
  {
    echo 'stmt_prepare';
    exit();
  }
  else
  {
    mysqli_stmt_bind_param($stmt, "s", $userEmail);
    mysqli_stmt_execute($stmt);
  }

  $sql = "INSERT INTO pwd_reset (pwdr_email, pwdr_selector, pwdr_token, pwdr_expires) VALUES (?, ?, ?, ?);";
  $stmt = mysqli_stmt_init($conn);

  if(!mysqli_stmt_prepare($stmt, $sql))
  {
    echo 'stmt_prepare_2';
    exit();
  }
  else
  {
    $hash_token = password_hash($token, PASSWORD_DEFAULT);
    mysqli_stmt_bind_param($stmt, "ssss", $userEmail, $selector, $hash_token, $expires);
    mysqli_stmt_execute($stmt);
  }

  mysqli_stmt_close($stmt);
  mysqli_close($conn);

  require '../PHPMailer/src/Exception.php';
  require '../PHPMailer/src/PHPMailer.php';
  require '../PHPMailer/src/SMTP.php';

  $mail = new PHPMailer(true);
  try
  {
    $mail->isSMTP();
    $mail->SMTPDebug = 3;
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;
    $mail->Username = 'localnewsresetpwd@gmail.com';
    $mail->Password = 'KjQt9A6kzBzgTW9n';

    $mail->setFrom('no-reply@localnews.com');
    $mail->addAddress($userEmail);

    $mail->isHTML(true);
    $mail->Subject = 'Password Reset Email for localnews';
    $mail->Body = '<p>We recieved a password reset request for this email. The link to reset your password is below. If you did not make this request, you can simply ignore this email.</p><p>Here is your password reset link: <br><a href="'.$url.'">'.$url.'</a></p>';

    $mail->Send();

    header("Location: ../index.php?reset=success");
  }
  catch (Exception $e)
  {
    echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
  }
}
else
{
  header("Location: ../index.php");
  exit();
}
