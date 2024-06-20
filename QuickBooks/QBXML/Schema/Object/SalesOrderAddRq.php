<?php

/**
 * Schema object for: SalesOrderAddRq
 *
 * @author "Keith Palmer Jr." <Keith@ConsoliByte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage QBXML
 */

/**
 *
 */
require_once __DIR__ . '/QuickBooks.php';

/**
 *
 */
require_once __DIR__ . '/QuickBooks/QBXML/Schema/Object.php';

/**
 *
 */
class QuickBooks_QBXML_Schema_Object_SalesOrderAddRq extends QuickBooks_QBXML_Schema_Object
{
    protected function &_qbxmlWrapper()
    {
        static $wrapper = 'SalesOrderAdd';

        return $wrapper;
    }

    protected function &_dataTypePaths()
    {
        static $paths =  [
  'CustomerRef ListID' => 'IDTYPE',
  'CustomerRef FullName' => 'STRTYPE',
  'ClassRef ListID' => 'IDTYPE',
  'ClassRef FullName' => 'STRTYPE',
  'TemplateRef ListID' => 'IDTYPE',
  'TemplateRef FullName' => 'STRTYPE',
  'TxnDate' => 'DATETYPE',
  'RefNumber' => 'STRTYPE',
  'BillAddress Addr1' => 'STRTYPE',
  'BillAddress Addr2' => 'STRTYPE',
  'BillAddress Addr3' => 'STRTYPE',
  'BillAddress Addr4' => 'STRTYPE',
  'BillAddress Addr5' => 'STRTYPE',
  'BillAddress City' => 'STRTYPE',
  'BillAddress State' => 'STRTYPE',
  'BillAddress PostalCode' => 'STRTYPE',
  'BillAddress Country' => 'STRTYPE',
  'BillAddress Note' => 'STRTYPE',
  'ShipAddress Addr1' => 'STRTYPE',
  'ShipAddress Addr2' => 'STRTYPE',
  'ShipAddress Addr3' => 'STRTYPE',
  'ShipAddress Addr4' => 'STRTYPE',
  'ShipAddress Addr5' => 'STRTYPE',
  'ShipAddress City' => 'STRTYPE',
  'ShipAddress State' => 'STRTYPE',
  'ShipAddress PostalCode' => 'STRTYPE',
  'ShipAddress Country' => 'STRTYPE',
  'ShipAddress Note' => 'STRTYPE',
  'PONumber' => 'STRTYPE',
  'TermsRef ListID' => 'IDTYPE',
  'TermsRef FullName' => 'STRTYPE',
  'DueDate' => 'DATETYPE',
  'SalesRepRef ListID' => 'IDTYPE',
  'SalesRepRef FullName' => 'STRTYPE',
  'FOB' => 'STRTYPE',
  'ShipDate' => 'DATETYPE',
  'ShipMethodRef ListID' => 'IDTYPE',
  'ShipMethodRef FullName' => 'STRTYPE',
  'ItemSalesTaxRef ListID' => 'IDTYPE',
  'ItemSalesTaxRef FullName' => 'STRTYPE',
  'IsManuallyClosed' => 'BOOLTYPE',
  'Memo' => 'STRTYPE',
  'CustomerMsgRef ListID' => 'IDTYPE',
  'CustomerMsgRef FullName' => 'STRTYPE',
  'IsToBePrinted' => 'BOOLTYPE',
  'IsToBeEmailed' => 'BOOLTYPE',
  'IsTaxIncluded' => 'BOOLTYPE',
  'CustomerSalesTaxCodeRef ListID' => 'IDTYPE',
  'CustomerSalesTaxCodeRef FullName' => 'STRTYPE',
  'Other' => 'STRTYPE',
  'SalesOrderLineAdd ItemRef ListID' => 'IDTYPE',
  'SalesOrderLineAdd ItemRef FullName' => 'STRTYPE',
  'SalesOrderLineAdd Desc' => 'STRTYPE',
  'SalesOrderLineAdd Quantity' => 'QUANTYPE',
  'SalesOrderLineAdd UnitOfMeasure' => 'STRTYPE',
  'SalesOrderLineAdd Rate' => 'PRICETYPE',
  'SalesOrderLineAdd RatePercent' => 'PERCENTTYPE',
  'SalesOrderLineAdd PriceLevelRef ListID' => 'IDTYPE',
  'SalesOrderLineAdd PriceLevelRef FullName' => 'STRTYPE',
  'SalesOrderLineAdd ClassRef ListID' => 'IDTYPE',
  'SalesOrderLineAdd ClassRef FullName' => 'STRTYPE',
  'SalesOrderLineAdd Amount' => 'AMTTYPE',
  'SalesOrderLineAdd InventorySiteRef ListID' => 'IDTYPE',
  'SalesOrderLineAdd InventorySiteRef FullName' => 'STRTYPE',
  'SalesOrderLineAdd SalesTaxCodeRef ListID' => 'IDTYPE',
  'SalesOrderLineAdd SalesTaxCodeRef FullName' => 'STRTYPE',
  'SalesOrderLineAdd IsManuallyClosed' => 'BOOLTYPE',
  'SalesOrderLineAdd Other1' => 'STRTYPE',
  'SalesOrderLineAdd Other2' => 'STRTYPE',
  'SalesOrderLineAdd DataExt OwnerID' => 'GUIDTYPE',
  'SalesOrderLineAdd DataExt DataExtName' => 'STRTYPE',
  'SalesOrderLineAdd DataExt DataExtValue' => 'STRTYPE',
  'SalesOrderLineGroupAdd ItemGroupRef ListID' => 'IDTYPE',
  'SalesOrderLineGroupAdd ItemGroupRef FullName' => 'STRTYPE',
  'SalesOrderLineGroupAdd Desc' => 'STRTYPE',
  'SalesOrderLineGroupAdd Quantity' => 'QUANTYPE',
  'SalesOrderLineGroupAdd UnitOfMeasure' => 'STRTYPE',
  'SalesOrderLineGroupAdd DataExt OwnerID' => 'GUIDTYPE',
  'SalesOrderLineGroupAdd DataExt DataExtName' => 'STRTYPE',
  'SalesOrderLineGroupAdd DataExt DataExtValue' => 'STRTYPE',
  'IncludeRetElement' => 'STRTYPE',
];

        return $paths;
    }

