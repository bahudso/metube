<?php

require_once 'libs/cDatabase.php';

class cBusiness
{
    protected $oDb;

    public function __construct()
    {
        $this->oDb = new cDatabase();
    }

    public function HandleIndex()
    {
        $aIndexData = array();

        $sGetUsers = "SELECT * FROM user";

        $aUsers = $this->oDb->GetQueryResults( $sGetUsers );

        return $aIndexData;
    }
}

?>
