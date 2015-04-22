<?php

/* Controller for user page. */

require_once 'includes/UserBootstrap.php';

try
{
    $aData = $oBusiness->GetRelationships();

    $sUserHTML = $oPresentation->GetRelationshipsPage( $aData );

    echo $sUserHTML;
}
catch( Exception $e )
{
    // Need error handling class that writes exception to log file and displays error page.
    cLogger::Write( $e );
}

?>