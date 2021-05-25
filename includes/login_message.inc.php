<?php
$greenBox = '<div class="bg-success w-100 rounded text-center mb-3 p-2 text-light">';
$yellowBox = '<div class="bg-warning w-100 rounded text-center mb-3 p-2 text-light">';

if(isset($_GET['login']))
{
  echo $greenBox.'You were successfully logged in!</div>';
}
else if(isset($_GET['logout']))
{
  echo $greenBox.'You were successfully logged out!</div>';
}
else if(isset($_GET['signup']))
{
  echo $greenBox.'You were successfully signed up! Log in to access your account.</div>';
}
else if(isset($_GET['reset']))
{
  echo $greenBox.'Check your email! If you didn\'t recieve an email, you will need to try again.</div>';
}
else if(isset($_GET['resetpwd']))
{
  echo $greenBox.'Password successfully reset! You are free to login with your new password now.</div>';
}
else if(isset($_GET['genre']))
{
  echo $greenBox.'Genre successfully added!</div>';
}
else if(isset($_GET['story']))
{
  echo $yellowBox.'Story is pending and awaiting approval.</div>';
}
?>
