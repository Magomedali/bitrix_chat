SELECT 
`social_chat_messages`.`ID` AS `ID`, 
`social_chat_messages`.`FROM_ID` AS `FROM_ID`, 
`social_chat_messages_from_member`.`ID` AS `FROM_MEMBER_ID`, 
`social_chat_messages_from_member`.`USER_ID` AS `FROM_MEMBER_USER_ID`, 
`social_chat_messages_from_member`.`AVA` AS `FROM_MEMBER_AVA`, 
`social_chat_messages_from_member`.`HIDE_PROFILE` AS `FROM_MEMBER_HIDE_PROFILE`, 
`social_chat_messages_from_member`.`AVA` AS `FROM_AVA`, 
`social_chat_messages_from_member_user`.`LAST_NAME` AS `FROM_MEMBER_USER_LAST_NAME`, 
`social_chat_messages_from_member_user`.`NAME` AS `FROM_MEMBER_USER_NAME`, 
`social_chat_messages`.`TO_ID` AS `TO_ID`, 
`social_chat_messages_to_member`.`ID` AS `TO_MEMBER_ID`, 
`social_chat_messages_to_member`.`USER_ID` AS `TO_MEMBER_USER_ID`, 
`social_chat_messages_to_member`.`AVA` AS `TO_MEMBER_AVA`, 
`social_chat_messages_to_member`.`HIDE_PROFILE` AS `TO_MEMBER_HIDE_PROFILE`, 
`social_chat_messages_to_member_luser`.`LAST_NAME` AS `TO_MEMBER_USER_LAST_NAME`, 
`social_chat_messages_to_member_luser`.`NAME` AS `TO_MEMBER_USER_NAME`, 
`social_chat_messages`.`TEXT` AS `TEXT`, 
`social_chat_messages`.`FILE_NAME` AS `FILE_NAME`, 
`social_chat_messages`.`TOPIC_ID` AS `TOPIC_ID`, 
`social_chat_messages`.`CREATED` AS `CREATED` 
FROM `ali_social_messages` `social_chat_messages` 
INNER JOIN `ali_social_members` `social_chat_messages_from_member` ON `social_chat_messages`.`FROM_ID` = `social_chat_messages_from_member`.`ID` 
INNER JOIN `b_user` `social_chat_messages_from_member_user` ON `social_chat_messages_from_member`.`USER_ID` = `social_chat_messages_from_member_user`.`ID` 
LEFT JOIN `ali_social_members` `social_chat_messages_to_member` ON `social_chat_messages`.`TO_ID` = `social_chat_messages_to_member`.`ID` 
LEFT JOIN `b_user` `social_chat_messages_to_member_luser` ON `social_chat_messages_to_member`.`USER_ID` = `social_chat_messages_to_member_luser`.`ID` 