    protected function &_maxLengthPaths()
    {
        static $paths =  [
  'CustomerRef ListID' => 0,
  'CustomerRef FullName' => 209,
  'ClassRef ListID' => 0,
  'ClassRef FullName' => 209,
  'TemplateRef ListID' => 0,
  'TemplateRef FullName' => 209,
  'TxnDate' => 0,
  'RefNumber' => 11,
  'BillAddress Addr1' => 41,
  'BillAddress Addr2' => 41,
  'BillAddress Addr3' => 41,
  'BillAddress Addr4' => 41,
  'BillAddress Addr5' => 41,
  'BillAddress City' => 31,
  'BillAddress State' => 21,
  'BillAddress PostalCode' => 13,
  'BillAddress Country' => 31,
  'BillAddress Note' => 41,
  'ShipAddress Addr1' => 41,
  'ShipAddress Addr2' => 41,
  'ShipAddress Addr3' => 41,
  'ShipAddress Addr4' => 41,
  'ShipAddress Addr5' => 41,
  'ShipAddress City' => 31,
  'ShipAddress State' => 21,
  'ShipAddress PostalCode' => 13,
  'ShipAddress Country' => 31,
  'ShipAddress Note' => 41,
  'PONumber' => 25,
  'TermsRef ListID' => 0,
  'TermsRef FullName' => 209,
  'DueDate' => 0,
  'SalesRepRef ListID' => 0,
  'SalesRepRef FullName' => 209,
  'FOB' => 13,
  'ShipDate' => 0,
  'ShipMethodRef ListID' => 0,
  'ShipMethodRef FullName' => 209,
  'ItemSalesTaxRef ListID' => 0,
  'ItemSalesTaxRef FullName' => 209,
  'IsManuallyClosed' => 0,
  'Memo' => 4095,
  'CustomerMsgRef ListID' => 0,
  'CustomerMsgRef FullName' => 209,
  'IsToBePrinted' => 0,
  'IsToBeEmailed' => 0,
  'IsTaxIncluded' => 0,
  'CustomerSalesTaxCodeRef ListID' => 0,
  'CustomerSalesTaxCodeRef FullName' => 209,
  'Other' => 29,
  'SalesOrderLineAdd ItemRef ListID' => 0,
  'SalesOrderLineAdd ItemRef FullName' => 209,
  'SalesOrderLineAdd Desc' => 4095,
  'SalesOrderLineAdd Quantity' => 0,
  'SalesOrderLineAdd UnitOfMeasure' => 31,
  'SalesOrderLineAdd Rate' => 0,
  'SalesOrderLineAdd RatePercent' => 0,
  'SalesOrderLineAdd PriceLevelRef ListID' => 0,
  'SalesOrderLineAdd PriceLevelRef FullName' => 209,
  'SalesOrderLineAdd ClassRef ListID' => 0,
  'SalesOrderLineAdd ClassRef FullName' => 209,
  'SalesOrderLineAdd Amount' => 0,
  'SalesOrderLineAdd InventorySiteRef ListID' => 0,
  'SalesOrderLineAdd InventorySiteRef FullName' => 209,
  'SalesOrderLineAdd SalesTaxCodeRef ListID' => 0,
  'SalesOrderLineAdd SalesTaxCodeRef FullName' => 209,
  'SalesOrderLineAdd IsManuallyClosed' => 0,
  'SalesOrderLineAdd Other1' => 29,
  'SalesOrderLineAdd Other2' => 29,
  'SalesOrderLineAdd DataExt OwnerID' => 0,
  'SalesOrderLineAdd DataExt DataExtName' => 31,
  'SalesOrderLineAdd DataExt DataExtValue' => 0,
  'SalesOrderLineGroupAdd ItemGroupRef ListID' => 0,
  'SalesOrderLineGroupAdd ItemGroupRef FullName' => 209,
  'SalesOrderLineGroupAdd Desc' => 4095,
  'SalesOrderLineGroupAdd Quantity' => 0,
  'SalesOrderLineGroupAdd UnitOfMeasure' => 31,
  'SalesOrderLineGroupAdd DataExt OwnerID' => 0,
  'SalesOrderLineGroupAdd DataExt DataExtName' => 31,
  'SalesOrderLineGroupAdd DataExt DataExtValue' => 0,
  'IncludeRetElement' => 50,
];

        return $paths;
    }

