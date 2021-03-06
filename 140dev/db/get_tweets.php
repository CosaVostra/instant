<?php

set_time_limit(0);
/**
* get_tweets.php
* Collect tweets from the Twitter streaming API
* This must be run as a continuous background process
* Latest copy of this code: http://140dev.com/free-twitter-api-source-code-library/
* @author Adam Green <140dev@gmail.com>
* @license GNU Public License
* @version BETA 0.30
*/
require_once('140dev_config.php');

require_once('../libraries/phirehose/Phirehose.php');
require_once('../libraries/phirehose/OauthPhirehose.php');
class Consumer extends OauthPhirehose
{
  // A database connection is established at launch and kept open permanently
  public $oDB;

  public function db_connect() {
    require_once('db_lib.php');
    $this->oDB = new db;
  }
	
  // This function is called automatically by the Phirehose class
  // when a new tweet is received with the JSON data in $status
  public function enqueueStatus($status) {
    $tweet_object = json_decode($status);
		
		// Ignore tweets without a properly formed tweet id value
    if (!(isset($tweet_object->id_str))) { return;}
		
    $tweet_id = $tweet_object->id_str;

    // If there's a ", ', :, or ; in object elements, serialize() gets corrupted 
    // You should also use base64_encode() before saving this
    $raw_tweet = base64_encode(serialize($tweet_object));
		
    $field_values = 'raw_tweet = "' . $raw_tweet . '", ' .
      'tweet_id = ' . $tweet_id;
    $this->oDB->insert('json_cache',$field_values);
  }

  public function checkFilterPredicates() {
    $result = $this->oDB->select('
      SELECT DISTINCT u.twitterID
      FROM fos_user u,Twittos t,Instant i
      WHERE u.id=t.user_id
      AND t.instant_id=i.id
      '.
      //'AND i.status=\'publish\''.
      'AND i.finish_at>now()
      LIMIT 4999
    ');
    $twitterIDs = array('12510322');
    if($result){
      while ($row = $result->fetch_array()){
        $twitterIDs[] = $row['twitterID'];
      }
      $result->close();
    }
    $this->setFollow($twitterIDs);
  }
}

// Open a persistent connection to the Twitter streaming API
$stream = new Consumer(OAUTH_TOKEN, OAUTH_SECRET, Phirehose::METHOD_FILTER);

// Establish a MySQL database connection
$stream->db_connect();

$stream->consume();

?>
