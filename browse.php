<?php

/* Controller for browse page. */

require_once 'includes/MediaBootstrap.php';

try
{
    $aBrowseData = $oBusiness->HandleBrowse($_GET);

    $sBrowseHTML = $oPresentation->GetBrowsePage( $aBrowseData );

    echo $sBrowseHTML;
}
catch( Exception $e )
{
    cLogger::Write( $e );
}

?>