    protected function &_isOptionalPaths()
    {
        static $paths =  [
  'CustomerRef ListID' => true,
  'CustomerRef FullName' => true,
  'ClassRef ListID' => true,
  'ClassRef FullName' => true,
  'TemplateRef ListID' => true,
  'TemplateRef FullName' => true,
  'TxnDate' => true,
  'RefNumber' => true,
  'BillAddress Addr1' => true,
  'BillAddress Addr2' => true,
  'BillAddress Addr3' => true,
  'BillAddress Addr4' => true,
  'BillAddress Addr5' => true,
  'BillAddress City' => true,
  'BillAddress State' => true,
  'BillAddress PostalCode' => true,
  'BillAddress Country' => true,
  'BillAddress Note' => true,
  'ShipAddress Addr1' => true,
  'ShipAddress Addr2' => true,
  'ShipAddress Addr3' => true,
  'ShipAddress Addr4' => true,
  'ShipAddress Addr5' => true,
  'ShipAddress City' => true,
  'ShipAddress State' => true,
  'ShipAddress PostalCode' => true,
  'ShipAddress Country' => true,
  'ShipAddress Note' => true,
  'PONumber' => true,
  'TermsRef ListID' => true,
  'TermsRef FullName' => true,
  'DueDate' => true,
  'SalesRepRef ListID' => true,
  'SalesRepRef FullName' => true,
  'FOB' => true,
  'ShipDate' => true,
  'ShipMethodRef ListID' => true,
  'ShipMethodRef FullName' => true,
  'ItemSalesTaxRef ListID' => true,
  'ItemSalesTaxRef FullName' => true,
  'IsManuallyClosed' => true,
  'Memo' => true,
  'CustomerMsgRef ListID' => true,
  'CustomerMsgRef FullName' => true,
  'IsToBePrinted' => true,
  'IsToBeEmailed' => true,
  'IsTaxIncluded' => true,
  'CustomerSalesTaxCodeRef ListID' => true,
  'CustomerSalesTaxCodeRef FullName' => true,
  'Other' => true,
  'SalesOrderLineAdd ItemRef ListID' => true,
  'SalesOrderLineAdd ItemRef FullName' => true,
  'SalesOrderLineAdd Desc' => true,
  'SalesOrderLineAdd Quantity' => true,
  'SalesOrderLineAdd UnitOfMeasure' => true,
  'SalesOrderLineAdd Rate' => false,
  'SalesOrderLineAdd RatePercent' => false,
  'SalesOrderLineAdd PriceLevelRef ListID' => true,
  'SalesOrderLineAdd PriceLevelRef FullName' => true,
  'SalesOrderLineAdd ClassRef ListID' => true,
  'SalesOrderLineAdd ClassRef FullName' => true,
  'SalesOrderLineAdd Amount' => true,
  'SalesOrderLineAdd InventorySiteRef ListID' => true,
  'SalesOrderLineAdd InventorySiteRef FullName' => true,
  'SalesOrderLineAdd SalesTaxCodeRef ListID' => true,
  'SalesOrderLineAdd SalesTaxCodeRef FullName' => true,
  'SalesOrderLineAdd IsManuallyClosed' => true,
  'SalesOrderLineAdd Other1' => true,
  'SalesOrderLineAdd Other2' => true,
  'SalesOrderLineAdd DataExt OwnerID' => false,
  'SalesOrderLineAdd DataExt DataExtName' => false,
  'SalesOrderLineAdd DataExt DataExtValue' => false,
  'SalesOrderLineGroupAdd ItemGroupRef ListID' => true,
  'SalesOrderLineGroupAdd ItemGroupRef FullName' => true,
  'SalesOrderLineGroupAdd Desc' => true,
  'SalesOrderLineGroupAdd Quantity' => true,
  'SalesOrderLineGroupAdd UnitOfMeasure' => true,
  'SalesOrderLineGroupAdd DataExt OwnerID' => false,
  'SalesOrderLineGroupAdd DataExt DataExtName' => false,
  'SalesOrderLineGroupAdd DataExt DataExtValue' => false,
  'IncludeRetElement' => true,
];
    }

