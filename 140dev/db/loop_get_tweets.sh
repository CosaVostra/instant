#!/bin/bash
cd /home/create/instant/140dev/db
while true; do
  php /home/create/instant/140dev/db/get_tweets.php > /dev/null 2>&1
done
