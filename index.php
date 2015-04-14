<?php

require_once 'includes/Bootstrap.php';

try
{
    $aIndexData = $oBusiness->HandleIndex();

    $sIndexHTML = $oPresentation->GetIndexPage( $aIndexData );

    echo $sIndexHTML;
}
catch( Exception $e )
{
    // Need error handling class that writes exception to log file and displays error page.
    cLogger::Write( $e );
}

?>