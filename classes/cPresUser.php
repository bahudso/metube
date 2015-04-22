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
    * Handles building user profile page HTML.
    **/
    public function GetUserProfilePage($aUserData) {
        $aUserProfilePage = array();
        $aUserProfilePage['template']       = 'user/profile.html';
        $aUserProfilePage['_:_ID_:_']       = $aUserData['id'];
        $aUserProfilePage['_:_USERNAME_:_'] = $aUserData['username'];
        $aUserProfilePage['_:_EMAIL_:_']    = $aUserData['email'];
        $aUserProfilePage['_:_DESCRIPTION_:_']    = $aUserData['description'];

        // check if it's current user's page or not
        if ($aUserData['username'] == $_SESSION['username'] || isset($aUserData['message'])) {
            $aUserProfilePage['_:_SUBSCRIBE_:_'] = '';
        } else {
            $aUserProfilePage['_:_SUBSCRIBE_:_'] = '<input type="submit" name="subscribe" value="Subscribe"/>';            
        }

        $sProfileHtml = $this->BuildPage($aUserProfilePage);
        
        return $sProfileHtml;
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

    /**
    * Build relationships template
    **/
    public function GetRelationshipsPage($relations, $message) {
        $page = array();
        $page['template'] = 'user/relationships.html';
        $page['_:_MESSAGE_:_'] = $message;
        $page['_:_PENDING_:_'] = '';
        $page['_:_FRIENDS_:_'] = '';
        $page['_:_FOES_:_'] = '';

        foreach($relations as $relation) {
            // pending requests
            if ($relation['type'] == 0) {
                $page['_:_PENDING_:_'] .= "<li>".$relation['username']."</li>";
            }
            // friends
            if ($relation['type'] == 1) {
                $page['_:_FRIENDS_:_'] .= "<li>".$relation['username']."</li>";
            }
            // foes
            if ($relation['type'] == 2) {
                $page['_:_FOES_:_'] .= "<li>".$relation['username']."</li>";
            }
        }

        $html = $this->BuildPage($page);
        
        return $html;
    }
}

?>