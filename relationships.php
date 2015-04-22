<?php

/* Controller for user page. */

require_once 'includes/UserBootstrap.php';

try
{
	$sMessage = '';

	if (isset($_POST['addRelationship'])) {
		$sMessage = $oBusiness->AddRelationship($_POST);
	}
    $aData = $oBusiness->GetRelationships();

    $sUserHTML = $oPresentation->GetRelationshipsPage( $aData, $sMessage );

    echo $sUserHTML;
}
catch( Exception $e )
{
    // Need error handling class that writes exception to log file and displays error page.
    cLogger::Write( $e );
}

?>