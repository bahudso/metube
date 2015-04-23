<?php

/* Download controller, may move this at a later point */

require_once 'includes/MediaBootstrap.php';

try
{
    $oBusiness->HandleDownload();
}
catch( Exception $e )
{
    cLogger::Write( $e );
}

?>