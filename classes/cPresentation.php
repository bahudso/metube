<?php

require_once 'libs/cTemplate.php';

class cPresentation
{
    protected $oTemplate;

    public function __construct()
    {
        $this->oTemplate = new cTemplate();
    }

    public function GetIndexPage( $aIndexData )
    {
        $aIndexPage = array();

        $aIndexPage[ 'template' ] = 'layout.html';

        $sIndexHTML = $this->oTemplate->PopulateTemplate( $aIndexPage );

        return $sIndexHTML;
    }
}

?>
