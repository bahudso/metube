<?php

define( 'sBASE_DIR', dirname(__FILE__) );

date_default_timezone_set( 'America/New_York' );

// Error reporting
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

// Debug Functions
function dv()
{
    $args = func_get_args();
    foreach( $args as $arg )
    {
        echo '<pre>';
        print_r( $arg );
        echo '</pre>';

        $aBacktrace = debug_backtrace();
        echo 'Called from ' . $aBacktrace[0][ 'file' ] . ' on line ' . $aBacktrace[0][ 'line' ];
    }
    
    die();
}

function v()
{
    $args = func_get_args();
    foreach( $args as $arg )
    {
        echo '<pre>';
        print_r( $arg );
        echo '</pre>';

        $aBacktrace = debug_backtrace();
        echo 'Called from ' . $aBacktrace[0][ 'file' ] . ' on line ' . $aBacktrace[0][ 'line' ];
    }
}
?>