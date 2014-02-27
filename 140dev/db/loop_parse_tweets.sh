#!/bin/bash
cd /home/create/instant/140dev/db
while true; do
  php /home/create/instant/140dev/db/parse_tweets.php > /home/create/instant/140dev/db/parse_tweets.log 2>&1
done
