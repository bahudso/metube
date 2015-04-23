<?php

/* Controller for user page. */

require_once 'includes/UserBootstrap.php';

try
{
	$page = array();

	if (isset($_POST['newPlaylist'])) {
		$pid = $oBusiness->addPlaylist($_POST);

		$sPlaylistHTML = $oPresentation->GetPlaylistPage( $pid );
	} else if (isset($_GET['addNew'])) {
		// just load add playlist template
		$page['template'] = 'user/addPlaylist.html';
		$sPlaylistHTML = $oPresentation->BuildPage($page);
	} else if (isset($_GET['pid'])) {
		$aPlaylist = $oBusiness->getPlaylist($_GET['pid']);
		
		$sPlaylistHTML = $oPresentation->GetPlaylistPage( $aPlaylist );
	} else {

	}
    
    // $aPlaylistData = $oBusiness->HandlePlaylist();

    // $sUserHTML = $oPresentation->GetUserPage( $aUserData );

    echo $sPlaylistHTML;
}
catch( Exception $e )
{
    // Need error handling class that writes exception to log file and displays error page.
    cLogger::Write( $e );
}

?>