<?php
/**
 * Database Class
 *
 * Handles common database functionality.
 *
 * @author  Dylan Pruitt
 */
class cDatabase
{
    private $oConnection = null;

    public function __construct()
    {
        try
        {
            $this->oConnection = new PDO( 'mysql:host=mysql1.cs.clemson.edu;dbname=metube_up08', 'metube_1tig', 'metube666' );
        }
        catch( Exception $e )
        {
            throw new Exception( 'Could not connect to database.' );
        }
    }

    public function RunQuery( $sSQL, $aBind = array() )
    {
        try
        {
            $oStatement = $this->oConnection->prepare( $sSQL );
            $oStatement = $this->BindValues( $oStatement, $aBind );
            
            $bReturn = $oStatement->execute();

            return $bReturn;
        }
        catch( Exception $e )
        {
            throw new Exception( 'Could not execute query.' );
        }
    }

    public function GetQueryResults( $sSQL, $aBind = array() )
    {
        try
        {
            $oStatement = $this->oConnection->prepare( $sSQL );
            $oStatement = $this->BindValues( $oStatement, $aBind );

            $oStatement->execute();

            $aResults = $oStatement->fetchAll( PDO::FETCH_ASSOC );

            return $aResults;
        }
        catch( Exception $e )
        {
            throw new Exception( 'Could not execute query.' );
        }
    }

    /**
     * Get last inserted id.
     *
     * @return int
     */
    public function GetLastID()
    {
        try
        {
            return $this->oConnection->lastInsertId();
        }
        catch( Exception $e )
        {
            throw new Exception( 'Could not execute query.' );
        }
    }

    public function BindValues( $oStatement, $aBind )
    {
        try
        {
            foreach( $aBind as $sKey => $vVal )
            {
                
                $vParam = 0;
                if( is_int( $vVal ) ) 
                {
                    $vParam = PDO::PARAM_INT;
                } 
                elseif( is_bool( $vVal ) ) 
                {
                    $vParam = PDO::PARAM_BOOL;
                } 
                elseif( is_null( $vVal ) ) 
                {
                    $vParam = PDO::PARAM_NULL;
                } 
                elseif( is_string( $vVal ) ) 
                {
                    $vParam = PDO::PARAM_STR;
                } 
                else 
                {
                    $vParam = FALSE;
                }

                if( $vParam )
                {
                    $bSuccess = $oStatement->bindValue( $sKey, $vVal, $vParam );
                }
            }
            
            return $oStatement;
        }
        catch( Exception $e )
        {
            throw new Exception( 'Could not bind query values.' );
        }
    }
}

?>