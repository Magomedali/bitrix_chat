create function get_last_msg (p_id int)
RETURNS int
DETERMINISTIC
BEGIN
 
 declare v_c int default 0;
 
 SELECT MAX(`ID`) INTO v_c FROM `ali_social_messages` WHERE `TOPIC_ID` = p_id;

 return v_c;

END ;