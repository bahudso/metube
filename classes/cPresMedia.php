<?php

/* User presentation class */

require_once 'cPresentation.php';

class cPresMedia extends cPresentation
{
    public function __construct()
    {
        parent::__construct();
    }

   /**
    *
    **/
    public function GetUploadPage( array $aUploadData )
    {
        $aUploadPage = array();
        $aUploadPage[ 'template' ] = 'media/upload.html';

        $aUploadPage[ '_:_FEEDBACK_:_' ] = '';
        $aUploadPage[ '_:_FEEDBACK-TYPE_:_' ] = '';

        if( isset( $aUploadData[ 'success' ] ) )
        {
            $aUploadPage[ '_:_FEEDBACK-TYPE_:_' ] = 'success';
            $aUploadPage[ '_:_FEEDBACK_:_' ]      = $aUploadData[ 'success' ];
        }
        if( isset( $aUploadData[ 'error' ] ) )
        {
            $aUploadPage[ '_:_FEEDBACK-TYPE_:_' ] = 'error';
            $aUploadPage[ '_:_FEEDBACK_:_' ]      = $aUploadData[ 'error' ];
        }

        $sUploadHTML = $this->BuildPage( $aUploadPage );

        return $sUploadHTML;
    }

    /**
    * build browse page template
    **/
    public function GetBrowsePage($aBrowseData) {
        $aBrowsePage = array();
        $aBrowsePage['template'] = 'browse.html';

        $sResults = '';
        if (count($aBrowseData) == 0) {
            $sResults = "<li class='panel'>No results</li>";
        } else {
            foreach($aBrowseData as $result) {
                $sResults .= "<li class='panel'><a href='view.php?id=" . $result['id'] . "'>" . $result['title'] . "</a></li>";
            }
        }

        $aBrowsePage['_:_RESULTS_:_'] = $sResults;

        $sBrowseHTML = $this->BuildPage( $aBrowsePage );

        return $sBrowseHTML;
    }

    /**
    * Build view media page.
    **/
    public function GetViewPage( $aViewData )
    {
        $aViewPage = array();
        $aViewPage[ 'template' ] = 'media/view.html';

        $aViewPage[ '_:_TITLE_:_' ] = $aViewData[ 'title' ];
        $aViewPage[ '_:_DESC_:_' ]  = !empty( $aViewData[ 'description' ] ) ?
                                      $aViewData[ 'description' ] : 'No description provided.';
        $aViewPage[ '_:_VIEWS_:_' ] = $aViewData[ 'views' ];

        // convert datetime to readable date
        $sDate = strtotime( $aViewData[ 'upload_date' ] );
        $sDate = date( 'M d, Y', $sDate );

        $aViewPage[ '_:_DATE_:_' ]  = $sDate;

        // get correct media player.
        $aViewPage[ '_:_PLAYER_:_' ] = $this->BuildMediaPlayer( $aViewData[ 'location' ], $aViewData[ 'type' ] );

        $aViewPage[ '_:_COMMENT-FORM_:_' ] = '';
        $aViewPage[ '_:_COMMENTS_:_' ] = '';

        $sViewHTML = $this->BuildPage( $aViewPage );

        return $sViewHTML;
    }

    public function BuildMediaPlayer( $sFile, $sExt )
    {
        $aPlayer = array();

        switch( $sExt )
        {
            case 'avi':
                $aPlayer[ '_:_TYPE_:_' ] = 'video/x-msvideo';
                $aPlayer[ 'template' ]   = 'media/view-video.html';
                break;
            case 'wmv':
                $aPlayer[ '_:_TYPE_:_' ] = 'video/x-ms-wmv';
                $aPlayer[ 'template' ]   = 'media/view-video.html';
                break;
            case 'mp4':
                $aPlayer[ '_:_TYPE_:_' ] = 'video/mp4';
                $aPlayer[ 'template' ]   = 'media/view-video.html';
                break;
            case 'mov':
                $aPlayer[ '_:_TYPE_:_' ] = 'video/quicktime';
                $aPlayer[ 'template' ]   = 'media/view-video.html';
                break;
            case 'wav':
                $aPlayer[ '_:_TYPE_:_' ] = 'audio/x-wav';
                $aPlayer[ 'template' ]   = 'media/view-audio.html';
                break;
            case 'mp3':
                $aPlayer[ '_:_TYPE_:_' ] = 'audio/mpeg';
                $aPlayer[ 'template' ]   = 'media/view-audio.html';
                break;
            case 'ogg':
                $aPlayer[ '_:_TYPE_:_' ] = 'audio/ogg';
                $aPlayer[ 'template' ]   = 'media/view-audio.html';
                break;
            case 'png':
            case 'jpg':
            case 'gif':
                $aPlayer[ 'template' ] = 'media/view-image.html';
                break;
        }

        $aPlayer[ '_:_SOURCE_:_' ] = './uploads/' . $sFile;

        $sPlayerHTML = $this->oTemplate->PopulateTemplate( $aPlayer );

        return $sPlayerHTML;
    }
}

?>