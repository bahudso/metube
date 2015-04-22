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
                $aFileUpload = $this->UploadFile( $_FILES[ 'file' ] );

                if( !isset( $aFileUpload[ 'error' ] ) )
                {
                    $aFileUpload[ 'uploader' ] = $_SESSION[ 'user' ];
                    $aFileUpload[ 'title' ]    = $_POST[ 'file-title' ];
                    $aFileUpload[ 'desc' ]     = !empty( $_POST[ 'file-desc' ] ) ? $_POST[ 'file-desc' ] : '';
                    $aFileUpload[ 'access' ]   = $_POST[ 'file-access' ];
                    $aFileUpload[ 'date' ]     = date( 'Y-m-d H:i:s' );

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
                $aReturn[ 'error' ] = 'Invalid parameters.';
            }

            switch ( $aFile[ 'error' ] ) 
            {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $aReturn[ 'error' ] ='No file sent.';
                    break;
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $aReturn[ 'error' ] = 'Exceeded filesize limit.';
                    break;
                default:
                    $aReturn[ 'error' ] = 'Unknown errors.';
            }

            if ( $aFile[ 'size' ] > 10000000 ) 
            {
                $aReturn[ 'error' ] = 'Exceeded file size limit of 10MB.';
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
                                'oga' => 'audio/ogg',
                                'png' => 'image/png',
                                'jpg' => 'image/jpeg',
                                'gif' => 'image/gif'  
                            );
            
            $sExt = array_search( $sMimeType, $aAcceptedTypes, true );
            if ( $sExt === false )
            {
                $aReturn[ 'error' ] = 'Invalid file format.';
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
                $aReturn[ 'error' ] = 'Failed to move uploaded file.';
            }

            return $aReturn;
        } 
        catch( RuntimeException $e ) 
        {
            cLogger::Write( $e );
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
    }
}

?>