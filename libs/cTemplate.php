<?php
require_once 'config.php';

class cTemplate
{
    private $sTemplateDir = null;

    public function __construct()
    {
        $this->sTemplateDir = sBASE_DIR . '/templates/';
    }

    public function SetTemplateDir( $sNewDir = '' )
    {
        $this->sTemplateDir = sBASE_DIR . '/templates/' . $sNewDir . '/';
    }

    public function PopulateTemplate( $aTemplateData )
    {
        try
        {
            if( !isset( $aTemplateData[ 'template' ] ) )
            {
                throw new Exception( 'Template not specified.' );
            }

            $sTemplateFile = $this->sTemplateDir . $aTemplateData[ 'template' ];

            if( !file_exists( $sTemplateFile ) )
            {
                throw new Exception( 'Template does not exist.' );
            }

            $sContents = file_get_contents( $sTemplateFile );

            foreach( $aTemplateData as $sKey => $vVal )
            {
                if( is_array( $vVal ) )
                {
                    $aTemplateData[ $sKey ] = $this->PopulateTemplate( $aTemplateData[ $sKey ] );
                    $sContents = str_replace( $sKey, $aTemplateData[ $sKey ], $sContents );
                }
                else
                {
                    $sContents = str_replace( $sKey, $vVal, $sContents );
                }
            }
            
            return $sContents;
        }
        catch( Exception $e )
        {
            
        }
    }
}

?>