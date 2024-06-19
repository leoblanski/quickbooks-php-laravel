<?php

/**
 * Schema object for: CreditCardChargeModRq
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
require_once 'QuickBooks.php';

/**
 *
 */
require_once 'QuickBooks/QBXML/Schema/Object.php';

/**
 *
 */
class QuickBooks_QBXML_Schema_Object_CreditCardChargeModRq extends QuickBooks_QBXML_Schema_Object
{
    protected function &_qbxmlWrapper()
    {
        static $wrapper = '';
        
        return $wrapper;
    }
    
    protected function &_dataTypePaths()
    {
        static $paths =  [
  'CreditCardChargeMod TxnID' => 'IDTYPE',
  'CreditCardChargeMod EditSequence' => 'STRTYPE',
  'CreditCardChargeMod AccountRef ListID' => 'IDTYPE',
  'CreditCardChargeMod AccountRef FullName' => 'STRTYPE',
  'CreditCardChargeMod PayeeEntityRef ListID' => 'IDTYPE',
  'CreditCardChargeMod PayeeEntityRef FullName' => 'STRTYPE',
  'CreditCardChargeMod TxnDate' => 'DATETYPE',
  'CreditCardChargeMod RefNumber' => 'STRTYPE',
  'CreditCardChargeMod Memo' => 'STRTYPE',
  'CreditCardChargeMod IsTaxIncluded' => 'BOOLTYPE',
  'CreditCardChargeMod SalesTaxCodeRef ListID' => 'IDTYPE',
  'CreditCardChargeMod SalesTaxCodeRef FullName' => 'STRTYPE',
  'CreditCardChargeMod ClearExpenseLines' => 'BOOLTYPE',
  'CreditCardChargeMod ExpenseLineMod TxnLineID' => 'IDTYPE',
  'CreditCardChargeMod ExpenseLineMod AccountRef ListID' => 'IDTYPE',
  'CreditCardChargeMod ExpenseLineMod AccountRef FullName' => 'STRTYPE',
  'CreditCardChargeMod ExpenseLineMod Amount' => 'AMTTYPE',
  'CreditCardChargeMod ExpenseLineMod TaxAmount' => 'AMTTYPE',
  'CreditCardChargeMod ExpenseLineMod Memo' => 'STRTYPE',
  'CreditCardChargeMod ExpenseLineMod CustomerRef ListID' => 'IDTYPE',
  'CreditCardChargeMod ExpenseLineMod CustomerRef FullName' => 'STRTYPE',
  'CreditCardChargeMod ExpenseLineMod ClassRef ListID' => 'IDTYPE',
  'CreditCardChargeMod ExpenseLineMod ClassRef FullName' => 'STRTYPE',
  'CreditCardChargeMod ExpenseLineMod SalesTaxCodeRef ListID' => 'IDTYPE',
  'CreditCardChargeMod ExpenseLineMod SalesTaxCodeRef FullName' => 'STRTYPE',
  'CreditCardChargeMod ExpenseLineMod BillableStatus' => 'ENUMTYPE',
  'CreditCardChargeMod ClearItemLines' => 'BOOLTYPE',
  'CreditCardChargeMod ItemLineMod TxnLineID' => 'IDTYPE',
  'CreditCardChargeMod ItemLineMod ItemRef ListID' => 'IDTYPE',
  'CreditCardChargeMod ItemLineMod ItemRef FullName' => 'STRTYPE',
  'CreditCardChargeMod ItemLineMod Desc' => 'STRTYPE',
  'CreditCardChargeMod ItemLineMod Quantity' => 'QUANTYPE',
  'CreditCardChargeMod ItemLineMod UnitOfMeasure' => 'STRTYPE',
  'CreditCardChargeMod ItemLineMod OverrideUOMSetRef ListID' => 'IDTYPE',
  'CreditCardChargeMod ItemLineMod OverrideUOMSetRef FullName' => 'STRTYPE',
  'CreditCardChargeMod ItemLineMod Cost' => 'PRICETYPE',
  'CreditCardChargeMod ItemLineMod Amount' => 'AMTTYPE',
  'CreditCardChargeMod ItemLineMod TaxAmount' => 'AMTTYPE',
  'CreditCardChargeMod ItemLineMod CustomerRef ListID' => 'IDTYPE',
  'CreditCardChargeMod ItemLineMod CustomerRef FullName' => 'STRTYPE',
  'CreditCardChargeMod ItemLineMod ClassRef ListID' => 'IDTYPE',
  'CreditCardChargeMod ItemLineMod ClassRef FullName' => 'STRTYPE',
  'CreditCardChargeMod ItemLineMod SalesTaxCodeRef ListID' => 'IDTYPE',
  'CreditCardChargeMod ItemLineMod SalesTaxCodeRef FullName' => 'STRTYPE',
  'CreditCardChargeMod ItemLineMod BillableStatus' => 'ENUMTYPE',
  'CreditCardChargeMod ItemLineMod OverrideItemAccountRef ListID' => 'IDTYPE',
  'CreditCardChargeMod ItemLineMod OverrideItemAccountRef FullName' => 'STRTYPE',
  'CreditCardChargeMod ItemGroupLineMod TxnLineID' => 'IDTYPE',
  'CreditCardChargeMod ItemGroupLineMod ItemGroupRef ListID' => 'IDTYPE',
  'CreditCardChargeMod ItemGroupLineMod ItemGroupRef FullName' => 'STRTYPE',
  'CreditCardChargeMod ItemGroupLineMod Quantity' => 'QUANTYPE',
  'CreditCardChargeMod ItemGroupLineMod UnitOfMeasure' => 'STRTYPE',
  'CreditCardChargeMod ItemGroupLineMod OverrideUOMSetRef ListID' => 'IDTYPE',
  'CreditCardChargeMod ItemGroupLineMod OverrideUOMSetRef FullName' => 'STRTYPE',
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod TxnLineID' => 'IDTYPE',
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod ItemRef ListID' => 'IDTYPE',
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod ItemRef FullName' => 'STRTYPE',
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod Desc' => 'STRTYPE',
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod Quantity' => 'QUANTYPE',
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod UnitOfMeasure' => 'STRTYPE',
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod OverrideUOMSetRef ListID' => 'IDTYPE',
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod OverrideUOMSetRef FullName' => 'STRTYPE',
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod Cost' => 'PRICETYPE',
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod Amount' => 'AMTTYPE',
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod TaxAmount' => 'AMTTYPE',
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod CustomerRef ListID' => 'IDTYPE',
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod CustomerRef FullName' => 'STRTYPE',
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod ClassRef ListID' => 'IDTYPE',
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod ClassRef FullName' => 'STRTYPE',
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod SalesTaxCodeRef ListID' => 'IDTYPE',
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod SalesTaxCodeRef FullName' => 'STRTYPE',
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod BillableStatus' => 'ENUMTYPE',
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod OverrideItemAccountRef ListID' => 'IDTYPE',
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod OverrideItemAccountRef FullName' => 'STRTYPE',
  'IncludeRetElement' => 'STRTYPE',
];
        
        return $paths;
    }
    
