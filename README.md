
![Image of Instant](http://www.createinstant.com/images/logo2.png)
!nstant allows newsroom reporters and editors to do what they do best: curate valuable content and relevant voices. On !nstant, timelines and temporary follow lists are easy to create and easy to find.


### Why we created !nstant?

During a breaking news situation, the volume of information on social media can be overwhelming for anyone. Hashtags may be ineffective and lists are not frequently updated or easily accessible. Valuable content is out there - it’s just **hard to find**. 

!nstant helps you identify experts and witnesses on the ground and share that information on a large scale. | 
------------ | 

**To understand and play with !nstant, [test ouf live demo](http://www.createinstant.com) now !** 

### !nstant for Publishers

!nstant allows newsroom reporters and editors to do what they do best: curate valuable content and relevant voices. On !nstant, timelines and temporary follow lists are easy to create and easy to find.

Anyone can be a publisher on !nstant: Users display their expertise around breaking news moments. Publishers choose the most reliable voices, add their own posts, create timelines, contextualize information and share their points of view. All of this in a platform designed to work easily and quickly, and focused on the place where most users are consuming the news nowadays: on mobile. 

### !nstant for Users

Find Twitter information that has been already selected and organised in a platform designed to isolate what’s important and look for valuable sources. !nstant offers quick and easy access to different curated lists around a story. Choose between specific !nstant timelines created by journalists, editors, experts and general users - without altering your regular followers. Unfollow the list after the news event - your Twitter follower list never changes.

To create an !nstant, sign in with your Twitter account at [createinstant.com](http://www.createinstant.com). To build your timeline, look for interesting accounts in the "Who to follow" column or through key words in the "Tweets" column. A third column serves as a final filter: making sure only relevant tweets will show up in your !nstant timeline. Your final !nstant timeline will be the sum of filtered tweets from recommended accounts and the messages specially selected by you.

As your !nstant is published, followed accounts will be notified that they have been selected as a recommended source for an event.

### Who's behind !nstant?

!nstant was created by experienced journalists and digital entrepreneurs living in France, Spain, Chile and United States. !nstant began when the team members were all fellows at the **Nieman Foundation for Journalism at Harvard**.
* Ludovic Blecher is director of the Digital Innovation Press Fund Google&AIPG - [@lblecher](http://twitter.com/lblecher)
* Alexandra Garcia, Senior Video Journalist, The New York Times. Ten years as visual and interactive journalist - [@garcia_alexndra](http://twitter.com/garcia_alexndra)
* Paula Molina, journalist, BBC correspondent, 10 years anchor and editor of a daily news Radio program at Radio Cooperativa, Chile - [@paulamolinat](http://twitter.com/paulamolinat)
* Borja Echevarría, former Deputy Managing Editor at EL PAÍS and soon Vice President, Digital, for Univision News - [@borjaechevarria](http://twitter.com/borjaechevarria)

Contact: [createinstant@gmail.com](createinstant@gmail.com)

### Browse public !nstants 

Here are the last public !nstants created on the platform : **[browse the latest public !nstants](http://createinstant.com/public_instants)**


# Install notes and guide

This should allow you to create a fresh install of !nstant on your server. !nstant runs on Symfony 2.1, the 140Dev library for accessing the Twitter Streaming API, and uses a standard up-to-date MySQL database.

### Symfony

* Execute : `cp app/config/parameters.yml.dist app/config/parameters.yml`

* Execute : `cp app/config/config.yml.dist app/config/config.yml`

* Edit app/config/parameters.yml as well as app/config/config.yml

* Execute : `php composer.phar install`

* Edit vendor/friendsofsymfony/twitter-bundle/FOS/TwitterBundle/Security/Authentication/Provider/TwitterProvider.php (line 77) => delete second `param (null) -> "throw new AuthenticationException($failed->getMessage(), $failed->getCode(), $failed)"`

* Edit vendor/friendsofsymfony/twitter-bundle/FOS/TwitterBundle/Security/Authentication/Provider/TwitterProvider.php (line 108) => add the following lines : 
` $user->setLoginCount($user->getLoginCount()+1);
$this->userProvider->updateUser($user);`

* Edit vendor/friendsofsymfony/twitter-bundle/FOS/TwitterBundle/Security/Authentication/Provider/TwitterProvider.php (lines 91 and 95) => replace $accessToken['screen_name'] by $accessToken['user_id'] :
```
91:            return new TwitterUserToken($accessToken['user_id'], null, array('ROLE_TWITTER_USER'));
92:        }
93:
94:        try {
95:            $user = $this->userProvider->loadUserByUsername($accessToken['user_id']);
```
* Execute : `app/console doctrine:schema:create`

(optional)
- [ ] Execute the root command : `chmod -R 777 app/cache/ app/logs/`
(you might have to run `rm -rf app/cache/*` )

### 140dev (Twitter Streaming API Framework)

* Execute : `cp 140dev/db/140dev_config.php.dist 140dev/db/140dev_config.php`

* Edit file `140dev/db/140dev_config.php` with your Twitter app info as well as the logs email address

* Execute : `cp 140dev/db/db_config.php.dist 140dev/db/db_config.php`

* Edit file `140dev/db/db_config.php` and specify your DB parameters

* Create a table called **json_cache** (you'll find the code in the file `140dev/db/mysql_database_schema.sql`)

* Create a recurring job to run the PHP script `140dev/db/garbage_tweet.php` to clean the Tweet table of "orphan" tweets (for instance it will remove duplicate RTs). The recurrence could be hourly.

### MySQL

* Create the process and the trigger to receive, sort and feed the !nstants with the tweets arriving through the Twitter Streaming API. The code to launch on a regular basis is in `trigger.sql`

### Authors and Contributors

!nstant was developed by [CosaVostra](http://www.cosavostra.com) and [createInstant.com](http://www.createinstant.com) is hosted on [Pilot Systems](http://www.pilotsystems.net/).
