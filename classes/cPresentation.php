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
