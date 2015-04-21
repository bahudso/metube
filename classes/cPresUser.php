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

    /**
    * Build message template
    **/
    public function GetMessagePage( $messages, $retMessage = '' ) {
        $page['template'] = 'user/messages.html';

        $sMessages = '';
        foreach($messages as $message) {
            $aMessage = array();
            $aMessage[ 'template' ] = 'user/message.html';
            $aMessage[ '_:_CONTENT_:_' ] = $message[ 'content' ];
            $aMessage[ '_:_FROM_:_' ]    = $message[ 'username' ];
            $sMessages .= $this->oTemplate->PopulateTemplate( $aMessage );
        }

        $page[ '_:_MESSAGES_:_' ] = $sMessages;
        $page['_:_MESSAGE_:_'] = $retMessage;

        $html = $this->BuildPage($page);

        return $html;
    }
}

?>