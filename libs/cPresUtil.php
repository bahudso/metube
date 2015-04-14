<?php
/**
 * Presentation utilities class.
 *
 * Useful shared functionality for presentation.
 *
 * @author  Dylan Pruitt
 */
class cPresUtil
{
    public function BuildSelectOptions( $aOptionData, $sVal, $sOpt, $sSelected = null )
    {
        $sOptionString = '';
        foreach( $aOptionData as $aOption )
        {
            $sValue  = $aOption[ $sVal ];
            $sOption = $aOption[ $sOpt ];

            if( $sValue == $sSelected )
            {
                $sOptionString .= "<option value='$sValue' selected>$sOption</option>";
            }
            else
            {
                $sOptionString .= "<option value='$sValue'>$sOption</option>";
            }
        }

        return $sOptionString;
    }
}