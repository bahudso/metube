<?php

/* Controller for user page. */

require_once 'includes/UserBootstrap.php';

try
{
    $aUserData = $oBusiness->HandleUser();

    $sUserHTML = $oPresentation->GetUserProfilePage( $aUserData );

    echo $sUserHTML;
}
catch( Exception $e )
{
    // Need error handling class that writes exception to log file and displays error page.
    cLogger::Write( $e );
}

?>