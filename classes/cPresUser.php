<?php

/* User presentation class */

require_once 'cPresentation.php';

class cPresUser extends cPresentation
{
    public function __construct()
    {
        parent::__construct();
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
            $aUserPage[ 'template' ]       = 'user/account.html';
            $aUserPage[ '_:_MESSAGE_:_' ]  = $aUserData[ 'message' ];
            $aUserPage[ '_:_EMAIL_:_' ]    = $aUserData[ 'email' ];
            $aUserPage[ '_:_USERNAME_:_' ] = $aUserData[ 'username' ];
        }
        else
        {
            $aUserPage[ 'template' ] = 'user/user.html';
        }

        $sUserHTML = $this->BuildPage( $aUserPage );

        return $sUserHTML;
    }
}

?>