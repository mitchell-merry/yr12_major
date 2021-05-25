<?php

include 'func.inc.php';

if (isset($_POST['reset-pwd-submit']))
{
  $selector = $_POST['selector'];
  $validator = $_POST['validator'];
  $pwd = $_POST['pwd'];
  $pwd_conf = $_POST['pwd_conf'];

  $urlAdditional = "&selector=".$selector."&validator=".$validator;

  if(empty($pwd) || empty($pwd_conf))
  {
    throw_error("rp", "efnewpwd", $urlAdditional);
    exit();
  }
  else if($pwd !== $pwd_conf)
  {
    throw_error("rp", "pwdnm", $urlAdditional);
    exit();
  }

  $currentDate = date("U");

  require 'dbh.inc.php';

  $sql = "SELECT * FROM pwd_reset WHERE pwdr_selector=? AND pwdr_expires >= ?";
  $stmt = mysqli_stmt_init($conn);

  if(!mysqli_stmt_prepare($stmt, $sql))
  {
    echo 'stmt_prepare1';
    exit();
  }
  else
  {
    mysqli_stmt_bind_param($stmt, "ss", $selector, $currentDate);
    mysqli_stmt_execute($stmt);
  }

  $result = mysqli_stmt_get_result($stmt);
  if(!$row = mysqli_fetch_assoc($result))
  {
    throw_error("rp", "rsm", $urlAdditional);
    exit();
  }

  $tokenBin = hex2bin($validator);
  $tokenCheck = password_verify($tokenBin, $row['token']);

  if($tokenCheck === false)
  {
    throw_error("rp", "rsm", $urlAdditional);
  }
  else if($tokenCheck === true)
  {
    $tokenEmail = $row['email'];
    $sql = "SELECT * FROM users WHERE users_email=?;";
    $stmt = mysqli_stmt_init($conn);

    if(!mysqli_stmt_prepare($stmt, $sql))
    {
      echo 'stmt_prepare2';
      exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $tokenEmail);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    if(!$row = mysqli_fetch_assoc($result))
    {
      throw_error("rp", "rsm", $urlAdditional);
      exit();
    }

    $sql = "UPDATE users SET users_pwd=?, users_salt=? WHERE users_email=?";
    $stmt = mysqli_stmt_init($conn);

    if(!mysqli_stmt_prepare($stmt, $sql))
    {
      echo 'stmt_prepare3';
      exit();
    }

    $salt = bin2hex(random_bytes(32));
    mysqli_stmt_bind_param($stmt, "sss", password_hash($pwd.$salt, PASSWORD_DEFAULT), $salt, $tokenEmail);
    mysqli_stmt_execute($stmt); // This is where it actually changes the password

    $sql = "DELETE FROM pwd_reset WHERE pwdr_email=?;";
    $stmt = mysqli_stmt_init($conn);

    if(!mysqli_stmt_prepare($stmt, $sql))
    {
      echo 'stmt_prepare4';
      exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $tokenEmail);
    mysqli_stmt_execute($stmt);
    header("Location: ../index.php?resetpwd=success");
  }
}
else
{
  header("Location: ../index.php");
  exit();
}
