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
        $sMessage  = '';

        if( isset( $_POST[ 'login' ] ) )
        {
            $sMessage = $this->LogIn( $_POST );
        }

        if( isset( $_POST[ 'register' ] ) )
        {
            $sMessage = $this->Register( $_POST );
        }

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
            $sGetUserInfo = "SELECT username, email FROM user
                             WHERE id = :id";

            $aBind = array( ':id' => $aUserData[ 'logged-in' ] );

            $aUserInfo = $this->oDb->GetQueryResults( $sGetUserInfo, $aBind );

            $aUserData[ 'email' ]    = $aUserInfo[ 'email' ];
            $aUserData[ 'username' ] = $aUserInfo[ 'username' ];

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
            $sMessage = 'Incorrect password.';
        }
        else
        {
            // Log user in.
            $iUserId = $aUserData[ 'id' ];
            $_SESSION[ 'user' ] = $iUserId;
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
}

?>