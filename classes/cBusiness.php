<?php

require_once 'libs/cDatabase.php';

class cBusiness
{
    private $oDb;

    public function __construct()
    {
        $this->oDb = new cDatabase();
    }

    public function HandleIndex()
    {
        $aIndexData = array();

        return $aIndexData;
    }
}

?>