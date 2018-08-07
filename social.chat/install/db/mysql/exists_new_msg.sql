create function exists_new_msg (p_id int,p_msg int)
RETURNS int
DETERMINISTIC
BEGIN
 
 declare v_c int default 0;
 
 SELECT COUNT(1) INTO v_c FROM `ali_social_messages` WHERE `TOPIC_ID` = p_id AND `ID` > p_msg;

 return v_c;

END;