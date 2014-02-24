---------------------------------------------------------------------------------
-- Mécanisme d'insertion automatique dans les timelines à réception des tweets --
---------------------------------------------------------------------------------

DELIMITER $$
 
CREATE PROCEDURE affectTweetStoredProc (IN p_twittosID VARCHAR(255), IN p_tweetid INT, IN p_text varchar(255))
BEGIN
 
    DECLARE v_finished INTEGER DEFAULT 0;
    DECLARE v_instantid INT DEFAULT 0;

    DEClARE instantid_cursor CURSOR FOR
        select t.instant_id from Twittos t inner join fos_user fu on fu.id = t.user_id where fu.twitterID=p_twittosid and (exists(select * from Keyword k where k.instant_id = t.instant_id and LOWER(p_text) like concat('%', LOWER(k.keyword), '%')) or not exists(select * from Keyword k where k.instant_id = t.instant_id));
 
    -- declare NOT FOUND handler
    DECLARE CONTINUE HANDLER
        FOR NOT FOUND SET v_finished = 1;

    OPEN instantid_cursor;
 
    get_instantid: LOOP
 
        FETCH instantid_cursor INTO v_instantid;
 
        IF v_finished = 1 THEN
            LEAVE get_instantid;
        END IF;
 
        insert into instant_tweet (instant_id, tweet_id) values (v_instantid, p_tweetid);

    END LOOP get_instantid;
 
    CLOSE instantid_cursor;
 
END$$
 
CREATE TRIGGER tweet_trigger AFTER INSERT ON Tweet
FOR EACH ROW
BEGIN
  IF NEW.from_stream = 1 THEN
    call affectTweetStoredProc(IFNULL(NEW.rt_by_twitterID, NEW.user_id), NEW.id, NEW.text);
  END IF;
END$$

DELIMITER ;

---------------------------------------------------------------------------------
---------------------------------------------------------------------------------

