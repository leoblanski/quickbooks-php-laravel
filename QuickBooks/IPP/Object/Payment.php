<?php

QuickBooks_Loader::load('/QuickBooks/IPP/Object.php');

class QuickBooks_IPP_Object_Payment extends QuickBooks_IPP_Object
{
    protected function _defaults()
    {
        return [
            //'TypeOf' => 'Person',
            ];
    }
    
    protected function _order()
    {
        return [
            'Id' => true,
            'SyncToken' => true,
            'MetaData' => true,
            'CustomField' => true,
            'Header' => true,
            'Line' => true,
            ];
    }
}