    protected function &_maxLengthPaths()
    {
        static $paths =  [
  'CreditCardChargeMod TxnID' => 0,
  'CreditCardChargeMod EditSequence' => 16,
  'CreditCardChargeMod AccountRef ListID' => 0,
  'CreditCardChargeMod AccountRef FullName' => 159,
  'CreditCardChargeMod PayeeEntityRef ListID' => 0,
  'CreditCardChargeMod PayeeEntityRef FullName' => 159,
  'CreditCardChargeMod TxnDate' => 0,
  'CreditCardChargeMod RefNumber' => 11,
  'CreditCardChargeMod Memo' => 4095,
  'CreditCardChargeMod IsTaxIncluded' => 0,
  'CreditCardChargeMod SalesTaxCodeRef ListID' => 0,
  'CreditCardChargeMod SalesTaxCodeRef FullName' => 159,
  'CreditCardChargeMod ClearExpenseLines' => 0,
  'CreditCardChargeMod ExpenseLineMod TxnLineID' => 0,
  'CreditCardChargeMod ExpenseLineMod AccountRef ListID' => 0,
  'CreditCardChargeMod ExpenseLineMod AccountRef FullName' => 159,
  'CreditCardChargeMod ExpenseLineMod Amount' => 0,
  'CreditCardChargeMod ExpenseLineMod TaxAmount' => 0,
  'CreditCardChargeMod ExpenseLineMod Memo' => 4095,
  'CreditCardChargeMod ExpenseLineMod CustomerRef ListID' => 0,
  'CreditCardChargeMod ExpenseLineMod CustomerRef FullName' => 159,
  'CreditCardChargeMod ExpenseLineMod ClassRef ListID' => 0,
  'CreditCardChargeMod ExpenseLineMod ClassRef FullName' => 159,
  'CreditCardChargeMod ExpenseLineMod SalesTaxCodeRef ListID' => 0,
  'CreditCardChargeMod ExpenseLineMod SalesTaxCodeRef FullName' => 159,
  'CreditCardChargeMod ExpenseLineMod BillableStatus' => 0,
  'CreditCardChargeMod ClearItemLines' => 0,
  'CreditCardChargeMod ItemLineMod TxnLineID' => 0,
  'CreditCardChargeMod ItemLineMod ItemRef ListID' => 0,
  'CreditCardChargeMod ItemLineMod ItemRef FullName' => 159,
  'CreditCardChargeMod ItemLineMod Desc' => 4095,
  'CreditCardChargeMod ItemLineMod Quantity' => 0,
  'CreditCardChargeMod ItemLineMod UnitOfMeasure' => 31,
  'CreditCardChargeMod ItemLineMod OverrideUOMSetRef ListID' => 0,
  'CreditCardChargeMod ItemLineMod OverrideUOMSetRef FullName' => 159,
  'CreditCardChargeMod ItemLineMod Cost' => 0,
  'CreditCardChargeMod ItemLineMod Amount' => 0,
  'CreditCardChargeMod ItemLineMod TaxAmount' => 0,
  'CreditCardChargeMod ItemLineMod CustomerRef ListID' => 0,
  'CreditCardChargeMod ItemLineMod CustomerRef FullName' => 159,
  'CreditCardChargeMod ItemLineMod ClassRef ListID' => 0,
  'CreditCardChargeMod ItemLineMod ClassRef FullName' => 159,
  'CreditCardChargeMod ItemLineMod SalesTaxCodeRef ListID' => 0,
  'CreditCardChargeMod ItemLineMod SalesTaxCodeRef FullName' => 159,
  'CreditCardChargeMod ItemLineMod BillableStatus' => 0,
  'CreditCardChargeMod ItemLineMod OverrideItemAccountRef ListID' => 0,
  'CreditCardChargeMod ItemLineMod OverrideItemAccountRef FullName' => 159,
  'CreditCardChargeMod ItemGroupLineMod TxnLineID' => 0,
  'CreditCardChargeMod ItemGroupLineMod ItemGroupRef ListID' => 0,
  'CreditCardChargeMod ItemGroupLineMod ItemGroupRef FullName' => 159,
  'CreditCardChargeMod ItemGroupLineMod Quantity' => 0,
  'CreditCardChargeMod ItemGroupLineMod UnitOfMeasure' => 31,
  'CreditCardChargeMod ItemGroupLineMod OverrideUOMSetRef ListID' => 0,
  'CreditCardChargeMod ItemGroupLineMod OverrideUOMSetRef FullName' => 159,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod TxnLineID' => 0,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod ItemRef ListID' => 0,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod ItemRef FullName' => 159,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod Desc' => 4095,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod Quantity' => 0,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod UnitOfMeasure' => 31,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod OverrideUOMSetRef ListID' => 0,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod OverrideUOMSetRef FullName' => 159,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod Cost' => 0,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod Amount' => 0,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod TaxAmount' => 0,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod CustomerRef ListID' => 0,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod CustomerRef FullName' => 159,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod ClassRef ListID' => 0,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod ClassRef FullName' => 159,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod SalesTaxCodeRef ListID' => 0,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod SalesTaxCodeRef FullName' => 159,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod BillableStatus' => 0,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod OverrideItemAccountRef ListID' => 0,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod OverrideItemAccountRef FullName' => 159,
  'IncludeRetElement' => 50,
];
        
        return $paths;
    }
    
