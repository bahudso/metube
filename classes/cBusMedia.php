<?php

/* Media business class */

require_once 'cBusiness.php';

class cBusMedia extends cBusiness
{
    public function __construct()
    {
        parent::__construct();
    }

   /**
    * Logic for handling upload page.
    **/
    public function HandleUpload()
    {
        $aUploadData = array();

        if( isset( $_POST[ 'upload-submit' ] ) )
        {
            if( empty( $_POST[ 'file-title' ] ) )
            {
                $aUploadData[ 'error' ] = 'Please include a file title.';
            }
            else
            {
                // $aFileUpload = $this->UploadFile( $_FILES[ 'file' ] );

                if( !isset( $aFileUpload[ 'error' ] ) )
                {
                    $aFileUpload[ 'uploader' ] = $_SESSION[ 'user' ];
                    $aFileUpload[ 'title' ]    = $_POST[ 'file-title' ];
                    $aFileUpload[ 'desc' ]     = !empty( $_POST[ 'file-description' ] ) ? $_POST[ 'file-description' ] : '';
                    $aFileUpload[ 'access' ]   = $_POST[ 'file-access' ];
                    $aFileUpload[ 'date' ]     = date( 'Y-m-d H:i:s' );
                    $aFileUpload[ 'tags' ]     = $_POST['file-tags'];

                    $this->SaveFile( $aFileUpload );

                    $aUploadData[ 'success' ] = 'Successfully uploaded file.';
                }
                else
                {
                    $aUploadData[ 'error' ] = $aFileUpload[ 'error' ];
                } 
            }
        }

        return $aUploadData;
    }

    /**
    * Logic for handling file upload.
    **/
    public function UploadFile( $aFile )
    {
        try 
        {
            $aReturn    = array();
            $sUploadDir = '/uploads/';
            $sFileName  = $aFile[ 'name' ];
            $sTempName  = $aFile[ 'tmp_name' ];
            
            if ( !isset( $aFile[ 'error' ] ) || is_array( $aFile[ 'error' ] ) )
            {
                throw new RuntimeException('Invalid parameters.');
            }

            switch ( $aFile[ 'error' ] ) 
            {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    throw new RuntimeException('No file sent.');
                    break;
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new RuntimeException('Exceeded filesize limit.');
                    break;
                default:
                    throw new RuntimeException('Unknown errors.');
            }

            if ( $aFile[ 'size' ] > 10000000 ) 
            {
                throw new RuntimeException( 'Exceeded file size limit of 10MB.' );
            }

            $sMimeType = mime_content_type( $sTempName );
            $sExtension = pathinfo( $sFileName, PATHINFO_EXTENSION );
            
            $aAcceptedTypes = array(
                                'avi' => 'video/x-msvideo',
                                'wmv' => 'video/x-ms-wmv',
                                'mp4' => 'video/mp4',
                                'mov' => 'video/quicktime',
                                'wav' => 'audio/x-wav',
                                'mp3' => 'audio/mpeg',
                                'ogg' => 'audio/ogg',
                                'png' => 'image/png',
                                'jpg' => 'image/jpeg',
                                'gif' => 'image/gif'  
                            );
            
            $sExt = array_search( $sMimeType, $aAcceptedTypes, true );
            if ( $sExt === false )
            {
                throw new RuntimeException( 'Invalid file format.' );
            }
            
            $sDate        = date( 'mdYHis' );
            $sNewName     = md5( $sFileName ) . $sDate . '.' . $sExt;
            $sDestination = $sUploadDir . $sNewName;

            $bFileMoved   = move_uploaded_file( $sTempName, sBASE_DIR . $sDestination );
            if( $bFileMoved )
            {
                $aReturn[ 'new-name' ] = $sNewName;
                $aReturn[ 'name' ]     = $sFileName;
                $aReturn[ 'type' ]     = $sExt;
            }
            else
            {
                throw new RuntimeException( 'Failed to move uploaded file.' );
            }

            return $aReturn;
        } 
        catch( RuntimeException $e ) 
        {
            $aReturn = array();
            $aReturn[ 'error' ] = $e->getMessage();

            return $aReturn;
        }
    }

    /**
    * Logic for saving file to database.
    **/
    public function SaveFile( array $aFile )
    {
        $sSaveFile = "INSERT INTO media
                      ( uploader, name, location, type, title, description, access, upload_date )
                      VALUES
                      ( :uploader, :name, :location, :type, :title, 
                        :description, :access, :upload_date )";

        $aBind = array( ':uploader'    => $aFile[ 'uploader' ],
                        ':name'        => $aFile[ 'name' ],
                        ':location'    => $aFile[ 'new-name' ],
                        ':type'        => $aFile[ 'type' ],
                        ':title'       => $aFile[ 'title' ],
                        ':description' => $aFile[ 'desc' ],
                        ':access'      => $aFile[ 'access' ],
                        ':upload_date' => $aFile[ 'date' ] );

        $this->oDb->RunQuery( $sSaveFile, $aBind );

        $mediaId = $this->oDb->GetLastId();

        foreach(explode(",", $aFile['tags']) as $tag) {
            $sInsertTag = "INSERT INTO tag (tag, media_id) VALUES (:tag, :media_id)";

            $aBind = array(':tag' => trim($tag),
                ':media_id' => $mediaId);

            $this->oDb->RunQuery($sInsertTag, $aBind);
        }
    }

