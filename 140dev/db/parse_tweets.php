<?php

set_time_limit(0);
/**
* parse_tweets.php
* Populate the database with new tweet data from the json_cache table
* Latest copy of this code: http://140dev.com/free-twitter-api-source-code-library/
* @author Adam Green <140dev@gmail.com>
* @license GNU Public License
* @version BETA 0.30
*/
require_once('140dev_config.php');
require_once('db_lib.php');
$oDB = new db;

// This should run continuously as a background process
while (true) {

  // Process all new tweets
  $query = 'SELECT cache_id, raw_tweet FROM json_cache LIMIT 100';
  $result = $oDB->select($query);
  while($row = mysqli_fetch_assoc($result)) {
		
    $cache_id = $row['cache_id'];
    // Each JSON payload for a tweet from the API was stored in the database  
    // by serializing it as text and saving it as base64 raw data
    $tweet_object = unserialize(base64_decode($row['raw_tweet']));
    // Delete cached copy of tweet
    $oDB->select("DELETE FROM json_cache WHERE cache_id = $cache_id");
	
    // CosaVostra -- A eventuellement decommenter, ca peut peut-etre ameliorer les perf
    // We check that the tweet's author is part of our users
    // (the streaming API sends us RT's that we don't need)
    $query2 = "SELECT 1 FROM fos_user where username_canonical = '".$tweet_object->user->id_str."' LIMIT 1";
    $result2 = $oDB->select($query2);
    if (!mysqli_fetch_assoc($result2)) {continue;}

		// Limit tweets tgo a single language,
		// such as 'en' for English
//		if ($tweet_object->lang <> 'en') {continue;}
		
		// The streaming API sometimes sends duplicates, 
    // Test the tweet_id before inserting
    $tweet_id = $tweet_object->id_str;
//    if ($oDB->in_table('Tweet','twitter_id=' . $tweet_id )) {continue;}
		
    // Gather tweet data from the JSON object
    // $oDB->escape() escapes ' and " characters, and blocks characters that
    // could be used in a SQL injection attempt
   
    $media_url = '';
    if (isset($tweet_object->retweeted_status)) {
      $entities = $tweet_object->retweeted_status->entities;
      $tweet_id_ori = $tweet_object->retweeted_status->id_str;
      $rt_by_twitter_realname = $oDB->escape($tweet_object->user->name);
      $description = $oDB->escape($tweet_object->user->description);
      $user_location = $oDB->escape($tweet_object->user->location);
      $rt_by_twitterID = $tweet_object->user->id_str;
      $tweet_text = $oDB->escape($tweet_object->retweeted_status->text);
      $created_at = $oDB->date($tweet_object->created_at);
      if(isset($entities->media)){
        foreach ($entities->media as $media) {
          $media_url = $media->media_url;break;
        }
      }
      if (isset($tweet_object->retweeted_status->geo)) {
        $geo_lat = $tweet_object->retweeted_status->geo->coordinates[0];
        $geo_long = $tweet_object->retweeted_status->geo->coordinates[1];
      } else {
        $geo_lat = $geo_long = 0;
      }
      $user_object = $tweet_object->retweeted_status->user;
      $user_id = $user_object->id_str;
      $screen_name = $oDB->escape($user_object->screen_name);
      $name = $oDB->escape($user_object->name);
      $profile_image_url = $user_object->profile_image_url;
      $is_rt = 1;
    } else {
      $entities = $tweet_object->entities;
      if(isset($entities->media)){
        foreach ($entities->media as $media) {
          $media_url = $media->media_url;break;
        }
      }
      $is_rt = 0;
      $tweet_text = $oDB->escape($tweet_object->text);	
      $created_at = $oDB->date($tweet_object->created_at);
      if (isset($tweet_object->geo)) {
        $geo_lat = $tweet_object->geo->coordinates[0];
        $geo_long = $tweet_object->geo->coordinates[1];
      } else {
        $geo_lat = $geo_long = 0;
      } 
      $user_object = $tweet_object->user;
      $user_id = $user_object->id_str;
      $description = $oDB->escape($user_object->description);
      $user_location = $oDB->escape($user_object->location);
      $screen_name = $oDB->escape($user_object->screen_name);
      $name = $oDB->escape($user_object->name);
      $profile_image_url = $user_object->profile_image_url;
    }

		
    // Add a new user row or update an existing one
/*    $field_values = 'screen_name = "' . $screen_name . '", ' .
      'profile_image_url = "' . $profile_image_url . '", ' .
      'user_id = ' . $user_id . ', ' .
      'name = "' . $name . '", ' .
      'location = "' . $oDB->escape($user_object->location) . '", ' . 
      'url = "' . $user_object->url . '", ' .
      'description = "' . $oDB->escape($user_object->description) . '", ' .
      'created_at = "' . $oDB->date($user_object->created_at) . '", ' .
      'followers_count = ' . $user_object->followers_count . ', ' .
      'friends_count = ' . $user_object->friends_count . ', ' .
      'statuses_count = ' . $user_object->statuses_count . ', ' . 
      'time_zone = "' . $user_object->time_zone . '", ' .
      'last_update = "' . $oDB->date($tweet_object->created_at) . '"' ;			

    if ($oDB->in_table('users','user_id="' . $user_id . '"')) {
      $oDB->update('users',$field_values,'user_id = "' .$user_id . '"');
    } else {			
      $oDB->insert('users',$field_values);
    }*/
		
    // Add the new tweet
	
    $field_values = 'twitter_id = "' . $tweet_id . '", ' .
        'text = "' . $tweet_text . '", ' .
        'created_at = "' . $created_at . '", ' .
        'location = "' . $geo_lat . '|' . $geo_long . '",' .
        'user_id = "' . $user_id . '", ' .				
        'screen_name = "' . $screen_name . '", ' .
        'user_description = "' . $description . '", ' .
        'user_location = "' . $user_location . '", ' .
        'name = "' . $name . '", ' .
        'profile_image_url = "' . $profile_image_url . '", ' .
        'is_rt = ' . $is_rt . ',' .
        (($is_rt)?
          'twitter_id_ori="'.$tweet_id_ori.'", rt_by_twitter_realname="'.$rt_by_twitter_realname.'",rt_by_twitterID="'.$rt_by_twitterID.'",':'').
        'media_url = "'.$media_url.'", from_stream = 1';// . $twitter_object->media[0]->media_url ;
			echo "$field_values\n";
    echo $oDB->insert('Tweet',$field_values);
	//exit;	
    // The mentions, tags, and URLs from the entities object are also
    // parsed into separate tables so they can be data mined later
/*    foreach ($entities->user_mentions as $user_mention) {
		
      $where = 'tweet_id=' . $tweet_id . ' ' .
        'AND source_user_id=' . $user_id . ' ' .
        'AND target_user_id=' . $user_mention->id;		
					 
      if(! $oDB->in_table('tweet_mentions',$where)) {
			
        $field_values = 'tweet_id=' . $tweet_id . ', ' .
        'source_user_id=' . $user_id . ', ' .
        'target_user_id=' . $user_mention->id;	
				
        $oDB->insert('tweet_mentions',$field_values);
      }
    }
    foreach ($entities->hashtags as $hashtag) {
			
      $where = 'tweet_id=' . $tweet_id . ' ' .
        'AND tag="' . $hashtag->text . '"';		
					
      if(! $oDB->in_table('tweet_tags',$where)) {
			
        $field_values = 'tweet_id=' . $tweet_id . ', ' .
          'tag="' . $hashtag->text . '"';	
				
        $oDB->insert('tweet_tags',$field_values);
      }
    }
    foreach ($entities->urls as $url) {
		
      if (empty($url->expanded_url)) {
        $url = $url->url;
      } else {
        $url = $url->expanded_url;
      }
			
      $where = 'tweet_id=' . $tweet_id . ' ' .
        'AND url="' . $url . '"';		
					
      if(! $oDB->in_table('tweet_urls',$where)) {
        $field_values = 'tweet_id=' . $tweet_id . ', ' .
          'url="' . $url . '"';	
				
        $oDB->insert('tweet_urls',$field_values);
      }
    }*/	
  }
		
  // You can adjust the sleep interval to handle the tweet flow and 
  // server load you experience
  //sleep(1);
}

?>
