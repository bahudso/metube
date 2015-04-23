<?php

/* Controller for login page. */

require_once 'includes/UserBootstrap.php';

try
{
    $aLoginData = $oBusiness->HandleLogin();

    $sLoginHTML = $oPresentation->GetLoginPage( $aLoginData );

    echo $sLoginHTML;
}
catch( Exception $e )
{
    // Need error handling class that writes exception to log file and displays error page.
    cLogger::Write( $e );
}

?>