<?php

require_once 'libs/cTemplate.php';

class cPresentation
{
    protected $oTemplate;

    public function __construct()
    {
        $this->oTemplate = new cTemplate();
    }

    public function GetIndexPage( array $aIndexData )
    {
        $aIndexPage = array();

        $aIndexPage[ 'template' ] = 'index.html';

        $sMedia = '';
        foreach($aIndexData['media'] as $media) {
            $aMedia = array();
            $aMedia[ 'template' ] = 'media/item.html';
            $aMedia[ '_:_TITLE_:_' ] = $media[ 'title' ];
            $aMedia[ '_:_DESCR_:_' ]    = $media[ 'description' ];
            $sMedia .= $this->oTemplate->PopulateTemplate( $aMedia );
        }

        $aIndexPage[ '_:_MEDIA_:_' ] = $sMedia;

        $sIndexHTML = $this->BuildPage( $aIndexPage );

        return $sIndexHTML;
    }

    public function BuildPage( array $aPage )
    {
        $aLayout = array();

        if (isset($_SESSION['user'])) {
            $aLayout[ 'template' ] = 'user/layout.html';
        } else {
            $aLayout[ 'template' ] = 'layout.html';
        }

        $aLayout[ '_:_CONTENT_:_' ] = $aPage;

        $sLayoutHTML = $this->oTemplate->PopulateTemplate( $aLayout );

        return $sLayoutHTML;
    }
}

?>
