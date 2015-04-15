<?php

/* User presentation class */

require_once 'cPresentation.php';

class cPresUser extends cPresentation
{
    public function __construct()
    {
        parent::__construct();
        $this->oTemplate->SetTemplateDir( 'user' );
    }

    /**
    * Handles building user page HTML.
    **/
    public function GetUserPage( $aUserData )
    {
        $aUserPage = array();

        if( isset( $aUserData[ 'logged-in' ] ) )
        {
            //show user account page
            $aUserPage[ 'template' ] = 'account.html';
        }
        else
        {
            $aUserPage[ 'template' ] = 'user.html';
        }

        $sUserHTML = $this->oTemplate->PopulateTemplate( $aUserPage );

        return $sUserHTML;
    }
}

?>