    protected function &_isOptionalPaths()
    {
        static $paths =  [
  'CreditCardChargeMod TxnID' => false,
  'CreditCardChargeMod EditSequence' => false,
  'CreditCardChargeMod AccountRef ListID' => true,
  'CreditCardChargeMod AccountRef FullName' => true,
  'CreditCardChargeMod PayeeEntityRef ListID' => true,
  'CreditCardChargeMod PayeeEntityRef FullName' => true,
  'CreditCardChargeMod TxnDate' => true,
  'CreditCardChargeMod RefNumber' => true,
  'CreditCardChargeMod Memo' => true,
  'CreditCardChargeMod IsTaxIncluded' => true,
  'CreditCardChargeMod SalesTaxCodeRef ListID' => true,
  'CreditCardChargeMod SalesTaxCodeRef FullName' => true,
  'CreditCardChargeMod ClearExpenseLines' => true,
  'CreditCardChargeMod ExpenseLineMod TxnLineID' => false,
  'CreditCardChargeMod ExpenseLineMod AccountRef ListID' => true,
  'CreditCardChargeMod ExpenseLineMod AccountRef FullName' => true,
  'CreditCardChargeMod ExpenseLineMod Amount' => true,
  'CreditCardChargeMod ExpenseLineMod TaxAmount' => true,
  'CreditCardChargeMod ExpenseLineMod Memo' => true,
  'CreditCardChargeMod ExpenseLineMod CustomerRef ListID' => true,
  'CreditCardChargeMod ExpenseLineMod CustomerRef FullName' => true,
  'CreditCardChargeMod ExpenseLineMod ClassRef ListID' => true,
  'CreditCardChargeMod ExpenseLineMod ClassRef FullName' => true,
  'CreditCardChargeMod ExpenseLineMod SalesTaxCodeRef ListID' => true,
  'CreditCardChargeMod ExpenseLineMod SalesTaxCodeRef FullName' => true,
  'CreditCardChargeMod ExpenseLineMod BillableStatus' => true,
  'CreditCardChargeMod ClearItemLines' => true,
  'CreditCardChargeMod ItemLineMod TxnLineID' => false,
  'CreditCardChargeMod ItemLineMod ItemRef ListID' => true,
  'CreditCardChargeMod ItemLineMod ItemRef FullName' => true,
  'CreditCardChargeMod ItemLineMod Desc' => true,
  'CreditCardChargeMod ItemLineMod Quantity' => true,
  'CreditCardChargeMod ItemLineMod UnitOfMeasure' => true,
  'CreditCardChargeMod ItemLineMod OverrideUOMSetRef ListID' => true,
  'CreditCardChargeMod ItemLineMod OverrideUOMSetRef FullName' => true,
  'CreditCardChargeMod ItemLineMod Cost' => true,
  'CreditCardChargeMod ItemLineMod Amount' => true,
  'CreditCardChargeMod ItemLineMod TaxAmount' => true,
  'CreditCardChargeMod ItemLineMod CustomerRef ListID' => true,
  'CreditCardChargeMod ItemLineMod CustomerRef FullName' => true,
  'CreditCardChargeMod ItemLineMod ClassRef ListID' => true,
  'CreditCardChargeMod ItemLineMod ClassRef FullName' => true,
  'CreditCardChargeMod ItemLineMod SalesTaxCodeRef ListID' => true,
  'CreditCardChargeMod ItemLineMod SalesTaxCodeRef FullName' => true,
  'CreditCardChargeMod ItemLineMod BillableStatus' => true,
  'CreditCardChargeMod ItemLineMod OverrideItemAccountRef ListID' => true,
  'CreditCardChargeMod ItemLineMod OverrideItemAccountRef FullName' => true,
  'CreditCardChargeMod ItemGroupLineMod TxnLineID' => false,
  'CreditCardChargeMod ItemGroupLineMod ItemGroupRef ListID' => true,
  'CreditCardChargeMod ItemGroupLineMod ItemGroupRef FullName' => true,
  'CreditCardChargeMod ItemGroupLineMod Quantity' => true,
  'CreditCardChargeMod ItemGroupLineMod UnitOfMeasure' => true,
  'CreditCardChargeMod ItemGroupLineMod OverrideUOMSetRef ListID' => true,
  'CreditCardChargeMod ItemGroupLineMod OverrideUOMSetRef FullName' => true,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod TxnLineID' => false,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod ItemRef ListID' => true,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod ItemRef FullName' => true,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod Desc' => true,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod Quantity' => true,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod UnitOfMeasure' => true,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod OverrideUOMSetRef ListID' => true,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod OverrideUOMSetRef FullName' => true,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod Cost' => true,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod Amount' => true,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod TaxAmount' => true,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod CustomerRef ListID' => true,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod CustomerRef FullName' => true,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod ClassRef ListID' => true,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod ClassRef FullName' => true,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod SalesTaxCodeRef ListID' => true,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod SalesTaxCodeRef FullName' => true,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod BillableStatus' => true,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod OverrideItemAccountRef ListID' => true,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod OverrideItemAccountRef FullName' => true,
  'IncludeRetElement' => true,
];
    }
    
