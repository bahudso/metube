<?php

/* Controller for user page. */

require_once 'includes/UserBootstrap.php';

try
{
	$sMessage = '';

	// catch send message form submission
    if (isset($_POST['sendMessage'])) {
        $sMessage = $oBusiness->sendMessage($_POST);
    }

    $aMessageData = $oBusiness->getMessages();
    
    $sMessageHTML = $oPresentation->GetMessagePage($aMessageData, $sMessage);
    
    echo $sMessageHTML;
}
catch( Exception $e )
{
    // Need error handling class that writes exception to log file and displays error page.
    cLogger::Write( $e );
}

?>