<?php

/* Controller for user page. */

require_once 'includes/MediaBootstrap.php';

try
{
    $aUploadData = $oBusiness->HandleUpload();

    $sUploadHTML = $oPresentation->GetUploadPage( $aUploadData );

    echo $sUploadHTML;
}
catch( Exception $e )
{
    cLogger::Write( $e );
}

?>