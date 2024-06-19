<?php

/**
 * Schema object for: CreditCardCreditModRq
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
class QuickBooks_QBXML_Schema_Object_CreditCardCreditModRq extends QuickBooks_QBXML_Schema_Object
{
    protected function &_qbxmlWrapper()
    {
        static $wrapper = '';
        
        return $wrapper;
    }
    
    protected function &_dataTypePaths()
    {
        static $paths =  [
  'CreditCardCreditMod TxnID' => 'IDTYPE',
  'CreditCardCreditMod EditSequence' => 'STRTYPE',
  'CreditCardCreditMod AccountRef ListID' => 'IDTYPE',
  'CreditCardCreditMod AccountRef FullName' => 'STRTYPE',
  'CreditCardCreditMod PayeeEntityRef ListID' => 'IDTYPE',
  'CreditCardCreditMod PayeeEntityRef FullName' => 'STRTYPE',
  'CreditCardCreditMod TxnDate' => 'DATETYPE',
  'CreditCardCreditMod RefNumber' => 'STRTYPE',
  'CreditCardCreditMod Memo' => 'STRTYPE',
  'CreditCardCreditMod ClearExpenseLines' => 'BOOLTYPE',
  'CreditCardCreditMod ExpenseLineMod TxnLineID' => 'IDTYPE',
  'CreditCardCreditMod ExpenseLineMod AccountRef ListID' => 'IDTYPE',
  'CreditCardCreditMod ExpenseLineMod AccountRef FullName' => 'STRTYPE',
  'CreditCardCreditMod ExpenseLineMod Amount' => 'AMTTYPE',
  'CreditCardCreditMod ExpenseLineMod TaxAmount' => 'AMTTYPE',
  'CreditCardCreditMod ExpenseLineMod Memo' => 'STRTYPE',
  'CreditCardCreditMod ExpenseLineMod CustomerRef ListID' => 'IDTYPE',
  'CreditCardCreditMod ExpenseLineMod CustomerRef FullName' => 'STRTYPE',
  'CreditCardCreditMod ExpenseLineMod ClassRef ListID' => 'IDTYPE',
  'CreditCardCreditMod ExpenseLineMod ClassRef FullName' => 'STRTYPE',
  'CreditCardCreditMod ExpenseLineMod SalesTaxCodeRef ListID' => 'IDTYPE',
  'CreditCardCreditMod ExpenseLineMod SalesTaxCodeRef FullName' => 'STRTYPE',
  'CreditCardCreditMod ExpenseLineMod BillableStatus' => 'ENUMTYPE',
  'CreditCardCreditMod ClearItemLines' => 'BOOLTYPE',
  'CreditCardCreditMod ItemLineMod TxnLineID' => 'IDTYPE',
  'CreditCardCreditMod ItemLineMod ItemRef ListID' => 'IDTYPE',
  'CreditCardCreditMod ItemLineMod ItemRef FullName' => 'STRTYPE',
  'CreditCardCreditMod ItemLineMod Desc' => 'STRTYPE',
  'CreditCardCreditMod ItemLineMod Quantity' => 'QUANTYPE',
  'CreditCardCreditMod ItemLineMod UnitOfMeasure' => 'STRTYPE',
  'CreditCardCreditMod ItemLineMod OverrideUOMSetRef ListID' => 'IDTYPE',
  'CreditCardCreditMod ItemLineMod OverrideUOMSetRef FullName' => 'STRTYPE',
  'CreditCardCreditMod ItemLineMod Cost' => 'PRICETYPE',
  'CreditCardCreditMod ItemLineMod Amount' => 'AMTTYPE',
  'CreditCardCreditMod ItemLineMod TaxAmount' => 'AMTTYPE',
  'CreditCardCreditMod ItemLineMod CustomerRef ListID' => 'IDTYPE',
  'CreditCardCreditMod ItemLineMod CustomerRef FullName' => 'STRTYPE',
  'CreditCardCreditMod ItemLineMod ClassRef ListID' => 'IDTYPE',
  'CreditCardCreditMod ItemLineMod ClassRef FullName' => 'STRTYPE',
  'CreditCardCreditMod ItemLineMod SalesTaxCodeRef ListID' => 'IDTYPE',
  'CreditCardCreditMod ItemLineMod SalesTaxCodeRef FullName' => 'STRTYPE',
  'CreditCardCreditMod ItemLineMod BillableStatus' => 'ENUMTYPE',
  'CreditCardCreditMod ItemLineMod OverrideItemAccountRef ListID' => 'IDTYPE',
  'CreditCardCreditMod ItemLineMod OverrideItemAccountRef FullName' => 'STRTYPE',
  'CreditCardCreditMod ItemGroupLineMod TxnLineID' => 'IDTYPE',
  'CreditCardCreditMod ItemGroupLineMod ItemGroupRef ListID' => 'IDTYPE',
  'CreditCardCreditMod ItemGroupLineMod ItemGroupRef FullName' => 'STRTYPE',
  'CreditCardCreditMod ItemGroupLineMod Quantity' => 'QUANTYPE',
  'CreditCardCreditMod ItemGroupLineMod UnitOfMeasure' => 'STRTYPE',
  'CreditCardCreditMod ItemGroupLineMod OverrideUOMSetRef ListID' => 'IDTYPE',
  'CreditCardCreditMod ItemGroupLineMod OverrideUOMSetRef FullName' => 'STRTYPE',
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod TxnLineID' => 'IDTYPE',
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod ItemRef ListID' => 'IDTYPE',
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod ItemRef FullName' => 'STRTYPE',
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod Desc' => 'STRTYPE',
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod Quantity' => 'QUANTYPE',
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod UnitOfMeasure' => 'STRTYPE',
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod OverrideUOMSetRef ListID' => 'IDTYPE',
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod OverrideUOMSetRef FullName' => 'STRTYPE',
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod Cost' => 'PRICETYPE',
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod Amount' => 'AMTTYPE',
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod TaxAmount' => 'AMTTYPE',
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod CustomerRef ListID' => 'IDTYPE',
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod CustomerRef FullName' => 'STRTYPE',
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod ClassRef ListID' => 'IDTYPE',
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod ClassRef FullName' => 'STRTYPE',
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod SalesTaxCodeRef ListID' => 'IDTYPE',
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod SalesTaxCodeRef FullName' => 'STRTYPE',
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod BillableStatus' => 'ENUMTYPE',
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod OverrideItemAccountRef ListID' => 'IDTYPE',
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod OverrideItemAccountRef FullName' => 'STRTYPE',
  'IncludeRetElement' => 'STRTYPE',
];
        
        return $paths;
    }
    
    protected function &_maxLengthPaths()
    {
        static $paths =  [
  'CreditCardCreditMod TxnID' => 0,
  'CreditCardCreditMod EditSequence' => 16,
  'CreditCardCreditMod AccountRef ListID' => 0,
  'CreditCardCreditMod AccountRef FullName' => 159,
  'CreditCardCreditMod PayeeEntityRef ListID' => 0,
  'CreditCardCreditMod PayeeEntityRef FullName' => 159,
  'CreditCardCreditMod TxnDate' => 0,
  'CreditCardCreditMod RefNumber' => 11,
  'CreditCardCreditMod Memo' => 4095,
  'CreditCardCreditMod ClearExpenseLines' => 0,
  'CreditCardCreditMod ExpenseLineMod TxnLineID' => 0,
  'CreditCardCreditMod ExpenseLineMod AccountRef ListID' => 0,
  'CreditCardCreditMod ExpenseLineMod AccountRef FullName' => 159,
  'CreditCardCreditMod ExpenseLineMod Amount' => 0,
  'CreditCardCreditMod ExpenseLineMod TaxAmount' => 0,
  'CreditCardCreditMod ExpenseLineMod Memo' => 4095,
  'CreditCardCreditMod ExpenseLineMod CustomerRef ListID' => 0,
  'CreditCardCreditMod ExpenseLineMod CustomerRef FullName' => 159,
  'CreditCardCreditMod ExpenseLineMod ClassRef ListID' => 0,
  'CreditCardCreditMod ExpenseLineMod ClassRef FullName' => 159,
  'CreditCardCreditMod ExpenseLineMod SalesTaxCodeRef ListID' => 0,
  'CreditCardCreditMod ExpenseLineMod SalesTaxCodeRef FullName' => 159,
  'CreditCardCreditMod ExpenseLineMod BillableStatus' => 0,
  'CreditCardCreditMod ClearItemLines' => 0,
  'CreditCardCreditMod ItemLineMod TxnLineID' => 0,
  'CreditCardCreditMod ItemLineMod ItemRef ListID' => 0,
  'CreditCardCreditMod ItemLineMod ItemRef FullName' => 159,
  'CreditCardCreditMod ItemLineMod Desc' => 4095,
  'CreditCardCreditMod ItemLineMod Quantity' => 0,
  'CreditCardCreditMod ItemLineMod UnitOfMeasure' => 31,
  'CreditCardCreditMod ItemLineMod OverrideUOMSetRef ListID' => 0,
  'CreditCardCreditMod ItemLineMod OverrideUOMSetRef FullName' => 159,
  'CreditCardCreditMod ItemLineMod Cost' => 0,
  'CreditCardCreditMod ItemLineMod Amount' => 0,
  'CreditCardCreditMod ItemLineMod TaxAmount' => 0,
  'CreditCardCreditMod ItemLineMod CustomerRef ListID' => 0,
  'CreditCardCreditMod ItemLineMod CustomerRef FullName' => 159,
  'CreditCardCreditMod ItemLineMod ClassRef ListID' => 0,
  'CreditCardCreditMod ItemLineMod ClassRef FullName' => 159,
  'CreditCardCreditMod ItemLineMod SalesTaxCodeRef ListID' => 0,
  'CreditCardCreditMod ItemLineMod SalesTaxCodeRef FullName' => 159,
  'CreditCardCreditMod ItemLineMod BillableStatus' => 0,
  'CreditCardCreditMod ItemLineMod OverrideItemAccountRef ListID' => 0,
  'CreditCardCreditMod ItemLineMod OverrideItemAccountRef FullName' => 159,
  'CreditCardCreditMod ItemGroupLineMod TxnLineID' => 0,
  'CreditCardCreditMod ItemGroupLineMod ItemGroupRef ListID' => 0,
  'CreditCardCreditMod ItemGroupLineMod ItemGroupRef FullName' => 159,
  'CreditCardCreditMod ItemGroupLineMod Quantity' => 0,
  'CreditCardCreditMod ItemGroupLineMod UnitOfMeasure' => 31,
  'CreditCardCreditMod ItemGroupLineMod OverrideUOMSetRef ListID' => 0,
  'CreditCardCreditMod ItemGroupLineMod OverrideUOMSetRef FullName' => 159,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod TxnLineID' => 0,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod ItemRef ListID' => 0,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod ItemRef FullName' => 159,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod Desc' => 4095,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod Quantity' => 0,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod UnitOfMeasure' => 31,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod OverrideUOMSetRef ListID' => 0,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod OverrideUOMSetRef FullName' => 159,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod Cost' => 0,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod Amount' => 0,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod TaxAmount' => 0,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod CustomerRef ListID' => 0,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod CustomerRef FullName' => 159,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod ClassRef ListID' => 0,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod ClassRef FullName' => 159,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod SalesTaxCodeRef ListID' => 0,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod SalesTaxCodeRef FullName' => 159,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod BillableStatus' => 0,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod OverrideItemAccountRef ListID' => 0,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod OverrideItemAccountRef FullName' => 159,
  'IncludeRetElement' => 50,
];
        
        return $paths;
    }
    
    protected function &_isOptionalPaths()
    {
        static $paths =  [
  'CreditCardCreditMod TxnID' => false,
  'CreditCardCreditMod EditSequence' => false,
  'CreditCardCreditMod AccountRef ListID' => true,
  'CreditCardCreditMod AccountRef FullName' => true,
  'CreditCardCreditMod PayeeEntityRef ListID' => true,
  'CreditCardCreditMod PayeeEntityRef FullName' => true,
  'CreditCardCreditMod TxnDate' => true,
  'CreditCardCreditMod RefNumber' => true,
  'CreditCardCreditMod Memo' => true,
  'CreditCardCreditMod ClearExpenseLines' => true,
  'CreditCardCreditMod ExpenseLineMod TxnLineID' => false,
  'CreditCardCreditMod ExpenseLineMod AccountRef ListID' => true,
  'CreditCardCreditMod ExpenseLineMod AccountRef FullName' => true,
  'CreditCardCreditMod ExpenseLineMod Amount' => true,
  'CreditCardCreditMod ExpenseLineMod TaxAmount' => true,
  'CreditCardCreditMod ExpenseLineMod Memo' => true,
  'CreditCardCreditMod ExpenseLineMod CustomerRef ListID' => true,
  'CreditCardCreditMod ExpenseLineMod CustomerRef FullName' => true,
  'CreditCardCreditMod ExpenseLineMod ClassRef ListID' => true,
  'CreditCardCreditMod ExpenseLineMod ClassRef FullName' => true,
  'CreditCardCreditMod ExpenseLineMod SalesTaxCodeRef ListID' => true,
  'CreditCardCreditMod ExpenseLineMod SalesTaxCodeRef FullName' => true,
  'CreditCardCreditMod ExpenseLineMod BillableStatus' => true,
  'CreditCardCreditMod ClearItemLines' => true,
  'CreditCardCreditMod ItemLineMod TxnLineID' => false,
  'CreditCardCreditMod ItemLineMod ItemRef ListID' => true,
  'CreditCardCreditMod ItemLineMod ItemRef FullName' => true,
  'CreditCardCreditMod ItemLineMod Desc' => true,
  'CreditCardCreditMod ItemLineMod Quantity' => true,
  'CreditCardCreditMod ItemLineMod UnitOfMeasure' => true,
  'CreditCardCreditMod ItemLineMod OverrideUOMSetRef ListID' => true,
  'CreditCardCreditMod ItemLineMod OverrideUOMSetRef FullName' => true,
  'CreditCardCreditMod ItemLineMod Cost' => true,
  'CreditCardCreditMod ItemLineMod Amount' => true,
  'CreditCardCreditMod ItemLineMod TaxAmount' => true,
  'CreditCardCreditMod ItemLineMod CustomerRef ListID' => true,
  'CreditCardCreditMod ItemLineMod CustomerRef FullName' => true,
  'CreditCardCreditMod ItemLineMod ClassRef ListID' => true,
  'CreditCardCreditMod ItemLineMod ClassRef FullName' => true,
  'CreditCardCreditMod ItemLineMod SalesTaxCodeRef ListID' => true,
  'CreditCardCreditMod ItemLineMod SalesTaxCodeRef FullName' => true,
  'CreditCardCreditMod ItemLineMod BillableStatus' => true,
  'CreditCardCreditMod ItemLineMod OverrideItemAccountRef ListID' => true,
  'CreditCardCreditMod ItemLineMod OverrideItemAccountRef FullName' => true,
  'CreditCardCreditMod ItemGroupLineMod TxnLineID' => false,
  'CreditCardCreditMod ItemGroupLineMod ItemGroupRef ListID' => true,
  'CreditCardCreditMod ItemGroupLineMod ItemGroupRef FullName' => true,
  'CreditCardCreditMod ItemGroupLineMod Quantity' => true,
  'CreditCardCreditMod ItemGroupLineMod UnitOfMeasure' => true,
  'CreditCardCreditMod ItemGroupLineMod OverrideUOMSetRef ListID' => true,
  'CreditCardCreditMod ItemGroupLineMod OverrideUOMSetRef FullName' => true,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod TxnLineID' => false,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod ItemRef ListID' => true,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod ItemRef FullName' => true,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod Desc' => true,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod Quantity' => true,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod UnitOfMeasure' => true,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod OverrideUOMSetRef ListID' => true,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod OverrideUOMSetRef FullName' => true,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod Cost' => true,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod Amount' => true,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod TaxAmount' => true,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod CustomerRef ListID' => true,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod CustomerRef FullName' => true,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod ClassRef ListID' => true,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod ClassRef FullName' => true,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod SalesTaxCodeRef ListID' => true,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod SalesTaxCodeRef FullName' => true,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod BillableStatus' => true,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod OverrideItemAccountRef ListID' => true,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod OverrideItemAccountRef FullName' => true,
  'IncludeRetElement' => true,
];
    }
    
    protected function &_sinceVersionPaths()
    {
        static $paths =  [
  'CreditCardCreditMod TxnID' => 999.99,
  'CreditCardCreditMod EditSequence' => 999.99,
  'CreditCardCreditMod AccountRef ListID' => 999.99,
  'CreditCardCreditMod AccountRef FullName' => 999.99,
  'CreditCardCreditMod PayeeEntityRef ListID' => 999.99,
  'CreditCardCreditMod PayeeEntityRef FullName' => 999.99,
  'CreditCardCreditMod TxnDate' => 999.99,
  'CreditCardCreditMod RefNumber' => 999.99,
  'CreditCardCreditMod Memo' => 999.99,
  'CreditCardCreditMod ClearExpenseLines' => 999.99,
  'CreditCardCreditMod ExpenseLineMod TxnLineID' => 999.99,
  'CreditCardCreditMod ExpenseLineMod AccountRef ListID' => 999.99,
  'CreditCardCreditMod ExpenseLineMod AccountRef FullName' => 999.99,
  'CreditCardCreditMod ExpenseLineMod Amount' => 999.99,
  'CreditCardCreditMod ExpenseLineMod TaxAmount' => 6.1,
  'CreditCardCreditMod ExpenseLineMod Memo' => 999.99,
  'CreditCardCreditMod ExpenseLineMod CustomerRef ListID' => 999.99,
  'CreditCardCreditMod ExpenseLineMod CustomerRef FullName' => 999.99,
  'CreditCardCreditMod ExpenseLineMod ClassRef ListID' => 999.99,
  'CreditCardCreditMod ExpenseLineMod ClassRef FullName' => 999.99,
  'CreditCardCreditMod ExpenseLineMod SalesTaxCodeRef ListID' => 999.99,
  'CreditCardCreditMod ExpenseLineMod SalesTaxCodeRef FullName' => 999.99,
  'CreditCardCreditMod ExpenseLineMod BillableStatus' => 999.99,
  'CreditCardCreditMod ClearItemLines' => 999.99,
  'CreditCardCreditMod ItemLineMod TxnLineID' => 999.99,
  'CreditCardCreditMod ItemLineMod ItemRef ListID' => 999.99,
  'CreditCardCreditMod ItemLineMod ItemRef FullName' => 999.99,
  'CreditCardCreditMod ItemLineMod Desc' => 999.99,
  'CreditCardCreditMod ItemLineMod Quantity' => 999.99,
  'CreditCardCreditMod ItemLineMod UnitOfMeasure' => 7,
  'CreditCardCreditMod ItemLineMod OverrideUOMSetRef ListID' => 999.99,
  'CreditCardCreditMod ItemLineMod OverrideUOMSetRef FullName' => 999.99,
  'CreditCardCreditMod ItemLineMod Cost' => 999.99,
  'CreditCardCreditMod ItemLineMod Amount' => 999.99,
  'CreditCardCreditMod ItemLineMod TaxAmount' => 6.1,
  'CreditCardCreditMod ItemLineMod CustomerRef ListID' => 999.99,
  'CreditCardCreditMod ItemLineMod CustomerRef FullName' => 999.99,
  'CreditCardCreditMod ItemLineMod ClassRef ListID' => 999.99,
  'CreditCardCreditMod ItemLineMod ClassRef FullName' => 999.99,
  'CreditCardCreditMod ItemLineMod SalesTaxCodeRef ListID' => 999.99,
  'CreditCardCreditMod ItemLineMod SalesTaxCodeRef FullName' => 999.99,
  'CreditCardCreditMod ItemLineMod BillableStatus' => 999.99,
  'CreditCardCreditMod ItemLineMod OverrideItemAccountRef ListID' => 999.99,
  'CreditCardCreditMod ItemLineMod OverrideItemAccountRef FullName' => 999.99,
  'CreditCardCreditMod ItemGroupLineMod TxnLineID' => 999.99,
  'CreditCardCreditMod ItemGroupLineMod ItemGroupRef ListID' => 999.99,
  'CreditCardCreditMod ItemGroupLineMod ItemGroupRef FullName' => 999.99,
  'CreditCardCreditMod ItemGroupLineMod Quantity' => 999.99,
  'CreditCardCreditMod ItemGroupLineMod UnitOfMeasure' => 7,
  'CreditCardCreditMod ItemGroupLineMod OverrideUOMSetRef ListID' => 999.99,
  'CreditCardCreditMod ItemGroupLineMod OverrideUOMSetRef FullName' => 999.99,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod TxnLineID' => 999.99,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod ItemRef ListID' => 999.99,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod ItemRef FullName' => 999.99,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod Desc' => 999.99,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod Quantity' => 999.99,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod UnitOfMeasure' => 7,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod OverrideUOMSetRef ListID' => 999.99,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod OverrideUOMSetRef FullName' => 999.99,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod Cost' => 999.99,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod Amount' => 999.99,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod TaxAmount' => 6.1,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod CustomerRef ListID' => 999.99,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod CustomerRef FullName' => 999.99,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod ClassRef ListID' => 999.99,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod ClassRef FullName' => 999.99,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod SalesTaxCodeRef ListID' => 999.99,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod SalesTaxCodeRef FullName' => 999.99,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod BillableStatus' => 999.99,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod OverrideItemAccountRef ListID' => 999.99,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod OverrideItemAccountRef FullName' => 999.99,
  'IncludeRetElement' => 4,
];
        
        return $paths;
    }
    
    protected function &_isRepeatablePaths()
    {
        static $paths =  [
  'CreditCardCreditMod TxnID' => false,
  'CreditCardCreditMod EditSequence' => false,
  'CreditCardCreditMod AccountRef ListID' => false,
  'CreditCardCreditMod AccountRef FullName' => false,
  'CreditCardCreditMod PayeeEntityRef ListID' => false,
  'CreditCardCreditMod PayeeEntityRef FullName' => false,
  'CreditCardCreditMod TxnDate' => false,
  'CreditCardCreditMod RefNumber' => false,
  'CreditCardCreditMod Memo' => false,
  'CreditCardCreditMod ClearExpenseLines' => false,
  'CreditCardCreditMod ExpenseLineMod TxnLineID' => false,
  'CreditCardCreditMod ExpenseLineMod AccountRef ListID' => false,
  'CreditCardCreditMod ExpenseLineMod AccountRef FullName' => false,
  'CreditCardCreditMod ExpenseLineMod Amount' => false,
  'CreditCardCreditMod ExpenseLineMod TaxAmount' => false,
  'CreditCardCreditMod ExpenseLineMod Memo' => false,
  'CreditCardCreditMod ExpenseLineMod CustomerRef ListID' => false,
  'CreditCardCreditMod ExpenseLineMod CustomerRef FullName' => false,
  'CreditCardCreditMod ExpenseLineMod ClassRef ListID' => false,
  'CreditCardCreditMod ExpenseLineMod ClassRef FullName' => false,
  'CreditCardCreditMod ExpenseLineMod SalesTaxCodeRef ListID' => false,
  'CreditCardCreditMod ExpenseLineMod SalesTaxCodeRef FullName' => false,
  'CreditCardCreditMod ExpenseLineMod BillableStatus' => false,
  'CreditCardCreditMod ClearItemLines' => false,
  'CreditCardCreditMod ItemLineMod TxnLineID' => false,
  'CreditCardCreditMod ItemLineMod ItemRef ListID' => false,
  'CreditCardCreditMod ItemLineMod ItemRef FullName' => false,
  'CreditCardCreditMod ItemLineMod Desc' => false,
  'CreditCardCreditMod ItemLineMod Quantity' => false,
  'CreditCardCreditMod ItemLineMod UnitOfMeasure' => false,
  'CreditCardCreditMod ItemLineMod OverrideUOMSetRef ListID' => false,
  'CreditCardCreditMod ItemLineMod OverrideUOMSetRef FullName' => false,
  'CreditCardCreditMod ItemLineMod Cost' => false,
  'CreditCardCreditMod ItemLineMod Amount' => false,
  'CreditCardCreditMod ItemLineMod TaxAmount' => false,
  'CreditCardCreditMod ItemLineMod CustomerRef ListID' => false,
  'CreditCardCreditMod ItemLineMod CustomerRef FullName' => false,
  'CreditCardCreditMod ItemLineMod ClassRef ListID' => false,
  'CreditCardCreditMod ItemLineMod ClassRef FullName' => false,
  'CreditCardCreditMod ItemLineMod SalesTaxCodeRef ListID' => false,
  'CreditCardCreditMod ItemLineMod SalesTaxCodeRef FullName' => false,
  'CreditCardCreditMod ItemLineMod BillableStatus' => false,
  'CreditCardCreditMod ItemLineMod OverrideItemAccountRef ListID' => false,
  'CreditCardCreditMod ItemLineMod OverrideItemAccountRef FullName' => false,
  'CreditCardCreditMod ItemGroupLineMod TxnLineID' => false,
  'CreditCardCreditMod ItemGroupLineMod ItemGroupRef ListID' => false,
  'CreditCardCreditMod ItemGroupLineMod ItemGroupRef FullName' => false,
  'CreditCardCreditMod ItemGroupLineMod Quantity' => false,
  'CreditCardCreditMod ItemGroupLineMod UnitOfMeasure' => false,
  'CreditCardCreditMod ItemGroupLineMod OverrideUOMSetRef ListID' => false,
  'CreditCardCreditMod ItemGroupLineMod OverrideUOMSetRef FullName' => false,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod TxnLineID' => false,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod ItemRef ListID' => false,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod ItemRef FullName' => false,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod Desc' => false,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod Quantity' => false,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod UnitOfMeasure' => false,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod OverrideUOMSetRef ListID' => false,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod OverrideUOMSetRef FullName' => false,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod Cost' => false,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod Amount' => false,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod TaxAmount' => false,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod CustomerRef ListID' => false,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod CustomerRef FullName' => false,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod ClassRef ListID' => false,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod ClassRef FullName' => false,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod SalesTaxCodeRef ListID' => false,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod SalesTaxCodeRef FullName' => false,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod BillableStatus' => false,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod OverrideItemAccountRef ListID' => false,
  'CreditCardCreditMod ItemGroupLineMod ItemLineMod OverrideItemAccountRef FullName' => false,
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
  0 => 'CreditCardCreditMod TxnID',
  1 => 'CreditCardCreditMod EditSequence',
  2 => 'CreditCardCreditMod AccountRef ListID',
  3 => 'CreditCardCreditMod AccountRef FullName',
  4 => 'CreditCardCreditMod PayeeEntityRef ListID',
  5 => 'CreditCardCreditMod PayeeEntityRef FullName',
  6 => 'CreditCardCreditMod TxnDate',
  7 => 'CreditCardCreditMod RefNumber',
  8 => 'CreditCardCreditMod Memo',
  9 => 'CreditCardCreditMod ClearExpenseLines',
  10 => 'CreditCardCreditMod ExpenseLineMod TxnLineID',
  11 => 'CreditCardCreditMod ExpenseLineMod AccountRef ListID',
  12 => 'CreditCardCreditMod ExpenseLineMod AccountRef FullName',
  13 => 'CreditCardCreditMod ExpenseLineMod Amount',
  14 => 'CreditCardCreditMod ExpenseLineMod TaxAmount',
  15 => 'CreditCardCreditMod ExpenseLineMod Memo',
  16 => 'CreditCardCreditMod ExpenseLineMod CustomerRef ListID',
  17 => 'CreditCardCreditMod ExpenseLineMod CustomerRef FullName',
  18 => 'CreditCardCreditMod ExpenseLineMod ClassRef ListID',
  19 => 'CreditCardCreditMod ExpenseLineMod ClassRef FullName',
  20 => 'CreditCardCreditMod ExpenseLineMod SalesTaxCodeRef ListID',
  21 => 'CreditCardCreditMod ExpenseLineMod SalesTaxCodeRef FullName',
  22 => 'CreditCardCreditMod ExpenseLineMod BillableStatus',
  23 => 'CreditCardCreditMod ClearItemLines',
  24 => 'CreditCardCreditMod ItemLineMod TxnLineID',
  25 => 'CreditCardCreditMod ItemLineMod ItemRef ListID',
  26 => 'CreditCardCreditMod ItemLineMod ItemRef FullName',
  27 => 'CreditCardCreditMod ItemLineMod Desc',
  28 => 'CreditCardCreditMod ItemLineMod Quantity',
  29 => 'CreditCardCreditMod ItemLineMod UnitOfMeasure',
  30 => 'CreditCardCreditMod ItemLineMod OverrideUOMSetRef ListID',
  31 => 'CreditCardCreditMod ItemLineMod OverrideUOMSetRef FullName',
  32 => 'CreditCardCreditMod ItemLineMod Cost',
  33 => 'CreditCardCreditMod ItemLineMod Amount',
  34 => 'CreditCardCreditMod ItemLineMod TaxAmount',
  35 => 'CreditCardCreditMod ItemLineMod CustomerRef ListID',
  36 => 'CreditCardCreditMod ItemLineMod CustomerRef FullName',
  37 => 'CreditCardCreditMod ItemLineMod ClassRef ListID',
  38 => 'CreditCardCreditMod ItemLineMod ClassRef FullName',
  39 => 'CreditCardCreditMod ItemLineMod SalesTaxCodeRef ListID',
  40 => 'CreditCardCreditMod ItemLineMod SalesTaxCodeRef FullName',
  41 => 'CreditCardCreditMod ItemLineMod BillableStatus',
  42 => 'CreditCardCreditMod ItemLineMod OverrideItemAccountRef ListID',
  43 => 'CreditCardCreditMod ItemLineMod OverrideItemAccountRef FullName',
  44 => 'CreditCardCreditMod ItemGroupLineMod TxnLineID',
  45 => 'CreditCardCreditMod ItemGroupLineMod ItemGroupRef ListID',
  46 => 'CreditCardCreditMod ItemGroupLineMod ItemGroupRef FullName',
  47 => 'CreditCardCreditMod ItemGroupLineMod Quantity',
  48 => 'CreditCardCreditMod ItemGroupLineMod UnitOfMeasure',
  49 => 'CreditCardCreditMod ItemGroupLineMod OverrideUOMSetRef ListID',
  50 => 'CreditCardCreditMod ItemGroupLineMod OverrideUOMSetRef FullName',
  51 => 'CreditCardCreditMod ItemGroupLineMod ItemLineMod TxnLineID',
  52 => 'CreditCardCreditMod ItemGroupLineMod ItemLineMod ItemRef ListID',
  53 => 'CreditCardCreditMod ItemGroupLineMod ItemLineMod ItemRef FullName',
  54 => 'CreditCardCreditMod ItemGroupLineMod ItemLineMod Desc',
  55 => 'CreditCardCreditMod ItemGroupLineMod ItemLineMod Quantity',
  56 => 'CreditCardCreditMod ItemGroupLineMod ItemLineMod UnitOfMeasure',
  57 => 'CreditCardCreditMod ItemGroupLineMod ItemLineMod OverrideUOMSetRef ListID',
  58 => 'CreditCardCreditMod ItemGroupLineMod ItemLineMod OverrideUOMSetRef FullName',
  59 => 'CreditCardCreditMod ItemGroupLineMod ItemLineMod Cost',
  60 => 'CreditCardCreditMod ItemGroupLineMod ItemLineMod Amount',
  61 => 'CreditCardCreditMod ItemGroupLineMod ItemLineMod TaxAmount',
  62 => 'CreditCardCreditMod ItemGroupLineMod ItemLineMod CustomerRef ListID',
  63 => 'CreditCardCreditMod ItemGroupLineMod ItemLineMod CustomerRef FullName',
  64 => 'CreditCardCreditMod ItemGroupLineMod ItemLineMod ClassRef ListID',
  65 => 'CreditCardCreditMod ItemGroupLineMod ItemLineMod ClassRef FullName',
  66 => 'CreditCardCreditMod ItemGroupLineMod ItemLineMod SalesTaxCodeRef ListID',
  67 => 'CreditCardCreditMod ItemGroupLineMod ItemLineMod SalesTaxCodeRef FullName',
  68 => 'CreditCardCreditMod ItemGroupLineMod ItemLineMod BillableStatus',
  69 => 'CreditCardCreditMod ItemGroupLineMod ItemLineMod OverrideItemAccountRef ListID',
  70 => 'CreditCardCreditMod ItemGroupLineMod ItemLineMod OverrideItemAccountRef FullName',
  71 => 'IncludeRetElement',
];
            
        return $paths;
    }
}
