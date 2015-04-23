<?php

/* Controller for user page. */

require_once 'includes/UserBootstrap.php';

try
{
    $aAccountData = $oBusiness->HandleAccount();
    
    $sAccountHTML = $oPresentation->GetAccountPage( $aAccountData );
    
    echo $sAccountHTML;
}
catch( Exception $e )
{
    // Need error handling class that writes exception to log file and displays error page.
    cLogger::Write( $e );
}

?>