    protected function &_sinceVersionPaths()
    {
        static $paths =  [
  'CustomerRef ListID' => 999.99,
  'CustomerRef FullName' => 999.99,
  'ClassRef ListID' => 999.99,
  'ClassRef FullName' => 999.99,
  'TemplateRef ListID' => 999.99,
  'TemplateRef FullName' => 999.99,
  'TxnDate' => 999.99,
  'RefNumber' => 999.99,
  'BillAddress Addr1' => 999.99,
  'BillAddress Addr2' => 999.99,
  'BillAddress Addr3' => 999.99,
  'BillAddress Addr4' => 2,
  'BillAddress Addr5' => 6,
  'BillAddress City' => 999.99,
  'BillAddress State' => 999.99,
  'BillAddress PostalCode' => 999.99,
  'BillAddress Country' => 999.99,
  'BillAddress Note' => 6,
  'ShipAddress Addr1' => 999.99,
  'ShipAddress Addr2' => 999.99,
  'ShipAddress Addr3' => 999.99,
  'ShipAddress Addr4' => 2,
  'ShipAddress Addr5' => 6,
  'ShipAddress City' => 999.99,
  'ShipAddress State' => 999.99,
  'ShipAddress PostalCode' => 999.99,
  'ShipAddress Country' => 999.99,
  'ShipAddress Note' => 6,
  'PONumber' => 999.99,
  'TermsRef ListID' => 999.99,
  'TermsRef FullName' => 999.99,
  'DueDate' => 999.99,
  'SalesRepRef ListID' => 999.99,
  'SalesRepRef FullName' => 999.99,
  'FOB' => 999.99,
  'ShipDate' => 999.99,
  'ShipMethodRef ListID' => 999.99,
  'ShipMethodRef FullName' => 999.99,
  'ItemSalesTaxRef ListID' => 999.99,
  'ItemSalesTaxRef FullName' => 999.99,
  'IsManuallyClosed' => 999.99,
  'Memo' => 999.99,
  'CustomerMsgRef ListID' => 999.99,
  'CustomerMsgRef FullName' => 999.99,
  'IsToBePrinted' => 999.99,
  'IsToBeEmailed' => 6,
  'IsTaxIncluded' => 6,
  'CustomerSalesTaxCodeRef ListID' => 999.99,
  'CustomerSalesTaxCodeRef FullName' => 999.99,
  'Other' => 6,
  'SalesOrderLineAdd ItemRef ListID' => 999.99,
  'SalesOrderLineAdd ItemRef FullName' => 999.99,
  'SalesOrderLineAdd Desc' => 999.99,
  'SalesOrderLineAdd Quantity' => 999.99,
  'SalesOrderLineAdd UnitOfMeasure' => 7,
  'SalesOrderLineAdd Rate' => 999.99,
  'SalesOrderLineAdd RatePercent' => 999.99,
  'SalesOrderLineAdd PriceLevelRef ListID' => 999.99,
  'SalesOrderLineAdd PriceLevelRef FullName' => 999.99,
  'SalesOrderLineAdd ClassRef ListID' => 999.99,
  'SalesOrderLineAdd ClassRef FullName' => 999.99,
  'SalesOrderLineAdd Amount' => 999.99,
  'SalesOrderLineAdd InventorySiteRef ListID' => 999.99,
  'SalesOrderLineAdd InventorySiteRef FullName' => 999.99,
  'SalesOrderLineAdd SalesTaxCodeRef ListID' => 999.99,
  'SalesOrderLineAdd SalesTaxCodeRef FullName' => 999.99,
  'SalesOrderLineAdd IsManuallyClosed' => 999.99,
  'SalesOrderLineAdd Other1' => 6,
  'SalesOrderLineAdd Other2' => 6,
  'SalesOrderLineAdd DataExt OwnerID' => 999.99,
  'SalesOrderLineAdd DataExt DataExtName' => 999.99,
  'SalesOrderLineAdd DataExt DataExtValue' => 999.99,
  'SalesOrderLineGroupAdd ItemGroupRef ListID' => 999.99,
  'SalesOrderLineGroupAdd ItemGroupRef FullName' => 999.99,
  'SalesOrderLineGroupAdd Desc' => 999.99,
  'SalesOrderLineGroupAdd Quantity' => 999.99,
  'SalesOrderLineGroupAdd UnitOfMeasure' => 7,
  'SalesOrderLineGroupAdd DataExt OwnerID' => 999.99,
  'SalesOrderLineGroupAdd DataExt DataExtName' => 999.99,
  'SalesOrderLineGroupAdd DataExt DataExtValue' => 999.99,
  'IncludeRetElement' => 4,
];

        return $paths;
    }

