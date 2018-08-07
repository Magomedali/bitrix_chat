create procedure  `get_new_msg` (in p_id int, in p_msg int)
BEGIN
	
	declare v_n int default 0;

	SELECT exists_new_msg(p_id,p_msg) INTO v_n;

	if v_n > 0 then
		SELECT * FROM view_messages WHERE `TOPIC_ID` = p_id AND `ID` > p_msg;
	else
		select 0 as 'nothas';
	end if;

END ;