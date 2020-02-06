<?php
/**
 * STRESS WEB
 * @author S.T.R.E.S.S.
 * @copyright 2008 - 2012 STRESS WEB
 * @version 13
 * @web http://stressweb.ru
 */
if ( !defined("STRESSWEB") )
    die( "Access denied..." );

if ( !$l2cfg["forum"]["enable"] ) {
    $tpl->SetResult( 'forum' );
} else {

    $mod_forum = $controller->GetCache( 'mod_forum' );

    if ( $mod_forum )
        $tpl->SetResult( 'forum', $mod_forum );
    else {

        if ( empty($l2cfg["forum"]["deny"]) )
            $l2cfg["forum"]["deny"] = "0";

        $fdb = new db( $l2cfg["forum"]["dbhost"], $l2cfg["forum"]["dbuser"], $l2cfg["forum"]["dbpass"], $l2cfg["forum"]["dbname"], $l2cfg["mysql"]["debug"] );
        $fdb->query( "SET NAMES '{$l2cfg["forum"]["dbcoll"]}'" );
        /**
         * =================
         * 		IPB
         * =================
         */
        if ( $l2cfg["forum"]["version"] == "ipb" ) {
            $fsql = "
		SELECT tid,title,description,last_post,last_poster_id,last_poster_name 
		FROM {$l2cfg["forum"]["prefix"]}topics 
		WHERE forum_id NOT IN ({$l2cfg["forum"]["deny"]}) 
		ORDER BY last_post DESC 
		LIMIT {$l2cfg["forum"]["count"]}";
            $furl_user = "index.php?showuser=";
            $furl_link = "index.php?showtopic={topic_id}' title='{desc_id}'";
        }
        /**
         * =================
         * 		phpBB
         * =================
         */
        if ( $l2cfg["forum"]["version"] == "phpbb" ) {
            $fsql = "
		SELECT topic_id,topic_title,forum_id,topic_last_post_time,topic_last_poster_id,topic_last_poster_name 
		FROM {$l2cfg["forum"]["prefix"]}topics 
		WHERE forum_id NOT IN ({$l2cfg["forum"]["deny"]}) 
		ORDER BY topic_last_post_time DESC 
		LIMIT {$l2cfg["forum"]["count"]}";
            $furl_user = "memberlist.php?mode=viewprofile&u=";
            $furl_link = "viewtopic.php?f={desc_id}&t={topic_id}'";
        }
        /**
         * =================
         * 		smf
         * =================
         */

        if ( $l2cfg["forum"]["version"] == "smf" ) {
            $fsql = "
		SELECT id_topic,subject,id_board,poster_time,id_member,poster_name 
		FROM {$l2cfg["forum"]["prefix"]}messages 
		WHERE id_board NOT IN ({$l2cfg["forum"]["deny"]}) 
		ORDER BY poster_time DESC 
		LIMIT {$l2cfg["forum"]["count"]}";
            $furl_user = "index.php?action=profile;u=";
            $furl_link = "index.php?topic={topic_id}'";
        }
        /**
         * =================
         * 		vBulletin
         * =================
         */
        if ( $l2cfg["forum"]["version"] == "vbulletin" ) {
            $fsql = "
		SELECT t.threadid,t.title,t.forumid,t.lastpost,u.userid,t.lastposter 
		FROM {$l2cfg["forum"]["prefix"]}thread t
		LEFT JOIN {$l2cfg["forum"]["prefix"]}user u ON t.lastposter = u.username  
		WHERE t.forumid NOT IN ({$l2cfg["forum"]["deny"]})
		ORDER BY t.lastpost DESC 
		LIMIT {$l2cfg["forum"]["count"]}";
            $furl_user = "member.php?u=";
            $furl_link = "showthread.php?t={topic_id}'";
        }
        /**
         * =================
         * 		XenFoRo
         *  	© thx Ream
         * =================
         */
        if ( $l2cfg["forum"]["version"] == "xenforo" ) {
            $fsql = "
        SELECT thread_id,title,node_id,last_post_date,user_id,last_post_username 
        FROM {$l2cfg["forum"]["prefix"]}thread
        WHERE node_id NOT IN ({$l2cfg["forum"]["deny"]})
        ORDER BY last_post_date DESC 
        LIMIT {$l2cfg["forum"]["count"]}";
            $furl_user = "members/";
            $furl_link = "threads/{topic_id}'";
        }
        /**
         * ----------------------------------------------------------------------------------------
         */

        $fquery = $fdb->query( $fsql );
        while ( list($ftid, $ftitle, $fdesc_id, $flast_post, $flast_poster_id, $flast_poster_name) = $fdb->fetch($fquery) ) {
            if ( strlen($ftitle) > $l2cfg["forum"]["length"] ) {
                $ftitle = iconv( 'UTF-8', 'windows-1251', $ftitle );
                $ftitle = substr( $ftitle, 0, $l2cfg["forum"]["length"] - 3 )."...";
                $ftitle = iconv( 'windows-1251', 'UTF-8', $ftitle );
            }
            $flast_post = date( $l2cfg["forum"]["date"], $flast_post );
            $flast_poster = "<a href='{$l2cfg["forum"]["url"]}/{$furl_user}{$flast_poster_id}' target='_blank'>{$flast_poster_name}</a>";
            $flast_link = "<a href='{$l2cfg["forum"]["url"]}/".$controller->buildString( $furl_link, array("topic_id" => $ftid, "desc_id" => $fdesc_id) )."  target='_blank'>{$ftitle}</a>";

            $tpl->LoadView( 'forum' );
            $tpl->Block( 'main', false );
            $tpl->Block( 'item' );
            $tpl->Set( 'date', $flast_post );
            $tpl->Set( 'author', $flast_poster );
            $tpl->Set( 'link', $flast_link );
            $tpl->Build( 'forum_item' );
        }
        $fdb->close();

        $tpl->LoadView( 'forum' );
        $tpl->Block( 'main' );
        $tpl->Block( 'item', false );
        $tpl->Set( 'item', $tpl->GetResult('forum_item', true) );
        $tpl->Build( "forum" );

        if ( $l2cfg['cache']['enable'] and $l2cfg['cache']['forum'] ) {
            $controller->SetCache( 'mod_forum', $tpl->GetResult('forum'), $l2cfg['cache']['forum'] );
        }
    }
}
?>