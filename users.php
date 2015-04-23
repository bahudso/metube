<?php

/* Controller for user page. */

require_once 'includes/UserBootstrap.php';

try
{
	$aUsersData = $oBusiness->GetAllUsers();

    $sUsersHTML = $oPresentation->GetUsersPage($aUsersData);
    
    echo $sUsersHTML;
}
catch( Exception $e )
{
    // Need error handling class that writes exception to log file and displays error page.
    cLogger::Write( $e );
}

?>