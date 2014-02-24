<?php
require_once('db_config.php');
$dbh = mysqli_connect($db_host, $db_user, $db_password, $db_name);
$query=" DELETE t.* FROM Tweet t LEFT JOIN instant_tweet it ON it.tweet_id = t.id WHERE it.tweet_id IS NULL;";
mysqli_query($dbh,$query);
if ($error_msg = mysqli_error($dbh)) {
  echo $error_msg;
}else{
  echo 'OK';
}
