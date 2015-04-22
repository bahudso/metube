<?php

/* Controller for browse page. */

require_once 'includes/MediaBootstrap.php';

try
{
    $aUploadData = $oBusiness->HandleBrowse($_GET);

    $sUploadHTML = $oPresentation->GetBrowsePage( $aUploadData );

    echo $sUploadHTML;
}
catch( Exception $e )
{
    cLogger::Write( $e );
}

?>