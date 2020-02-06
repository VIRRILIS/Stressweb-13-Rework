<?php
/**
 * STRESS WEB
 * @author S.T.R.E.S.S.
 * @copyright 2008 - 2012 STRESS WEB
 * @version 13
 * @web http://stressweb.ru
*/
if (!defined("STRESSWEB")) die ("Access denied...");

$qList["L2Dream"] = array (

	"fields" => array(
		"accessLevel"=>"access_level",
		"charID"=>"obj_Id",
		),
	
	"itemType" => array (
		0	=> "dress",
		1	=> "helmet",
		2	=> "lefthair",
		3	=> "righthair",
		4	=> "necklace",
		5	=> "weapon",
		6	=> "top",
		7	=> "shield",
		8	=> "leftearring",
		9	=> "rightearring",
		10	=> "gloves",
		11	=> "lower",
		12	=> "bots",
		14	=> "leftring",
		15	=> "rightring",
		16	=> "ring",
//		18	=> "lefthair",
//		20	=> "righthair",
//		22	=> "braslet",
		),

	"insAccount" => "
		INSERT INTO `accounts` (`login`,`password`,`access_level`,`l2email`) 
		VALUES ('{login}','{pass}','0','{l2email}')",
	
	"insItem" => "
			INSERT INTO `items` (`owner_id`,`object_id`,`item_id`,`count`,`enchant_level`,`loc`,`loc_data`) 
			VALUES ('{ownerID}', '{objectID}', '{itemID}', '{count}', '{enchant}', 'INVENTORY', '0')",
	
	"setPassword" => "
		UPDATE `accounts` 
		SET `password`='{pass}' 
		WHERE `login`='{login}'",
	
	"setAccessLevelAccount" => "
		UPDATE `accounts` 
		SET `access_level`='{level}' 
		WHERE `login`='{login}'",
		
	"setAccessLevelCharacter" => "
		UPDATE `characters` 
		SET `accesslevel`='{level}' 
		WHERE `obj_Id`='{charID}'",
		
	"setTeleport" => "
		UPDATE `characters` 
		SET `x`='{x}',`y`='{y}',`z`='{z}',`lastteleport`='{lastteleport}'
		WHERE `obj_Id`='{charID}'",
	
	"setItem" => "
		UPDATE `items` 
		SET `count`='{count}', `enchant_level`='{enchant}' 
		WHERE `object_id`='{objectID}'",
	
	"setItemCount" => "
		UPDATE `items`
		SET `count` = '{count}'
		WHERE `owner_id` = '{ownerID}' AND `object_id` = '{objectID}'",
		
	"getCountAccounts" => "
		SELECT count(0) 
		FROM accounts {where}",
	
	"getCountCharacters" => "
		SELECT count(0) 
		FROM characters {where}",
	
	"getCountClans" => "
		SELECT count(0) 
		FROM clan_data",
	
	"getCountHuman" => "
		SELECT count(0) 
		FROM characters
		LEFT JOIN character_subclasses ON character_subclasses.char_obj_id = characters.obj_Id AND character_subclasses.isBase='1'
		LEFT JOIN char_templates ON character_subclasses.class_id = char_templates.ClassId 
		WHERE char_templates.RaceId='0' AND characters.accesslevel='0'",
	
	"getCountElf" => "
		SELECT count(0) 
		FROM characters
		LEFT JOIN character_subclasses ON character_subclasses.char_obj_id = characters.obj_Id AND character_subclasses.isBase='1'
		LEFT JOIN char_templates ON character_subclasses.class_id = char_templates.ClassId 
		WHERE char_templates.RaceId='1' AND characters.accesslevel='0'",
	
	"getCountDElf" => "
		SELECT count(0) 
		FROM characters 
		LEFT JOIN character_subclasses ON character_subclasses.char_obj_id = characters.obj_Id AND character_subclasses.isBase='1'
		LEFT JOIN char_templates ON character_subclasses.class_id = char_templates.ClassId 
		WHERE char_templates.RaceId='2' AND characters.accesslevel='0'",
	
	"getCountOrc" => "
		SELECT count(0) 
		FROM characters 
		LEFT JOIN character_subclasses ON character_subclasses.char_obj_id = characters.obj_Id AND character_subclasses.isBase='1'
		LEFT JOIN char_templates ON character_subclasses.class_id = char_templates.ClassId 
		WHERE char_templates.RaceId='3' AND characters.accesslevel='0'",
	
	"getCountDwarf" => "
		SELECT count(0) 
		FROM characters 
		LEFT JOIN character_subclasses ON character_subclasses.char_obj_id = characters.obj_Id AND character_subclasses.isBase='1'
		LEFT JOIN char_templates ON character_subclasses.class_id = char_templates.ClassId 
		WHERE char_templates.RaceId='4' AND characters.accesslevel='0'",
	
	"getCountKamael" => "
		SELECT count(0) 
		FROM characters 
		LEFT JOIN character_subclasses ON character_subclasses.char_obj_id = characters.obj_Id AND character_subclasses.isBase='1'
		LEFT JOIN char_templates ON character_subclasses.class_id = char_templates.ClassId 
		WHERE char_templates.RaceId='5' AND characters.accesslevel='0'",
	
	"getCountDawn" => "
		SELECT count(0) 
		FROM seven_signs 
		WHERE cabal='dawn'",
	
	"getCountDusk" => "
		SELECT count(0) 
		FROM seven_signs 
		WHERE cabal='dusk'",
	
	"getAccount" => "
		SELECT login,password,lastactive,access_level AS accessLevel,lastIP 
		FROM `accounts` 
		WHERE `login`='{login}' {where} 
		LIMIT 1",
		
	"getAccounts" => "
		SELECT login,lastactive,access_level,lastIP 
		FROM `accounts` {where}
		ORDER BY {order} 
		LIMIT {limit}",
		
	"getCharactersList" => "
		SELECT characters.account_name, characters.obj_Id, characters.char_name, character_subclasses.level, characters.accesslevel, characters.lastAccess, char_templates.ClassName 
		FROM `characters` 
		LEFT JOIN `character_subclasses` ON characters.obj_Id = character_subclasses.char_obj_id
		LEFT JOIN `char_templates` ON character_subclasses.class_id = char_templates.ClassId {where}
		ORDER BY characters.char_name 
		LIMIT {limit}",
	
	"getCharacter" => "
		SELECT characters.account_name, characters.char_name, character_subclasses.level, characters.sex, character_subclasses.class_id, characters.online, character_subclasses.exp, character_subclasses.sp, characters.karma, characters.pvpkills, characters.pkkills, characters.accesslevel, characters.onlinetime, characters.lastAccess, char_templates.ClassName, clan_data.clan_name 
		FROM `characters`
		LEFT JOIN `character_subclasses` ON characters.obj_Id = character_subclasses.char_obj_id AND character_subclasses.isBase='1' 
		LEFT JOIN `char_templates` ON character_subclasses.class_id = char_templates.ClassId 
		LEFT JOIN `clan_data` ON characters.clanid = clan_data.clan_id 
		WHERE characters.obj_Id='{charID}'",
		
	"getCharacterInfo" => "
		SELECT characters.account_name, characters.char_name, character_subclasses.level, character_subclasses.maxHp, character_subclasses.maxCp, character_subclasses.maxMp, characters.sex, character_subclasses.exp, character_subclasses.sp, characters.pvpkills, characters.pkkills, characters.karma, char_templates.RaceId AS race, character_subclasses.class_id AS base_class, characters.accesslevel, characters.lastAccess, char_templates.ClassName, char_templates.STR, char_templates.CON, char_templates.DEX, char_templates._INT, char_templates.WIT, char_templates.MEN 
		FROM `characters`
		LEFT JOIN `character_subclasses` ON characters.obj_Id = character_subclasses.char_obj_id
		LEFT JOIN `char_templates` ON character_subclasses.class_id = char_templates.ClassId 
		WHERE characters.obj_Id='{charID}'",
		
	"getAccountCharacters" => "
		SELECT characters.account_name, characters.obj_Id AS charID, characters.char_name, character_subclasses.level, characters.accesslevel, characters.lastAccess, characters.online, characters.onlinetime, char_templates.ClassName, clan_data.clan_name 
		FROM `characters` 
		LEFT JOIN `character_subclasses` ON characters.obj_Id = character_subclasses.char_obj_id
		LEFT JOIN `char_templates` ON character_subclasses.class_id = char_templates.ClassId
		LEFT JOIN `clan_data` ON characters.clanid = clan_data.clan_id
		WHERE characters.account_name='{account}' 
		ORDER BY characters.char_name",
	
	"getTopClan"=>"
		SELECT clan_data.clan_name, clan_data.clan_id, ally_data.ally_name, clan_data.clan_level, clan_data.reputation_score, clan_data.hasCastle, characters.char_name, ccount 
		FROM `clan_data` 
		LEFT JOIN `characters` ON characters.obj_Id = clan_data.leader_id 
		LEFT JOIN (
			SELECT clanid, count(0) AS ccount 
			FROM characters 
			WHERE clanid GROUP BY clanid
			) AS levels ON clan_data.clan_id = levels.clanid
		LEFT JOIN `ally_data` ON clan_data.ally_id = ally_data.ally_id 
		ORDER BY clan_data.clan_level DESC, clan_data.reputation_score DESC 
		LIMIT {limit}",
	
	"getTop" => "
		SELECT characters.char_name, character_subclasses.level, characters.sex, characters.pvpkills, characters.pkkills, characters.online, characters.onlinetime, char_templates.ClassName, clan_data.clan_name, clan_data.clan_id 
		FROM `characters`
		LEFT JOIN `character_subclasses` ON characters.obj_Id = character_subclasses.char_obj_id AND character_subclasses.isBase='1'
		LEFT JOIN `char_templates` ON character_subclasses.class_id = char_templates.ClassId
		LEFT JOIN `clan_data` ON characters.clanid = clan_data.clan_id 
		WHERE characters.accesslevel='0'
		ORDER BY {order} DESC 
		LIMIT {limit}",
		
	"getRich" => "
		SELECT characters.char_name, character_subclasses.level, characters.sex, characters.online, characters.onlinetime, char_templates.ClassName, clan_data.clan_name, clan_data.clan_id, count 
		FROM `characters` 
		LEFT JOIN `character_subclasses` ON characters.obj_Id = character_subclasses.char_obj_id AND character_subclasses.isBase='1'
		LEFT JOIN `char_templates` ON character_subclasses.class_id = char_templates.ClassId 
		LEFT JOIN `clan_data` ON characters.clanid = clan_data.clan_id
		LEFT JOIN (SELECT owner_id,SUM(count) AS count FROM items WHERE items.item_id={item_id} GROUP BY owner_id) AS count ON characters.obj_Id=count.owner_id 
		WHERE characters.accesslevel='0'
		ORDER BY count DESC, level DESC, onlinetime DESC 
		LIMIT {limit}",
	
	"getClanCharacters" => "
		SELECT characters.char_name, character_subclasses.level, characters.sex, characters.pvpkills, characters.pkkills, characters.online, characters.onlinetime, char_templates.ClassName, clan_data.clan_name, clan_data.clan_id 
		FROM `characters` 
		LEFT JOIN `character_subclasses` ON characters.obj_Id = character_subclasses.char_obj_id AND character_subclasses.isBase='1'
		LEFT JOIN `char_templates` ON character_subclasses.class_id = char_templates.ClassId 
		LEFT JOIN `clan_data` ON characters.clanid = clan_data.clan_id 
		WHERE characters.clanid='{clanid}'
		ORDER BY character_subclasses.level DESC",
	
	"getOnline" => "
		SELECT characters.char_name, character_subclasses.level, characters.sex, characters.pvpkills, characters.pkkills, characters.online, characters.onlinetime, char_templates.ClassName, clan_data.clan_name, clan_data.clan_id 
		FROM `characters` 
		LEFT JOIN `character_subclasses` ON characters.obj_Id = character_subclasses.char_obj_id AND character_subclasses.isBase='1'
		LEFT JOIN `char_templates` ON character_subclasses.class_id = char_templates.ClassId 
		LEFT JOIN `clan_data` ON characters.clanid = clan_data.clan_id 
		WHERE characters.accesslevel='0' AND characters.online='1'
		ORDER BY character_subclasses.level DESC, characters.onlinetime DESC",
	
	"getEpicStatus" => "
		SELECT epic_boss_spawn.respawnDate AS respawn_time, npc.name, npc.level 
		FROM epic_boss_spawn 
		LEFT JOIN npc ON epic_boss_spawn.bossId = npc.id 
		ORDER BY npc.level DESC",
	
	"getRaidStatus" => "
		SELECT raidboss_status.respawn_delay AS respawn_time, npc.level, npc.name
		FROM raidboss_status
		LEFT JOIN npc ON raidboss_status.id = npc.id
		ORDER BY npc.level DESC, npc.name ASC",
		
	"getClan" => "
		SELECT clan_name
		FROM clan_data
		WHERE clan_id='{clanid}'",
		
	"getCastles" => "
		SELECT castle.name, castle.id, castle.taxPercent, castle.siegeDate, clan_data.clan_name, clan_data.clan_id
		FROM castle
		LEFT JOIN clan_data ON clan_data.hasCastle = castle.id",
	
	"getSiege" => "
		SELECT siege_clans.unit_id as castle_id, siege_clans.clan_id, siege_clans.type, clan_data.clan_name
		FROM siege_clans
		LEFT JOIN clan_data ON clan_data.clan_id = siege_clans.clan_id
		WHERE unit_id='{castle}'",
	
	"getOlympiad" => "
		SELECT olympiad_nobles.char_name, olympiad_nobles.olympiad_points, olympiad_nobles.competitions_done, char_templates.ClassName, characters.sex 
		FROM olympiad_nobles 
		LEFT JOIN char_templates ON olympiad_nobles.class_id = char_templates.ClassId 
		LEFT JOIN characters ON olympiad_nobles.char_id = characters.obj_Id
		ORDER BY olympiad_nobles.class_id, olympiad_nobles.olympiad_points DESC",
	
	"getInventory" => "
		SELECT items.object_id,items.item_id,items.count,items.enchant_level,items.loc, 
			CASE WHEN armor.name != '' THEN armor.name 
			WHEN weapon.name != '' THEN weapon.name 
			WHEN etcitem.name != '' THEN etcitem.name 
			END AS name, 
			CASE WHEN armor.crystal_type != '' THEN 'armor' 
			WHEN weapon.crystal_type != '' THEN 'weapon' 
			WHEN etcitem.crystal_type != '' THEN 'etc' 
			END AS `type` 
		FROM `items` 
		LEFT JOIN `armor` ON armor.item_id = items.item_id 
		LEFT JOIN weapon ON weapon.item_id = items .item_id 
		LEFT JOIN etcitem ON etcitem.item_id = items.item_id 
		WHERE items.owner_id='{charID}' 
		ORDER BY {order}",
		
	"getCharInventory" => "
		SELECT items.object_id,items.item_id,items.count,items.enchant_level,items.loc,items.loc_data,armorName,weaponName,etcName,armorType,weaponType,etcType
		FROM `items` 
		LEFT JOIN (
			SELECT item_id, name AS armorName, crystal_type AS armorType 
			FROM `armor`
			) AS aa ON aa.item_id = items.item_id 
		LEFT JOIN (
			SELECT item_id, name AS weaponName, crystal_type AS weaponType 
			FROM `weapon`
			) AS ww ON ww.item_id = items.item_id
		LEFT JOIN (
			SELECT item_id, name AS etcName, crystal_type AS etcType 
			FROM `etcitem`
			) AS ee ON ee.item_id = items.item_id
		WHERE items.owner_id='{charID}' AND items.loc='{loc}' 
		ORDER BY items.loc_data",
	
	"getItemByObjectID" => "
		SELECT `count`, `enchant_level`, `item_id` 
		FROM `items` 
		WHERE `object_id`='{objectID}'",
	
	"getLastTeleport" => "
		SELECT `char_name`,`online`,`accesslevel`,`lastteleport` 
		FROM `characters` 
		WHERE `obj_Id`='{charID}'",
	
	"getItem" => "
		SELECT `object_id`, `count`
		FROM `items`
		WHERE `owner_id` = '{charID}' AND `item_id` = '{itemID}' AND `loc` = 'INVENTORY'
		LIMIT 1",
	
	"getMax" => "
			SELECT MAX(`object_id`)+1 AS `max` 
			FROM `items`",
	
	"delAccounts" => "
		DELETE FROM accounts 
		WHERE login='{login}'",
	
	"delItemByID" => "
		DELETE FROM `items` 
		WHERE `item_id`='{item}'",
	
	"delCharByID" => "
		DELETE FROM `characters` 
		WHERE `obj_Id`='{charID}'",
		
	"delItemByOwner" => "
		DELETE FROM `items` 
		WHERE `owner_id`='{charID}'",
		
	"delItemByObjectID" => "
		DELETE FROM `items` 
		WHERE `object_id`='{objectID}'",
	
	"delItemByIDOwner" => "
		DELETE FROM `items` 
		WHERE `item_id`='{item}' AND `owner_id`='{charID}'",
	
	"other" => array(
		"DELETE FROM character_friends	WHERE char_id='{charID}' OR friend_id='{charID}'",
		"DELETE FROM character_hennas WHERE char_obj_id='{charID}'",
		"DELETE FROM character_macroses WHERE char_obj_id='{charID}'",
		"DELETE FROM character_quests WHERE char_id='{charID}'",
		"DELETE FROM character_recipebook WHERE char_id='{charID}'",
		"DELETE FROM character_shortcuts WHERE char_obj_id='{charID}'",
		"DELETE FROM character_skills WHERE char_obj_id='{charID}'",
		"DELETE FROM character_skills_save WHERE char_obj_id='{charID}'",
		"DELETE FROM character_subclasses WHERE char_obj_id='{charID}'",	
		"DELETE FROM seven_signs WHERE char_obj_id='{charID}'",
		"DELETE FROM items WHERE owner_id='{charID}'",
		"DELETE FROM clan_data WHERE leader_id='{charID}'",
		),

	"l2top" => array(
		
		"getChar" => "
			SELECT account_name, obj_Id AS charID, online
			FROM `characters`
			WHERE `char_name`='{name}'",
		
		"getItem" => "
			SELECT `item_id`,`count` 
			FROM `items` 
			WHERE `owner_id`='{ownerID}' AND `item_id`='{itemID}' AND `loc`='INVENTORY'",
		
		"getMax" => "
			SELECT MAX(`object_id`)+1 AS `max` 
			FROM `items`",
		
		"insItem" => "
			INSERT INTO `items` (`owner_id`,`object_id`,`item_id`,`count`,`enchant_level`,`loc`,`loc_data`) 
			VALUES ('{charID}', '{objectID}', '{itemID}', '{count}', '0', 'INVENTORY', '0')",
		
		"insl2top" => "
			INSERT INTO `l2top` (`nick`,`ip`,`time`) 
			VALUES ('{nick}','{ip}','{time}')",
		
		"setItem" => "
			UPDATE `items` 
			SET `count`=`count`+'{count}' 
			WHERE `owner_id`='{ownerID}' AND `item_id`='{itemID}' AND `loc`='INVENTORY'",
		),
	
	"getByLevel" => "
		SELECT characters.char_name
		FROM characters
		LEFT JOIN character_subclasses ON character_subclasses.char_obj_id = characters.obj_Id AND character_subclasses.isBase='1'  
		WHERE characters.account_name='{account}' AND character_subclasses.level>={level} 
		LIMIT 1",
		
);
?>