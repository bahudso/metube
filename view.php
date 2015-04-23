<?php

/* Controller for view media page. */

require_once 'includes/MediaBootstrap.php';

try
{
    $aViewData = $oBusiness->HandleView();

    $sViewHTML = $oPresentation->GetViewPage( $aViewData );

    echo $sViewHTML;
}
catch( Exception $e )
{
    // Need error handling class that writes exception to log file and displays error page.
    cLogger::Write( $e );
}

?>