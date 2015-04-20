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
    public function HandleUser()
    {
        $aUserData = array();

        if( isset( $_POST[ 'login' ] ) )
        {
            $iError = $this->LogIn( $_POST );
        }

        if( isset( $_POST[ 'register' ] ) )
        {
            $iError = $this->Register( $_POST );
        }

        if( isset( $_POST[ 'edit-email' ] ) )
        {
            // edit user email
        }

        if( isset( $_POST[ 'edit-password' ] ) )
        {
            // edit user password
        }

        if( isset( $_POST[ 'edit-username' ] ) )
        {
            // edit username
        }

        if( isset( $_SESSION[ 'user' ] ) )
        {
            // Someone is logged in, so show them their page.
            $aUserData[ 'logged-in' ] = $_SESSION[ 'user' ];

            // Get data for user.
            $sGetUserInfo = "SELECT username, email FROM user
                             WHERE id = :id";

            $aBind = array( ':id' => $aUserData[ 'logged-in' ] );

            $aUserInfo = $this->oDb->GetQueryResults( $sGetUserInfo, $aBind );

            $aUserData[ 'email' ]    = $aUserInfo[ 'email' ];
            $aUserData[ 'username' ] = $aUserInfo[ 'username' ];
        }

        return $aUserData;
    }

    /**
    * Handles logic for registering a new user.
    **/
    public function Register( $aFormData )
    {
        $iError = 0;

        // Check for valid email.
        $sEmail = $aFormData[ 'email' ];
        $bValidEmail = filter_var( $sEmail, FILTER_VALIDATE_EMAIL );

        if( $bValidEmail === FALSE )
        {
            // Invalid email address.
            $iError = 1;
        }

        // Check for password match.
        $sPassword     = $aFormData[ 'password' ];
        $sPasswordConf = $aFormData[ 'password-conf' ];

        $iPasswordConf = strcmp( $sPassword, $sPasswordConf );

        if( $iPasswordConf !== 0 )
        {
            // Passwords do not match.
            $iError = 2;
        }

        // Check for safe password. ? (Not sure if we need to do this)

        // If we have made it this far without an error we can register the user.
        if( $iError === 0 )
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

        return $iError;
    }

    /**
    * Handles logic for logging in.
    **/
    public function LogIn( $aFormData )
    {
        $iError = 0;

        $sEmail = $aFormData[ 'email' ];

        // Get hash and salt from database where username = supplied username.
        $sGetUser = "SELECT id, salt, hash 
                     FROM user
                     WHERE email = :email";

        $aBind = array( ':email' => $sEmail );

        $aUserData = $this->oDb->GetQueryResults( $sGetUser, $aBind );

        $sPassword = $aFormData[ 'password' ];
        $sHash     = $aUserData[ 'hash' ];
        $sSalt     = $aUserData[ 'salt' ];

        $sNewHash  = crypt( $sPassword, '$2y$12$' . $sSalt );

        if( $sNewHash !== $sHash )
        {
            // Incorrect password. 
            $iError = 1;
        }
        else
        {
            // Log user in.
            $iUserId = $aUserData[ 'id' ];
            $_SESSION[ 'user' ] = $iUserId;
        }

        return $iError;
    }

    /**
    * Handles logic for logging a user out.
    **/
    public function LogOut()
    {
        session_destroy();
        $_SESSION = array();
    }
}

?>