<?php

/* Controller for user page. */

require_once 'includes/UserBootstrap.php';

try
{

	if (isset($_POST['newPlaylist'])) {
		// add new playlist and load it after adding
		$pid = $oBusiness->addPlaylist($_POST);

		$sPlaylistHTML = $oPresentation->GetPlaylistPage( $pid );

	} else if (isset($_GET['addNew'])) {
		// just load add playlist template
		$page = array();
		$page['template'] = 'user/addPlaylist.html';
		$sPlaylistHTML = $oPresentation->BuildPage($page);


	} else if (isset($_GET['pid'])) {
		// load single playlist template
		$aPlaylist = $oBusiness->getPlaylist($_GET['pid']);

		$sPlaylistHTML = $oPresentation->GetPlaylistPage( $aPlaylist );

	} else if (isset($_GET['addToPlaylist'])) {
		// just load add to playlist template
		$page = array();
		$page['template'] = 'user/addToPlaylist.html';
		$page['_:_MEDIAID_:_'] = $_GET['media_id'];

		$aPlaylists = $oBusiness->getPlaylists();
		$sPlaylists = '<option value="">-----</option>';
		foreach($aPlaylists as $playlist) {
			$sPlaylists .= "<option value='" . $playlist["id"] . "'>" . $playlist["title"] . "</option>";
		}

		$page['_:_PLAYLISTS_:_'] = $sPlaylists;

		$sPlaylistHTML = $oPresentation->BuildPage($page);
	} else if (isset($_POST['addToPlaylist'])) {
		$oBusiness->addToPlaylist($_POST);

		$sMessage = "Successfully added media to playlist";

		header("Location: view.php?media=".$_POST['media_id']);
	} else {
		// load all user playlists & playlists template
		$aPlaylists = $oBusiness->getPlaylists();
		
		$sPlaylistHTML = $oPresentation->GetPlaylistsPage($aPlaylists);
	}

    echo $sPlaylistHTML;
}
catch( Exception $e )
{
    // Need error handling class that writes exception to log file and displays error page.
    cLogger::Write( $e );
}

?>