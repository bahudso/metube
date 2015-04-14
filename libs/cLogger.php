<?php
/**
 * Logger class
 *
 * Handles logging
 *
 * @author  Dylan Pruitt
 */
class cLogger
{
    private $sLogDir = 'logs';

    private $sLogFile = 'log.txt';

    public function __construct()
    {

    }

    public function SetLogFile( $sLogFile )
    {
        $this->sLogFile = $sLogFile;
    }

    public function Write( $sMessage )
    {
        $sLogFile = $sLogDir . DIRECTORY_SEPARATOR . $sLogFile;

        $sToday = getdate();
        $sLogMessage = $sToday . ' ' . $sMessage;

        // write to the log file.
        file_put_contents( $sLogFile, $sLogMessage, FILE_APPEND );
    }
}