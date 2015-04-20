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

        $aIndexPage[ 'template' ] = 'layout.html';

        $sIndexHTML = $this->oTemplate->PopulateTemplate( $aIndexPage );

        return $sIndexHTML;
    }

    public function BuildPage( array $aPage )
    {
        $aLayout = array();

        $aLayout[ 'template' ] = 'layout.html';

        $aLayout[ '_:_CONTENT_:_' ] = $aPage;

        $sLayoutHTML = $this->oTemplate->PopulateTemplate( $aLayout );

        return $sLayoutHTML;
    }
}

?>
