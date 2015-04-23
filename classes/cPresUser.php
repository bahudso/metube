<?php

/* User presentation class */

require_once 'cPresentation.php';

class cPresUser extends cPresentation
{
    public function __construct()
    {
        parent::__construct();
    }

    public function GetLoginPage( $aLoginData )
    {
        $aLoginPage = array();

        $aLoginPage[ 'template' ] = 'user/login.html';

        $sLoginHTML = $this->BuildPage( $aLoginPage );

        return $sLoginHTML;
    }

    /**
    * Handles building user page HTML.
    **/
    public function GetAccountPage( $aUserData )
    {
        $aAccountPage = array();

        //show user account page
        $aAccountPage[ 'template' ]       = 'user/account.html';
        $aAccountPage[ '_:_MESSAGE_:_' ]  = $aUserData[ 'message' ];
        $aAccountPage[ '_:_EMAIL_:_' ]    = $aUserData[ 'email' ];
        $aAccountPage[ '_:_USERNAME_:_' ] = $aUserData[ 'username' ];

        $sAccountHTML = $this->BuildPage( $aAccountPage );

        return $sAccountHTML;
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
                $page['_:_PENDING_:_'] .= "<li>".$relation['username']."<form name='handleRelationship' action='relationships.php' method='POST'>
                    <input name='accept' type='submit' value='Accept'/>
                    <input name='decline' type='submit' value='Decline'/>
                    <input name='user_a' type='hidden' value='".$relation['user_a']."'/>
                </form></li>";
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

    /**
    * Build playlist page for single playlist
    **/
    public function GetPlaylistPage($playlist) {
        $page = array();
        $page['template'] = 'user/playlist.html';

        $page['_:_TITLE_:_'] = $playlist[0]['playlist_title'];
        
        $sMedia = '<ul>';
        if (count($playlist) == 1 && !isset($playlist[0]['media_id'])) {
            $sMedia .= "<li class='panel'>No Media in Playlist</li>";
        } else {
            foreach($playlist as $p) {
                $sMedia .= "<li class='panel'><a href='view.php?media=" . $p['media_id'] . "'>" . $p['media_title'] . "</a></li>";
            }
        }

        $sMedia .= "</ul>";

        $page[ '_:_MEDIA_:_' ] = $sMedia;

        $html = $this->BuildPage($page);
        
        return $html;
    }

    /**
    * Builds playlists page for all user playlists
    **/
    public function GetPlaylistsPage($playlists) {
        $page = array();
        $page['template'] = 'user/playlists.html';

        $sPlaylists = '';

        foreach($playlists as $p) {
            $sPlaylists .= "<li class='panel'><a href='playlist.php?pid=" . $p['id'] . "'>" . $p['title'] . "</a></li>";
        }

        $page['_:_PLAYLISTS_:_'] = $sPlaylists;

        $html = $this->BuildPage($page);

        return $html;
    }
}

?>