    protected function &_isRepeatablePaths()
    {
        static $paths =  [
  'CustomerRef ListID' => false,
  'CustomerRef FullName' => false,
  'ClassRef ListID' => false,
  'ClassRef FullName' => false,
  'TemplateRef ListID' => false,
  'TemplateRef FullName' => false,
  'TxnDate' => false,
  'RefNumber' => false,
  'BillAddress Addr1' => false,
  'BillAddress Addr2' => false,
  'BillAddress Addr3' => false,
  'BillAddress Addr4' => false,
  'BillAddress Addr5' => false,
  'BillAddress City' => false,
  'BillAddress State' => false,
  'BillAddress PostalCode' => false,
  'BillAddress Country' => false,
  'BillAddress Note' => false,
  'ShipAddress Addr1' => false,
  'ShipAddress Addr2' => false,
  'ShipAddress Addr3' => false,
  'ShipAddress Addr4' => false,
  'ShipAddress Addr5' => false,
  'ShipAddress City' => false,
  'ShipAddress State' => false,
  'ShipAddress PostalCode' => false,
  'ShipAddress Country' => false,
  'ShipAddress Note' => false,
  'PONumber' => false,
  'TermsRef ListID' => false,
  'TermsRef FullName' => false,
  'DueDate' => false,
  'SalesRepRef ListID' => false,
  'SalesRepRef FullName' => false,
  'FOB' => false,
  'ShipDate' => false,
  'ShipMethodRef ListID' => false,
  'ShipMethodRef FullName' => false,
  'ItemSalesTaxRef ListID' => false,
  'ItemSalesTaxRef FullName' => false,
  'IsManuallyClosed' => false,
  'Memo' => false,
  'CustomerMsgRef ListID' => false,
  'CustomerMsgRef FullName' => false,
  'IsToBePrinted' => false,
  'IsToBeEmailed' => false,
  'IsTaxIncluded' => false,
  'CustomerSalesTaxCodeRef ListID' => false,
  'CustomerSalesTaxCodeRef FullName' => false,
  'Other' => false,
  'SalesOrderLineAdd ItemRef ListID' => false,
  'SalesOrderLineAdd ItemRef FullName' => false,
  'SalesOrderLineAdd Desc' => false,
  'SalesOrderLineAdd Quantity' => false,
  'SalesOrderLineAdd UnitOfMeasure' => false,
  'SalesOrderLineAdd Rate' => false,
  'SalesOrderLineAdd RatePercent' => false,
  'SalesOrderLineAdd PriceLevelRef ListID' => false,
  'SalesOrderLineAdd PriceLevelRef FullName' => false,
  'SalesOrderLineAdd ClassRef ListID' => false,
  'SalesOrderLineAdd ClassRef FullName' => false,
  'SalesOrderLineAdd Amount' => false,
  'SalesOrderLineAdd InventorySiteRef ListID' => false,
  'SalesOrderLineAdd InventorySiteRef FullName' => false,
  'SalesOrderLineAdd SalesTaxCodeRef ListID' => false,
  'SalesOrderLineAdd SalesTaxCodeRef FullName' => false,
  'SalesOrderLineAdd IsManuallyClosed' => false,
  'SalesOrderLineAdd Other1' => false,
  'SalesOrderLineAdd Other2' => false,
  'SalesOrderLineAdd DataExt OwnerID' => false,
  'SalesOrderLineAdd DataExt DataExtName' => false,
  'SalesOrderLineAdd DataExt DataExtValue' => false,
  'SalesOrderLineGroupAdd ItemGroupRef ListID' => false,
  'SalesOrderLineGroupAdd ItemGroupRef FullName' => false,
  'SalesOrderLineGroupAdd Desc' => false,
  'SalesOrderLineGroupAdd Quantity' => false,
  'SalesOrderLineGroupAdd UnitOfMeasure' => false,
  'SalesOrderLineGroupAdd DataExt OwnerID' => false,
  'SalesOrderLineGroupAdd DataExt DataExtName' => false,
  'SalesOrderLineGroupAdd DataExt DataExtValue' => false,
  'IncludeRetElement' => true,
];

        return $paths;
    }

