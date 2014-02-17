#!/bin/bash

found=false
for i in `ps ux|grep loop_get_tweets|grep -v 'grep'| awk '{print $12}'`;
do
  found=true
done

if $found
then
    echo "running"
else
    echo "launch"
    /homez.151/cosavost/www/dev/instant/140dev/db/loop_get_tweets.sh
fi