    /**
    * Logic for downloading files.
    **/ 
    public function HandleDownload()
    {
        $sFile = isset( $_GET[ 'file' ] ) ? $_GET[ 'file' ] : '';

        $aPath = pathinfo( $sFile );
        $sName = $aPath[ 'basename' ];
        $sExt  = $aPath[ 'extension' ];
        $sPath = './uploads/' . $sName;

        $aContentTypes = array(
                                'avi' => 'video/x-msvideo',
                                'wmv' => 'video/x-ms-wmv',
                                'mp4' => 'video/mp4',
                                'mov' => 'video/quicktime',
                                'wav' => 'audio/x-wav',
                                'mp3' => 'audio/mpeg',
                                'ogg' => 'audio/ogg',
                                'png' => 'image/png',
                                'jpg' => 'image/jpeg',
                                'gif' => 'image/gif'  
                            );
        
        $sDefaultType = 'application/octet-stream';

        $sContentType = isset( $aContentTypes[ $sExt ] ) ? $aContentTypes[ $sExt ] : $sDefaultType;

        if( is_file( $sPath ) )
        {
            header( 'Pragma: public' );
            header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
            header( 'Content-Disposition: attachment; filename=' . $sName );
            header( 'Content-Type: ' . $sContentType );
            
            ob_clean();
            flush();
            
            readfile( $sPath );
        }
        else
        {
            header( 'HTTP/1.0 404 Not Found' );
        }
    }

    /**
    * Handle browse for media files
    **/
    public function HandleBrowse($_GET) {
        // if search is seach perform search
        if (isset($_GET['search'])) {
            $sGetSearch = "SELECT * FROM media WHERE 
                (title LIKE '%" . $_GET['search'] . "%' OR
                description LIKE '%" . $_GET['search'] . "%') AND
                (access = 'public' OR
                uploader = :user)";

            $aBind = array(':user' => $_SESSION['user']);

            $aSearchData = $this->oDb->GetQueryResults( $sGetSearch, $aBind );

            return $aSearchData;
        }
    }

    /**
    * Handle logic for view media page.
    **/
    public function HandleView()
    {
        $aViewData = array();

        if( isset( $_GET[ 'media' ] ) )
        {
            $iMediaId = !empty( $_GET[ 'media' ] ) ? $_GET[ 'media' ] : '';

            // check for submitted comment
            if( isset( $_POST[ 'submit-comment' ] ) )
            {
                // save comment.
                if( !empty( $_POST[ 'comment' ] ) )
                {
                    $sComment = $_POST[ 'comment' ];
                    $this->SaveComment( $sComment, $iMediaId );
                }
            }

            // increment view count
            $sIncrViews = "UPDATE media SET views = views + 1 WHERE id = :id";

            $aBind = array( ':id' => $iMediaId );

            $this->oDb->RunQuery( $sIncrViews, $aBind );

            $aViewData = $this->GetMedia( $iMediaId );
        }

        return $aViewData;
    }

    /**
    * Get media information for media id.
    **/
    public function GetMedia( $iMediaId )
    {
        $aMediaData = array();

        // get media data
        $sGetMedia = "SELECT * FROM media WHERE id = :id";

        $aBind = array( ':id' => $iMediaId );

        $aMediaData = $this->oDb->GetSingleQueryResults( $sGetMedia, $aBind );

        // get media comments
        $sGetComments = "SELECT * FROM comment WHERE media_id = :id ORDER BY timestamp DESC";

        $aBind = array( ':id' => $iMediaId );

        $aComments = $this->oDb->GetQueryResults( $sGetComments, $aBind );

        $aMediaData[ 'comments' ] = $aComments;

        return $aMediaData;
    }

    /**
    * Logic for saving media comments.
    **/
    public function SaveComment( $sComment, $iMediaId )
    {
        $sSaveComment = "INSERT INTO comment
                         ( media_id, user_id, comment, timestamp )
                         VALUES
                         ( :media_id, :user_id, :comment, :timestamp )";

        $aBind = array( ':media_id'  => $iMediaId,
                        ':user_id'   => $_SESSION[ 'user' ],
                        ':comment'   => $sComment,
                        ':timestamp' => date( 'Y-m-d H:i:s' ) );

        $this->oDb->RunQuery( $sSaveComment, $aBind );
    }

}

?>