<?php

	if (!defined( 'STRESSWEB' )) {
		exit( 'Access denied...' );
	}

	$_nid = (isset( $_GET['nid'] ) ? intval( $_GET['nid'] ) : 0);
	$_lang = (( isset( $_COOKIE['swlang'] ) && $_COOKIE['swlang'] == 'en' ) ? '_en' : '');

	
	
	if ($_nid) {
		
		$select = $db->query( 'SELECT * FROM `stress_news` WHERE `nid`=\'' . $_nid . '\' LIMIT 1' );

		if ($db->num_rows( $select ) == 0) {
			$tpl->ShowError( $lang['error'], $lang['news_err_1'] );
			$tpl->SetResult( 'content' );
			return 1;
		}

		
		$data = $db->fetch( $select );

		if (empty( $data['title_en'] )) {
			$data['title_en'] = $data['title'];
		}


		if (empty( $data['content_en'] )) {
			$data['content_en'] = $data['content'];
		}


		if (empty( $data['full' . $_lang] )) {
			$data['full' . $_lang] = $data['content' . $_lang];
		}

		$tpl->LoadView( 'newsfull' );
		$tpl->Set( 'id', $data['nid'] );
		$tpl->Set( 'ntitle', $data['title' . $_lang] );
		$tpl->Set( 'newsfull', $data['full' . $_lang] );
		$tpl->Set( 'author', $data['author'] );
		$tpl->Set( 'date', date( '' . $l2cfg['news']['date'] . '', $data['date'] ) );
		$tpl->Set( 'day', date( 'd', $data['date'] ) );
		$tpl->Set( 'month', date( 'm', $data['date'] ) );
		$tpl->Set( 'year', date( 'Y', $data['date'] ) );

		if ($data['img']) {
			$tpl->Block( 'img' );
			$tpl->Set( 'img', urldecode( $data['img'] ) );
		} 
else {
			$tpl->Block( 'img', false );
		}


		if ($data['flink']) {
			$tpl->Block( 'forum-link' );
			$tpl->Set( 'flink', urldecode( $data['flink'] ) );
		} 
else {
			$tpl->Block( 'forum-link', false );
		}

		$tpl->Build( 'content' );
		return 1;
	}

	$offset = $l2cfg['news']['perpage'] * abs( $page - 1 );
	
	$select = $db->query( "SELECT * FROM `stress_news` ORDER BY date {$l2cfg['news']['sort']} LIMIT $offset, {$l2cfg['news']['perpage']}" );

	/*if ($db->num_rows( $select ) == 0) {
		$tpl->SetResult( 'content', '<div class=\'error\'>' . $lang['news_err_2'] . '</div>' );
		return 1;
	}*/

	$rows = $db->fetchAll( $select );
	
	//echo '<pre>';
	//print_r( $rows );
	//var_dump(  );
	//echo '</pre>';
	
	if ( is_array( $rows ) && count( $rows ) ) 
	{
		foreach ( $rows as $row )
		{
			if ( empty( $row['title_en'] )) {
				$row['title_en'] = $row['title'];
			}


			if (empty( $row['content_en'] )) {
				$row['content_en'] = $row['content'];
			}

			$tpl->LoadView( 'news' );
			$tpl->Set( 'id', $row['nid'] );
			$tpl->Set( 'ntitle', $row['title' . $_lang] );
			$tpl->Set( 'news', $row['content' . $_lang] );
			$tpl->Set( 'author', $row['author'] );
			$tpl->Set( 'date', date( '' . $l2cfg['news']['date'] . '', $row['date'] ) );
			$tpl->Set( 'day', date( 'd', $row['date'] ) );
			$tpl->Set( 'month', date( 'm', $row['date'] ) );
			$tpl->Set( 'year', date( 'Y', $row['date'] ) );
			$link = ($l2cfg['mod_rewrite'] ? '/news/' : '/index.php?f=news&nid=');
			$tpl->Set( 'full-link', '<a href=\'' . HTTP_HOME_URL . ( $link . $row['nid'] . '\'>' ) );
			$tpl->Set( '/full-link', '</a>' );

			if ($row['img']) {
				$tpl->Block( 'img' );
				$tpl->Set( 'img', urldecode( $row['img'] ) );
			} 
			else {
				$tpl->Block( 'img', false );
			}


			if ($row['flink']) {
				$tpl->Block( 'forum-link' );
				$tpl->Set( 'flink', urldecode( $row['flink'] ) );
			} 
			else {
				$tpl->Block( 'forum-link', false );
			}

			$tpl->Build( 'content' );
		}
	}
	
	$news_count = $db->result( $db->query( 'SELECT COUNT(0) FROM `stress_news`' ), 0 );

	if ($l2cfg['news']['perpage'] < $news_count) {
		
		$numpages = ceil( $news_count / $l2cfg['news']['perpage'] );
		$link = ($l2cfg['mod_rewrite'] ? '/news/page-' : '/index.php?f=news&page=');
		$tpl->SetResult( 'content', $controller->PageList( HTTP_HOME_URL . $link, $numpages, $page ) );
	}

?>