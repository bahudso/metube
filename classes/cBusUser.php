<?php

/* User business class */

require_once 'cBusiness.php';

class cBusUser extends cBusiness
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
    * Handles logic for user page ( login, registration... )
    **/
    public function HandleLogin()
    {
        $aUserData = array();
        $sMessage  = '';

        if( isset( $_POST[ 'login' ] ) )
        {
            $sMessage = $this->LogIn( $_POST );
        }

        if( isset( $_POST[ 'register' ] ) )
        {
            $sMessage = $this->Register( $_POST );
        }

        $aUserData[ 'message' ] = $sMessage;

        if ( isset($_GET['logout']) ) {
            $this->LogOut();
        }

        return $aUserData;
    }

    /**
    * Handles logic for account settings page.
    **/ 
    public function HandleAccount()
    {
        $aUserData = array();
        $sMessage  = '';

        if( isset( $_POST[ 'edit-email' ] ) )
        {
            $sMessage = $this->EditEmail( $_POST );
        }

        if( isset( $_POST[ 'edit-password' ] ) )
        {
            $sMessage = $this->EditPassword( $_POST );
        }

        if( isset( $_POST[ 'edit-username' ] ) )
        {
            $sMessage = $this->EditUsername( $_POST );
        }

        if( isset( $_SESSION[ 'user' ] ) )
        {
            // Someone is logged in, so show them their page.
            $aUserData[ 'logged-in' ] = $_SESSION[ 'user' ];

            // Get data for user.
            $sGetUserInfo = "SELECT id, username, email, description FROM user
                             WHERE id = :id";

            $aBind = array( ':id' => $aUserData[ 'logged-in' ] );

            $aUserInfo = $this->oDb->GetSingleQueryResults( $sGetUserInfo, $aBind );

            $aUserData[ 'id' ]    = $aUserInfo[ 'id' ];
            $aUserData[ 'email' ]    = $aUserInfo[ 'email' ];
            $aUserData[ 'username' ] = $aUserInfo[ 'username' ];
            $aUserData[ 'description' ] = $aUserInfo[ 'description' ];

            //$aUserData[ 'uploads' ]   = $this->GetUploads();
            $aUserData[ 'favorites' ] = $this->GetFavorites();

            $aUserData[ 'message' ] = $sMessage;
        }

        return $aUserData;
    }

    /**
    * Handles logic for registering a new user.
    **/
    public function Register( $aFormData )
    {
        $sMessage = '';

        // Check for valid email.
        $sEmail = $aFormData[ 'email' ];
        $bValidEmail = filter_var( $sEmail, FILTER_VALIDATE_EMAIL );

        if( $bValidEmail === FALSE )
        {
            $sMessage = 'Invalid email address.';
        }

        // Check for password match.
        $sPassword     = $aFormData[ 'password' ];
        $sPasswordConf = $aFormData[ 'password-conf' ];

        $iPasswordConf = strcmp( $sPassword, $sPasswordConf );

        if( $iPasswordConf !== 0 )
        {
            $sMessage = 'Passwords must match.';
        }

        // If we have made it this far without an error we can register the user.
        if( $sMessage === '' )
        {
            // Generate random salt.
            $sSalt = substr(strtr(base64_encode(openssl_random_pseudo_bytes(22)), '+', '.'), 0, 22);

            // Hash password.
            $sHash = crypt( $sPassword, '$2y$12$' . $sSalt );

            //Store email, salt, and password hash in db.
            $sInsertUser = "INSERT INTO user ( email, salt, hash )
                            VALUES ( :email, :salt, :hash )";

            $aBind = array( ':email' => $sEmail,
                            ':salt'  => $sSalt,
                            ':hash'  => $sHash );

            $this->oDb->RunQuery( $sInsertUser, $aBind ); 
        }

        return $sMessage;
    }

    /**
    * Handles logic for logging in.
    **/
    public function LogIn( $aFormData )
    {
        $sMessage = '';

        $sEmail = $aFormData[ 'email' ];

        // Get hash and salt from database where username = supplied username.
        $sGetUser = "SELECT id, salt, hash, username 
                     FROM user
                     WHERE email = :email";

        $aBind = array( ':email' => $sEmail );

        $aUserData = $this->oDb->GetSingleQueryResults( $sGetUser, $aBind );

        $sPassword = $aFormData[ 'password' ];
        $sHash     = $aUserData[ 'hash' ];
        $sSalt     = $aUserData[ 'salt' ];

        $sNewHash  = crypt( $sPassword, '$2y$12$' . $sSalt );

        if( $sNewHash !== $sHash )
        {
            $sMessage = 'Incorrect password.';
        }
        else
        {
            // Log user in.
            $iUserId = $aUserData[ 'id' ];
            $_SESSION[ 'user' ] = $iUserId;
            $_SESSION['username'] = $aUserData['username'];
            header( 'Location: profile.php' );
        }

        return $sMessage;
    }

    /**
    * Handles logic for logging a user out.
    **/
    public function LogOut()
    {
        session_destroy();
        $_SESSION = array();
        header( 'Location: index.php' );
    }

    /**
    * Edit user email address.
    **/
    public function EditEmail( array $aPost )
    {
        $sMessage = '';

        // Check for valid email.
        $sEmail = $aPost[ 'email' ];
        $bValidEmail = filter_var( $sEmail, FILTER_VALIDATE_EMAIL );

        if( $bValidEmail === FALSE )
        {
            $sMessage = 'Invalid email address.';
        }

        if( $sMessage === '' )
        {
            // No errors so we can update the email address.
            $sUpdateEmail = "UPDATE user
                             SET email = :email
                             WHERE id = :id";

            $aBind = array( ':email' => $sEmail,
                            ':id'    => $_SESSION[ 'user' ] );

            $this->oDb->RunQuery( $sUpdateEmail, $aBind );

            $sMessage = 'Your email address has been updated.';
        }

        return $sMessage;
    }

    /**
    * Edit user password.
    **/
    public function EditPassword( array $aPost )
    {
        $sMessage = '';

        // Check for password match.
        $sPassword     = $aPost[ 'password' ];
        $sPasswordConf = $aPost[ 'password-conf' ];

        $iPasswordConf = strcmp( $sPassword, $sPasswordConf );

        if( $iPasswordConf !== 0 )
        {
            $sMessage = 'Passwords must match.';
        }

        // If we have made it this far without an error we can change the password
        if( $sMessage === '' )
        {
            // Generate random salt.
            $sSalt = substr(strtr(base64_encode(openssl_random_pseudo_bytes(22)), '+', '.'), 0, 22);

            // Hash password.
            $sHash = crypt( $sPassword, '$2y$12$' . $sSalt );

            // Update with new hash and salt.
            $sUpdatePassword = "UPDATE  user 
                                SET salt = :salt,
                                    hash = :hash
                                WHERE id = :id";

            $aBind = array( ':salt'  => $sSalt,
                            ':hash'  => $sHash,
                            ':id'    => $_SESSION[ 'user' ], );

            $this->oDb->RunQuery( $sUpdatePassword, $aBind ); 

            $sMessage = 'Your password has been updated.';
        }

        return $sMessage;
    }

    /**
    * Edit user username.
    **/
    public function EditUsername( array $aPost )
    {
        $sMessage = '';;

        $sUsername = $aPost[ 'username' ];

        $sUpdateUsername = "UPDATE user
                            SET username = :username
                            WHERE id = :id";

        $aBind = array( ':username' => $sUsername,
                        ':id'       => $_SESSION[ 'user' ] );

        $this->oDb->RunQuery( $sUpdateUsername, $aBind );

        $sMessage = 'Your username has been updated.';

        return $sMessage;
    }

    /**
    * Get user's messages
    **/
    public function getMessages() {
        $sGetMessages = "SELECT sender, content, date, username FROM message JOIN user ON user.id = message.sender WHERE receiver = :user";
        $aBind = array(':user' => $_SESSION['user']);
        $messages = $this->oDb->GetQueryResults( $sGetMessages, $aBind );
        return $messages;
    }

    /**
    * Send a message to another user
    **/
    public function sendMessage( $aFormData ) {
        $sInsertMessage = "INSERT INTO message (sender, receiver, content, date) 
                            VALUES (:sender, :receiver, :content, NOW())";
        
        // check if receiver (to) is number
        if (is_numeric($aFormData['receiver'])) {
            $receiver = $aFormData['receiver'];
        } else { // if not number then search username and get user id
            $sGetUserId = "SELECT id FROM user WHERE username = :username";
            $aBind = array(':username' => $aFormData['receiver']);
            $result = $this->oDb->GetQueryResults( $sGetUserId, $aBind );
            $receiver = $result[0]['id'];
        }
        $aBind = array(':sender' => $_SESSION['user'],
            ':receiver' => $receiver,
            ':content' => $aFormData['content']);

        $this->oDb->RunQuery( $sInsertMessage, $aBind );

        $sMessage = 'Your message has been sent.';

        return $sMessage;
    }

    /**
    * Get's relationships for a user
    **/
    public function GetRelationships() {
        $sGetRelations = "SELECT type, username 
            FROM relationship JOIN user ON user.id = relationship.user_b 
            WHERE user_a = :user";
        
        $aBind = array(':user' => $_SESSION['user']);

        $relations = $this->oDb->GetQueryResults( $sGetRelations, $aBind );

        return $relations;
    }

    /**
    * Handle adding a new relationship for a user
    **/
    public function AddRelationship($aFormData) {
        // get user id for user_b
        $sGetUserId = "SELECT id FROM user WHERE username = :username";
        $aBind = array(':username' => $aFormData['username']);
        $result = $this->oDb->GetSingleQueryResults( $sGetUserId, $aBind );
        $userId = $result["id"];

        $sInsertMessage = "INSERT INTO relationship (user_a, user_b, type, timestamp) 
            VALUES (:user_a, :user_b, :type, NOW())";

        $aBind = array(':user_a' => $_SESSION['user'],
            ':user_b' => $userId,
            ':type' => $_POST['type']);

        $this->oDb->RunQuery( $sInsertMessage, $aBind );

        $sMessage = 'Success.';

        return $sMessage;
    }

    /**
    * Load user profile
    **/
    public function LoadProfile($userid) {
        // Get data for user.
        $sGetUserInfo = "SELECT username, email, description FROM user
                         WHERE id = :id";

        $aBind = array( ':id' => $userid );

        $aUserInfo = $this->oDb->GetSingleQueryResults( $sGetUserInfo, $aBind );

        $aUserData[ 'id' ]          = $userid;
        $aUserData[ 'email' ]       = $aUserInfo[ 'email' ];
        $aUserData[ 'username' ]    = $aUserInfo[ 'username' ];
        $aUserData[ 'description' ] = $aUserInfo[ 'description' ];

        $aUserData[ 'favorites' ] = $this->GetFavorites();

        return $aUserData;
    }

    /**
    * Subscribe to user's channel
    **/
    public function Subscribe($userId) {// userid is id of user being subscribed to
        $sInsertMessage = "INSERT INTO subscription (user_a, user_b, timestamp) 
            VALUES (:user_a, :user_b, NOW())";

        $aBind = array(':user_a' => $_SESSION['user'],
            ':user_b' => $userId);

        $this->oDb->RunQuery( $sInsertMessage, $aBind );

        $sMessage = 'Success.';

        return $sMessage;
    }

    /**
    * Add Playlist
    **/
    public function addPlaylist($aFormData) {
        $sInsertMessage = "INSERT INTO playlist (title, user) 
            VALUES (:title, :user)";

        $aBind = array(':title' => $aFormData['title'],
            ':user' => $_SESSION['user']);

        $this->oDb->RunQuery( $sInsertMessage, $aBind );

        $pid = $this->oDb->GetLastId();

        return $pid;
    }

    /**
    * Get playlist data
    **/
    public function getPlaylist($pid) {
        $sGetPlaylist = "SELECT playlist.title AS playlist_title, media_id, media.title AS media_title
            FROM playlist LEFT JOIN playlist_media 
            ON playlist_id = playlist.id 
            LEFT JOIN media ON media_id = media.id 
            WHERE playlist.id = :id";

        $aBind = array( ':id' => $pid );

        $playlist = $this->oDb->GetQueryResults( $sGetPlaylist, $aBind );

        return $playlist;
    }

    /**
    * Get all of a user's playlists
    **/
    public function getPlaylists() {
        $sGetPlaylists = "SELECT * FROM playlist WHERE user = :user";

        $aBind = array( ':user' => $_SESSION['user']);

        $playlists = $this->oDb->GetQueryResults($sGetPlaylists, $aBind);

        return $playlists;
    }

    /**
    * Add media to playlist
    **/
    public function addToPlaylist($aFormData) {
        $sInsert = "INSERT INTO playlist_media (playlist_id, media_id) VALUE (:playlist_id, :media_id)";

        $aBind = array(":playlist_id" => $aFormData['playlist'], ":media_id" => $aFormData['media_id']);

        $this->oDb->RunQuery( $sInsert, $aBind );

        return 0;
    }

    public function GetFavorites()
    {
        $aFavorites = array();

        $sGetFavorites = "SELECT media.id, media.title, media.type, media.description FROM media 
                          JOIN favorite ON media.id = media_id
                          WHERE user_id=:user";

        $aBind = array( ':user' => $_SESSION[ 'user' ] );

        $aFavorites = $this->oDb->GetQueryResults( $sGetFavorites, $aBind );

        return $aFavorites;
    }
}

?>