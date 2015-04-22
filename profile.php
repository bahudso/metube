<?php

/* Controller for user page. */

require_once 'includes/UserBootstrap.php';

try
{
	if (isset($_GET['userid'])) {
		$aUserData = $oBusiness->LoadProfile($_GET['userid']);
	} else {
		$aUserData = $oBusiness->HandleUser();
	}

	$sProfileHTML = $oPresentation->GetUserProfilePage( $aUserData );

    echo $sProfileHTML;
}
catch( Exception $e )
{
    // Need error handling class that writes exception to log file and displays error page.
    cLogger::Write( $e );
}

?>