    protected function &_sinceVersionPaths()
    {
        static $paths =  [
  'CreditCardChargeMod TxnID' => 999.99,
  'CreditCardChargeMod EditSequence' => 999.99,
  'CreditCardChargeMod AccountRef ListID' => 999.99,
  'CreditCardChargeMod AccountRef FullName' => 999.99,
  'CreditCardChargeMod PayeeEntityRef ListID' => 999.99,
  'CreditCardChargeMod PayeeEntityRef FullName' => 999.99,
  'CreditCardChargeMod TxnDate' => 999.99,
  'CreditCardChargeMod RefNumber' => 999.99,
  'CreditCardChargeMod Memo' => 999.99,
  'CreditCardChargeMod IsTaxIncluded' => 6,
  'CreditCardChargeMod SalesTaxCodeRef ListID' => 999.99,
  'CreditCardChargeMod SalesTaxCodeRef FullName' => 999.99,
  'CreditCardChargeMod ClearExpenseLines' => 999.99,
  'CreditCardChargeMod ExpenseLineMod TxnLineID' => 999.99,
  'CreditCardChargeMod ExpenseLineMod AccountRef ListID' => 999.99,
  'CreditCardChargeMod ExpenseLineMod AccountRef FullName' => 999.99,
  'CreditCardChargeMod ExpenseLineMod Amount' => 999.99,
  'CreditCardChargeMod ExpenseLineMod TaxAmount' => 6.1,
  'CreditCardChargeMod ExpenseLineMod Memo' => 999.99,
  'CreditCardChargeMod ExpenseLineMod CustomerRef ListID' => 999.99,
  'CreditCardChargeMod ExpenseLineMod CustomerRef FullName' => 999.99,
  'CreditCardChargeMod ExpenseLineMod ClassRef ListID' => 999.99,
  'CreditCardChargeMod ExpenseLineMod ClassRef FullName' => 999.99,
  'CreditCardChargeMod ExpenseLineMod SalesTaxCodeRef ListID' => 999.99,
  'CreditCardChargeMod ExpenseLineMod SalesTaxCodeRef FullName' => 999.99,
  'CreditCardChargeMod ExpenseLineMod BillableStatus' => 999.99,
  'CreditCardChargeMod ClearItemLines' => 999.99,
  'CreditCardChargeMod ItemLineMod TxnLineID' => 999.99,
  'CreditCardChargeMod ItemLineMod ItemRef ListID' => 999.99,
  'CreditCardChargeMod ItemLineMod ItemRef FullName' => 999.99,
  'CreditCardChargeMod ItemLineMod Desc' => 999.99,
  'CreditCardChargeMod ItemLineMod Quantity' => 999.99,
  'CreditCardChargeMod ItemLineMod UnitOfMeasure' => 7,
  'CreditCardChargeMod ItemLineMod OverrideUOMSetRef ListID' => 999.99,
  'CreditCardChargeMod ItemLineMod OverrideUOMSetRef FullName' => 999.99,
  'CreditCardChargeMod ItemLineMod Cost' => 999.99,
  'CreditCardChargeMod ItemLineMod Amount' => 999.99,
  'CreditCardChargeMod ItemLineMod TaxAmount' => 6.1,
  'CreditCardChargeMod ItemLineMod CustomerRef ListID' => 999.99,
  'CreditCardChargeMod ItemLineMod CustomerRef FullName' => 999.99,
  'CreditCardChargeMod ItemLineMod ClassRef ListID' => 999.99,
  'CreditCardChargeMod ItemLineMod ClassRef FullName' => 999.99,
  'CreditCardChargeMod ItemLineMod SalesTaxCodeRef ListID' => 999.99,
  'CreditCardChargeMod ItemLineMod SalesTaxCodeRef FullName' => 999.99,
  'CreditCardChargeMod ItemLineMod BillableStatus' => 999.99,
  'CreditCardChargeMod ItemLineMod OverrideItemAccountRef ListID' => 999.99,
  'CreditCardChargeMod ItemLineMod OverrideItemAccountRef FullName' => 999.99,
  'CreditCardChargeMod ItemGroupLineMod TxnLineID' => 999.99,
  'CreditCardChargeMod ItemGroupLineMod ItemGroupRef ListID' => 999.99,
  'CreditCardChargeMod ItemGroupLineMod ItemGroupRef FullName' => 999.99,
  'CreditCardChargeMod ItemGroupLineMod Quantity' => 999.99,
  'CreditCardChargeMod ItemGroupLineMod UnitOfMeasure' => 7,
  'CreditCardChargeMod ItemGroupLineMod OverrideUOMSetRef ListID' => 999.99,
  'CreditCardChargeMod ItemGroupLineMod OverrideUOMSetRef FullName' => 999.99,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod TxnLineID' => 999.99,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod ItemRef ListID' => 999.99,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod ItemRef FullName' => 999.99,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod Desc' => 999.99,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod Quantity' => 999.99,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod UnitOfMeasure' => 7,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod OverrideUOMSetRef ListID' => 999.99,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod OverrideUOMSetRef FullName' => 999.99,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod Cost' => 999.99,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod Amount' => 999.99,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod TaxAmount' => 6.1,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod CustomerRef ListID' => 999.99,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod CustomerRef FullName' => 999.99,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod ClassRef ListID' => 999.99,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod ClassRef FullName' => 999.99,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod SalesTaxCodeRef ListID' => 999.99,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod SalesTaxCodeRef FullName' => 999.99,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod BillableStatus' => 999.99,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod OverrideItemAccountRef ListID' => 999.99,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod OverrideItemAccountRef FullName' => 999.99,
  'IncludeRetElement' => 4,
];
        
        return $paths;
    }
    
