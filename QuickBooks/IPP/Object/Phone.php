<?php

QuickBooks_Loader::load('/QuickBooks/IPP/Object.php');

class QuickBooks_IPP_Object_Phone extends QuickBooks_IPP_Object
{
    public const DEVICETYPE_LANDLINE = 'LandLine';

    public const DEVICETYPE_MOBILE = 'Mobile';

    public const DEVICETYPE_FAX = 'Fax';

    protected function _order()
    {
        return [
            'Id' => true,
            'DeviceType' => true,
            'CountryCode' => true,
            'AreaCode' => true,
            'ExchangeCode' => true,
            'Extension' => true,
            'FreeFormNumber' => true,
            'PIN' => true,
            'Default' => true,
            'Tag' => true,
            ];
    }
}
