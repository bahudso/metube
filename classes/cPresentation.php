<?php

require_once 'libs/cTemplate.php';

class cPresentation
{
    private $oTemplate;

    public function __construct()
    {
        $this->oTemplate = new cTemplate();
    }

    public function GetIndexPage( $aIndexData )
    {
        $aIndexPage = array();

        $aIndexPage[ 'template' ] = 'index.html';

        $sIndexHTML = $this->oTemplate->PopulateTemplate( $aIndexPage );

        return $sIndexHTML;
    }
}

?>