    /*
    abstract protected function &_inLocalePaths()
    {
        static $paths = array(
            'FirstName' => array( 'QBD', 'QBCA', 'QBUK', 'QBAU' ),
            'LastName' => array( 'QBD', 'QBCA', 'QBUK', 'QBAU' ),
            );

        return $paths;
    }
    */

    protected function &_reorderPathsPaths()
    {
        static $paths =  [
  0 => 'CustomerRef ListID',
  1 => 'CustomerRef FullName',
  2 => 'ClassRef ListID',
  3 => 'ClassRef FullName',
  4 => 'TemplateRef ListID',
  5 => 'TemplateRef FullName',
  6 => 'TxnDate',
  7 => 'RefNumber',
  8 => 'BillAddress Addr1',
  9 => 'BillAddress Addr2',
  10 => 'BillAddress Addr3',
  11 => 'BillAddress Addr4',
  12 => 'BillAddress Addr5',
  13 => 'BillAddress City',
  14 => 'BillAddress State',
  15 => 'BillAddress PostalCode',
  16 => 'BillAddress Country',
  17 => 'BillAddress Note',
  18 => 'ShipAddress Addr1',
  19 => 'ShipAddress Addr2',
  20 => 'ShipAddress Addr3',
  21 => 'ShipAddress Addr4',
  22 => 'ShipAddress Addr5',
  23 => 'ShipAddress City',
  24 => 'ShipAddress State',
  25 => 'ShipAddress PostalCode',
  26 => 'ShipAddress Country',
  27 => 'ShipAddress Note',
  28 => 'PONumber',
  29 => 'TermsRef ListID',
  30 => 'TermsRef FullName',
  31 => 'DueDate',
  32 => 'SalesRepRef ListID',
  33 => 'SalesRepRef FullName',
  34 => 'FOB',
  35 => 'ShipDate',
  36 => 'ShipMethodRef ListID',
  37 => 'ShipMethodRef FullName',
  38 => 'ItemSalesTaxRef ListID',
  39 => 'ItemSalesTaxRef FullName',
  40 => 'IsManuallyClosed',
  41 => 'Memo',
  42 => 'CustomerMsgRef ListID',
  43 => 'CustomerMsgRef FullName',
  44 => 'IsToBePrinted',
  45 => 'IsToBeEmailed',
  46 => 'IsTaxIncluded',
  47 => 'CustomerSalesTaxCodeRef ListID',
  48 => 'CustomerSalesTaxCodeRef FullName',
  49 => 'Other',
  50 => 'SalesOrderLineAdd',
  51 => 'SalesOrderLineAdd ItemRef',
  52 => 'SalesOrderLineAdd ItemRef ListID',
  53 => 'SalesOrderLineAdd ItemRef FullName',
  54 => 'SalesOrderLineAdd Desc',
  55 => 'SalesOrderLineAdd Quantity',
  56 => 'SalesOrderLineAdd UnitOfMeasure',
  57 => 'SalesOrderLineAdd Rate',
  58 => 'SalesOrderLineAdd RatePercent',
  59 => 'SalesOrderLineAdd PriceLevelRef ListID',
  60 => 'SalesOrderLineAdd PriceLevelRef FullName',
  61 => 'SalesOrderLineAdd ClassRef ListID',
  62 => 'SalesOrderLineAdd ClassRef FullName',
  63 => 'SalesOrderLineAdd Amount',
  'SalesOrderLineAdd InventorySiteRef ListID',
  'SalesOrderLineAdd InventorySiteRef FullName',
  'SalesOrderLineAdd SalesTaxCodeRef ListID',
  'SalesOrderLineAdd SalesTaxCodeRef FullName',
  'SalesOrderLineAdd IsManuallyClosed',
  'SalesOrderLineAdd Other1',
  'SalesOrderLineAdd Other2',
  'SalesOrderLineAdd DataExt OwnerID',
  'SalesOrderLineAdd DataExt DataExtName',
  'SalesOrderLineAdd DataExt DataExtValue',
  'SalesOrderLineGroupAdd ItemGroupRef ListID',
  'SalesOrderLineGroupAdd ItemGroupRef FullName',
  'SalesOrderLineGroupAdd Desc',
  'SalesOrderLineGroupAdd Quantity',
  'SalesOrderLineGroupAdd UnitOfMeasure',
  'SalesOrderLineGroupAdd DataExt OwnerID',
  'SalesOrderLineGroupAdd DataExt DataExtName',
  'SalesOrderLineGroupAdd DataExt DataExtValue',
  'IncludeRetElement',
];

        return $paths;
    }
}
