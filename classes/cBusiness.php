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

        $sGetMedia = "SELECT * FROM media ORDER BY upload_date DESC LIMIT 6";

        $aMedia = $this->oDb->GetQueryResults( $sGetMedia );

        $aIndexData['media'] = $aMedia;

        return $aIndexData;
    }
}

?>