    protected function &_isRepeatablePaths()
    {
        static $paths =  [
  'CreditCardChargeMod TxnID' => false,
  'CreditCardChargeMod EditSequence' => false,
  'CreditCardChargeMod AccountRef ListID' => false,
  'CreditCardChargeMod AccountRef FullName' => false,
  'CreditCardChargeMod PayeeEntityRef ListID' => false,
  'CreditCardChargeMod PayeeEntityRef FullName' => false,
  'CreditCardChargeMod TxnDate' => false,
  'CreditCardChargeMod RefNumber' => false,
  'CreditCardChargeMod Memo' => false,
  'CreditCardChargeMod IsTaxIncluded' => false,
  'CreditCardChargeMod SalesTaxCodeRef ListID' => false,
  'CreditCardChargeMod SalesTaxCodeRef FullName' => false,
  'CreditCardChargeMod ClearExpenseLines' => false,
  'CreditCardChargeMod ExpenseLineMod TxnLineID' => false,
  'CreditCardChargeMod ExpenseLineMod AccountRef ListID' => false,
  'CreditCardChargeMod ExpenseLineMod AccountRef FullName' => false,
  'CreditCardChargeMod ExpenseLineMod Amount' => false,
  'CreditCardChargeMod ExpenseLineMod TaxAmount' => false,
  'CreditCardChargeMod ExpenseLineMod Memo' => false,
  'CreditCardChargeMod ExpenseLineMod CustomerRef ListID' => false,
  'CreditCardChargeMod ExpenseLineMod CustomerRef FullName' => false,
  'CreditCardChargeMod ExpenseLineMod ClassRef ListID' => false,
  'CreditCardChargeMod ExpenseLineMod ClassRef FullName' => false,
  'CreditCardChargeMod ExpenseLineMod SalesTaxCodeRef ListID' => false,
  'CreditCardChargeMod ExpenseLineMod SalesTaxCodeRef FullName' => false,
  'CreditCardChargeMod ExpenseLineMod BillableStatus' => false,
  'CreditCardChargeMod ClearItemLines' => false,
  'CreditCardChargeMod ItemLineMod TxnLineID' => false,
  'CreditCardChargeMod ItemLineMod ItemRef ListID' => false,
  'CreditCardChargeMod ItemLineMod ItemRef FullName' => false,
  'CreditCardChargeMod ItemLineMod Desc' => false,
  'CreditCardChargeMod ItemLineMod Quantity' => false,
  'CreditCardChargeMod ItemLineMod UnitOfMeasure' => false,
  'CreditCardChargeMod ItemLineMod OverrideUOMSetRef ListID' => false,
  'CreditCardChargeMod ItemLineMod OverrideUOMSetRef FullName' => false,
  'CreditCardChargeMod ItemLineMod Cost' => false,
  'CreditCardChargeMod ItemLineMod Amount' => false,
  'CreditCardChargeMod ItemLineMod TaxAmount' => false,
  'CreditCardChargeMod ItemLineMod CustomerRef ListID' => false,
  'CreditCardChargeMod ItemLineMod CustomerRef FullName' => false,
  'CreditCardChargeMod ItemLineMod ClassRef ListID' => false,
  'CreditCardChargeMod ItemLineMod ClassRef FullName' => false,
  'CreditCardChargeMod ItemLineMod SalesTaxCodeRef ListID' => false,
  'CreditCardChargeMod ItemLineMod SalesTaxCodeRef FullName' => false,
  'CreditCardChargeMod ItemLineMod BillableStatus' => false,
  'CreditCardChargeMod ItemLineMod OverrideItemAccountRef ListID' => false,
  'CreditCardChargeMod ItemLineMod OverrideItemAccountRef FullName' => false,
  'CreditCardChargeMod ItemGroupLineMod TxnLineID' => false,
  'CreditCardChargeMod ItemGroupLineMod ItemGroupRef ListID' => false,
  'CreditCardChargeMod ItemGroupLineMod ItemGroupRef FullName' => false,
  'CreditCardChargeMod ItemGroupLineMod Quantity' => false,
  'CreditCardChargeMod ItemGroupLineMod UnitOfMeasure' => false,
  'CreditCardChargeMod ItemGroupLineMod OverrideUOMSetRef ListID' => false,
  'CreditCardChargeMod ItemGroupLineMod OverrideUOMSetRef FullName' => false,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod TxnLineID' => false,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod ItemRef ListID' => false,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod ItemRef FullName' => false,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod Desc' => false,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod Quantity' => false,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod UnitOfMeasure' => false,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod OverrideUOMSetRef ListID' => false,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod OverrideUOMSetRef FullName' => false,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod Cost' => false,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod Amount' => false,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod TaxAmount' => false,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod CustomerRef ListID' => false,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod CustomerRef FullName' => false,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod ClassRef ListID' => false,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod ClassRef FullName' => false,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod SalesTaxCodeRef ListID' => false,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod SalesTaxCodeRef FullName' => false,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod BillableStatus' => false,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod OverrideItemAccountRef ListID' => false,
  'CreditCardChargeMod ItemGroupLineMod ItemLineMod OverrideItemAccountRef FullName' => false,
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
  0 => 'CreditCardChargeMod TxnID',
  1 => 'CreditCardChargeMod EditSequence',
  2 => 'CreditCardChargeMod AccountRef ListID',
  3 => 'CreditCardChargeMod AccountRef FullName',
  4 => 'CreditCardChargeMod PayeeEntityRef ListID',
  5 => 'CreditCardChargeMod PayeeEntityRef FullName',
  6 => 'CreditCardChargeMod TxnDate',
  7 => 'CreditCardChargeMod RefNumber',
  8 => 'CreditCardChargeMod Memo',
  9 => 'CreditCardChargeMod IsTaxIncluded',
  10 => 'CreditCardChargeMod SalesTaxCodeRef ListID',
  11 => 'CreditCardChargeMod SalesTaxCodeRef FullName',
  12 => 'CreditCardChargeMod ClearExpenseLines',
  13 => 'CreditCardChargeMod ExpenseLineMod TxnLineID',
  14 => 'CreditCardChargeMod ExpenseLineMod AccountRef ListID',
  15 => 'CreditCardChargeMod ExpenseLineMod AccountRef FullName',
  16 => 'CreditCardChargeMod ExpenseLineMod Amount',
  17 => 'CreditCardChargeMod ExpenseLineMod TaxAmount',
  18 => 'CreditCardChargeMod ExpenseLineMod Memo',
  19 => 'CreditCardChargeMod ExpenseLineMod CustomerRef ListID',
  20 => 'CreditCardChargeMod ExpenseLineMod CustomerRef FullName',
  21 => 'CreditCardChargeMod ExpenseLineMod ClassRef ListID',
  22 => 'CreditCardChargeMod ExpenseLineMod ClassRef FullName',
  23 => 'CreditCardChargeMod ExpenseLineMod SalesTaxCodeRef ListID',
  24 => 'CreditCardChargeMod ExpenseLineMod SalesTaxCodeRef FullName',
  25 => 'CreditCardChargeMod ExpenseLineMod BillableStatus',
  26 => 'CreditCardChargeMod ClearItemLines',
  27 => 'CreditCardChargeMod ItemLineMod TxnLineID',
  28 => 'CreditCardChargeMod ItemLineMod ItemRef ListID',
  29 => 'CreditCardChargeMod ItemLineMod ItemRef FullName',
  30 => 'CreditCardChargeMod ItemLineMod Desc',
  31 => 'CreditCardChargeMod ItemLineMod Quantity',
  32 => 'CreditCardChargeMod ItemLineMod UnitOfMeasure',
  33 => 'CreditCardChargeMod ItemLineMod OverrideUOMSetRef ListID',
  34 => 'CreditCardChargeMod ItemLineMod OverrideUOMSetRef FullName',
  35 => 'CreditCardChargeMod ItemLineMod Cost',
  36 => 'CreditCardChargeMod ItemLineMod Amount',
  37 => 'CreditCardChargeMod ItemLineMod TaxAmount',
  38 => 'CreditCardChargeMod ItemLineMod CustomerRef ListID',
  39 => 'CreditCardChargeMod ItemLineMod CustomerRef FullName',
  40 => 'CreditCardChargeMod ItemLineMod ClassRef ListID',
  41 => 'CreditCardChargeMod ItemLineMod ClassRef FullName',
  42 => 'CreditCardChargeMod ItemLineMod SalesTaxCodeRef ListID',
  43 => 'CreditCardChargeMod ItemLineMod SalesTaxCodeRef FullName',
  44 => 'CreditCardChargeMod ItemLineMod BillableStatus',
  45 => 'CreditCardChargeMod ItemLineMod OverrideItemAccountRef ListID',
  46 => 'CreditCardChargeMod ItemLineMod OverrideItemAccountRef FullName',
  47 => 'CreditCardChargeMod ItemGroupLineMod TxnLineID',
  48 => 'CreditCardChargeMod ItemGroupLineMod ItemGroupRef ListID',
  49 => 'CreditCardChargeMod ItemGroupLineMod ItemGroupRef FullName',
  50 => 'CreditCardChargeMod ItemGroupLineMod Quantity',
  51 => 'CreditCardChargeMod ItemGroupLineMod UnitOfMeasure',
  52 => 'CreditCardChargeMod ItemGroupLineMod OverrideUOMSetRef ListID',
  53 => 'CreditCardChargeMod ItemGroupLineMod OverrideUOMSetRef FullName',
  54 => 'CreditCardChargeMod ItemGroupLineMod ItemLineMod TxnLineID',
  55 => 'CreditCardChargeMod ItemGroupLineMod ItemLineMod ItemRef ListID',
  56 => 'CreditCardChargeMod ItemGroupLineMod ItemLineMod ItemRef FullName',
  57 => 'CreditCardChargeMod ItemGroupLineMod ItemLineMod Desc',
  58 => 'CreditCardChargeMod ItemGroupLineMod ItemLineMod Quantity',
  59 => 'CreditCardChargeMod ItemGroupLineMod ItemLineMod UnitOfMeasure',
  60 => 'CreditCardChargeMod ItemGroupLineMod ItemLineMod OverrideUOMSetRef ListID',
  61 => 'CreditCardChargeMod ItemGroupLineMod ItemLineMod OverrideUOMSetRef FullName',
  62 => 'CreditCardChargeMod ItemGroupLineMod ItemLineMod Cost',
  63 => 'CreditCardChargeMod ItemGroupLineMod ItemLineMod Amount',
  64 => 'CreditCardChargeMod ItemGroupLineMod ItemLineMod TaxAmount',
  65 => 'CreditCardChargeMod ItemGroupLineMod ItemLineMod CustomerRef ListID',
  66 => 'CreditCardChargeMod ItemGroupLineMod ItemLineMod CustomerRef FullName',
  67 => 'CreditCardChargeMod ItemGroupLineMod ItemLineMod ClassRef ListID',
  68 => 'CreditCardChargeMod ItemGroupLineMod ItemLineMod ClassRef FullName',
  69 => 'CreditCardChargeMod ItemGroupLineMod ItemLineMod SalesTaxCodeRef ListID',
  70 => 'CreditCardChargeMod ItemGroupLineMod ItemLineMod SalesTaxCodeRef FullName',
  71 => 'CreditCardChargeMod ItemGroupLineMod ItemLineMod BillableStatus',
  72 => 'CreditCardChargeMod ItemGroupLineMod ItemLineMod OverrideItemAccountRef ListID',
  73 => 'CreditCardChargeMod ItemGroupLineMod ItemLineMod OverrideItemAccountRef FullName',
  74 => 'IncludeRetElement',
];
            
        return $paths;
    }
}
