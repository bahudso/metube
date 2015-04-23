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
}

?>