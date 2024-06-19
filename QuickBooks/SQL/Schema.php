<?php

/**
 * Schema mapping methods for mapping XML schemas to SQL schemas, and vice-versa
 *
 * * THANKS! *
 * Extra special thanks go out to Garrett at Space Coast IC for putting gobs of
 * time and effort into completing this schema for a project for his company.
 *
 * @author Keith Palmer <keith@consolibyte.com>, Garrett <grgisme@gmail.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage SQL
 */

/**
 * QuickBooks SQL base class (is this even required?)
 */
QuickBooks_Loader::load('/QuickBooks/SQL.php');

/**
 * XML parsing
 */
QuickBooks_Loader::load('/QuickBooks/XML.php');

/**
 * Various utilities methods
 */
QuickBooks_Loader::load('/QuickBooks/Utilities.php');

/**
 * Map a SQL schema to a qbXML schema
 * @var char
 */
define('QUICKBOOKS_SQL_SCHEMA_MAP_TO_XML', 'q');

/**
 * Map a qbXML schema to an SQL schema
 * @var char
 */
define('QUICKBOOKS_SQL_SCHEMA_MAP_TO_SQL', 's');

/**
 * Schema mapping methods for mapping XML schemas to SQL schemas, and vice versa
 *
 * The QuickBooks SQL mirror server needs to map the QuickBooks qbXML XML
 * schema to an SQL schema that can be stored in a standard SQL database. This
 * class provides static methods which provide mapping from XML to SQL schemas,
 * and then vice-versa for when you need to convert SQL objects back to qbXML
 * objects.
 */
class QuickBooks_SQL_Schema
{
    /**
     * Take a qbXML schema and transform that schema to an SQL schema definition
     *
     * @param string $xml			The XML string to transform
     * @param array $tables			An array of... erm... something?
     * @return boolean
     */
    public static function mapSchemaToSQLDefinition($xml, &$tables)
    {
        $Parser = new QuickBooks_XML_Parser($xml);

        $errnum = 0;
        $errmsg = '';
        $tmp = $Parser->parse($errnum, $errmsg);

        $tmp = $tmp->children();
        $base = current($tmp);

        $tmp = $base->children();
        $rs = next($tmp);

        foreach ($rs->children() as $qbxml) {
            QuickBooks_SQL_Schema::_transform('', $qbxml, $tables);
        }

        /*
        while (count($subtables) > 0)
        {
            $node = array_shift($subtables);

            $subsubtables = array();
            $tables[] = QuickBooks_SQL_Schema::_transform('', $node, $subsubtables);

            $subtables = array_merge($subtables, $subsubtables);
        }
        */

        // The code below tries to guess as a good set of indexes to use for
        //	any database tables we've generated from the schema. The code looks
        //	at all of the fields in the table and if any of them are *ListID or
        //	*TxnID it makes them indexes.

        // This is a list of field names that will *always* be assigned
        //	indexes, regardless of what table they are in
        $always_index_fields = [
            'qbsql_external_id',
            'Name',
            'FullName',
            'EntityType',
            'TxnType',
            'Email',
            //'Phone',
            'IsActive',
            'RefNumber',
            //'Address_City',
            //'Address_State',
            'Address_Country',
            //'Address_PostalCode',
            //'BillAddress_City',
            //'BillAddress_State',
            'BillAddress_Country',
            //'BillAddress_PostalCode',
            //'ShipAddress_City',
            //'ShipAddress_State',
            'ShipAddress_Country',
            //'ShipAddress_PostalCode',
            'CompanyName',
            //'FirstName',
            'LastName',
            //'Contact',
            'TxnDate',
            'IsPaid',
            'IsPending',
            'IsManuallyClosed',
            'IsFullyReceived',
            'IsToBePrinted',
            'IsToBeEmailed',
            'IsFullyInvoiced',
            //'IsFinanceCharge',
            ];

        // This is a list of table.field names that will be assigned indexes
        $always_index_tablefields = [
            //'Account.AccountType',
            ];

        /*
        '*FullName',
        '*ListID',
        '*TxnID',
        '*EntityType',
        '*TxnType',
        '*LineID',
        */

        foreach ($tables as $table => $tabledef) {
            $uniques = [];
            $indexes = [];

            foreach ($tabledef[1] as $field => $fielddef) {
                if ($field == 'ListID' or 		// Unique keys
                    $field == 'TxnID' or
                    $field == 'Name') {
                    // We can't apply indexes to TEXT columns, so we need to
                    //	check and make sure the column isn't of type TEXT
                    //	before we decide to use this as an index

                    if ($fielddef[0] != QUICKBOOKS_DRIVER_SQL_TEXT) {
                        $uniques[] = $field;
                    }
                } elseif (substr($field, -6, 6) == 'ListID' or 		// Other things we should index for performance
                    substr($field, -5, 5) == 'TxnID' or
                    substr($field, -6, 6) == 'LineID' or
                    in_array($field, $always_index_fields) or
                    in_array($table . '.' . $field, $always_index_tablefields)) {
                    // We can't apply indexes to TEXT columns, so we need to
                    //	check and make sure the column isn't of type TEXT
                    //	before we decide to use this as an index

                    if ($fielddef[0] != QUICKBOOKS_DRIVER_SQL_TEXT) {
                        $indexes[] = $field;
                    }
                }
            }

            //print_r($indexes);
            //print_r($uniques);

            $tables[$table][3] = $indexes;
            $tables[$table][4] = $uniques;
        }

        return true;
    }

    /**
     * Transform an XML document into an SQL schema
     *
     * @param string $curpath
     * @param QuickBooks_XML_Node $node
     * @param array $tables
     * @return
     */
    protected static function _transform($curpath, $node, &$tables)
    {
        print('' . $curpath . '   node: ' . $node->name() . "\n");

        $table = '';
        $field = '';

        $this_sql = [];
        $other_sql = [];
        QuickBooks_SQL_Schema::mapToSchema($curpath . ' ' . $node->name(), QUICKBOOKS_SQL_SCHEMA_MAP_TO_SQL, $this_sql, $other_sql);

        foreach (array_merge([ $this_sql ], $other_sql) as $sql) {
            $table = $sql[0];
            $field = $sql[1];

            /*
            if (!$sql[0] or !$sql[1])
            {
                print('		table for node: ' . $sql[0] . "\n");
                print('		field for node: ' . $sql[1] . "\n");
            }
            else
            {
                print("\n");
            }
            */

            if ($table) {
                if (!isset($tables[$table])) {
                    $tables[$table] = [
                        0 => $table,
                        1 => [],		// fields
                        2 => null, 			// primary key
                        3 => [], 		// other keys
                        4 => [  ], 		// uniques
                        ];
                }
            }

            if ($table and $field) {
                if (!isset($tables[$table][1][$field])) {
                    $tables[$table][1][$field] = QuickBooks_SQL_Schema::mapFieldToSQLDefinition($table, $field, $node->data());
                }
            }
        }

        if ($node->childCount()) {
            foreach ($node->children() as $child) {
                QuickBooks_SQL_Schema::_transform($curpath . ' ' . $node->name(), $child, $tables);
            }
        }

        return true;
    }

    /**
     * Tell whether or not a string matches the given pattern (replacement for fnmatch(), which isn't available on some systems)
     *
     * @param string $pattern		The pattern (use wild-cards like * and ?)
     * @param string $str			The string to test
     * @return boolean
     */
    protected static function _fnmatch($pattern, $str)
    {
        return QuickBooks_Utilities::fnmatch($pattern, $str);
    }

    /**
     *
     *
     *
     *
     */
    public static function mapIndexes($table)
    {

    }

    /**
     * Tell the SQL primary key for a given XML path, or the XML path for a given table/field combination
     *
     * @todo This should support the uppercase/lowercase table/field names option set (->_defaults() a generic method, everything calls it to get default options)
     *
     * @param string $path_or_tablefield
     * @param string $mode
     * @param mixed $map					In SCHEMA_MAP_TO_SQL mode, this is set to a tuple containing the SQL table and SQL field name, in SQL_MAP_TO_SCHEMA mode this is set to the XML path
     * @return void
     */
    public static function mapPrimaryKey($path_or_tablefield, $mode, &$map, $options = [])
    {
        static $xml_to_sql = [
            'AccountRet' => 																			[ 'Account', 'ListID' ],
            'AccountRet TaxLineInfoRet' => 																[ 'Account_TaxLineInfo', [ 'Account_ListID', 'TaxLineInfo_TaxLineID'] ],
            'AccountRet DataExtRet' => 																	[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'BillingRateRet' =>  																		[ 'BillingRate', 'ListID' ],
            'BillingRateRet BillingRatePerItemRet' => 													[ 'BillingRate_BillingRatePerItem', [ 'BillingRate_ListID', 'Item_ListID' ] ],
            'BillPaymentCheckRet' => 																	[ 'BillPaymentCheck', 'TxnID' ],
            'BillPaymentCheckRet AppliedToTxnRet' => 													[ 'BillPaymentCheck_AppliedToTxn', [ 'ToTxnID', 'FromTxnID' ] ],
            'BillPaymentCheckRet DataExtRet' => 														[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'BillPaymentCreditCardRet' => 																[ 'BillPaymentCreditCard', 'TxnID' ],
            'BillPaymentCreditCardRet AppliedToTxnRet' => 												[ 'BillPaymentCreditCard_AppliedToTxn', [ 'ToTxnID', 'FromTxnID' ] ],
            'BillPaymentCreditCardRet DataExtRet' => 													[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'BillRet' => 																				[ 'Bill', 'TxnID' ],
            'BillRet LinkedTxn' => 																		[ 'Bill_LinkedTxn', [ 'ToTxnID', 'FromTxnID' ] ],
            'BillRet ExpenseLineRet' => 																[ 'Bill_ExpenseLine', [ 'Bill_TxnID', 'TxnLineID'] ],
            'BillRet ItemLineRet' => 																	[ 'Bill_ItemLine', [ 'Bill_TxnID', 'TxnLineID' ] ],
            'BillRet ItemGroupLineRet' => 																[ 'Bill_ItemGroupLine', [ 'Bill_TxnID', 'TxnLineID' ] ],
            'BillRet ItemGroupLineRet ItemLineRet' => 													[ 'Bill_ItemGroupLine_ItemLine', [ 'Bill_TxnID', 'Bill_ItemGroupLine_TxnLineID', 'TxnLineID' ] ],
            'BillRet DataExtRet' => 																	[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'BillToPayRet BillToPay' => 																[ 'BillToPay', 'TxnID' ],
            'BillToPayRet CreditToApply' => 															[ 'CreditToApply', 'TxnID' ],
            'BuildAssemblyRet' => 																		[ 'BuildAssembly', 'TxnID' ],
            'ChargeRet' => 																				[ 'Charge', 'TxnID' ],
            'ChargeRet DataExtRet' => 																	[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'CheckRet' => 																				[ 'Check', 'TxnID' ],
            'CheckRet ExpenseLineRet' => 																[ 'Check_ExpenseLine', [ 'Check_TxnID', 'TxnLineID' ] ],
            'CheckRet ItemLineRet' => 																	[ 'Check_ItemLine', [ 'Check_TxnID', 'TxnLineID' ] ],
            'CheckRet ItemGroupLineRet' => 																[ 'Check_ItemGroupLine', [ 'Check_TxnID', 'TxnLineID' ] ],
            'CheckRet ItemGroupLineRet ItemLineRet' => 													[ 'Check_ItemGroupLine_ItemLine', [ 'Check_TxnID', 'Check_ItemGroupLine_TxnLineID', 'TxnLineID' ] ],
            'CheckRet DataExtRet' => 																	[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'CheckRet LinkedTxn' => 																	[ 'Check_LinkedTxn', [ 'ToTxnID', 'FromTxnID' ] ],
            'ClassRet' => 																				[ 'Class', 'ListID' ],
            'CompanyRet' => 																			[ 'Company', 'CompanyName' ],
            'CompanyRet SubscribedServices Services' =>													[ 'Company_SubscribedServices_Services', ['Company_CompanyName', 'Name'] ],
            'CompanyRet DataExtRet' => 																	[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'HostRet' => 																				[ 'Host', 'ProductName' ],
            'PreferencesRet' => 																		[ 'Preferences', 'qbsql_external_id' ],
            'CreditCardChargeRet' =>																	[ 'CreditCardCharge', 'TxnID' ],
            'CreditCardChargeRet ExpenseLineRet' =>														[ 'CreditCardCharge_ExpenseLine', [ 'CreditCardCharge_TxnID', 'TxnLineID' ] ],
            'CreditCardChargeRet ItemLineRet' =>														[ 'CreditCardCharge_ItemLine', [ 'CreditCardCharge_TxnID', 'TxnLineID' ] ],
            'CreditCardChargeRet ItemGroupLineRet' =>													[ 'CreditCardCharge_ItemGroupLine', [ 'CreditCardCharge_TxnID', 'TxnLineID' ] ],
            'CreditCardChargeRet ItemGroupLineRet ItemLineRet' =>										[ 'CreditCardCharge_ItemGroupLine_ItemLine', [ 'CreditCardCharge_TxnID', 'CreditCardCharge_ItemGroupLine_TxnLineID', 'TxnLineID' ] ],
            'CreditCardChargeRet DataExtRet' => 														[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'CreditCardCreditRet' =>																	[ 'CreditCardCredit', 'TxnID' ],
            'CreditCardCreditRet ExpenseLineRet' =>														[ 'CreditCardCredit_ExpenseLine', [ 'CreditCardCredit_TxnID', 'TxnLineID' ] ],
            'CreditCardCreditRet ItemLineRet' =>														[ 'CreditCardCredit_ItemLine', [ 'CreditCardCredit_TxnID', 'TxnLineID' ] ],
            'CreditCardCreditRet ItemGroupLineRet' =>													[ 'CreditCardCredit_ItemGroupLine', [ 'CreditCardCredit_TxnID', 'TxnLineID' ] ],
            'CreditCardCreditRet ItemGroupLineRet ItemLineRet' =>										[ 'CreditCardCredit_ItemGroupLine_ItemLine', [ 'CreditCardCredit_TxnID', 'CreditCardCredit_ItemGroupLine_TxnLineID', 'TxnLineID' ] ],
            'CreditCardCreditRet DataExtRet' => 														[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'CreditMemoRet' => 																			[ 'CreditMemo', 'TxnID' ],
            'CreditMemoRet CreditMemoLineRet' => 														[ 'CreditMemo_CreditMemoLine', [ 'CreditMemo_TxnID', 'TxnLineID' ] ],
            'CreditMemoRet CreditMemoLineGroupRet' => 													[ 'CreditMemo_CreditMemoLineGroup', [ 'CreditMemo_TxnID', 'TxnLineID' ] ],
            //'CreditMemoRet CreditMemoLineGroupRet ItemGroupRef' => 									array( null, null ),
            //'CreditMemoRet CreditMemoLineGroupRet ItemGroupRef *' => 									array( 'CreditMemo_CreditMemoLineGroup', 'ItemGroup_*' ),
            'CreditMemoRet CreditMemoLineGroupRet CreditMemoLineRet' => 								[ 'CreditMemo_CreditMemoLineGroup_CreditMemoLine', [ 'CreditMemo_TxnID', 'CreditMemo_CreditMemoLineGroup_TxnLineID', 'TxnLineID' ] ],
            'CreditMemoRet DataExtRet' => 																[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'CreditMemoRet CreditMemoLineGroupRet DataExtRet' => 										[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'CreditMemoRet CreditMemoLineGroupRet CreditMemoLineRet DataExtRet' => 						[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'CreditMemoRet LinkedTxn' => 																[ 'CreditMemo_LinkedTxn', [ 'ToTxnID', 'FromTxnID' ] ],
            'CustomerRet' => 																			[ 'Customer', 'ListID' ],
            'CustomerRet DataExtRet' => 																[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'CustomerMsgRet' => 																		[ 'CustomerMsg', 'ListID' ],
            'CustomerTypeRet' => 																		[ 'CustomerType', 'ListID' ],
            'CurrencyRet' => 																			[ 'Currency', 'ListID' ],
            'DataExtDefRet' => 																			[ 'DataExtDef', 'DataExtName' ],
            'DataExtDefRet AssignToObject' => 															[ 'DataExtDef_AssignToObject', [ 'DataExtDef_DataExtName', 'AssignToObject' ] ],
            'DateDrivenTermsRet' => 																	[ 'DateDrivenTerms', 'ListID' ],
            'DepositRet' => 																			[ 'Deposit', 'TxnID' ],
            'DepositRet DepositLineRet' => 																[ 'Deposit_DepositLine', [ 'Deposit_TxnID', 'TxnID' ] ],
            'DepositRet DataExtRet' => 																	[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'EmployeeRet' => 																			[ 'Employee', 'ListID' ],
            'EmployeeRet EmployeePayrollInfo Earnings' => 												[ 'Employee_Earnings', [ 'Employee_ListID', 'PayrollItemWage_ListID' ] ],
            'EmployeeRet DataExtRet' => 																[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'EstimateRet' => 																			[ 'Estimate', 'TxnID' ],
            'EstimateRet EstimateLineRet' => 															[ 'Estimate_EstimateLine', [ 'Estimate_TxnID', 'TxnLineID' ] ],
            'EstimateRet EstimateLineRet DataExtRet' => 												[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'EstimateRet EstimateLineGroupRet' => 														[ 'Estimate_EstimateLineGroup', [ 'Estimate_TxnID', 'TxnLineID' ] ],
            'EstimateRet EstimateLineGroupRet DataExtRet' => 											[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'EstimateRet EstimateLineGroupRet EstimateLineRet' => 										[ 'Estimate_EstimateLineGroup_EstimateLine', [ 'Estimate_TxnID', 'Estimate_EstimateLineGroup_TxnLineID', 'TxnLineID' ] ],
            'EstimateRet EstimateLineGroupRet EstimateLineRet DataExtRet' => 							[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'EstimateRet LinkedTxn' => 																	[ 'Estimate_LinkedTxn', [ 'ToTxnID', 'FromTxnID' ] ],
            'EstimateRet DataExtRet' => 																[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'InventoryAdjustmentRet' => 																[ 'InventoryAdjustment', 'TxnID' ],
            'InventoryAdjustmentRet InventoryAdjustmentLineRet' => 										[ 'InventoryAdjustment_InventoryAdjustmentLine', [ 'InventoryAdjustment_TxnID', 'TxnLineID' ] ],
            'InventoryAdjustmentRet DataExtRet' => 														[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'InvoiceRet' => 																			[ 'Invoice', 'TxnID' ],
            'InvoiceRet InvoiceLineRet' => 																[ 'Invoice_InvoiceLine', [ 'Invoice_TxnID', 'TxnLineID' ] ],
            'InvoiceRet InvoiceLineRet DataExtRet' => 													[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'InvoiceRet InvoiceLineGroupRet' => 														[ 'Invoice_InvoiceLineGroup', [ 'Invoice_TxnID', 'TxnLineID' ] ],
            'InvoiceRet InvoiceLineGroupRet DataExtRet' => 												[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'InvoiceRet InvoiceLineGroupRet InvoiceLineRet' => 											[ 'Invoice_InvoiceLineGroup_InvoiceLine', [ 'Invoice_TxnID', 'Invoice_InvoiceLineGroup_TxnLineID', 'TxnLineID' ] ],
            'InvoiceRet InvoiceLineGroupRet InvoiceLineRet DataExtRet' => 								[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'InvoiceRet LinkedTxn' => 																	[ 'Invoice_LinkedTxn', [ 'ToTxnID', 'FromTxnID' ] ],
            'InvoiceRet DataExtRet' => 																	[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'ItemInventoryRet' => 																		[ 'ItemInventory', 'ListID' ],
            'ItemInventoryRet DataExtRet' => 															[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'ItemInventoryAssemblyRet' => 																[ 'ItemInventoryAssembly', 'ListID' ],
            'ItemInventoryAssemblyRet ItemInventoryAssemblyLine' => 									[ 'ItemInventoryAssembly_ItemInventoryAssemblyLine', [ 'ItemInventoryAssembly_ListID', 'ItemInventory_ListID' ] ],
            'ItemInventoryAssemblyRet DataExtRet' => 													[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'ItemNonInventoryRet' => 																	[ 'ItemNonInventory', 'ListID' ],
            'ItemNonInventoryRet DataExtRet' => 														[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'ItemDiscountRet' => 																		[ 'ItemDiscount', 'ListID' ],
            'ItemDiscountRet DataExtRet' => 															[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'ItemFixedAssetRet' => 																		[ 'ItemFixedAsset', 'ListID' ],
            'ItemFixedAssetRet DataExtRet' => 															[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'ItemGroupRet' => 																			[ 'ItemGroup', 'ListID' ],
            'ItemGroupRet ItemGroupLine' => 															[ 'ItemGroup_ItemGroupLine', [ 'ItemGroup_ListID', 'Item_ListID' ] ],
            'ItemGroupRet DataExtRet' => 																[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'ItemOtherChargeRet' => 																	[ 'ItemOtherCharge', 'ListID' ],
            'ItemOtherChargeRet DataExtRet' => 															[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'ItemPaymentRet' => 																		[ 'ItemPayment', 'ListID' ],
            'ItemPaymentRet DataExtRet' => 																[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'ItemReceiptRet' => 																		[ 'ItemReceipt', 'TxnID' ],
            'ItemReceiptRet ExpenseLineRet' => 															[ 'ItemReceipt_ExpenseLine', [ 'ItemReceipt_TxnID', 'TxnLineID' ] ],
            'ItemReceiptRet ItemLineRet' => 															[ 'ItemReceipt_ItemLine', [ 'ItemReceipt_TxnID', 'TxnLineID' ] ],
            'ItemReceiptRet ItemGroupLineRet' => 														[ 'ItemReceipt_ItemGroupLine', [ 'ItemReceipt_TxnID', 'TxnLineID' ] ],
            'ItemReceiptRet ItemGroupLineRet ItemLineRet' => 											[ 'ItemReceipt_ItemGroupLine_ItemLine', [ 'ItemReceipt_TxnID', 'ItemReceipt_ItemGroupLine_TxnLineID', 'TxnLineID' ] ],
            'ItemReceiptRet LinkedTxn' => 																[ 'ItemReceipt_LinkedTxn', [ 'ToTxnID', 'FromTxnID' ] ],
            'ItemReceiptRet DataExtRet' => 																[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'ItemSalesTaxRet' => 																		[ 'ItemSalesTax', 'ListID' ],
            'ItemSalesTaxRet DataExtRet' => 															[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'ItemSalesTaxGroupRet' => 																	[ 'ItemSalesTaxGroup', 'ListID' ],
            'ItemSalesTaxGroupRet ItemSalesTaxRef' => 													[ 'ItemSalesTaxGroup_ItemSalesTax', [ 'ItemSalesTaxGroup_ListID', 'ListID' ] ],
            'ItemSalesTaxGroupRet DataExtRet' => 														[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'ItemServiceRet' => 																		[ 'ItemService', 'ListID' ],
            'ItemServiceRet DataExtRet' => 																[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'ItemSubtotalRet' => 																		[ 'ItemSubtotal', 'ListID' ],
            'ItemSubtotalRet DataExtRet' => 															[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'JobTypeRet' => 																			[ 'JobType', 'ListID' ],
            'JournalEntryRet' => 																		[ 'JournalEntry', 'TxnID' ],
            'JournalEntryRet JournalCreditLine' => 														[ 'JournalEntry_JournalCreditLine', [ 'JournalEntry_TxnID', 'TxnLineID' ] ],
            'JournalEntryRet JournalDebitLine' => 														[ 'JournalEntry_JournalDebitLine', [ 'JournalEntry_TxnID', 'TxnLineID' ] ],
            'JournalEntryRet DataExtRet' => 															[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'PaymentMethodRet' => 																		[ 'PaymentMethod', 'ListID' ],
            'PayrollItemWageRet' => 																	[ 'PayrollItemWage', 'ListID' ],
            'PriceLevelRet' => 																			[ 'PriceLevel', 'ListID' ],
            'PriceLevelRet PriceLevelPerItemRet' => 													[ 'PriceLevel_PriceLevelPerItem', [ 'PriceLevel_ListID', 'Item_ListID' ] ],
            'PurchaseOrderRet' =>																		[ 'PurchaseOrder', 'TxnID' ],
            'PurchaseOrderRet PurchaseOrderLineRet' =>													[ 'PurchaseOrder_PurchaseOrderLine', [ 'PurchaseOrder_TxnID', 'TxnLineID' ] ],
            'PurchaseOrderRet PurchaseOrderLineRet DataExtRet' => 										[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'PurchaseOrderRet PurchaseOrderLineGroupRet' =>												[ 'PurchaseOrder_PurchaseOrderLineGroup', [ 'PurchaseOrder_TxnID', 'TxnLineID' ] ],
            'PurchaseOrderRet PurchaseOrderLineGroupRet DataExtRet' => 									[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'PurchaseOrderRet PurchaseOrderLineGroupRet PurchaseOrderLineRet' =>						[ 'PurchaseOrder_PurchaseOrderLineGroup_PurchaseOrderLine', [ 'PurchaseOrder_TxnID', 'PurchaseOrder_PurchaseOrderLineGroup_TxnLineID', 'TxnLineID' ] ],
            'PurchaseOrderRet PurchaseOrderLineGroupRet PurchaseOrderLineRet DataExtRet' => 			[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'PurchaseOrderRet LinkedTxn' => 															[ 'PurchaseOrder_LinkedTxn', [ 'ToTxnID', 'FromTxnID' ] ],
            'PurchaseOrderRet DataExtRet' => 															[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'ReceivePaymentRet' => 																		[ 'ReceivePayment', 'TxnID' ],
            'ReceivePaymentRet AppliedToTxnRet' => 														[ 'ReceivePayment_AppliedToTxn', [ 'ToTxnID', 'FromTxnID' ] ],
            'ReceivePaymentRet DataExtRet' => 															[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'SalesOrderRet' => 																			[ 'SalesOrder', 'TxnID' ],
            'SalesOrderRet SalesOrderLineRet' => 														[ 'SalesOrder_SalesOrderLine', [ 'SalesOrder_TxnID', 'TxnLineID' ] ],
            'SalesOrderRet SalesOrderLineRet DataExtRet' => 											[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'SalesOrderRet SalesOrderLineGroupRet' => 													[ 'SalesOrder_SalesOrderLineGroup', [ 'SalesOrder_TxnID', 'TxnLineID' ] ],
            'SalesOrderRet SalesOrderLineGroupRet DataExtRet' => 										[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'SalesOrderRet SalesOrderLineGroupRet SalesOrderLineRet' => 								[ 'SalesOrder_SalesOrderLineGroup_SalesOrderLine', [ 'SalesOrder_TxnID', 'SalesOrder_SalesOrderLineGroup_TxnLineID', 'TxnLineID' ] ],
            'SalesOrderRet SalesOrderLineGroupRet SalesOrderLineRet DataExtRet' => 						[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'SalesOrderRet LinkedTxn' => 																[ 'SalesOrder_LinkedTxn', [ 'ToTxnID', 'FromTxnID' ] ],
            'SalesOrderRet DataExtRet' => 																[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'SalesReceiptRet' =>																		[ 'SalesReceipt', 'TxnID' ],
            'SalesReceiptRet SalesReceiptLineRet' =>													[ 'SalesReceipt_SalesReceiptLine', [ 'SalesReceipt_TxnID', 'TxnLineID' ] ],
            'SalesReceiptRet SalesReceiptLineRet DataExtRet' => 										[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'SalesReceiptRet SalesReceiptLineGroupRet' =>												[ 'SalesReceipt_SalesReceiptLineGroup', [ 'SalesReceipt_TxnID', 'TxnLineID' ] ],
            'SalesReceiptRet SalesReceiptLineGroupRet DataExtRet' => 									[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'SalesReceiptRet SalesReceiptLineGroupRet SalesReceiptLineRet' =>							[ 'SalesReceipt_SalesReceiptLineGroup_SalesReceiptLine', [ 'SalesReceipt_TxnID', 'SalesReceipt_SalesReceiptLineGroup_TxnLineID', 'TxnLineID' ] ],
            'SalesReceiptRet SalesReceiptLineGroupRet SalesReceiptLineRet DataExtRet' => 				[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'SalesReceiptRet DataExtRet' => 															[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'SalesRepRet' =>																			[ 'SalesRep', 'ListID' ],
            'SalesTaxCodeRet' =>																		[ 'SalesTaxCode', 'ListID' ],
            'ShipMethodRet' =>																			[ 'ShipMethod', 'ListID' ],
            'StandardTermsRet' => 																		[ 'StandardTerms', 'ListID' ],
            'TimeTrackingRet' => 																		[ 'TimeTracking', 'TxnID' ],
            'UnitOfMeasureSetRet' => 																	[ 'UnitOfMeasureSet', 'ListID' ],
            'UnitOfMeasureSetRet RelatedUnit' => 														[ 'UnitOfMeasureSet_RelatedUnit', [ 'UnitOfMeasureSet_ListID', 'Name' ] ],
            'UnitOfMeasureSetRet DefaultUnit' => 														[ 'UnitOfMeasureSet_DefaultUnit', [ 'UnitOfMeasureSet_ListID', 'UnitUsedFor' ] ],
            'VehicleRet' => 																			[ 'Vehicle', 'ListID' ],
            'VehicleMileageRet' => 																		[ 'VehicleMileage', 'TxnID' ],
            'VendorRet' => 																				[ 'Vendor', 'ListID' ],
            'VendorRet DataExtRet' => 																	[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'VendorCreditRet' => 																		[ 'VendorCredit', 'TxnID' ],
            'VendorCreditRet ExpenseLineRet' => 														[ 'VendorCredit_ExpenseLine', [ 'VendorCredit_TxnID', 'TxnLineID' ] ],
            'VendorCreditRet ItemLineRet' => 															[ 'VendorCredit_ItemLine', [ 'VendorCredit_TxnID', 'TxnLineID' ] ],
            'VendorCreditRet ItemGroupLineRet' => 														[ 'VendorCredit_ItemGroupLine', [ 'VendorCredit_TxnID', 'TxnLineID' ] ],
            'VendorCreditRet ItemGroupLineRet ItemLineRet' => 											[ 'VendorCredit_ItemGroupLine_ItemLine', [ 'VendorCredit_TxnID', 'VendorCredit_ItemGroupLine_TxnLineID', 'TxnLineID' ] ],
            'VendorCreditRet LinkedTxn' => 																[ 'VendorCredit_LinkedTxn', [ 'ToTxnID', 'FromTxnID' ] ],
            'VendorCreditRet DataExtRet' => 															[ 'DataExt', [ 'EntityType', 'TxnType', 'Entity_ListID', 'Txn_TxnID' ] ],
            'VendorTypeRet' => 																			[ 'VendorType', 'ListID' ],
            'WorkersCompCodeRet' => 																	[ 'WorkersCompCode', 'ListID' ],

            ];

        if ($mode == QUICKBOOKS_SQL_SCHEMA_MAP_TO_SQL) {
            if (!isset($xml_to_sql[$path_or_tablefield])) {
                if (substr($path_or_tablefield, -3, 3) != 'Ret') {
                    //$path_or_tablefield = substr($path_or_tablefield, 0, -3);
                    $path_or_tablefield .= 'Ret';

                    if (isset($xml_to_sql[$path_or_tablefield])) {
                        $map = $xml_to_sql[$path_or_tablefield];
                        QuickBooks_SQL_Schema::_applyOptions($map, QUICKBOOKS_SQL_SCHEMA_MAP_TO_SQL, $options);
                    }
                }
            } else {
                $map = $xml_to_sql[$path_or_tablefield];
                QuickBooks_SQL_Schema::_applyOptions($map, QUICKBOOKS_SQL_SCHEMA_MAP_TO_SQL, $options);
            }
        } else {

        }

        return;
    }

    /**
     * Map an XML node path to an SQL table/field OR map an SQL table/field to an XML node path
     *
     * @param string $path			The XML path *or*
     * @param char $mode
     * @param array $map
     * @return void
     */
    public static function mapToSchema($path_or_tablefield, $mode, &$map, &$others, $options = [])
    {
        static $xml_to_sql = [
            'AccountRet' => 							[ 'Account', null ],
            'AccountRet ParentRef' => 					[ null, null ],
            'AccountRet ParentRef *' => 				[ 'Account', 'Parent_*' ],
            'AccountRet TaxLineInfoRet' => 				[ 'Account_TaxLineInfo', null ],
            //'AccountRet TaxLineInfoRet TaxLineID' => 	array( 'Account_TaxLineInfo', 'TaxLineInfo_TaxLineID' ),
            'AccountRet TaxLineInfoRet *' => 			[ 'Account_TaxLineInfo', 'TaxLineInfo_*' ],
            //'AccountRet DataExtRet' => 				array( null, null ),
            //'AccountRet DataExtRet *' => 				array( 'DataExt', '*' ),
            'AccountRet Desc' => 						[ 'Account', 'Descrip' ],

            'AccountRet DataExtRet' => 					[ 'DataExt', null ],
            'AccountRet DataExtRet *' => 				[ 'DataExt', '*' ],

            'AccountRet *' => 							[ 'Account', '*' ],

            'BillingRateRet' => 									[ 'BillingRate', null ],
            'BillingRateRet BillingRatePerItemRet' => 				[ null, null ],
            'BillingRateRet BillingRatePerItemRet ItemRef' => 		[ null, null ],
            'BillingRateRet BillingRatePerItemRet ItemRef *' => 	[ 'BillingRate_BillingRatePerItem', 'Item_*' ],
            'BillingRateRet BillingRatePerItemRet *' => 			[ 'BillingRate_BillingRatePerItem', '*' ],
            'BillingRateRet *' => 									[ 'BillingRate', '*' ],

            'BillPaymentRet' => 									[ 'BillPayment', null ],
            'BillPaymentRet *' => 									[ 'BillPayment', '*' ],

            'BillPaymentCheckRet' => 										[ 'BillPaymentCheck', null ],

            'BillPaymentCheckRet PayeeEntityRef' => 						[ null, null ],
            'BillPaymentCheckRet PayeeEntityRef *' =>						[ 'BillPaymentCheck', 'PayeeEntity_*' ],
            'BillPaymentCheckRet APAccountRef' => 							[ null, null ],
            'BillPaymentCheckRet APAccountRef *' => 						[ 'BillPaymentCheck', 'APAccount_*' ],
            'BillPaymentCheckRet BankAccountRef' =>							[ null, null ],
            'BillPaymentCheckRet BankAccountRef *' => 						[ 'BillPaymentCheck', 'BankAccount_*' ],
            'BillPaymentCheckRet Address' => 								[ null, null ],
            'BillPaymentCheckRet Address *' => 								[ 'BillPaymentCheck', 'Address_*' ],
            'BillPaymentCheckRet AddressBlock' => 							[ null, null ],
            'BillPaymentCheckRet AddressBlock *' => 						[ 'BillPaymentCheck', 'AddressBlock_*' ],
            'BillPaymentCheckRet AppliedToTxnRet' => 						[ null, null ],
            'BillPaymentCheckRet AppliedToTxnRet TxnID' => 					[ 'BillPaymentCheck_AppliedToTxn', 'ToTxnID' ],
            'BillPaymentCheckRet AppliedToTxnRet DiscountAccountRef' => 	[ null, null ],
            'BillPaymentCheckRet AppliedToTxnRet DiscountAccountRef *' => 	[ 'BillPaymentCheck_AppliedToTxn', 'DiscountAccount_*' ],
            'BillPaymentCheckRet AppliedToTxnRet *' => 						[ 'BillPaymentCheck_AppliedToTxn', '*' ],
            'BillPaymentCheckRet DataExtRet' => 							[ 'DataExt', null ],
            'BillPaymentCheckRet DataExtRet *' => 							[ 'DataExt', '*' ],
            'BillPaymentCheckRet *' => 										[ 'BillPaymentCheck', '*' ],

            'BillPaymentCreditCardRet' => 										[ 'BillPaymentCreditCard', null ],

            'BillPaymentCreditCardRet PayeeEntityRef' => 						[ null, null ],
            'BillPaymentCreditCardRet PayeeEntityRef *' => 						[ 'BillPaymentCreditCard', 'PayeeEntity_*' ],
            'BillPaymentCreditCardRet APAccountRef' => 							[ null, null ],
            'BillPaymentCreditCardRet APAccountRef *' => 						[ 'BillPaymentCreditCard', 'APAccount_*' ],
            'BillPaymentCreditCardRet CreditCardAccountRef' => 					[ null, null ],
            'BillPaymentCreditCardRet CreditCardAccountRef *' => 				[ 'BillPaymentCreditCard', 'CreditCardAccount_*' ],
            'BillPaymentCreditCardRet AppliedToTxnRet' => 						[ null, null ],
            'BillPaymentCreditCardRet AppliedToTxnRet TxnID' => 				[ 'BillPaymentCreditCard_AppliedToTxn', 'ToTxnID' ],
            'BillPaymentCreditCardRet AppliedToTxnRet DiscountAccountRef' => 	[ null, null ],
            'BillPaymentCreditCardRet AppliedToTxnRet DiscountAccountRef *' => 	[ 'BillPaymentCreditCard_AppliedToTxn', 'DiscountAccount_*' ],
            'BillPaymentCreditCardRet AppliedToTxnRet *' => 					[ 'BillPaymentCreditCard_AppliedToTxn', '*' ],
            'BillPaymentCreditCardRet DataExtRet' => 							[ 'DataExt', null ],
            'BillPaymentCreditCardRet DataExtRet *' => 							[ 'DataExt', '*' ],

            'BillPaymentCreditCardRet *' => 									[ 'BillPaymentCreditCard', '*' ],

            'BillRet' => 												[ 'Bill', null ],
            'BillRet VendorRef' => 										[ null, null ],
            'BillRet VendorRef *' => 									[ 'Bill', 'Vendor_*' ],
            'BillRet APAccountRef' => 									[ null, null ],
            'BillRet APAccountRef *' => 								[ 'Bill', 'APAccount_*' ],
            'BillRet TermsRef' => 										[ null, null ],
            'BillRet TermsRef *' => 									[ 'Bill', 'Terms_*' ],
            'BillRet CurrencyRef' => 									[ null, null ],
            'BillRet CurrencyRef *' => 									[ 'Bill', 'Currency_*' ],
            'BillRet LinkedTxn' => 										[ null, null ],
            'BillRet LinkedTxn TxnID' => 								[ 'Bill_LinkedTxn', 'ToTxnID' ],
            'BillRet LinkedTxn *' => 									[ 'Bill_LinkedTxn', '*' ],
            'BillRet ExpenseLineRet' => 								[ null, null ],
            'BillRet ExpenseLineRet AccountRef' => 						[ null, null ],
            'BillRet ExpenseLineRet AccountRef *' => 					[ 'Bill_ExpenseLine', 'Account_*' ],
            'BillRet ExpenseLineRet CustomerRef' => 					[ null, null ],
            'BillRet ExpenseLineRet CustomerRef *' =>					[ 'Bill_ExpenseLine', 'Customer_*' ],
            'BillRet ExpenseLineRet ClassRef' => 						[ null, null ],
            'BillRet ExpenseLineRet ClassRef *' => 						[ 'Bill_ExpenseLine', 'Class_*' ],
            'BillRet ExpenseLineRet *' => 								[ 'Bill_ExpenseLine', '*' ],
            'BillRet ItemLineRet' => 									[ null, null ],
            'BillRet ItemLineRet ItemRef' => 							[ null, null ],
            'BillRet ItemLineRet ItemRef *' => 							[ 'Bill_ItemLine', 'Item_*' ],
            'BillRet ItemLineRet CustomerRef' => 						[ null, null ],
            'BillRet ItemLineRet CustomerRef *' => 						[ 'Bill_ItemLine', 'Customer_*' ],
            'BillRet ItemLineRet ClassRef' => 							[ null, null ],
            'BillRet ItemLineRet ClassRef *' => 						[ 'Bill_ItemLine', 'Class_*' ],
            'BillRet ItemLineRet Desc' => 								[ 'Bill_ItemLine', 'Descrip' ],
            'BillRet ItemLineRet *' => 									[ 'Bill_ItemLine', '*' ],
            'BillRet ItemGroupLineRet' => 								[ null, null ],
            'BillRet ItemGroupLineRet ItemGroupRef' => 					[ null, null ],
            'BillRet ItemGroupLineRet ItemGroupRef *' => 				[ 'Bill_ItemGroupLine', 'ItemGroup_*' ],
            'BillRet ItemGroupLineRet Desc' => 							[ 'Bill_ItemGroupLine', 'Descrip' ],
            'BillRet ItemGroupLineRet ItemLineRet' => 					[ null, null ],
            'BillRet ItemGroupLineRet ItemLineRet ItemRef' => 			[ null, null ],
            'BillRet ItemGroupLineRet ItemLineRet ItemRef *' => 		[ 'Bill_ItemGroupLine_ItemLine', 'Item_*' ],
            'BillRet ItemGroupLineRet ItemLineRet Desc' => 				[ 'Bill_ItemGroupLine_ItemLine', 'Descrip' ],
            'BillRet ItemGroupLineRet ItemLineRet CustomerRef' => 		[ null, null ],
            'BillRet ItemGroupLineRet ItemLineRet CustomerRef *' => 	[ 'Bill_ItemGroupLine_ItemLine', 'Customer_*' ],
            'BillRet ItemGroupLineRet ItemLineRet ClassRef' => 			[ null, null ],
            'BillRet ItemGroupLineRet ItemLineRet ClassRef *' => 		[ 'Bill_ItemGroupLine_ItemLine', 'Class_*' ],
            'BillRet ItemGroupLineRet ItemLineRet *' => 				[ 'Bill_ItemGroupLine_ItemLine', '*' ],
            'BillRet ItemGroupLineRet *' => 							[ 'Bill_ItemGroupLine', '*' ],
            'BillRet DataExtRet' => 									[ 'DataExt', null ],
            'BillRet DataExtRet *' => 									[ 'DataExt', '*' ],

            'BillRet *' => 												[ 'Bill', '*' ],

            'BillToPayRet' => 									[ 'BillToPay', null ],
            'BillToPayRet BillToPay' => 						[ null, null ],
            'BillToPayRet BillToPay APAccountRef' => 			[ null, null ],
            'BillToPayRet BillToPay APAccountRef *' => 			[ 'BillToPay', 'APAccount_*' ],
            'BillToPayRet BillToPay *' => 						[ 'BillToPay', '*' ],
            'BillToPayRet CreditToApply' => 					[ null, null ],
            'BillToPayRet CreditToApply APAccountRef' => 		[ null, null ],
            'BillToPayRet CreditToApply APAccountRef *' => 		[ 'CreditToApply', 'APAccount_*' ],
            'BillToPayRet CreditToApply *' => 					[ 'CreditToApply', '*' ],
            'BillToPayRet *' => 								[ null, null ],

            'ChargeRet' => 							[ 'Charge', null ],
            'ChargeRet CustomerRef' => 				[ null, null ],
            'ChargeRet CustomerRef *' => 			[ 'Charge', 'Customer_*' ],
            'ChargeRet ItemRef' => 					[ null, null ],
            'ChargeRet ItemRef *' => 				[ 'Charge', 'Item_*' ],
            'ChargeRet OverrideUOMSetRef' => 		[ null, null ],
            'ChargeRet OverrideUOMSetRef *' => 		[ 'Charge', 'OverrideUOMSet_*' ],
            'ChargeRet Desc' => 					[ 'Charge', 'Descrip' ],
            'ChargeRet ARAccountRef' => 			[ null, null ],
            'ChargeRet ARAccountRef *' => 			[ 'Charge', 'ARAccount_*' ],
            'ChargeRet ClassRef' => 				[ null, null ],
            'ChargeRet ClassRef *' => 				[ 'Charge', 'Class_*' ],
            'ChargeRet DataExtRet' => 				[ 'DataExt', null ],
            'ChargeRet DataExtRet *' => 			[ 'DataExt', '*' ],
            'ChargeRet *' => 						[ 'Charge', '*' ],

            'CheckRet' => 														[ 'Check', null ],
            'CheckRet AccountRef' => 											[ null, null ],
            'CheckRet AccountRef *' => 											[ 'Check', 'Account_*' ],
            'CheckRet PayeeEntityRef' => 										[ null, null ],
            'CheckRet PayeeEntityRef *' => 										[ 'Check', 'PayeeEntityRef_*' ],
            'CheckRet AddressBlock' => 											[ null, null ],
            'CheckRet AddressBlock *' => 										[ 'Check', 'AddressBlock_*' ],
            'CheckRet Address' => 												[ null, null ],
            'CheckRet Address *' => 											[ 'Check', 'Address_*' ],
            'CheckRet CurrencyRef' => 											[ null, null ],
            'CheckRet CurrencyRef *' => 										[ 'Check', 'Currency_*' ],
            'CheckRet LinkedTxn' => 											[ null, null ],
            'CheckRet LinkedTxn TxnID' => 										[ 'Check_LinkedTxn', 'ToTxnID' ],
            'CheckRet LinkedTxn *' => 											[ 'Check_LinkedTxn', '*' ],
            'CheckRet ExpenseLineRet' => 										[ null, null ],
            'CheckRet ExpenseLineRet AccountRef' => 							[ null, null ],
            'CheckRet ExpenseLineRet AccountRef *' => 							[ 'Check_ExpenseLine', 'Account_*' ],
            'CheckRet ExpenseLineRet CustomerRef' => 							[ null, null ],
            'CheckRet ExpenseLineRet CustomerRef *' => 							[ 'Check_ExpenseLine', 'Customer_*' ],
            'CheckRet ExpenseLineRet ClassRef' => 								[ null, null ],
            'CheckRet ExpenseLineRet ClassRef *' => 							[ 'Check_ExpenseLine', 'Class_*' ],
            'CheckRet ExpenseLineRet *' => 										[ 'Check_ExpenseLine', '*' ],
            'CheckRet ItemLineRet' => 											[ null, null ],
            'CheckRet ItemLineRet ItemRef' => 									[ null, null ],
            'CheckRet ItemLineRet ItemRef *' => 								[ 'Check_ItemLine', 'Item_*' ],
            'CheckRet ItemLineRet OverrideUOMSetRef' => 						[ null, null ],
            'CheckRet ItemLineRet OverrideUOMSetRef *' => 						[ 'Check_ItemLine', 'OverrideUOMSet_*' ],
            'CheckRet ItemLineRet CustomerRef' => 								[ null, null ],
            'CheckRet ItemLineRet CustomerRef *' => 							[ 'Check_ItemLine', 'Customer_*' ],
            'CheckRet ItemLineRet ClassRef' => 									[ null, null ],
            'CheckRet ItemLineRet ClassRef *' => 								[ 'Check_ItemLine', 'Class_*' ],
            'CheckRet ItemLineRet Desc' => 										[ 'Check_ItemLine', 'Descrip' ],
            'CheckRet ItemLineRet *' => 										[ 'Check_ItemLine', '*' ],
            'CheckRet ItemGroupLineRet' => 										[ null, null ],
            'CheckRet ItemGroupLineRet ItemGroupRef' => 						[ null, null ],
            'CheckRet ItemGroupLineRet ItemGroupRef *' => 						[ 'Check_ItemGroupLine', 'ItemGroup_*' ],
            'CheckRet ItemGroupLineRet Desc' => 								[ 'Check_ItemGroupLine', 'Descrip' ],
            'CheckRet ItemGroupLineRet OverrideUOMSetRef' => 					[ null, null ],
            'CheckRet ItemGroupLineRet OverrideUOMSetRef *' => 					[ 'Check_ItemGroupLine', 'OverrideUOMSet_*' ],
            'CheckRet ItemGroupLineRet ItemLineRet' => 							[ null, null ],
            'CheckRet ItemGroupLineRet ItemLineRet ItemRef' => 					[ null, null ],
            'CheckRet ItemGroupLineRet ItemLineRet ItemRef *' => 				[ 'Check_ItemGroupLine_ItemLine', 'Item_*' ],
            'CheckRet ItemGroupLineRet ItemLineRet Desc' => 					[ 'Check_ItemGroupLine_ItemLine', 'Descrip' ],
            'CheckRet ItemGroupLineRet ItemLineRet OverrideUOMSetRef' => 		[ null, null ],
            'CheckRet ItemGroupLineRet ItemLineRet OverrideUOMSetRef *' => 		[ 'Check_ItemGroupLine_ItemLine', 'OverrideUOMSet_*' ],
            'CheckRet ItemGroupLineRet ItemLineRet CustomerRef' => 				[ null, null ],
            'CheckRet ItemGroupLineRet ItemLineRet CustomerRef *' => 			[ 'Check_ItemGroupLine_ItemLine', 'Customer_*' ],
            'CheckRet ItemGroupLineRet ItemLineRet ClassRef' => 				[ null, null ],
            'CheckRet ItemGroupLineRet ItemLineRet ClassRef *' => 				[ 'Check_ItemGroupLine_ItemLine', 'Class_*' ],
            'CheckRet ItemGroupLineRet ItemLineRet *' => 						[ 'Check_ItemGroupLine_ItemLine', '*' ],
            'CheckRet ItemGroupLineRet *' => 									[ 'Check_ItemGroupLine', '*' ],
            'CheckRet DataExtRet' => 											[ null, null ],
            'CheckRet DataExtRet *' => 											[ 'DataExt', '*' ],
            'CheckRet *' => 													[ 'Check', '*' ],

            'ClassRet' => 									[ 'Class', null ],
            'ClassRet ParentRef' => 						[ null, null ],
            'ClassRet ParentRef *' => 						[ 'Class', 'Parent_*' ],

            'ClassRet *' => 								[ 'Class', '*' ],

            'CompanyRet' => 									[ 'Company', null ],
            'CompanyRet Address' => 							[ null, null ],
            'CompanyRet Address *' => 							[ 'Company', 'Address_*' ],
            'CompanyRet AddressBlock' => 						[ null, null ],
            'CompanyRet AddressBlock *' => 						[ 'Company', 'AddressBlock_*' ],
            'CompanyRet LegalAddress' => 						[ null, null ],
            'CompanyRet LegalAddress *' => 						[ 'Company', 'LegalAddress_*' ],
            'CompanyRet CompanyAddressForCustomer' => 			[ null, null ],
            'CompanyRet CompanyAddressForCustomer *' => 		[ 'Company', 'Company_CompanyAddressForCustomer_*' ],
            'CompanyRet CompanyAddressBlockForCustomer' => 		[ null, null ],
            'CompanyRet CompanyAddressBlockForCustomer *' => 	[ 'Company', 'Company_CompanyAddressBlockForCustomer_*' ],

            'CompanyRet SubscribedServices' => 				[ null, null ],
            'CompanyRet SubscribedServices Service' => 		[ null, null ],
            'CompanyRet SubscribedServices Service *' => 	[ 'Company_SubscribedServices_Service', '*' ],
            'CompanyRet SubscribedServices *' => 			[ 'Company', 'SubscribedServices_*' ],

            'CompanyRet DataExtRet' => 						[ null, null ],
            'CompanyRet DataExtRet *' => 					[ 'DataExt', '*' ],

            'CompanyRet *' => 								[ 'Company', '*' ],

            'CurrencyRet' => 								[ 'Currency', null ],

            'CurrencyRet CurrencyFormat' => 				[ null, null ],
            'CurrencyRet CurrencyFormat *' => 				[ 'Currency', 'Currency_CurrencyFormat_*' ],

            'CurrencyRet *' => 								[ 'Currency', '*' ],

            'HostRet' => 									[ 'Host', null ],
            'HostRet *' => 									[ 'Host', '*' ],

            'PreferencesRet' => 							[ 'Preferences', null ],

            'PreferencesRet AccountingPreferences' => 		[ null, null ],
            'PreferencesRet AccountingPreferences *' => 	[ 'Preferences', 'AccountingPrefs_*' ],

            'PreferencesRet FinanceChargePreferences' => 	[ null, null ],

            'PreferencesRet FinanceChargePreferences FinanceChargeAccountRef' => [ null, null ],
            'PreferencesRet FinanceChargePreferences FinanceChargeAccountRef *' => [ 'Preferences', 'FinanceChargePrefs_FinanceChargeAccount_*' ],

            'PreferencesRet FinanceChargePreferences *' => 	[ 'Preferences', 'FinanceChargePrefs_*' ],

            'PreferencesRet JobsAndEstimatesPreferences' => [ null, null ],
            'PreferencesRet JobsAndEstimatesPreferences *' => [ 'Preferences', 'JobsAndEstimatesPrefs_*' ],

            'PreferencesRet MultiCurrencyPreferences' => 	[ null, null ],
            'PreferencesRet MultiCurrencyPreferences HomeCurrencyRef' => [ null, null ],
            'PreferencesRet MultiCurrencyPreferences HomeCurrencyRef *' => [ 'Preferences', 'MultiCurrencyPrefs_HomeCurrency_*' ],
            'PreferencesRet MultiCurrencyPreferences *' => 	[ 'Preferences', 'MultiCurrencyPrefs_*' ],

            'PreferencesRet MultiLocationInventoryPreferences' => [ null, null ],
            'PreferencesRet MultiLocationInventoryPreferences *' => [ 'Preferences', 'MultiLocationInventoryPrefs_*' ],

            'PreferencesRet PurchasesAndVendorsPreferences' => [ null, null ],
            'PreferencesRet PurchasesAndVendorsPreferences DefaultDiscountAccountRef' => [ null, null ],
            'PreferencesRet PurchasesAndVendorsPreferences DefaultDiscountAccountRef *' => [ 'Preferences', 'PurchasesAndVendorsPrefs_DefaultDiscountAccount_*' ],
            'PreferencesRet PurchasesAndVendorsPreferences *' => [ 'Preferences', 'PurchasesAndVendorsPrefs_*' ],

            'PreferencesRet ReportsPreferences' => 			[ null, null ],
            'PreferencesRet ReportsPreferences *' => 		[ 'Preferences', 'ReportsPrefs_*' ],

            'PreferencesRet SalesAndCustomersPreferences' => [ null, null ],
            'PreferencesRet SalesAndCustomersPreferences DefaultShipMethodRef' => [ null, null ],
            'PreferencesRet SalesAndCustomersPreferences DefaultShipMethodRef *' => [ 'Preferences', 'SalesAndCustomersPrefs_DefaultShipMethod_*' ],
            'PreferencesRet SalesAndCustomersPreferences PriceLevels' => [ null, null ],
            'PreferencesRet SalesAndCustomersPreferences PriceLevels *' => [ 'Preferences', 'SalesAndCustomersPrefs_PriceLevels_*' ],
            'PreferencesRet SalesAndCustomersPreferences *' => [ 'Preferences', 'SalesAndCustomersPrefs_*' ],

            'PreferencesRet SalesTaxPreferences' => 		[ null, null ],
            'PreferencesRet SalesTaxPreferences DefaultItemSalesTaxRef' => [ null, null ],
            'PreferencesRet SalesTaxPreferences DefaultItemSalesTaxRef *' => [ 'Preferences', 'SalesTaxPrefs_DefaultItemSalesTax_*' ],
            'PreferencesRet SalesTaxPreferences DefaultTaxableSalesTaxCodeRef' => [ null, null ],
            'PreferencesRet SalesTaxPreferences DefaultTaxableSalesTaxCodeRef *' => [ 'Preferences', 'SalesTaxPrefs_DefaultTaxableSalesTaxCode_*' ],
            'PreferencesRet SalesTaxPreferences DefaultNonTaxableSalesTaxCodeRef' => [ null, null ],
            'PreferencesRet SalesTaxPreferences DefaultNonTaxableSalesTaxCodeRef *' => [ 'Preferences', 'SalesTaxPrefs_DefaultNonTaxableSalesTaxCode_*' ],
            'PreferencesRet SalesTaxPreferences *' => 		[ 'Preferences', 'SalesTaxPrefs_*' ],

            'PreferencesRet TimeTrackingPreferences' => 	[ null, null ],
            'PreferencesRet TimeTrackingPreferences *' => 	[ 'Preferences', 'TimeTrackingPrefs_*' ],

            'PreferencesRet CurrentAppAccessRights' => 		[ null, null ],
            'PreferencesRet CurrentAppAccessRights *' => 	[ 'Preferences', 'CurrentAppAccessRights_*' ],

            'PreferencesRet *' => 							[ 'Preferences', '*' ],

            'CreditCardChargeRet' => 						[ 'CreditCardCharge', null ],
            'CreditCardChargeRet AccountRef' => 			[ null, null ],
            'CreditCardChargeRet AccountRef *' => 			[ 'CreditCardCharge', 'Account_*' ],
            'CreditCardChargeRet PayeeEntityRef' => 		[ null, null ],
            'CreditCardChargeRet PayeeEntityRef *' => 		[ 'CreditCardCharge', 'PayeeEntity_*' ],
            'CreditCardChargeRet CurrencyRef' => 			[ null, null ],
            'CreditCardChargeRet CurrencyRef *' => 			[ 'CreditCardCharge', 'Currency_*' ],

            'CreditCardChargeRet ItemLineRet' => 							[ null, null ],
            'CreditCardChargeRet ItemLineRet Desc' => 						[ 'CreditCardCharge_ItemLine', 'Descrip' ],
            'CreditCardChargeRet ItemLineRet ItemRef' => 					[ null, null ],
            'CreditCardChargeRet ItemLineRet ItemRef *' => 					[ 'CreditCardCharge_ItemLine', 'Item_*' ],
            'CreditCardChargeRet ItemLineRet OverrideUOMSetRef' => 			[ null, null ],
            'CreditCardChargeRet ItemLineRet OverrideUOMSetRef *' => 		[ 'CreditCardCharge_ItemLine', 'OverrideUOMSet_*' ],
            'CreditCardChargeRet ItemLineRet CustomerRef' => 				[ null, null ],
            'CreditCardChargeRet ItemLineRet CustomerRef *' => 				[ 'CreditCardCharge_ItemLine', 'Customer_*' ],
            'CreditCardChargeRet ItemLineRet ClassRef' => 					[ null, null ],
            'CreditCardChargeRet ItemLineRet ClassRef *' => 				[ 'CreditCardCharge_ItemLine', 'Class_*' ],
            'CreditCardChargeRet ItemLineRet *' => 							[ 'CreditCardCharge_ItemLine', '*' ],

            'CreditCardChargeRet ItemGroupLineRet' => 									[ null, null ],
            'CreditCardChargeRet ItemGroupLineRet Desc' => 								[ 'CreditCardCharge_ItemGroupLine', 'Descrip' ],
            'CreditCardChargeRet ItemGroupLineRet ItemGroupRef' => 						[ null, null ],
            'CreditCardChargeRet ItemGroupLineRet ItemGroupRef *' => 					[ 'CreditCardCharge_ItemGroupLine', 'ItemGroup_*' ],
            'CreditCardChargeRet ItemGroupLineRet OverrideUOMSetRef' => 				[ null, null ],
            'CreditCardChargeRet ItemGroupLineRet OverrideUOMSetRef *' => 				[ 'CreditCardCharge_ItemGroupLine', 'OverrideUOMSet_*' ],
            'CreditCardChargeRet ItemGroupLineRet ItemLineRet' => 						[ null, null ],
            'CreditCardChargeRet ItemGroupLineRet ItemLineRet ItemRef' => 				[ null, null ],
            'CreditCardChargeRet ItemGroupLineRet ItemLineRet ItemRef *' => 			[ 'CreditCardCharge_ItemGroupLine_ItemLine', 'Item_*' ],
            'CreditCardChargeRet ItemGroupLineRet ItemLineRet Desc' => 					[ 'CreditCardCharge_ItemGroupLine_ItemLine', 'Descrip' ],
            'CreditCardChargeRet ItemGroupLineRet ItemLineRet OverrideUOMSetRef' => 	[ null, null ],
            'CreditCardChargeRet ItemGroupLineRet ItemLineRet OverrideUOMSetRef *' => 	[ 'CreditCardCharge_ItemGroupLine_ItemLine', 'OverrideUOMSet_*' ],
            'CreditCardChargeRet ItemGroupLineRet ItemLineRet CustomerRef' => 			[ null, null ],
            'CreditCardChargeRet ItemGroupLineRet ItemLineRet CustomerRef *' => 		[ 'CreditCardCharge_ItemGroupLine_ItemLine', 'Customer_*' ],
            'CreditCardChargeRet ItemGroupLineRet ItemLineRet ClassRef' => 				[ null, null ],
            'CreditCardChargeRet ItemGroupLineRet ItemLineRet ClassRef *' => 			[ 'CreditCardCharge_ItemGroupLine_ItemLine', 'Class_*' ],
            'CreditCardChargeRet ItemGroupLineRet ItemLineRet *' => 					[ 'CreditCardCharge_ItemGroupLine_ItemLine', '*' ],
            'CreditCardChargeRet ItemGroupLineRet *' => 								[ 'CreditCardCharge_ItemGroupLine', '*' ],

            'CreditCardChargeRet ExpenseLineRet' => 					[ null, null ],
            'CreditCardChargeRet ExpenseLineRet AccountRef' => 			[ null, null ],
            'CreditCardChargeRet ExpenseLineRet AccountRef *' => 		[ 'CreditCardCharge_ExpenseLine', 'Account_*' ],
            'CreditCardChargeRet ExpenseLineRet CustomerRef' => 		[ null, null ],
            'CreditCardChargeRet ExpenseLineRet CustomerRef *' => 		[ 'CreditCardCharge_ExpenseLine', 'Customer_*' ],
            'CreditCardChargeRet ExpenseLineRet ClassRef' => 			[ null, null ],
            'CreditCardChargeRet ExpenseLineRet ClassRef *' => 			[ 'CreditCardCharge_ExpenseLine', 'Class_*' ],
            'CreditCardChargeRet ExpenseLineRet *' => 					[ 'CreditCardCharge_ExpenseLine', '*' ],

            'CreditCardChargeRet DataExtRet' => 			[ null, null ],
            'CreditCardChargeRet DataExtRet *' => 			[ 'DataExt', '*' ],
            'CreditCardChargeRet *' => 						[ 'CreditCardCharge', '*' ],

            'CreditCardCreditRet' => 						[ 'CreditCardCredit', null ],
            'CreditCardCreditRet AccountRef' => 			[ null, null ],
            'CreditCardCreditRet AccountRef *' => 			[ 'CreditCardCredit', 'Account_*' ],
            'CreditCardCreditRet PayeeEntityRef' => 		[ null, null ],
            'CreditCardCreditRet PayeeEntityRef *' => 		[ 'CreditCardCredit', 'PayeeEntity_*' ],
            'CreditCardCreditRet CurrencyRef' => 			[ null, null ],
            'CreditCardCreditRet CurrencyRef *' => 			[ 'CreditCardCredit', 'Currency_*' ],

            'CreditCardCreditRet ExpenseLineRet' => 					[ null, null ],
            'CreditCardCreditRet ExpenseLineRet AccountRef' => 			[ null, null ],
            'CreditCardCreditRet ExpenseLineRet AccountRef *' => 		[ 'CreditCardCredit_ExpenseLine', 'Account_*' ],
            'CreditCardCreditRet ExpenseLineRet CustomerRef' => 		[ null, null ],
            'CreditCardCreditRet ExpenseLineRet CustomerRef *' => 		[ 'CreditCardCredit_ExpenseLine', 'Customer_*' ],
            'CreditCardCreditRet ExpenseLineRet ClassRef' => 			[ null, null ],
            'CreditCardCreditRet ExpenseLineRet ClassRef *' => 			[ 'CreditCardCredit_ExpenseLine', 'Class_*' ],
            'CreditCardCreditRet ExpenseLineRet *' => 					[ 'CreditCardCredit_ExpenseLine', '*' ],

            'CreditCardCreditRet ItemLineRet' => 							[ null, null ],
            'CreditCardCreditRet ItemLineRet Desc' => 						[ 'CreditCardCredit_ItemLine', 'Descrip' ],
            'CreditCardCreditRet ItemLineRet ItemRef' => 					[ null, null ],
            'CreditCardCreditRet ItemLineRet ItemRef *' => 					[ 'CreditCardCredit_ItemLine', 'Item_*' ],
            'CreditCardCreditRet ItemLineRet OverrideUOMSetRef' => 			[ null, null ],
            'CreditCardCreditRet ItemLineRet OverrideUOMSetRef *' => 		[ 'CreditCardCredit_ItemLine', 'OverrideUOMSet_*' ],
            'CreditCardCreditRet ItemLineRet CustomerRef' => 				[ null, null ],
            'CreditCardCreditRet ItemLineRet CustomerRef *' => 				[ 'CreditCardCredit_ItemLine', 'Customer_*' ],
            'CreditCardCreditRet ItemLineRet ClassRef' => 					[ null, null ],
            'CreditCardCreditRet ItemLineRet ClassRef *' => 				[ 'CreditCardCredit_ItemLine', 'Class_*' ],
            'CreditCardCreditRet ItemLineRet *' => 							[ 'CreditCardCredit_ItemLine', '*' ],

            'CreditCardCreditRet ItemGroupLineRet' => 							[ null, null ],
            'CreditCardCreditRet ItemGroupLineRet Desc' => 						[ 'CreditCardCredit_ItemGroupLine', 'Descrip' ],
            'CreditCardCreditRet ItemGroupLineRet ItemGroupRef' => 				[ null, null ],
            'CreditCardCreditRet ItemGroupLineRet ItemGroupRef *' => 			[ 'CreditCardCredit_ItemGroupLine', 'ItemGroup_*' ],
            'CreditCardCreditRet ItemGroupLineRet OverrideUOMSetRef' => 		[ null, null ],
            'CreditCardCreditRet ItemGroupLineRet OverrideUOMSetRef *' => 		[ 'CreditCardCredit_ItemGroupLine', 'OverrideUOMSet_*' ],
            'CreditCardCreditRet ItemGroupLineRet ItemLineRet' => 				[ null, null ],
            'CreditCardCreditRet ItemGroupLineRet ItemLineRet Desc' => 			[ 'CreditCardCredit_ItemGroupLine_ItemLine', 'Descrip' ],
            'CreditCardCreditRet ItemGroupLineRet ItemLineRet *' => 			[ 'CreditCardCredit_ItemGroupLine_ItemLine', '*' ],
            'CreditCardCreditRet ItemGroupLineRet *' => 						[ 'CreditCardCredit_ItemGroupLine', '*' ],

            'CreditCardCreditRet DataExtRet' => 				[ null, null ],
            'CreditCardCreditRet DataExtRet *' => 				[ 'DataExt', '*' ],

            'CreditCardCreditRet *' => 							[ 'CreditCardCredit', '*' ],

            'CreditMemoRet' => 									[ 'CreditMemo', null ],
            'CreditMemoRet CustomerRef' => 						[ null, null ],
            'CreditMemoRet CustomerRef *' => 					[ 'CreditMemo', 'Customer_*' ],
            'CreditMemoRet ClassRef' => 						[ null, null ],
            'CreditMemoRet ClassRef *' => 						[ 'CreditMemo', 'Class_*' ],
            'CreditMemoRet ARAccountRef' => 					[ null, null ],
            'CreditMemoRet ARAccountRef *' => 					[ 'CreditMemo', 'ARAccount_*' ],
            'CreditMemoRet TemplateRef' => 						[ null, null ],
            'CreditMemoRet TemplateRef *' => 					[ 'CreditMemo', 'Template_*' ],
            'CreditMemoRet BillAddress' => 						[ null, null ],
            'CreditMemoRet BillAddress *' => 					[ 'CreditMemo', 'BillAddress_*' ],
            'CreditMemoRet BillAddressBlock' => 				[ null, null ],
            'CreditMemoRet BillAddressBlock *' => 				[ 'CreditMemo', 'BillAddressBlock_*' ],
            'CreditMemoRet ShipAddress' => 						[ null, null ],
            'CreditMemoRet ShipAddress *' => 					[ 'CreditMemo', 'ShipAddress_*' ],
            'CreditMemoRet ShipAddressBlock' => 				[ null, null ],
            'CreditMemoRet ShipAddressBlock *' => 				[ 'CreditMemo', 'ShipAddressBlock_*' ],
            'CreditMemoRet TermsRef' => 						[ null, null ],
            'CreditMemoRet TermsRef *' => 						[ 'CreditMemo', 'Terms_*' ],
            'CreditMemoRet SalesRepRef' => 						[ null, null ],
            'CreditMemoRet SalesRepRef *' => 					[ 'CreditMemo', 'SalesRep_*' ],
            'CreditMemoRet ShipMethodRef' => 					[ null, null ],
            'CreditMemoRet ShipMethodRef *' => 					[ 'CreditMemo', 'ShipMethod_*' ],
            'CreditMemoRet ItemSalesTaxRef' => 					[ null, null ],
            'CreditMemoRet ItemSalesTaxRef *' => 				[ 'CreditMemo', 'ItemSalesTax_*' ],
            'CreditMemoRet CustomerMsgRef' => 					[ null, null ],
            'CreditMemoRet CustomerMsgRef *' => 				[ 'CreditMemo', 'CustomerMsg_*' ],
            'CreditMemoRet CustomerSalesTaxCodeRef' => 			[ null, null ],
            'CreditMemoRet CustomerSalesTaxCodeRef *' => 		[ 'CreditMemo', 'CustomerSalesTaxCode_*' ],

            'CreditMemoRet LinkedTxn' => 			[ null, null ],
            'CreditMemoRet LinkedTxn TxnID' => 		[ 'CreditMemo_LinkedTxn', 'ToTxnID' ],
            'CreditMemoRet LinkedTxn *' => 			[ 'CreditMemo_LinkedTxn', '*' ],

            'CreditMemoRet CreditMemoLineRet' => 												[ null, null ],
            'CreditMemoRet CreditMemoLineRet Desc' => 											[ 'CreditMemo_CreditMemoLine', 'Descrip' ],
            'CreditMemoRet CreditMemoLineRet ItemRef' => 										[ null, null ],
            'CreditMemoRet CreditMemoLineRet ItemRef *' => 										[ 'CreditMemo_CreditMemoLine', 'Item_*' ],
            'CreditMemoRet CreditMemoLineRet OverrideUOMSetRef' => 								[ null, null ],
            'CreditMemoRet CreditMemoLineRet OverrideUOMSetRef *' => 							[ 'CreditMemo_CreditMemoLine', 'OverrideUOMSet_*' ],
            'CreditMemoRet CreditMemoLineRet ClassRef' => 										[ null, null ],
            'CreditMemoRet CreditMemoLineRet ClassRef *' => 									[ 'CreditMemo_CreditMemoLine', 'Class_*' ],
            'CreditMemoRet CreditMemoLineRet SalesTaxCodeRef' => 								[ null, null ],
            'CreditMemoRet CreditMemoLineRet SalesTaxCodeRef *' => 								[ 'CreditMemo_CreditMemoLine', 'SalesTaxCode_*' ],
            'CreditMemoRet CreditMemoLineRet CreditCardTxnInfo' => 								[ null, null ],
            'CreditMemoRet CreditMemoLineRet CreditCardTxnInfo CreditCardTxnInputInfo' => 		[ null, null ],
            'CreditMemoRet CreditMemoLineRet CreditCardTxnInfo CreditCardTxnInputInfo *' => 	[ 'CreditMemo_CreditMemoLine', 'CreditCardTxnInputInfo_*' ],
            'CreditMemoRet CreditMemoLineRet CreditCardTxnInfo CreditCardTxnResultInfo' => 		[ null, null ],
            'CreditMemoRet CreditMemoLineRet CreditCardTxnInfo CreditCardTxnResultInfo *' => 	[ 'CreditMemo_CreditMemoLine', 'CreditCardTxnResultInfo_*' ],

            'CreditMemoRet CreditMemoLineRet DataExtRet' => 			[ 'DataExt', null ],
            'CreditMemoRet CreditMemoLineRet DataExtRet *' => 			[ 'DataExt', '*' ],
            'CreditMemoRet CreditMemoLineRet *' => 						[ 'CreditMemo_CreditMemoLine', '*' ],

            'CreditMemoRet CreditMemoLineGroupRet' => 							[ 'CreditMemo_CreditMemoLineGroup', null ],
            'CreditMemoRet CreditMemoLineGroupRet Desc' => 						[ 'CreditMemo_CreditMemoLineGroup', 'Descrip' ],
            'CreditMemoRet CreditMemoLineGroupRet ItemGroupRef' => 				[ null, null ],
            'CreditMemoRet CreditMemoLineGroupRet ItemGroupRef *' => 			[ 'CreditMemo_CreditMemoLineGroup', 'ItemGroup_*' ],
            'CreditMemoRet CreditMemoLineGroupRet ItemRef' => 					[ null, null ],
            'CreditMemoRet CreditMemoLineGroupRet ItemRef *' => 				[ 'CreditMemo_CreditMemoLineGroup', 'ItemGroup_*' ],
            'CreditMemoRet CreditMemoLineGroupRet OverrideUOMSetRef' => 		[ null, null ],
            'CreditMemoRet CreditMemoLineGroupRet OverrideUOMSetRef *' => 		[ 'CreditMemo_CreditMemoLineGroup', 'OverrideUOMSet_*' ],

            'CreditMemoRet CreditMemoLineGroupRet CreditMemoLineRet' => 												[ null, null ],
            'CreditMemoRet CreditMemoLineGroupRet CreditMemoLineRet ItemRef' => 										[ null, null ],
            'CreditMemoRet CreditMemoLineGroupRet CreditMemoLineRet ItemRef *' => 										[ 'CreditMemo_CreditMemoLineGroup_CreditMemoLine', 'Item_*' ],
            'CreditMemoRet CreditMemoLineGroupRet CreditMemoLineRet Desc' => 											[ 'CreditMemo_CreditMemoLineGroup_CreditMemoLine', 'Descrip' ],
            'CreditMemoRet CreditMemoLineGroupRet CreditMemoLineRet OverrideUOMSetRef' => 								[ null, null ],
            'CreditMemoRet CreditMemoLineGroupRet CreditMemoLineRet OverrideUOMSetRef *' => 							[ 'CreditMemo_CreditMemoLineGroup_CreditMemoLine', 'OverrideUOMSet_*' ],
            'CreditMemoRet CreditMemoLineGroupRet CreditMemoLineRet ClassRef' => 										[ null, null ],
            'CreditMemoRet CreditMemoLineGroupRet CreditMemoLineRet ClassRef *' => 										[ 'CreditMemo_CreditMemoLineGroup_CreditMemoLine', 'Class_*' ],
            'CreditMemoRet CreditMemoLineGroupRet CreditMemoLineRet SalesTaxCodeRef' => 								[ null, null ],
            'CreditMemoRet CreditMemoLineGroupRet CreditMemoLineRet SalesTaxCodeRef *' => 								[ 'CreditMemo_CreditMemoLineGroup_CreditMemoLine', 'SalesTaxCode_*' ],
            'CreditMemoRet CreditMemoLineGroupRet CreditMemoLineRet CreditCardTxnInfo' => 								[ null, null ],
            'CreditMemoRet CreditMemoLineGroupRet CreditMemoLineRet CreditCardTxnInfo CreditCardTxnInputInfo' => 		[ null, null ],
            'CreditMemoRet CreditMemoLineGroupRet CreditMemoLineRet CreditCardTxnInfo CreditCardTxnInputInfo *' => 		[ 'CreditMemo_CreditMemoLineGroup_CreditMemoLine', 'CreditCardTxnInfo_CreditCardTxnInputInfo_*' ],
            'CreditMemoRet CreditMemoLineGroupRet CreditMemoLineRet CreditCardTxnInfo CreditCardTxnResultInfo' => 		[ null, null ],
            'CreditMemoRet CreditMemoLineGroupRet CreditMemoLineRet CreditCardTxnInfo CreditCardTxnResultInfo *' => 	[ 'CreditMemo_CreditMemoLineGroup_CreditMemoLine', 'CreditCardTxnInfo_CreditCardTxnResultInfo_*' ],
            'CreditMemoRet CreditMemoLineGroupRet CreditMemoLineRet CreditCardTxnInfo *' => 							[ 'CreditMemo_CreditMemoLineGroup_CreditMemoLine', 'CreditCardTxnInfo_*' ],
            'CreditMemoRet CreditMemoLineGroupRet CreditMemoLineRet DataExtRet' => 										[ null, null ],
            'CreditMemoRet CreditMemoLineGroupRet CreditMemoLineRet DataExtRet *' => 									[ 'DataExt', '*' ],
            'CreditMemoRet CreditMemoLineGroupRet CreditMemoLineRet *' => 												[ 'CreditMemo_CreditMemoLineGroup_CreditMemoLine', '*' ],

            'CreditMemoRet CreditMemoLineGroupRet DataExtRet' => 			[ 'DataExt', null ],
            'CreditMemoRet CreditMemoLineGroupRet DataExtRet *' => 			[ 'DataExt', '*' ],
            'CreditMemoRet CreditMemoLineGroupRet *' => 					[ 'CreditMemo_CreditMemoLineGroup', '*' ],

            'CreditMemoRet DataExtRet' => 			[ 'DataExt', null ],
            'CreditMemoRet DataExtRet *' => 		[ 'DataExt', '*' ],
            'CreditMemoRet *' => 					[ 'CreditMemo', '*' ],

            'CustomerRet' =>								[ 'Customer', null ],
            'CustomerRet ParentRef'	=> 						[ null, null ],
            'CustomerRet ParentRef *' => 					[ 'Customer', 'Parent_*' ],
            'CustomerRet BillAddress' => 					[ null, null ],
            'CustomerRet BillAddress *' => 					[ 'Customer', 'BillAddress_*' ],
            'CustomerRet ShipAddress' => 					[ null, null ],
            'CustomerRet ShipAddress *' => 					[ 'Customer', 'ShipAddress_*' ],
            'CustomerRet BillAddressBlock' => 				[ null, null ],
            'CustomerRet BillAddressBlock *' => 			[ 'Customer', 'BillAddressBlock_*' ],
            'CustomerRet ShipAddressBlock' => 				[ null, null ],
            'CustomerRet ShipAddressBlock *' => 			[ 'Customer', 'ShipAddressBlock_*' ],
            'CustomerRet CreditCardInfo' => 				[ null, null ],
            'CustomerRet CreditCardInfo *' => 				[ 'Customer', 'CreditCardInfo_*' ],
            'CustomerRet CustomerTypeRef' => 				[ null, null ],
            'CustomerRet CustomerTypeRef *' => 				[ 'Customer', 'CustomerType_*' ],
            'CustomerRet TermsRef' => 						[ null, null ],
            'CustomerRet TermsRef *' => 					[ 'Customer', 'Terms_*' ],
            'CustomerRet SalesRepRef' => 					[ null, null ],
            'CustomerRet SalesRepRef *' => 					[ 'Customer', 'SalesRep_*' ],
            'CustomerRet SalesTaxCodeRef' => 				[ null, null ],
            'CustomerRet SalesTaxCodeRef *' => 				[ 'Customer', 'SalesTaxCode_*' ],
            'CustomerRet ItemSalesTaxRef' => 				[ null, null ],
            'CustomerRet ItemSalesTaxRef *' => 				[ 'Customer', 'ItemSalesTax_*' ],
            'CustomerRet PreferredPaymentMethodRef' => 		[ null, null ],
            'CustomerRet PreferredPaymentMethodRef *' => 	[ 'Customer', 'PreferredPaymentMethod_*' ],
            'CustomerRet JobTypeRef' => 					[ null, null ],
            'CustomerRet JobTypeRef *' => 					[ 'Customer', 'JobType_*' ],
            'CustomerRet PriceLevelRef' => 					[ null, null ],
            'CustomerRet PriceLevelRef *' => 				[ 'Customer', 'PriceLevel_*' ],

            'CustomerRet DataExtRet' => 				[ 'DataExt', null ],
            'CustomerRet DataExtRet *' => 				[ 'DataExt', '*' ],

            'CustomerRet *' => 						[ 'Customer', '*' ],

            'CustomerTypeRet' => 					[ 'CustomerType', null ],
            'CustomerTypeRet ParentRef' => 			[ 'CustomerType', null ],
            'CustomerTypeRet ParentRef *' => 		[ 'CustomerType', 'Parent_*' ],

            'CustomerTypeRet *' => 					[ 'CustomerType', '*' ],

            'CustomerMsgRet' => 					[ 'CustomerMsg', null ],

            'CustomerMsgRet *' => 					[ 'CustomerMsg', '*' ],

            'DataExtDefRet' =>						[ 'DataExtDef', null ],
            'DataExtDefRet AssignToObject' => 		[ 'DataExtDef_AssignToObject', 'AssignToObject' ],
            'DataExtDefRet *' => 					[ 'DataExtDef', '*' ],

            'DateDrivenTermsRet' => 				[ 'DateDrivenTerms', null ],
            'DateDrivenTermsRet *' => 				[ 'DateDrivenTerms', '*' ],

            'DepositRet' => 								[ 'Deposit', null ],
            'DepositRet DepositToAccountRef' => 			[ null, null ],
            'DepositRet DepositToAccountRef *' => 			[ 'Deposit', 'DepositToAccount_*' ],
            'DepositRet CashBackInfoRet' => 				[ null, null ],
            'DepositRet CashBackInfoRet AccountRef' => 		[ null, null ],
            'DepositRet CashBackInfoRet AccountRef *' => 	[ 'Deposit', 'CashBackInfo_Account_*' ],
            'DepositRet CashBackInfoRet *' => 				[ 'Deposit', 'CashBackInfo_*' ],

            'DepositRet DepositLineRet' => 							[ null, null ],
            'DepositRet DepositLineRet EntityRef' => 				[ null, null ],
            'DepositRet DepositLineRet EntityRef *' => 				[ 'Deposit_DepositLine', 'Entity_*' ],
            'DepositRet DepositLineRet AccountRef' => 				[ null, null ],
            'DepositRet DepositLineRet AccountRef *' => 			[ 'Deposit_DepositLine', 'Account_*' ],
            'DepositRet DepositLineRet PaymentMethodRef' => 		[ null, null ],
            'DepositRet DepositLineRet PaymentMethodRef *' => 		[ 'Deposit_DepositLine', 'PaymentMethod_*' ],
            'DepositRet DepositLineRet ClassRef' => 				[ null, null ],
            'DepositRet DepositLineRet ClassRef *' => 				[ 'Deposit_DepositLine', 'Class_*' ],
            'DepositRet DepositLineRet *' => 						[ 'Deposit_DepositLine', '*' ],

            'DepositRet DataExtRet' => 								[ null, null ],
            'DepositRet DataExtRet *' => 							[ 'DataExt', '*' ],

            'DepositRet *' => 										[ 'Deposit', '*' ],

            'EmployeeRet' => 										[ 'Employee', null ],
            'EmployeeRet EmployeeAddress' => 						[ null, null ],
            'EmployeeRet EmployeeAddress *' => 						[ 'Employee', 'EmployeeAddress_*' ],
            'EmployeeRet BillingRateRef' => 						[ null, null ],
            'EmployeeRet BillingRateRef *' => 						[ 'Employee', 'BillingRate_*' ],

            'EmployeeRet EmployeePayrollInfo' => 								[ null, null ],
            'EmployeeRet EmployeePayrollInfo ClassRef' => 						[ null, null ],
            'EmployeeRet EmployeePayrollInfo ClassRef *' => 					[ 'Employee', 'EmployeePayrollInfo_Class_*' ],
            'EmployeeRet EmployeePayrollInfo Earnings' => 						[ null, null ],
            'EmployeeRet EmployeePayrollInfo Earnings PayrollItemWageRef' => 	[ null, null ],
            'EmployeeRet EmployeePayrollInfo Earnings PayrollItemWageRef *' => 	[ 'Employee_Earnings', 'PayrollItemWage_*' ],
            'EmployeeRet EmployeePayrollInfo Earnings *' => 					[ 'Employee_Earnings', '*' ],

            'EmployeeRet EmployeePayrollInfo SickHours' => 			[ null, null ],
            'EmployeeRet EmployeePayrollInfo SickHours *' => 		[ 'Employee', 'EmployeePayrollInfo_SickHours_*' ],

            'EmployeeRet EmployeePayrollInfo VacationHours' => 		[ null, null ],
            'EmployeeRet EmployeePayrollInfo VacationHours *' => 	[ 'Employee', 'EmployeePayrollInfo_VacationHours_*' ],

            'EmployeeRet EmployeePayrollInfo *' => 		[ 'Employee', 'EmployeePayrollInfo_*' ],

            'EmployeeRet DataExtRet' => 				[ null, null ],
            'EmployeeRet DataExtRet *' => 				[ 'DataExt', '*' ],

            'EmployeeRet *' => 							[ 'Employee', '*' ],

            'EstimateRet' => 							[ 'Estimate', null ],

            'EstimateRet CustomerRef' => 				[ null, null ],
            'EstimateRet CustomerRef *' => 				[ 'Estimate', 'Customer_*' ],
            'EstimateRet ClassRef' => 					[ null, null ],
            'EstimateRet ClassRef *' => 				[ 'Estimate', 'Class_*' ],
            'EstimateRet TemplateRef' => 				[ null, null ],
            'EstimateRet TemplateRef *' => 				[ 'Estimate', 'Template_*' ],
            'EstimateRet BillAddress' => 				[ null, null ],
            'EstimateRet BillAddress *' => 				[ 'Estimate', 'BillAddress_*' ],
            'EstimateRet ShipAddress' => 				[ null, null ],
            'EstimateRet ShipAddress *' => 				[ 'Estimate', 'ShipAddress_*' ],
            'EstimateRet BillAddressBlock' => 			[ null, null ],
            'EstimateRet BillAddressBlock *' => 		[ 'Estimate', 'BillAddressBlock_*' ],
            'EstimateRet ShipAddressBlock' => 			[ null, null ],
            'EstimateRet ShipAddressBlock *' => 		[ 'Estimate', 'ShipAddressBlock_*' ],
            'EstimateRet TermsRef' => 					[ null, null ],
            'EstimateRet TermsRef *' => 				[ 'Estimate', 'Terms_*' ],
            'EstimateRet ItemSalesTaxRef' => 			[ null, null ],
            'EstimateRet ItemSalesTaxRef *' => 			[ 'Estimate', 'ItemSalesTax_*' ],
            'EstimateRet SalesRepRef' => 				[ null, null ],
            'EstimateRet SalesRepRef *' => 				[ 'Estimate', 'SalesRep_*' ],
            'EstimateRet CurrencyRef' => 				[ null, null ],
            'EstimateRet CurrencyRef *' => 				[ 'Estimate', 'Currency_*' ],
            'EstimateRet CustomerMsgRef' => 			[ null, null ],
            'EstimateRet CustomerMsgRef *' => 			[ 'Estimate', 'CustomerMsg_*' ],
            'EstimateRet CustomerSalesTaxCodeRef' =>	[ null, null ],
            'EstimateRet CustomerSalesTaxCodeRef *' => 	[ 'Estimate', 'CustomerSalesTaxCode_*' ],

            'EstimateRet LinkedTxn' => 					[ 'Estimate_LinkedTxn', null ],
            'EstimateRet LinkedTxn TxnID' => 			[ 'Estimate_LinkedTxn', 'ToTxnID' ],
            'EstimateRet LinkedTxn *' => 				[ 'Estimate_LinkedTxn', '*' ],

            'EstimateRet EstimateLineRet' => 							[ null, null ],
            'EstimateRet EstimateLineRet Desc' => 						[ 'Estimate_EstimateLine', 'Descrip' ],
            'EstimateRet EstimateLineRet ItemRef' => 					[ null, null ],
            'EstimateRet EstimateLineRet ItemRef *' => 					[ 'Estimate_EstimateLine', 'Item_*' ],
            'EstimateRet EstimateLineRet OverrideUOMSetRef' => 			[ null, null ],
            'EstimateRet EstimateLineRet OverrideUOMSetRef *' => 		[ 'Estimate_EstimateLine', 'OverrideUOMSet_*' ],
            'EstimateRet EstimateLineRet ClassRef' => 					[ null, null ],
            'EstimateRet EstimateLineRet ClassRef *' => 				[ 'Estimate_EstimateLine', 'Class_*' ],
            'EstimateRet EstimateLineRet InventorySiteRef' => 			[ null, null ],
            'EstimateRet EstimateLineRet InventorySiteRef *' => 		[ 'Estimate_EstimateLine', 'InventorySite_*' ],
            'EstimateRet EstimateLineRet SalesTaxCodeRef' => 			[ null, null ],
            'EstimateRet EstimateLineRet SalesTaxCodeRef *' => 			[ 'Estimate_EstimateLine', 'SalesTaxCode_*' ],

            'EstimateRet EstimateLineRet DataExtRet' => 				[ 'DataExt', null ],
            'EstimateRet EstimateLineRet DataExtRet *' => 				[ 'DataExt', '*' ],

            'EstimateRet EstimateLineRet *' => 							[ 'Estimate_EstimateLine', '*' ],

            'EstimateRet EstimateLineGroupRet' => 										[ null, null ],
            'EstimateRet EstimateLineGroupRet Desc' => 									[ 'Estimate_EstimateLineGroup', 'Descrip' ],
            'EstimateRet EstimateLineGroupRet ItemGroupRef' =>							[ null, null ],
            'EstimateRet EstimateLineGroupRet ItemGroupRef *' =>						[ 'Estimate_EstimateLineGroup', 'ItemGroup_*' ],
            'EstimateRet EstimateLineGroupRet OverrideUOMSetRef' =>						[ null, null ],
            'EstimateRet EstimateLineGroupRet OverrideUOMSetRef *' => 					[ 'Estimate_EstimateLineGroup', 'OverrideUOMSet_*' ],
            'EstimateRet EstimateLineGroupRet EstimateLineRet' => 						[ null, null ],
            'EstimateRet EstimateLineGroupRet EstimateLineRet ItemRef' => 				[ null, null ],
            'EstimateRet EstimateLineGroupRet EstimateLineRet ItemRef *' => 			[ 'Estimate_EstimateLineGroup_EstimateLine', 'Item_*' ],
            'EstimateRet EstimateLineGroupRet EstimateLineRet Desc' => 					[ 'Estimate_EstimateLineGroup_EstimateLine', 'Descrip' ],
            'EstimateRet EstimateLineGroupRet EstimateLineRet OverrideUOMSetRef' => 	[ null, null ],
            'EstimateRet EstimateLineGroupRet EstimateLineRet OverrideUOMSetRef *' => 	[ 'Estimate_EstimateLineGroup_EstimateLine', 'OverrideUOMSet_*' ],
            'EstimateRet EstimateLineGroupRet EstimateLineRet ClassRef' => 				[ null, null ],
            'EstimateRet EstimateLineGroupRet EstimateLineRet ClassRef *' => 			[ 'Estimate_EstimateLineGroup_EstimateLine', 'Class_*' ],
            'EstimateRet EstimateLineGroupRet EstimateLineRet SalesTaxCodeRef' => 		[ null, null ],
            'EstimateRet EstimateLineGroupRet EstimateLineRet SalesTaxCodeRef *' => 	[ 'Estimate_EstimateLineGroup_EstimateLine', 'SalesTaxCode_*' ],
            'EstimateRet EstimateLineGroupRet EstimateLineRet DataExtRet' => 			[ null, null ],
            'EstimateRet EstimateLineGroupRet EstimateLineRet DataExtRet *' => 			[ 'DataExt', '*' ],
            'EstimateRet EstimateLineGroupRet EstimateLineRet *' => 					[ 'Estimate_EstimateLineGroup_EstimateLine', '*' ],
            'EstimateRet EstimateLineGroupRet DataExtRet' => 							[ null, null ],
            'EstimateRet EstimateLineGroupRet DataExtRet *' => 							[ 'DataExt', '*' ],

            'EstimateRet EstimateLineGroupRet *' => 									[ 'Estimate_EstimateLineGroup', '*' ],

            'EstimateRet DataExtRet' => 				[ null, null ],
            'EstimateRet DataExtRet *' => 				[ 'DataExt', '*' ],

            'EstimateRet *' => 							[ 'Estimate', '*' ],

            'InventoryAdjustmentRet' => 				[ 'InventoryAdjustment', null ],
            'InventoryAdjustmentRet AccountRef' => 		[ null, null ],
            'InventoryAdjustmentRet AccountRef *' => 	[ 'InventoryAdjustment', 'Account_*' ],
            'InventoryAdjustmentRet CustomerRef' => 	[ null, null ],
            'InventoryAdjustmentRet CustomerRef *' => 	[ 'InventoryAdjustment', 'Customer_*' ],
            'InventoryAdjustmentRet ClassRef' => 		[ null, null ],
            'InventoryAdjustmentRet ClassRef *' => 		[ 'InventoryAdjustment', 'Class_*' ],

            'InventoryAdjustmentRet InventoryAdjustmentLineRet' => 					[ null, null ],
            'InventoryAdjustmentRet InventoryAdjustmentLineRet ItemRef' => 			[ null, null ],
            'InventoryAdjustmentRet InventoryAdjustmentLineRet ItemRef *' => 		[ 'InventoryAdjustment_InventoryAdjustmentLine', 'Item_*' ],
            'InventoryAdjustmentRet InventoryAdjustmentLineRet QuantityAdjustment' => [ null, null ],
            'InventoryAdjustmentRet InventoryAdjustmentLineRet QuantityAdjustment *' => [ 'InventoryAdjustment_InventoryAdjustmentLine', 'QuantityAdjustment_*' ],
            'InventoryAdjustmentRet InventoryAdjustmentLineRet ValueAdjustment' => [ null, null ],
            'InventoryAdjustmentRet InventoryAdjustmentLineRet ValueAdjustment *' => [ 'InventoryAdjustment_InventoryAdjustmentLine', 'ValueAdjustment_*' ],
            'InventoryAdjustmentRet InventoryAdjustmentLineRet *' => 				[ 'InventoryAdjustment_InventoryAdjustmentLine', '*' ],

            'InventoryAdjustmentRet DataExtRet' => 		[ null, null ],
            'InventoryAdjustmentRet DataExtRet *' => 	[ 'DataExt', '*' ],

            'InventoryAdjustmentRet *' => 				[ 'InventoryAdjustment', '*' ],

            'InvoiceRet' => 							[ 'Invoice', null ],
            'InvoiceRet CustomerRef' => 				[ null, null ],
            'InvoiceRet CustomerRef *' => 				[ 'Invoice', 'Customer_*' ],
            'InvoiceRet ARAccountRef' => 				[ null, null ],
            'InvoiceRet ARAccountRef *' => 				[ 'Invoice', 'ARAccount_*' ],
            'InvoiceRet ClassRef' =>					[ null, null ],
            'InvoiceRet ClassRef *' => 					[ 'Invoice', 'Class_*' ],
            'InvoiceRet TemplateRef' => 				[ null, null ],
            'InvoiceRet TemplateRef *' =>				[ 'Invoice', 'Template_*' ],
            'InvoiceRet BillAddress' => 				[ 'Invoice', null ],
            'InvoiceRet BillAddress *' => 				[ 'Invoice', 'BillAddress_*' ],
            'InvoiceRet ShipAddress' => 				[ 'Invoice', null ],
            'InvoiceRet ShipAddress *' => 				[ 'Invoice', 'ShipAddress_*' ],
            'InvoiceRet BillAddressBlock' =>			[ 'Invoice', null ],
            'InvoiceRet BillAddressBlock *' => 			[ 'Invoice', 'BillAddressBlock_*' ],
            'InvoiceRet ShipAddressBlock' => 			[ 'Invoice', null ],
            'InvoiceRet ShipAddressBlock *' => 			[ 'Invoice', 'ShipAddressBlock_*' ],
            'InvoiceRet TermsRef' => 					[ null, null ],
            'InvoiceRet TermsRef *' => 					[ 'Invoice', 'Terms_*' ],
            'InvoiceRet ItemSalesTaxRef' => 			[ null, null ],
            'InvoiceRet ItemSalesTaxRef *' => 			[ 'Invoice', 'ItemSalesTax_*' ],
            'InvoiceRet ShipMethodRef' => 				[ null, null ],
            'InvoiceRet ShipMethodRef *' => 			[ 'Invoice', 'ShipMethod_*' ],
            'InvoiceRet SalesRepRef' => 				[ null, null ],
            'InvoiceRet SalesRepRef *' => 				[ 'Invoice', 'SalesRep_*' ],
            'InvoiceRet CurrencyRef' => 				[ null, null ],
            'InvoiceRet CurrencyRef *' => 				[ 'Invoice', 'Currency_*' ],
            'InvoiceRet CustomerMsgRef' => 				[ null, null ],
            'InvoiceRet CustomerMsgRef *' => 			[ 'Invoice', 'CustomerMsg_*' ],
            'InvoiceRet CustomerSalesTaxCodeRef' =>		[ null, null ],
            'InvoiceRet CustomerSalesTaxCodeRef *' => 	[ 'Invoice', 'CustomerSalesTaxCode_*' ],

            'InvoiceRet LinkedTxn' => 				[ 'Invoice_LinkedTxn', null ],
            'InvoiceRet LinkedTxn TxnID' => 		[ 'Invoice_LinkedTxn', 'ToTxnID' ],
            'InvoiceRet LinkedTxn *' => 			[ 'Invoice_LinkedTxn', '*' ],

            'InvoiceRet InvoiceLineRet' => 							[ null, null ],
            'InvoiceRet InvoiceLineRet ItemRef' => 					[ null, null ],
            'InvoiceRet InvoiceLineRet ItemRef *' => 				[ 'Invoice_InvoiceLine', 'Item_*' ],
            'InvoiceRet InvoiceLineRet OverrideUOMSetRef' => 		[ null, null ],
            'InvoiceRet InvoiceLineRet OverrideUOMSetRef *' => 		[ 'Invoice_InvoiceLine', 'OverrideUOMSet_*' ],
            'InvoiceRet InvoiceLineRet ClassRef' => 				[ null, null ],
            'InvoiceRet InvoiceLineRet ClassRef *' => 				[ 'Invoice_InvoiceLine', 'Class_*' ],
            'InvoiceRet InvoiceLineRet InventorySiteRef' => 		[ null, null ],
            'InvoiceRet InvoiceLineRet InventorySiteRef *' => 		[ 'Invoice_InvoiceLine', 'InventorySite_*' ],
            'InvoiceRet InvoiceLineRet SalesTaxCodeRef' => 			[ null, null ],
            'InvoiceRet InvoiceLineRet SalesTaxCodeRef *' => 		[ 'Invoice_InvoiceLine', 'SalesTaxCode_*' ],

            'InvoiceRet InvoiceLineRet Desc' =>						[ 'Invoice_InvoiceLine', 'Descrip' ],

            'InvoiceRet InvoiceLineRet DataExtRet' => 				[ 'DataExt', null ],
            'InvoiceRet InvoiceLineRet DataExtRet *' => 			[ 'DataExt', '*' ],

            'InvoiceRet InvoiceLineRet *' => 						[ 'Invoice_InvoiceLine', '*' ],

            'InvoiceRet InvoiceLineGroupRet' => 					[ null, null ],
            'InvoiceRet InvoiceLineGroupRet ItemGroupRef' =>		[ null, null ],
            'InvoiceRet InvoiceLineGroupRet ItemGroupRef *' => 		[ 'Invoice_InvoiceLineGroup', 'ItemGroup_*' ],
            'InvoiceRet InvoiceLineGroupRet OverrideUOMSetRef' =>	[ null, null ],
            'InvoiceRet InvoiceLineGroupRet OverrideUOMSetRef *' => [ 'Invoice_InvoiceLineGroup', 'OverrideUOMSet_*' ],

            'InvoiceRet InvoiceLineGroupRet Desc' =>								[ 'Invoice_InvoiceLineGroup', 'Descrip' ],

            'InvoiceRet InvoiceLineGroupRet InvoiceLineRet' => 						[ null, null ],
            'InvoiceRet InvoiceLineGroupRet InvoiceLineRet ItemRef' => 				[ null, null ],
            'InvoiceRet InvoiceLineGroupRet InvoiceLineRet ItemRef *' => 			[ 'Invoice_InvoiceLineGroup_InvoiceLine', 'Item_*' ],
            'InvoiceRet InvoiceLineGroupRet InvoiceLineRet Desc' => 				[ 'Invoice_InvoiceLineGroup_InvoiceLine', 'Descrip' ],
            'InvoiceRet InvoiceLineGroupRet InvoiceLineRet OverrideUOMSetRef' => 	[ null, null ],
            'InvoiceRet InvoiceLineGroupRet InvoiceLineRet OverrideUOMSetRef *' => 	[ 'Invoice_InvoiceLineGroup_InvoiceLine', 'OverrideUOMSet_*' ],
            'InvoiceRet InvoiceLineGroupRet InvoiceLineRet ClassRef' => 			[ null, null ],
            'InvoiceRet InvoiceLineGroupRet InvoiceLineRet ClassRef *' => 			[ 'Invoice_InvoiceLineGroup_InvoiceLine', 'Class_*' ],
            'InvoiceRet InvoiceLineGroupRet InvoiceLineRet SalesTaxCodeRef' => 		[ null, null ],
            'InvoiceRet InvoiceLineGroupRet InvoiceLineRet SalesTaxCodeRef *' => 	[ 'Invoice_InvoiceLineGroup_InvoiceLine', 'SalesTaxCode_*' ],
            'InvoiceRet InvoiceLineGroupRet InvoiceLineRet DataExtRet' => 			[ null, null ],
            'InvoiceRet InvoiceLineGroupRet InvoiceLineRet DataExtRet *' => 		[ 'DataExt', '*' ],
            'InvoiceRet InvoiceLineGroupRet InvoiceLineRet *' => 					[ 'Invoice_InvoiceLineGroup_InvoiceLine', '*' ] ,

            'InvoiceRet InvoiceLineGroupRet DataExtRet' => 			[ null, null ],
            'InvoiceRet InvoiceLineGroupRet DataExtRet *' => 		[ 'DataExt', '*' ],

            'InvoiceRet InvoiceLineGroupRet *' => 					[ 'Invoice_InvoiceLineGroup', '*' ],

            'InvoiceRet DataExtRet' => 				[ null, null ],
            'InvoiceRet DataExtRet *' => 			[ 'DataExt', '*' ],

            'InvoiceRet *' => 						[ 'Invoice', '*' ],

            'ItemDiscountRet' => 					[ 'ItemDiscount', null ],
            'ItemDiscountRet ParentRef' => 			[ null, null ],
            'ItemDiscountRet ParentRef *' => 		[ 'ItemDiscount', 'Parent_*' ],
            'ItemDiscountRet SalesTaxCodeRef' => 	[ null, null ],
            'ItemDiscountRet SalesTaxCodeRef *' => 	[ 'ItemDiscount', 'SalesTaxCode_*' ],
            'ItemDiscountRet AccountRef' => 		[ null, null ],
            'ItemDiscountRet AccountRef *' => 		[ 'ItemDiscount', 'Account_*' ],
            'ItemDiscountRet DataExtRet' => 		[ null, null ],
            'ItemDiscountRet DataExtRet *' => 		[ 'DataExt', '*' ],
            'ItemDiscountRet *' => 					[ 'ItemDiscount', '*' ],

            'ItemServiceRet' => 											[ 'ItemService', null ],
            'ItemServiceRet ParentRef' => 									[ null, null ],
            'ItemServiceRet ParentRef *' => 								[ 'ItemService', 'Parent_*' ],
            'ItemServiceRet UnitOfMeasureSetRef' => 						[ null, null ],
            'ItemServiceRet UnitOfMeasureSetRef *' => 						[ 'ItemService', 'UnitOfMeasureSet_*' ],
            'ItemServiceRet SalesTaxCodeRef' => 							[ null, null ],
            'ItemServiceRet SalesTaxCodeRef *' => 							[ 'ItemService', 'SalesTaxCode_*' ],
            'ItemServiceRet SalesOrPurchase' => 							[ null, null ],
            'ItemServiceRet SalesOrPurchase AccountRef' => 					[ null, null ],
            'ItemServiceRet SalesOrPurchase AccountRef *' => 				[ 'ItemService', 'SalesOrPurchase_Account_*' ],
            'ItemServiceRet SalesOrPurchase *' => 							[ 'ItemService', 'SalesOrPurchase_*' ],
            'ItemServiceRet SalesAndPurchase' => 							[ null, null ],
            'ItemServiceRet SalesAndPurchase IncomeAccountRef' => 			[ null, null ],
            'ItemServiceRet SalesAndPurchase IncomeAccountRef *' => 		[ 'ItemService', 'SalesAndPurchase_IncomeAccount_*' ],
            'ItemServiceRet SalesAndPurchase ExpenseAccountRef' => 			[ null, null ],
            'ItemServiceRet SalesAndPurchase ExpenseAccountRef *' => 		[ 'ItemService', 'SalesAndPurchase_ExpenseAccount_*' ],
            'ItemServiceRet SalesAndPurchase PrefVendorRef' => 				[ null, null ],
            'ItemServiceRet SalesAndPurchase PrefVendorRef *' => 			[ 'ItemService', 'SalesAndPurchase_PrefVendor_*' ],
            'ItemServiceRet SalesAndPurchase *' => 							[ 'ItemService', 'SalesAndPurchase_*' ],

            'ItemServiceRet DataExtRet' => 									[ null, null ],
            'ItemServiceRet DataExtRet *' => 								[ 'DataExt', '*' ],
            'ItemServiceRet *' => 											[ 'ItemService', '*' ],

            'ItemNonInventoryRet' => 										[ 'ItemNonInventory', null ],
            'ItemNonInventoryRet ParentRef' => 								[ null, null ],
            'ItemNonInventoryRet ParentRef *' => 							[ 'ItemNonInventory', 'Parent_*' ],
            'ItemNonInventoryRet UnitOfMeasureRef' => 						[ null, null ],
            'ItemNonInventoryRet UnitOfMeasureRef *' => 					[ 'itemnoninventory', 'UnitOfMeasure_*' ],
            'ItemNonInventoryRet SalesTaxCodeRef' => 						[ null, null ],
            'ItemNonInventoryRet SalesTaxCodeRef' => 						[ 'itemnoninventory', 'SalesTaxCode_*' ],
            'ItemNonInventoryRet UnitOfMeasureSetRef' => 					[ null, null ],
            'ItemNonInventoryRet UnitOfMeasureSetRef *' => 					[ 'ItemNonInventory', 'UnitOfMeasureSet_*' ],
            'ItemNonInventoryRet SalesTaxCodeRef' => 						[ null, null ],
            'ItemNonInventoryRet SalesTaxCodeRef *' => 						[ 'ItemNonInventory', 'SalesTaxCode_*' ],
            'ItemNonInventoryRet SalesOrPurchase' => 						[ null, null ],
            'ItemNonInventoryRet SalesOrPurchase *' => 						[ 'ItemNonInventory', 'SalesOrPurchase_*' ],
            'ItemNonInventoryRet SalesOrPurchase AccountRef' => 			[ null, null ],
            'ItemNonInventoryRet SalesOrPurchase AccountRef *' => 			[ 'ItemNonInventory', 'SalesOrPurchase_Account_*' ],
            'ItemNonInventoryRet SalesAndPurchase' => 						[ null, null ],
            'ItemNonInventoryRet SalesAndPurchase IncomeAccountRef' => 		[ null, null ],
            'ItemNonInventoryRet SalesAndPurchase IncomeAccountRef *' => 	[ 'ItemNonInventory', 'SalesAndPurchase_IncomeAccount_*' ],
            'ItemNonInventoryRet SalesAndPurchase ExpenseAccountRef' => 	[ null, null ],
            'ItemNonInventoryRet SalesAndPurchase ExpenseAccountRef *' => 	[ 'ItemNonInventory', 'SalesAndPurchase_ExpenseAccount_*' ],
            'ItemNonInventoryRet SalesAndPurchase PrefVendorRef' => 		[ null, null ],
            'ItemNonInventoryRet SalesAndPurchase PrefVendorRef *' => 		[ 'ItemNonInventory', 'SalesAndPurchase_PrefVendor_*' ],
            'ItemNonInventoryRet SalesAndPurchase *' => 					[ 'ItemNonInventory', 'SalesAndPurchase_*' ],
            'ItemNonInventoryRet DataExtRet' => 							[ null, null ],
            'ItemNonInventoryRet DataExtRet *' => 							[ 'DataExt', '*' ],
            'ItemNonInventoryRet *' => 										[ 'ItemNonInventory', '*' ],

            'ItemOtherChargeRet' => 											[ 'ItemOtherCharge', null ],
            'ItemOtherChargeRet ParentRef' => 									[ null, null ],
            'ItemOtherChargeRet ParentRef *' => 								[ 'ItemOtherCharge', 'Parent_*' ],
            'ItemOtherChargeRet SalesTaxCodeRef' => 							[ null, null ],
            'ItemOtherChargeRet SalesTaxCodeRef *' => 							[ 'ItemOtherCharge', 'SalesTaxCode_*' ],
            'ItemOtherChargeRet SalesOrPurchase' => 							[ null, null ],
            'ItemOtherChargeRet SalesOrPurchase *' => 							[ 'ItemOtherCharge', 'SalesOrPurchase_*' ],
            'ItemOtherChargeRet SalesOrPurchase AccountRef' => 					[ null, null ],
            'ItemOtherChargeRet SalesOrPurchase AccountRef *' => 				[ 'ItemOtherCharge', 'SalesOrPurchase_Account_*' ],
            'ItemOtherChargeRet SalesAndPurchase' => 							[ null, null ],
            'ItemOtherChargeRet SalesAndPurchase IncomeAccountRef' => 			[ null, null ],
            'ItemOtherChargeRet SalesAndPurchase IncomeAccountRef *' => 		[ 'ItemOtherCharge', 'SalesAndPurchase_IncomeAccount_*' ],
            'ItemOtherChargeRet SalesAndPurchase ExpenseAccountRef' => 			[ null, null ],
            'ItemOtherChargeRet SalesAndPurchase ExpenseAccountRef *' => 		[ 'ItemOtherCharge', 'SalesAndPurchase_ExpenseAccount_*' ],
            'ItemOtherChargeRet SalesAndPurchase PrefVendorRef' => 				[ null, null ],
            'ItemOtherChargeRet SalesAndPurchase PrefVendorRef *' => 			[ 'ItemOtherCharge', 'SalesAndPurchase_PrefVendor_*' ],
            'ItemOtherChargeRet SalesAndPurchase *' => 							[ 'ItemOtherCharge', 'SalesAndPurchase_*' ],

            'ItemOtherChargeRet DataExtRet' => 				[ null, null ],
            'ItemOtherChargeRet DataExtRet *' => 			[ 'DataExt', '*' ],
            'ItemOtherChargeRet *' => 						[ 'ItemOtherCharge', '*' ],

            'ItemInventoryRet' => 							[ 'ItemInventory', null ],
            'ItemInventoryRet ParentRef' => 				[ null, null ],
            'ItemInventoryRet ParentRef *' => 				[ 'ItemInventory', 'Parent_*' ],
            'ItemInventoryRet SalesTaxCodeRef' => 			[ null, null ],
            'ItemInventoryRet SalesTaxCodeRef *' => 		[ 'ItemInventory', 'SalesTaxCode_*' ],
            'ItemInventoryRet UnitOfMeasureSetRef' => 		[ null, null ],
            'ItemInventoryRet UnitOfMeasureSetRef *' => 	[ 'ItemInventory', 'UnitOfMeasureSet_*' ],
            'ItemInventoryRet IncomeAccountRef' => 			[ null, null ],
            'ItemInventoryRet IncomeAccountRef *' => 		[ 'ItemInventory', 'IncomeAccount_*', ],
            'ItemInventoryRet COGSAccountRef' => 			[ null, null ],
            'ItemInventoryRet COGSAccountRef *' => 			[ 'ItemInventory', 'COGSAccount_*' ],
            'ItemInventoryRet PrefVendorRef' => 			[ null, null ],
            'ItemInventoryRet PrefVendorRef *' => 			[ 'ItemInventory', 'PrefVendor_*' ],
            'ItemInventoryRet AssetAccountRef' => 			[ null, null ],
            'ItemInventoryRet AssetAccountRef *' => 		[ 'ItemInventory', 'AssetAccount_*' ],
            'ItemInventoryRet DataExtRet' => 				[ null, null ],
            'ItemInventoryRet DataExtRet *' => 				[ 'DataExt', '*' ],
            'ItemInventoryRet *' =>							[ 'ItemInventory', '*' ],

            'ItemInventoryAssemblyRet' => 						[ 'ItemInventoryAssembly', null ],
            'ItemInventoryAssemblyRet ParentRef' => 			[ null, null ],
            'ItemInventoryAssemblyRet ParentRef *' => 			[ 'ItemInventoryAssembly', 'Parent_*' ],
            'ItemInventoryAssemblyRet UnitOfMeasureSetRef' => 	[ null, null ],
            'ItemInventoryAssemblyRet UnitOfMeasureSetRef *' => [ 'ItemInventoryAssembly', 'UnitOfMeasureSet_*' ],
            'ItemInventoryAssemblyRet SalesTaxCodeRef' => 		[ null, null ],
            'ItemInventoryAssemblyRet SalesTaxCodeRef *' => 	[ 'ItemInventoryAssembly', 'SalesTaxCode_*' ],
            'ItemInventoryAssemblyRet IncomeAccountRef' => 		[ null, null ],
            'ItemInventoryAssemblyRet IncomeAccountRef *' => 	[ 'ItemInventoryAssembly', 'IncomeAccount_*' ],
            'ItemInventoryAssemblyRet COGSAccountRef' => 		[ null, null ],
            'ItemInventoryAssemblyRet COGSAccountRef *' => 		[ 'ItemInventoryAssembly', 'COGSAccount_*' ],
            'ItemInventoryAssemblyRet PrefVendorRef' => 		[ null, null ],
            'ItemInventoryAssemblyRet PrefVendorRef *' => 		[ 'ItemInventoryAssembly', 'PrefVendor_*' ],
            'ItemInventoryAssemblyRet AssetAccountRef' => 		[ null, null ],
            'ItemInventoryAssemblyRet AssetAccountRef *' => 	[ 'ItemInventoryAssembly', 'AssetAccount_*' ],

            'ItemInventoryAssemblyRet ItemInventoryAssemblyLine' => 					[ null, null ],
            'ItemInventoryAssemblyRet ItemInventoryAssemblyLine ItemInventoryRef' => 	[ null, null ],
            'ItemInventoryAssemblyRet ItemInventoryAssemblyLine ItemInventoryRef *' => 	[ 'ItemInventoryAssembly_ItemInventoryAssemblyLine', 'ItemInventory_*' ],
            'ItemInventoryAssemblyRet ItemInventoryAssemblyLine *' => 					[ 'ItemInventoryAssembly_ItemInventoryAssemblyLine', '*' ],

            'ItemInventoryAssemblyRet DataExtRet' => 		[ null, null ],
            'ItemInventoryAssemblyRet DataExtRet *' => 		[ 'DataExt', '*' ],

            'ItemInventoryAssemblyRet *' => 				[ 'ItemInventoryAssembly', '*' ],

            'ItemFixedAssetRet' => 							[ 'ItemFixedAsset', null ],
            'ItemFixedAssetRet AssetAccountRef' => 			[ null, null ],
            'ItemFixedAssetRet AssetAccountRef *' => 		[ 'ItemFixedAsset', 'AssetAccount_*' ],
            'ItemFixedAssetRet FixedAssetSalesInfo' => 		[ null, null ],
            'ItemFixedAssetRet FixedAssetSalesInfo *' => 	[ 'ItemFixedAsset', 'FixedAssetSalesInfo_*' ],
            'ItemFixedAssetRet DataExtRet' => 				[ null, null ],
            'ItemFixedAssetRet DataExtRet *' => 			[ 'DataExt', '*' ],

            'ItemFixedAssetRet *' => 						[ 'ItemFixedAsset', '*' ],

            'ItemGroupRet' => 								[ 'ItemGroup', null ],
            'ItemGroupRet UnitOfMeasureSetRef' => 			[ null, null ],
            'ItemGroupRet UnitOfMeasureSetRef *' => 		[ 'ItemGroup', 'UnitOfMeasureSet_*' ],
            'ItemGroupRet ItemGroupLine' => 				[ null, null ],
            'ItemGroupRet ItemGroupLine ItemRef' => 		[ null, null ],
            'ItemGroupRet ItemGroupLine ItemRef *' => 		[ 'ItemGroup_ItemGroupLine', 'Item_*' ],
            'ItemGroupRet ItemGroupLine *' => 				[ 'ItemGroup_ItemGroupLine', '*' ],
            'ItemGroupRet DataExtRet' => 					[ null, null ],
            'ItemGroupRet DataExtRet *' => 					[ 'DataExt', '*' ],

            'ItemGroupRet *' => 							[ 'ItemGroup', '*' ],

            'ItemSubtotalRet' => 							[ 'ItemSubtotal', null ],
            'ItemSubtotalRet DataExtRet' => 				[ null, null ],
            'ItemSubtotalRet DataExtRet *' => 				[ 'DataExt', '*' ],

            'ItemSubtotalRet *' => 							[ 'ItemSubtotal', '*' ],

            'ItemPaymentRet' => 							[ 'ItemPayment', null ],
            'ItemPaymentRet DepositToAccountRef' => 		[ null, null ],
            'ItemPaymentRet DepositToAccountRef *' => 		[ 'ItemPayment', 'DepositToAccount_*' ],
            'ItemPaymentRet PaymentMethodRef' => 			[ null, null ],
            'ItemPaymentRet PaymentMethodRef *' => 			[ 'ItemPayment', 'PaymentMethod_*' ],

            'ItemPaymentRet DataExtRet' => 					[ null, null ],
            'ItemPaymentRet DataExtRet *' => 				[ 'DataExt', '*' ],
            'ItemPaymentRet *' => 							[ 'ItemPayment', '*' ],

            'ItemSalesTaxRet' => 								[ 'ItemSalesTax', null ],
            'ItemSalesTaxRet TaxVendorRef' => 					[ null, null ],
            'ItemSalesTaxRet TaxVendorRef *' => 				[ 'ItemSalesTax', 'TaxVendor_*' ],
            'ItemSalesTaxRet DataExtRet' => 					[ null, null ],
            'ItemSalesTaxRet DataExtRet *' => 					[ 'DataExt', '*' ],

            'ItemSalesTaxRet *' => 							[ 'ItemSalesTax', '*' ],

            'ItemSalesTaxGroupRet' => 						[ 'ItemSalesTaxGroup', null ],
            'ItemSalesTaxGroupRet ItemSalesTaxRef' => 		[ null, null ],
            'ItemSalesTaxGroupRet ItemSalesTaxRef *' => 	[ 'ItemSalesTaxGroup_ItemSalesTax', '*' ],
            'ItemSalesTaxGroupRet DataExtRet' => 			[ null, null ],
            'ItemSalesTaxGroupRet DataExtRet *' => 			[ 'DataExt', '*' ],
            'ItemSalesTaxGroupRet *' => 					[ 'ItemSalesTaxGroup', '*' ],

            'ItemReceiptRet' => 							[ 'ItemReceipt', null ],
            'ItemReceiptRet VendorRef' => 					[ null, null ],
            'ItemReceiptRet VendorRef *' => 				[ 'ItemReceipt', 'Vendor_*' ],
            'ItemReceiptRet APAccountRef' => 				[ null, null ],
            'ItemReceiptRet APAccountRef *' => 				[ 'ItemReceipt', 'APAccount_*' ],

            'ItemReceiptRet LinkedTxn' => 					[ 'ItemReceipt_LinkedTxn', null ],
            'ItemReceiptRet LinkedTxn TxnID' => 			[ 'ItemReceipt_LinkedTxn', 'ToTxnID' ],
            'ItemReceiptRet LinkedTxn *' => 				[ 'ItemReceipt_LinkedTxn', '*' ],

            'ItemReceiptRet ExpenseLineRet' => 						[ 'ItemReceipt_ExpenseLine', null ],
            'ItemReceiptRet ExpenseLineRet AccountRef' => 			[ null, null ],
            'ItemReceiptRet ExpenseLineRet AccountRef *' => 		[ 'ItemReceipt_ExpenseLine', 'Account_*' ],
            'ItemReceiptRet ExpenseLineRet CustomerRef' => 			[ null, null ],
            'ItemReceiptRet ExpenseLineRet CustomerRef *' => 		[ 'ItemReceipt_ExpenseLine', 'Customer_*' ],
            'ItemReceiptRet ExpenseLineRet ClassRef' => 			[ null, null ],
            'ItemReceiptRet ExpenseLineRet ClassRef *' => 			[ 'ItemReceipt_ExpenseLine', 'Class_*' ],
            'ItemReceiptRet ExpenseLineRet *' => 					[ 'ItemReceipt_ExpenseLine', '*' ],

            'ItemReceiptRet ItemLineRet' => 							[ 'ItemReceipt_ItemLine', null ],
            'ItemReceiptRet ItemLineRet Desc' => 						[ 'ItemReceipt_ItemLine', 'Descrip' ],
            'ItemReceiptRet ItemLineRet ItemRef' => 					[ null, null ],
            'ItemReceiptRet ItemLineRet ItemRef *' => 					[ 'ItemReceipt_ItemLine', 'Item_*' ],
            'ItemReceiptRet ItemLineRet OverrideUOMSetRef' => 			[ null, null ],
            'ItemReceiptRet ItemLineRet OverrideUOMSetRef *' => 		[ 'ItemReceipt_ItemLine', 'OverrideUOMSet_*' ],
            'ItemReceiptRet ItemLineRet CustomerRef' => 				[ null, null ],
            'ItemReceiptRet ItemLineRet CustomerRef *' => 				[ 'ItemReceipt_ItemLine', 'Customer_*' ],
            'ItemReceiptRet ItemLineRet ClassRef' => 					[ null, null ],
            'ItemReceiptRet ItemLineRet ClassRef *' => 					[ 'ItemReceipt_ItemLine', 'Class_*' ],
            'ItemReceiptRet ItemLineRet *' => 							[ 'ItemReceipt_ItemLine', '*' ],

            'ItemReceiptRet ItemGroupLineRet' => 									[ 'ItemReceipt_ItemGroupLine', null ],
            'ItemReceiptRet ItemGroupLineRet Desc' => 								[ 'ItemReceipt_ItemGroupLine', 'Descrip' ],
            'ItemReceiptRet ItemGroupLineRet ItemGroupRef' => 						[ null, null ],
            'ItemReceiptRet ItemGroupLineRet ItemGroupRef *' => 					[ 'ItemReceipt_ItemGroupLine', 'ItemGroup_*' ],
            'ItemReceiptRet ItemGroupLineRet OverrideUOMSetRef' => 					[ null, null ],
            'ItemReceiptRet ItemGroupLineRet OverrideUOMSetRef *' => 				[ 'ItemReceipt_ItemGroupLine', 'OverrideUOMSet_*' ],
            'ItemReceiptRet ItemGroupLineRet ItemLineRet' => 						[ null, null ],
            'ItemReceiptRet ItemGroupLineRet ItemLineRet ItemRef' => 				[ null, null ],
            'ItemReceiptRet ItemGroupLineRet ItemLineRet ItemRef *' => 				[ 'ItemReceipt_ItemGroupLine_ItemLine', 'Item_*' ],
            'ItemReceiptRet ItemGroupLineRet ItemLineRet Desc' => 					[ 'ItemReceipt_ItemGroupLine_ItemLine', 'Descrip' ],
            'ItemReceiptRet ItemGroupLineRet ItemLineRet OverrideUOMSetRef' => 		[ null, null ],
            'ItemReceiptRet ItemGroupLineRet ItemLineRet OverrideUOMSetRef *' => 	[ 'ItemReceipt_ItemGroupLine_ItemLine', 'OverrideUOMSet_*' ],
            'ItemReceiptRet ItemGroupLineRet ItemLineRet CustomerRef' => 			[ null, null ],
            'ItemReceiptRet ItemGroupLineRet ItemLineRet CustomerRef *' => 			[ 'ItemReceipt_ItemGroupLine_ItemLine', 'Customer_*' ],
            'ItemReceiptRet ItemGroupLineRet ItemLineRet ClassRef' => 				[ null, null ],
            'ItemReceiptRet ItemGroupLineRet ItemLineRet ClassRef *' => 			[ 'ItemReceipt_ItemGroupLine_ItemLine', 'Class_*' ],
            'ItemReceiptRet ItemGroupLineRet ItemLineRet *' => 						[ 'ItemReceipt_ItemGroupLine_ItemLine', '*' ],

            'ItemReceiptRet ItemGroupLineRet *' => 									[ 'ItemReceipt_ItemGroupLine', '*' ],

            'ItemReceiptRet DataExtRet' => 			[ null, null ],
            'ItemReceiptRet DataExtRet *' => 		[ 'DataExt', '*' ],
            'ItemReceiptRet *' => 					[ 'ItemReceipt', '*' ],

            'JobTypeRet' => 						[ 'JobType', null ],
            'JobTypeRet ParentRef' => 				[ null, null ],
            'JobTypeRet ParentRef *' => 			[ 'JobType', 'Parent_*'  ],
            'JobTypeRet *' => 						[ 'JobType', '*' ],

            'JournalEntryRet' => 									[ 'JournalEntry', null ],
            'JournalEntryRet JournalDebitLine' => 					[ null, null ],
            'JournalEntryRet JournalDebitLine AccountRef' => 		[ null, null ],
            'JournalEntryRet JournalDebitLine AccountRef *' => 		[ 'JournalEntry_JournalDebitLine', 'Account_*' ],
            'JournalEntryRet JournalDebitLine EntityRef' => 		[ null, null ],
            'JournalEntryRet JournalDebitLine EntityRef *' => 		[ 'JournalEntry_JournalDebitLine', 'Entity_*' ],
            'JournalEntryRet JournalDebitLine ClassRef' => 			[ null, null ],
            'JournalEntryRet JournalDebitLine ClassRef *' => 		[ 'JournalEntry_JournalDebitLine', 'Class_*' ],
            'JournalEntryRet JournalDebitLine *' => 				[ 'JournalEntry_JournalDebitLine', '*' ],

            'JournalEntryRet JournalCreditLine' => 						[ null, null ],
            'JournalEntryRet JournalCreditLine AccountRef' => 			[ null, null ],
            'JournalEntryRet JournalCreditLine AccountRef *' => 		[ 'JournalEntry_JournalCreditLine', 'Account_*' ],
            'JournalEntryRet JournalCreditLine EntityRef' => 			[ null, null ],
            'JournalEntryRet JournalCreditLine EntityRef *' => 			[ 'JournalEntry_JournalCreditLine', 'Entity_*' ],
            'JournalEntryRet JournalCreditLine ClassRef' => 			[ null, null ],
            'JournalEntryRet JournalCreditLine ClassRef *' => 			[ 'JournalEntry_JournalCreditLine', 'Class_*' ],
            'JournalEntryRet JournalCreditLine *' => 					[ 'JournalEntry_JournalCreditLine', '*' ],

            'JournalEntryRet DataExtRet' => 		[ null, null ],
            'JournalEntryRet DataExtRet *' => 		[ 'DataExt', '*' ],
            'JournalEntryRet *' => 					[ 'JournalEntry', '*' ],

            'PaymentMethodRet' => 					[ 'PaymentMethod', null ],
            'PaymentMethodRet *' => 				[ 'PaymentMethod', '*' ],

            'PayrollItemWageRet' => 					[ 'PayrollItemWage', null ],
            'PayrollItemWageRet ExpenseAccountRef' => 	[ null, null ],
            'PayrollItemWageRet ExpenseAccountRef *' => [ 'PayrollItemWage', 'ExpenseAccount_*' ],

            'PayrollItemWageRet *' => 					[ 'PayrollItemWage', '*' ],

            'PriceLevelRet' => 									[ 'PriceLevel', null ],
            'PriceLevelRet PriceLevelPerItemRet' => 			[ null, null ],
            'PriceLevelRet PriceLevelPerItemRet ItemRef' => 	[ null, null ],
            'PriceLevelRet PriceLevelPerItemRet ItemRef *' => 	[ 'PriceLevel_PriceLevelPerItem', 'Item_*' ],
            'PriceLevelRet PriceLevelPerItemRet *' => 			[ 'PriceLevel_PriceLevelPerItem', '*' ],
            'PriceLevelRet *' => 								[ 'PriceLevel', '*' ],

            'PurchaseOrderRet' => 											[ 'PurchaseOrder', null ],
            'PurchaseOrderRet VendorRef' => 								[ null, null ],
            'PurchaseOrderRet VendorRef *' => 								[ 'PurchaseOrder', 'Vendor_*' ],
            'PurchaseOrderRet VendorRef FullName' => 						[ 'PurchaseOrder', 'Vendor_FullName' ],
            'PurchaseOrderRet ClassRef' =>									[ null, null ],
            'PurchaseOrderRet ClassRef *' => 								[ 'PurchaseOrder', 'Class_*' ],
            'PurchaseOrderRet ShipToEntityRef' => 							[ null, null ],
            'PurchaseOrderRet ShipToEntityRef *' => 						[ 'PurchaseOrder', 'ShipToEntity_*' ],
            'PurchaseOrderRet ShipToEntityRef FullName' => 					[ 'PurchaseOrder', 'ShipToEntity_FullName' ],
            'PurchaseOrderRet TemplateRef' => 								[ null, null ],
            'PurchaseOrderRet TemplateRef *' =>								[ 'PurchaseOrder', 'Template_*' ],
            'PurchaseOrderRet VendorAddress' => 							[ 'PurchaseOrder', null ],
            'PurchaseOrderRet VendorAddress *' => 							[ 'PurchaseOrder', 'VendorAddress_*' ],
            'PurchaseOrderRet VendorAddressBlock' =>						[ 'PurchaseOrder', null ],
            'PurchaseOrderRet VendorAddressBlock *' => 						[ 'PurchaseOrder', 'VendorAddressBlock_*' ],
            'PurchaseOrderRet ShipAddress' => 								[ 'PurchaseOrder', null ],
            'PurchaseOrderRet ShipAddress *' => 							[ 'PurchaseOrder', 'ShipAddress_*' ],
            'PurchaseOrderRet ShipAddressBlock' => 							[ 'PurchaseOrder', null ],
            'PurchaseOrderRet ShipAddressBlock *' => 						[ 'PurchaseOrder', 'ShipAddressBlock_*' ],
            'PurchaseOrderRet TermsRef' => 									[ null, null ],
            'PurchaseOrderRet TermsRef *' => 								[ 'PurchaseOrder', 'Terms_*' ],
            'PurchaseOrderRet ShipMethodRef' => 							[ null, null ],
            'PurchaseOrderRet ShipMethodRef *' => 							[ 'PurchaseOrder', 'ShipMethod_*' ],
            'PurchaseOrderRet CurrencyRef' => 								[ null, null ],
            'PurchaseOrderRet CurrencyRef *' => 							[ 'PurchaseOrder', 'Currency_*' ],
            'PurchaseOrderRet PurchaseOrderLineRet' => 						[ null, null ],
            'PurchaseOrderRet PurchaseOrderLineRet ItemRef' => 				[ null, null ],
            'PurchaseOrderRet PurchaseOrderLineRet ItemRef *' => 			[ 'PurchaseOrder_PurchaseOrderLine', 'Item_*' ],
            'PurchaseOrderRet PurchaseOrderLineRet OverrideUOMSetRef' => 	[ null, null ],
            'PurchaseOrderRet PurchaseOrderLineRet OverrideUOMSetRef *' => 	[ 'PurchaseOrder_PurchaseOrderLine', 'OverrideUOMSet_*' ],
            'PurchaseOrderRet PurchaseOrderLineRet ClassRef' => 			[ null, null ],
            'PurchaseOrderRet PurchaseOrderLineRet ClassRef *' => 			[ 'PurchaseOrder_PurchaseOrderLine', 'Class_*' ],
            'PurchaseOrderRet PurchaseOrderLineRet CustomerRef' => 			[ null, null ],
            'PurchaseOrderRet PurchaseOrderLineRet CustomerRef *' => 		[ 'PurchaseOrder_PurchaseOrderLine', 'Customer_*' ],
            'PurchaseOrderRet PurchaseOrderLineRet Desc' => 				[ 'PurchaseOrder_PurchaseOrderLine', 'Descrip' ],
            'PurchaseOrderRet PurchaseOrderLineRet DataExtRet' => 			[ null, null ],
            'PurchaseOrderRet PurchaseOrderLineRet DataExtRet *' => 		[ 'DataExt', '*' ],
            'PurchaseOrderRet PurchaseOrderLineRet *' => 					[ 'PurchaseOrder_PurchaseOrderLine', '*' ],

            'PurchaseOrderRet PurchaseOrderLineGroupRet' => 											[ null, null ],
            'PurchaseOrderRet PurchaseOrderLineGroupRet ItemGroupRef' => 								[ null, null ],
            'PurchaseOrderRet PurchaseOrderLineGroupRet ItemGroupRef *' => 								[ 'PurchaseOrder_PurchaseOrderLineGroup', 'ItemGroup_*' ],
            'PurchaseOrderRet PurchaseOrderLineGroupRet OverrideUOMSetRef' => 							[ null, null ],
            'PurchaseOrderRet PurchaseOrderLineGroupRet OverrideUOMSetRef *' => 						[ 'PurchaseOrder_PurchaseOrderLineGroup', 'OverrideUOMSet_*' ],
            'PurchaseOrderRet PurchaseOrderLineGroupRet Desc' => 										[ 'PurchaseOrder_PurchaseOrderLineGroup', 'Descrip' ],
            'PurchaseOrderRet PurchaseOrderLineGroupRet PurchaseOrderLineRet' => 						[ null, null ],
            'PurchaseOrderRet PurchaseOrderLineGroupRet PurchaseOrderLineRet ItemRef' => 				[ null, null ],
            'PurchaseOrderRet PurchaseOrderLineGroupRet PurchaseOrderLineRet ItemRef *' => 				[ 'PurchaseOrder_PurchaseOrderLineGroup_PurchaseOrderLine', 'Item_*' ],
            'PurchaseOrderRet PurchaseOrderLineGroupRet PurchaseOrderLineRet Desc' => 					[ 'PurchaseOrder_PurchaseOrderLineGroup_PurchaseOrderLine', 'Descrip' ],
            'PurchaseOrderRet PurchaseOrderLineGroupRet PurchaseOrderLineRet OverrideUOMSetRef' => 		[ null, null ],
            'PurchaseOrderRet PurchaseOrderLineGroupRet PurchaseOrderLineRet OverrideUOMSetRef *' => 	[ 'PurchaseOrder_PurchaseOrderLineGroup_PurchaseOrderLine', 'OverrideUOMSet_*' ],

            'PurchaseOrderRet PurchaseOrderLineGroupRet PurchaseOrderLineRet ClassRef' => 				[ null, null ],
            'PurchaseOrderRet PurchaseOrderLineGroupRet PurchaseOrderLineRet ClassRef *' =>				[ 'PurchaseOrder_PurchaseOrderLineGroup_PurchaseOrderLine', 'Class_*' ],
            'PurchaseOrderRet PurchaseOrderLineGroupRet PurchaseOrderLineRet CustomerRef' => 			[ null, null ],
            'PurchaseOrderRet PurchaseOrderLineGroupRet PurchaseOrderLineRet CustomerRef *' => 			[ 'PurchaseOrder_PurchaseOrderLineGroup_PurchaseOrderLine', 'Customer_*' ],

            'PurchaseOrderRet PurchaseOrderLineGroupRet PurchaseOrderLineRet DataExtRet' => 			[ null, null ],
            'PurchaseOrderRet PurchaseOrderLineGroupRet PurchaseOrderLineRet DataExtRet *' => 			[ null, null ],
            'PurchaseOrderRet PurchaseOrderLineGroupRet PurchaseOrderLineRet DataExtRet *' => 			[ 'DataExt', '*' ],

            'PurchaseOrderRet PurchaseOrderLineGroupRet PurchaseOrderLineRet *' => 						[ 'PurchaseOrder_PurchaseOrderLineGroup_PurchaseOrderLine', '*' ],
            'PurchaseOrderRet PurchaseOrderLineGroupRet DataExtRet' => 									[ null, null ],
            'PurchaseOrderRet PurchaseOrderLineGroupRet DataExtRet *' => 								[ 'DataExt', '*' ],

            'PurchaseOrderRet PurchaseOrderLineGroupRet *' => 											[ 'PurchaseOrder_PurchaseOrderLineGroup', '*' ],

            'PurchaseOrderRet DataExtRet' => 			[ null, null ],
            'PurchaseOrderRet DataExtRet *' => 			[ 'DataExt', '*' ],

            'PurchaseOrderRet LinkedTxn' => 			[ null, null ],
            'PurchaseOrderRet LinkedTxn TxnID' => 		[ 'PurchaseOrder_LinkedTxn', 'ToTxnID' ],
            'PurchaseOrderRet LinkedTxn *' => 			[ 'PurchaseOrder_LinkedTxn', '*' ],
            'PurchaseOrderRet *' => 					[ 'PurchaseOrder', '*' ],

            'ReceivePaymentRet' => 						[ 'ReceivePayment', null ],
            'ReceivePaymentRet CustomerRef' => 			[ null, null ],
            'ReceivePaymentRet CustomerRef *' => 		[ 'ReceivePayment', 'Customer_*' ],
            'ReceivePaymentRet ARAccountRef' => 		[ null, null ],
            'ReceivePaymentRet ARAccountRef *' => 		[ 'ReceivePayment', 'ARAccount_*', ],
            'ReceivePaymentRet PaymentMethodRef' => 	[ null, null ],
            'ReceivePaymentRet PaymentMethodRef *' => 	[ 'ReceivePayment', 'PaymentMethod_*' ],

            'ReceivePaymentRet DepositToAccountRef' => 							[ null, null ],
            'ReceivePaymentRet DepositToAccountRef *' => 						[ 'ReceivePayment', 'DepositToAccount_*' ],
            'ReceivePaymentRet CreditCardTxnInfo' => 							[ null, null ],
            'ReceivePaymentRet CreditCardTxnInfo CreditCardTxnInputInfo' => 	[ null, null ],
            'ReceivePaymentRet CreditCardTxnInfo CreditCardTxnInputInfo *' => 	[ 'ReceivePayment', 'CreditCardTxnInfo_CreditCardTxnInputInfo_*' ],
            'ReceivePaymentRet CreditCardTxnInfo CreditCardTxnResultInfo' => 	[ null, null ],
            'ReceivePaymentRet CreditCardTxnInfo CreditCardTxnResultInfo *' => 	[ 'ReceivePayment', 'CreditCardTxnInfo_CreditCardTxnResultInfo_*' ],
            'ReceivePaymentRet AppliedToTxnRet' => 								[ null, null ],
            'ReceivePaymentRet AppliedToTxnRet TxnID' => 						[ 'ReceivePayment_AppliedToTxn', 'ToTxnID' ],
            'ReceivePaymentRet AppliedToTxnRet DiscountAccountRef' => 			[ null, null ],
            'ReceivePaymentRet AppliedToTxnRet DiscountAccountRef *' => 		[ 'ReceivePayment_AppliedToTxn', 'DiscountAccount_*' ],
            'ReceivePaymentRet AppliedToTxnRet *' => 							[ 'ReceivePayment_AppliedToTxn', '*' ],
            'ReceivePaymentRet DataExtRet' => 									[ null, null ],
            'ReceivePaymentRet DataExtRet *' => 								[ 'DataExt', '*' ],
            'ReceivePaymentRet *' => 											[ 'ReceivePayment', '*' ],

            'SalesOrderRet' => 									[ 'SalesOrder', null ],
            'SalesOrderRet CustomerRef' => 						[ null, null ],
            'SalesOrderRet CustomerRef *' => 					[ 'SalesOrder', 'Customer_*' ],
            'SalesOrderRet ClassRef' => 						[ null, null ],
            'SalesOrderRet ClassRef *' => 						[ 'SalesOrder', 'Class_*' ],
            'SalesOrderRet TemplateRef' => 						[ null, null ],
            'SalesOrderRet TemplateRef *' => 					[ 'SalesOrder', 'Template_*' ],
            'SalesOrderRet BillAddress' => 						[ null, null ],
            'SalesOrderRet BillAddress *' => 					[ 'SalesOrder', 'BillAddress_*' ],
            'SalesOrderRet BillAddressBlock' => 				[ null, null ],
            'SalesOrderRet BillAddressBlock *' => 				[ 'SalesOrder', 'BillAddressBlock_*' ],
            'SalesOrderRet ShipAddress' => 						[ null, null ],
            'SalesOrderRet ShipAddress *' => 					[ 'SalesOrder', 'ShipAddress_*' ],
            'SalesOrderRet ShipAddressBlock' => 				[ null, null ],
            'SalesOrderRet ShipAddressBlock *' => 				[ 'SalesOrder', 'ShipAddressBlock_*' ],
            'SalesOrderRet TermsRef' => 						[ null, null ],
            'SalesOrderRet TermsRef *' => 						[ 'SalesOrder', 'Terms_*' ],
            'SalesOrderRet SalesRepRef' => 						[ null, null ],
            'SalesOrderRet SalesRepRef *' => 					[ 'SalesOrder', 'SalesRep_*' ],
            'SalesOrderRet ShipMethodRef' => 					[ null, null ],
            'SalesOrderRet ShipMethodRef *' => 					[ 'SalesOrder', 'ShipMethod_*' ],
            'SalesOrderRet ItemSalesTaxRef' => 					[ null, null ],
            'SalesOrderRet ItemSalesTaxRef *' => 				[ 'SalesOrder', 'ItemSalesTax_*' ],
            'SalesOrderRet CustomerMsgRef' => 					[ null, null ],
            'SalesOrderRet CustomerMsgRef *' => 				[ 'SalesOrder', 'CustomerMsg_*' ],
            'SalesOrderRet CustomerSalesTaxCodeRef' => 			[ null, null ],
            'SalesOrderRet CustomerSalesTaxCodeRef *' => 		[ 'SalesOrder', 'CustomerSalesTaxCode_*' ],

            'SalesOrderRet LinkedTxn' => 				[ 'SalesOrder_LinkedTxn', null ],
            'SalesOrderRet LinkedTxn TxnID' => 			[ 'SalesOrder_LinkedTxn', 'ToTxnID' ],
            'SalesOrderRet LinkedTxn *' => 				[ 'SalesOrder_LinkedTxn', '*' ],

            'SalesOrderRet SalesOrderLineRet' => 							[ 'SalesOrder_SalesOrderLine', null ],
            'SalesOrderRet SalesOrderLineRet Desc' => 						[ 'SalesOrder_SalesOrderLine', 'Descrip' ],
            'SalesOrderRet SalesOrderLineRet ItemRef' => 					[ null, null ],
            'SalesOrderRet SalesOrderLineRet ItemRef *' => 					[ 'SalesOrder_SalesOrderLine', 'Item_*' ],
            'SalesOrderRet SalesOrderLineRet OverrideUOMSetRef' => 			[ null, null ],
            'SalesOrderRet SalesOrderLineRet OverrideUOMSetRef *' => 		[ 'SalesOrder_SalesOrderLine', 'OverrideUOMSet_*' ],
            'SalesOrderRet SalesOrderLineRet ClassRef' => 					[ null, null ],
            'SalesOrderRet SalesOrderLineRet ClassRef *' => 				[ 'SalesOrder_SalesOrderLine', 'Class_*' ],
            'SalesOrderRet SalesOrderLineRet InventorySiteRef' => 			[ null, null ],
            'SalesOrderRet SalesOrderLineRet InventorySiteRef *' => 		[ 'SalesOrder_SalesOrderLine', 'InventorySite_*' ],
            'SalesOrderRet SalesOrderLineRet SalesTaxCodeRef' => 			[ null, null ],
            'SalesOrderRet SalesOrderLineRet SalesTaxCodeRef *' => 			[ 'SalesOrder_SalesOrderLine', 'SalesTaxCode_*' ],

            'SalesOrderRet SalesOrderLineRet DataExtRet' => 	[ null, null ],
            'SalesOrderRet SalesOrderLineRet DataExtRet *' => 	[ 'DataExt', '*' ],
            'SalesOrderRet SalesOrderLineRet *' => 				[ 'SalesOrder_SalesOrderLine', '*' ],

            'SalesOrderRet SalesOrderLineGroupRet' => 							[ 'SalesOrder_SalesOrderLineGroup', null ],
            'SalesOrderRet SalesOrderLineGroupRet Desc' => 						[ 'SalesOrder_SalesOrderLineGroup', 'Descrip' ],
            'SalesOrderRet SalesOrderLineGroupRet ItemGroupRef' => 				[ null, null ],
            'SalesOrderRet SalesOrderLineGroupRet ItemGroupRef *' => 			[ 'SalesOrder_SalesOrderLineGroup', 'ItemGroup_*' ],
            'SalesOrderRet SalesOrderLineGroupRet OverrideUOMSetRef' => 		[ null, null ],
            'SalesOrderRet SalesOrderLineGroupRet OverrideUOMSetRef *' => 		[ 'SalesOrder_SalesOrderLineGroup', 'OverrideUOMSet_*' ],

            'SalesOrderRet SalesOrderLineGroupRet SalesOrderLineRet' => 						[ null, null ],
            'SalesOrderRet SalesOrderLineGroupRet SalesOrderLineRet ItemRef' =>					[ null, null ],
            'SalesOrderRet SalesOrderLineGroupRet SalesOrderLineRet ItemRef *' => 				[ 'SalesOrder_SalesOrderLineGroup_SalesOrderLine', 'Item_*' ],
            'SalesOrderRet SalesOrderLineGroupRet SalesOrderLineRet Desc' => 					[ 'SalesOrder_SalesOrderLineGroup_SalesOrderLine', 'Descrip' ],
            'SalesOrderRet SalesOrderLineGroupRet SalesOrderLineRet OverrideUOMSetRef' => 		[ null, null ],
            'SalesOrderRet SalesOrderLineGroupRet SalesOrderLineRet OverrideUOMSetRef *' => 	[ 'SalesOrder_SalesOrderLineGroup_SalesOrderLine', 'OverrideUOMSet_*' ],
            'SalesOrderRet SalesOrderLineGroupRet SalesOrderLineRet SalesTaxCodeRef' => 		[ null, null ],
            'SalesOrderRet SalesOrderLineGroupRet SalesOrderLineRet SalesTaxCodeRef *' => 		[ 'SalesOrder_SalesOrderLineGroup_SalesOrderLine', 'SalesTaxCode_*' ],
            'SalesOrderRet SalesOrderLineGroupRet SalesOrderLineRet ClassRef' => 				[ null, null ],
            'SalesOrderRet SalesOrderLineGroupRet SalesOrderLineRet ClassRef *' => 				[ 'SalesOrder_SalesOrderLineGroup_SalesOrderLine', 'Class_*' ],

            'SalesOrderRet SalesOrderLineGroupRet SalesOrderLineRet DataExtRet' => 				[ null, null ],
            'SalesOrderRet SalesOrderLineGroupRet SalesOrderLineRet DataExtRet *' => 			[ 'DataExt', '*' ],

            'SalesOrderRet SalesOrderLineGroupRet SalesOrderLineRet *' => 						[ 'SalesOrder_SalesOrderLineGroup_SalesOrderLine', '*' ],

            'SalesOrderRet SalesOrderLineGroupRet DataExtRet' => 	[ null, null ],
            'SalesOrderRet SalesOrderLineGroupRet DataExtRet *' => 	[ 'DataExt', '*' ],
            'SalesOrderRet SalesOrderLineGroupRet *' => 			[ 'SalesOrder_SalesOrderLineGroup', '*' ],

            'SalesOrderRet DataExtRet' => 	[ null, null ],
            'SalesOrderRet DataExtRet *' => [ 'DataExt', '*' ],
            'SalesOrderRet *' => 			[ 'SalesOrder', '*' ],

            'SalesReceiptRet' => 								[ 'SalesReceipt', null ],
            'SalesReceiptRet CustomerRef' => 					[ null, null ],
            'SalesReceiptRet CustomerRef *' => 					[ 'SalesReceipt', 'Customer_*' ],
            'SalesReceiptRet ClassRef' => 						[ null, null ],
            'SalesReceiptRet ClassRef *' => 					[ 'SalesReceipt', 'Class_*' ],
            'SalesReceiptRet TemplateRef' => 					[ null, null ],
            'SalesReceiptRet TemplateRef *' => 					[ 'SalesReceipt', 'Template_*' ],
            'SalesReceiptRet BillAddressBlock' => 				[ null, null ],
            'SalesReceiptRet BillAddressBlock *' => 			[ 'SalesReceipt', 'BillAddressBlock_*' ],
            'SalesReceiptRet BillAddress' => 					[ null, null ],
            'SalesReceiptRet BillAddress *' => 					[ 'SalesReceipt', 'BillAddress_*' ],
            'SalesReceiptRet ShipAddressBlock' => 				[ null, null ],
            'SalesReceiptRet ShipAddressBlock *' => 			[ 'SalesReceipt', 'ShipAddressBlock_*' ],
            'SalesReceiptRet ShipAddress' => 					[ null, null ],
            'SalesReceiptRet ShipAddress *' => 					[ 'SalesReceipt', 'ShipAddress_*' ],
            'SalesReceiptRet PaymentMethodRef' => 				[ null, null ],
            'SalesReceiptRet PaymentMethodRef *' => 			[ 'SalesReceipt', 'PaymentMethod_*' ],
            'SalesReceiptRet SalesRepRef' => 					[ null, null ],
            'SalesReceiptRet SalesRepRef *' => 					[ 'SalesReceipt', 'SalesRep_*' ],
            'SalesReceiptRet ShipMethodRef' => 					[ null, null ],
            'SalesReceiptRet ShipMethodRef *' => 				[ 'SalesReceipt', 'ShipMethod_*' ],
            'SalesReceiptRet ItemSalesTaxRef' => 				[ null, null ],
            'SalesReceiptRet ItemSalesTaxRef *' => 				[ 'SalesReceipt', 'ItemSalesTax_*' ],
            'SalesReceiptRet CurrencyRef' =>					[ null, null ],
            'SalesReceiptRet CurrencyRef *' => 					[ 'SalesReceipt', 'Currency_*' ],
            'SalesReceiptRet CustomerMsgRef' => 				[ null, null ],
            'SalesReceiptRet CustomerMsgRef *' => 				[ 'SalesReceipt', 'CustomerMsg_*' ],
            'SalesReceiptRet CustomerSalesTaxCodeRef' => 		[ null, null ],
            'SalesReceiptRet CustomerSalesTaxCodeRef *' => 		[ 'SalesReceipt', 'CustomerSalesTaxCode_*' ],
            'SalesReceiptRet DepositToAccountRef' => 			[ null, null ],
            'SalesReceiptRet DepositToAccountRef *' => 			[ 'SalesReceipt', 'DepositToAccount_*' ],

            'SalesReceiptRet CreditCardTxnInfo' => 								[ null, null ],
            'SalesReceiptRet CreditCardTxnInfo CreditCardTxnInputInfo' => 		[ null, null ],
            'SalesReceiptRet CreditCardTxnInfo CreditCardTxnInputInfo *' => 	[ 'SalesReceipt', 'CreditCardTxnInfo_CreditCardTxnInputInfo_*' ],
            'SalesReceiptRet CreditCardTxnInfo CreditCardTxnResultInfo' => 		[ null, null ],
            'SalesReceiptRet CreditCardTxnInfo CreditCardTxnResultInfo *' => 	[ 'SalesReceipt', 'CreditCardTxnInfo_CreditCardTxnResultInfo_*' ],
            'SalesReceiptRet CreditCardTxnInfo *' => 							[ 'SalesReceipt', 'CreditCardTxnInfo_*' ],

            'SalesReceiptRet SalesReceiptLineRet' => 								[ null, null ],
            'SalesReceiptRet SalesReceiptLineRet Desc' => 							[ 'SalesReceipt_SalesReceiptLine', 'Descrip' ],
            'SalesReceiptRet SalesReceiptLineRet ItemRef' => 						[ null, null ],
            'SalesReceiptRet SalesReceiptLineRet ItemRef *' => 						[ 'SalesReceipt_SalesReceiptLine', 'Item_*' ],
            'SalesReceiptRet SalesReceiptLineRet OverrideUOMSetRef' => 				[ null, null ],
            'SalesReceiptRet SalesReceiptLineRet OverrideUOMSetRef *' => 			[ 'SalesReceipt_SalesReceiptLine', 'OverrideUOMSet_*' ],
            'SalesReceiptRet SalesReceiptLineRet ClassRef' => 						[ null, null ],
            'SalesReceiptRet SalesReceiptLineRet ClassRef *' => 					[ 'SalesReceipt_SalesReceiptLine', 'Class_*' ],
            'SalesReceiptRet SalesReceiptLineRet InventorySiteRef' => 				[ null, null ],
            'SalesReceiptRet SalesReceiptLineRet InventorySiteRef *' => 			[ 'SalesReceipt_SalesReceiptLine', 'InventorySite_*' ],
            'SalesReceiptRet SalesReceiptLineRet SalesTaxCodeRef' => 				[ null, null ],
            'SalesReceiptRet SalesReceiptLineRet SalesTaxCodeRef *' => 				[ 'SalesReceipt_SalesReceiptLine', 'SalesTaxCode_*' ],
            'SalesReceiptRet SalesReceiptLineRet CreditCardTxnInfo' => 				[ null, null ],

            'SalesReceiptRet SalesReceiptLineRet CreditCardTxnInfo CreditCardTxnInputInfo' => 			[ null, null ],
            'SalesReceiptRet SalesReceiptLineRet CreditCardTxnInfo CreditCardTxnInputInfo *' => 		[ 'SalesReceipt_SalesReceiptLine', 'CreditCardTxnInfo_CreditCardTxnInputInfo_*' ],
            'SalesReceiptRet SalesReceiptLineRet CreditCardTxnInfo CreditCardTxnResultInfo' => 			[ null, null ],
            'SalesReceiptRet SalesReceiptLineRet CreditCardTxnInfo CreditCardTxnResultInfo *' => 		[ 'SalesReceipt_SalesReceiptLine', 'CreditCardTxnInfo_CreditCardTxnResultInfo_*' ],
            'SalesReceiptRet SalesReceiptLineRet CreditCardTxnInfo *' => 								[ 'SalesReceipt_SalesReceiptLine_CreditCardTxnInfo', '*' ],

            'SalesReceiptRet SalesReceiptLineRet DataExtRet' => 	[ null, null ],
            'SalesReceiptRet SalesReceiptLineRet DataExtRet *' => 	[ 'DataExt', '*' ],
            'SalesReceiptRet SalesReceiptLineRet *' => 				[ 'SalesReceipt_SalesReceiptLine', '*' ],

            'SalesReceiptRet SalesReceiptLineGroupRet' => 											[ null, null ],
            'SalesReceiptRet SalesReceiptLineGroupRet ItemGroupRef' => 								[ null, null ],
            'SalesReceiptRet SalesReceiptLineGroupRet ItemGroupRef *' => 							[ 'SalesReceipt_SalesReceiptLineGroup', 'ItemGroup_*' ],
            'SalesReceiptRet SalesReceiptLineGroupRet OverrideUOMSetRef' => 						[ null, null ],
            'SalesReceiptRet SalesReceiptLineGroupRet OverrideUOMSetRef *' => 						[ 'SalesReceipt_SalesReceiptLineGroup', 'OverrideUOMSet_*' ],
            'SalesReceiptRet SalesReceiptLineGroupRet Desc' => 										[ 'SalesReceipt_SalesReceiptLineGroup', 'Descrip' ],
            'SalesReceiptRet SalesReceiptLineGroupRet SalesReceiptLineRet' => 						[ null, null ],
            'SalesReceiptRet SalesReceiptLineGroupRet SalesReceiptLineRet ItemRef' => 				[ null, null ],
            'SalesReceiptRet SalesReceiptLineGroupRet SalesReceiptLineRet ItemRef *' =>				[ 'SalesReceipt_SalesReceiptLineGroup_SalesReceiptLine', 'Item_*' ],
            'SalesReceiptRet SalesReceiptLineGroupRet SalesReceiptLineRet OverrideUOMSetRef' => 	[ null, null ],
            'SalesReceiptRet SalesReceiptLineGroupRet SalesReceiptLineRet OverrideUOMSetRef *' => 	[ 'SalesReceipt_SalesReceiptLineGroup_SalesReceiptLine', 'OverrideUOMSet_*' ],
            'SalesReceiptRet SalesReceiptLineGroupRet SalesReceiptLineRet ClassRef' => 				[ null, null ],
            'SalesReceiptRet SalesReceiptLineGroupRet SalesReceiptLineRet ClassRef *' => 			[ 'SalesReceipt_SalesReceiptLineGroup_SalesReceiptLine', 'Class_*' ],
            'SalesReceiptRet SalesReceiptLineGroupRet SalesReceiptLineRet SalesTaxCodeRef' => 		[ null, null ],
            'SalesReceiptRet SalesReceiptLineGroupRet SalesReceiptLineRet SalesTaxCodeRef *' => 	[ 'SalesReceipt_SalesReceiptLineGroup_SalesReceiptLine', 'SalesTaxCode_*' ],

            'SalesReceiptRet SalesReceiptLineGroupRet SalesReceiptLineRet Desc' => 											[ 'SalesReceipt_SalesReceiptLineGroup_SalesReceiptLine', 'Descrip' ],
            'SalesReceiptRet SalesReceiptLineGroupRet SalesReceiptLineRet CreditCardTxnInfo' => 							[ null, null ],
            'SalesReceiptRet SalesReceiptLineGroupRet SalesReceiptLineRet CreditCardTxnInfo CreditCardTxnInputInfo' => 		[ null, null ],
            'SalesReceiptRet SalesReceiptLineGroupRet SalesReceiptLineRet CreditCardTxnInfo CreditCardTxnInputInfo *' => 	[ 'SalesReceipt_SalesReceiptLineGroup_SalesReceiptLine', 'CreditCardTxnInfo_CreditCardTxnInputInfo_*' ],
            'SalesReceiptRet SalesReceiptLineGroupRet SalesReceiptLineRet CreditCardTxnInfo CreditCardTxnResultInfo' => 	[ null, null ],
            'SalesReceiptRet SalesReceiptLineGroupRet SalesReceiptLineRet CreditCardTxnInfo CreditCardTxnResultInfo *' => 	[ 'SalesReceipt_SalesReceiptLineGroup_SalesReceiptLine', 'CreditCardTxnInfo_CreditCardTxnResultInfo_*' ],
            'SalesReceiptRet SalesReceiptLineGroupRet SalesReceiptLineRet CreditCardTxnInfo *' => 							[ 'SalesReceipt_SalesReceiptLineGroup_SalesReceiptLine', 'CreditCardTxnInfo_*' ],
            'SalesReceiptRet SalesReceiptLineGroupRet SalesReceiptLineRet DataExtRet' => 									[ null, null ],
            'SalesReceiptRet SalesReceiptLineGroupRet SalesReceiptLineRet DataExtRet *' => 									[ 'DataExt', '*' ],
            'SalesReceiptRet SalesReceiptLineGroupRet SalesReceiptLineRet *' => 											[ 'SalesReceipt_SalesReceiptLineGroup_SalesReceiptLine', '*' ],

            'SalesReceiptRet SalesReceiptLineGroupRet DataExtRet' => 	[ null, null ],
            'SalesReceiptRet SalesReceiptLineGroupRet DataExtRet *' => 	[ 'DataExt', '*' ],
            'SalesReceiptRet SalesReceiptLineGroupRet *' => 			[ 'SalesReceipt_SalesReceiptLineGroup', '*' ],

            'SalesReceiptRet DataExtRet' => 	[ null, null ],
            'SalesReceiptRet DataExtRet *' => 	[ 'DataExt', '*' ],
            'SalesReceiptRet *' => 				[ 'SalesReceipt', '*' ],

            'SalesRepRet' => 								[ 'SalesRep', null ],
            'SalesRepRet SalesRepEntityRef' => 				[ null, null ],
            'SalesRepRet SalesRepEntityRef *' => 			[ 'SalesRep', 'SalesRepEntity_*' ],
            'SalesRepRet *' => 								[ 'SalesRep', '*' ],

            'SalesTaxCodeRet' => 							[ 'SalesTaxCode', null ],
            'SalesTaxCodeRet Desc' => 						[ 'SalesTaxCode', 'Descrip' ],
            'SalesTaxCodeRet *' => 							[ 'SalesTaxCode', '*' ],

            'ShipMethodRet' => 								[ 'ShipMethod', null ],
            'ShipMethodRet *' => 							[ 'ShipMethod', '*' ],

            'StandardTermsRet' => 							[ 'StandardTerms', null ],

            'StandardTermsRet *' => 						[ 'StandardTerms', '*' ],

            'StandardTermsRet' => 							[ null, null ],
            'StandardTermsRet *' =>		 					[ 'StandardTerms', '*' ],

            'TemplateRet' => 								[ 'Template', null ],
            'TemplateRet *' => 								[ 'Template', '*' ],

            'TimeTrackingRet' => 							[ 'TimeTracking', null ],
            'TimeTrackingRet EntityRef' => 					[ null, null ],
            'TimeTrackingRet EntityRef *' => 				[ 'TimeTracking', 'Entity_*' ],
            'TimeTrackingRet CustomerRef' => 				[ null, null ],
            'TimeTrackingRet CustomerRef *' => 				[ 'TimeTracking', 'Customer_*' ],
            'TimeTrackingRet ItemServiceRef' => 			[ null, null ],
            'TimeTrackingRet ItemServiceRef *' => 			[ 'TimeTracking', 'ItemService_*' ],
            'TimeTrackingRet ClassRef' => 					[ null, null ],
            'TimeTrackingRet ClassRef *' => 				[ 'TimeTracking', 'Class_*' ],
            'TimeTrackingRet PayrollItemWageRef' => 		[ null, null ],
            'TimeTrackingRet PayrollItemWageRef *' => 		[ 'TimeTracking', 'PayrollItemWage_*' ],
            'TimeTrackingRet *' => 							[ 'TimeTracking', '*' ],

            'UnitOfMeasureSetRet' => 				[ 'UnitOfMeasureSet', null ],
            'UnitOfMeasureSetRet BaseUnit' => 		[ null, null ],
            'UnitOfMeasureSetRet BaseUnit *' => 	[ 'UnitOfMeasureSet', 'BaseUnit_*' ],
            'UnitOfMeasureSetRet RelatedUnit' => 	[ null, null ],
            'UnitOfMeasureSetRet RelatedUnit *' => 	[ 'UnitOfMeasureSet_RelatedUnit', '*' ],
            'UnitOfMeasureSetRet DefaultUnit' => 	[ null, null ],
            'UnitOfMeasureSetRet DefaultUnit *' => 	[ 'UnitOfMeasureSet_DefaultUnit', '*' ],
            'UnitOfMeasureSetRet *' => 				[ 'UnitOfMeasureSet', '*' ],

            'VehicleRet' => 						[ 'Vehicle', null ],
            'VehicleRet Desc' => 					[ 'Vehicle', 'Descrip' ],

            'VehicleRet *' => 						[ 'Vehicle', '*' ],

            'VehicleMileageRet' => 						[ 'VehicleMileage', null ],
            'VehicleMileageRet VehicleRef' => 			[ null, null ],
            'VehicleMileageRet VehicleRef *' => 		[ 'VehicleMileage', 'Vehicle_*' ],
            'VehicleMileageRet CustomerRef' => 			[ null, null ],
            'VehicleMileageRet CustomerRef *' => 		[ 'VehicleMileage', 'Customer_*' ],
            'VehicleMileageRet ItemRef' => 				[ null, null ],
            'VehicleMileageRet ItemRef *' => 			[ 'VehicleMileage', 'Item_*' ],
            'VehicleMileageRet ClassRef' => 			[ null, null ],
            'VehicleMileageRet ClassRef *' => 			[ 'VehicleMileage', 'Class_*' ],
            'VehicleMileageRet *' => 					[ 'VehicleMileage', '*' ],

            'VendorRet' => 							[ 'Vendor', null ],
            'VendorRet VendorAddress' => 			[ null, null ],
            'VendorRet VendorAddress *' => 			[ 'Vendor', 'VendorAddress_*' ],
            'VendorRet VendorAddressBlock' => 		[ null, null ],
            'VendorRet VendorAddressBlock *' => 	[ 'Vendor', 'VendorAddressBlock_*' ],
            'VendorRet VendorTypeRef' => 			[ null, null ],
            'VendorRet VendorTypeRef *' => 			[ 'Vendor', 'VendorType_*' ],
            'VendorRet TermsRef' => 				[ null, null ],
            'VendorRet TermsRef *' => 				[ 'Vendor', 'Terms_*' ],
            'VendorRet BillingRateRef' => 			[ null, null ],
            'VendorRet BillingRateRef *' => 		[ 'Vendor', 'BillingRate_*' ],
            'VendorRet DataExtRet' => 				[ null, null ],
            'VendorRet DataExtRet *' => 			[ 'DataExt', '*' ],
            'VendorRet *' => 						[ 'Vendor', '*' ],

            'VendorCreditRet' => 					[ 'VendorCredit', null, ],
            'VendorCreditRet VendorRef' => 			[ null, null ],
            'VendorCreditRet VendorRef *' => 		[ 'VendorCredit', 'Vendor_*' ],
            'VendorCreditRet APAccountRef' => 		[ null, null ],
            'VendorCreditRet APAccountRef *' =>		[ 'VendorCredit', 'APAccount_*' ],

            'VendorCreditRet LinkedTxn' => 			[ null, null ],
            'VendorCreditRet LinkedTxn TxnID' => 	[ 'VendorCredit_LinkedTxn', 'ToTxnID' ],
            'VendorCreditRet LinkedTxn *' => 		[ 'VendorCredit_LinkedTxn', '*' ],

            'VendorCreditRet ExpenseLineRet' => 					[ null, null ],
            'VendorCreditRet ExpenseLineRet AccountRef' => 			[ null, null ],
            'VendorCreditRet ExpenseLineRet AccountRef *' => 		[ 'VendorCredit_ExpenseLine', 'Account_*' ],
            'VendorCreditRet ExpenseLineRet CustomerRef' => 		[ null, null ],
            'VendorCreditRet ExpenseLineRet CustomerRef *' => 		[ 'VendorCredit_ExpenseLine', 'Customer_*' ],
            'VendorCreditRet ExpenseLineRet ClassRef' => 			[ null, null ],
            'VendorCreditRet ExpenseLineRet ClassRef *' => 			[ 'VendorCredit_ExpenseLine', 'Class_*' ],
            'VendorCreditRet ExpenseLineRet *' => 					[ 'VendorCredit_ExpenseLine', '*' ],

            'VendorCreditRet ItemLineRet' => 							[ null, null ],
            'VendorCreditRet ItemLineRet Desc' => 						[ 'VendorCredit_ItemLine', 'Descrip' ],
            'VendorCreditRet ItemLineRet ItemRef' => 					[ null, null ],
            'VendorCreditRet ItemLineRet ItemRef *' => 					[ 'VendorCredit_ItemLine', 'Item_*' ],
            'VendorCreditRet ItemLineRet OverrideUOMSetRef' => 			[ null, null ],
            'VendorCreditRet ItemLineRet OverrideUOMSetRef *' => 		[ 'VendorCredit_ItemLine', 'OverrideUOMSet_*' ],
            'VendorCreditRet ItemLineRet CustomerRef' => 				[ null, null ],
            'VendorCreditRet ItemLineRet CustomerRef *' => 				[ 'VendorCredit_ItemLine', 'Customer_*' ],
            'VendorCreditRet ItemLineRet ClassRef' => 					[ null, null ],
            'VendorCreditRet ItemLineRet ClassRef *' => 				[ 'VendorCredit_ItemLine', 'Class_*' ],
            'VendorCreditRet ItemLineRet *' => 							[ 'VendorCredit_ItemLine', '*' ],

            'VendorCreditRet ItemGroupLineRet' => 									[ null, null ],
            'VendorCreditRet ItemGroupLineRet Desc' => 								[ 'VendorCredit_ItemGroupLine', 'Descrip' ],
            'VendorCreditRet ItemGroupLineRet ItemGroupRef' => 						[ null, null ],
            'VendorCreditRet ItemGroupLineRet ItemGroupRef *' => 					[ 'VendorCredit_ItemGroupLine', 'ItemGroup_*' ],
            'VendorCreditRet ItemGroupLineRet OverrideUOMSetRef' => 				[ null, null ],
            'VendorCreditRet ItemGroupLineRet OverrideUOMSetRef *' => 				[ 'VendorCredit_ItemGroupLine', 'OverrideUOMSet_*' ],
            'VendorCreditRet ItemGroupLineRet ItemLineRet' => 						[ null, null ],
            'VendorCreditRet ItemGroupLineRet ItemLineRet ItemRef' => 				[ null, null ],
            'VendorCreditRet ItemGroupLineRet ItemLineRet ItemRef *' => 			[ 'VendorCredit_ItemGroupLine_ItemLine', 'Item_*' ],
            'VendorCreditRet ItemGroupLineRet ItemLineRet Desc' => 					[ 'VendorCredit_ItemGroupLine_ItemLine', 'Descrip' ],
            'VendorCreditRet ItemGroupLineRet ItemLineRet OverrideUOMSetRef' => 	[ null, null ],
            'VendorCreditRet ItemGroupLineRet ItemLineRet OverrideUOMSetRef *' => 	[ 'VendorCredit_ItemGroupLine_ItemLine', 'OverrideUOMSet_*' ],
            'VendorCreditRet ItemGroupLineRet ItemLineRet CustomerRef' => 			[ null, null ],
            'VendorCreditRet ItemGroupLineRet ItemLineRet CustomerRef *' => 		[ 'VendorCredit_ItemGroupLine_ItemLine', 'Customer_*' ],
            'VendorCreditRet ItemGroupLineRet ItemLineRet ClassRef' => 				[ null, null ],
            'VendorCreditRet ItemGroupLineRet ItemLineRet ClassRef *' => 			[ 'VendorCredit_ItemGroupLine_ItemLine', 'Class_*' ],
            'VendorCreditRet ItemGroupLineRet ItemLineRet *' => 					[ 'VendorCredit_ItemGroupLine_ItemLine', '*' ],
            'VendorCreditRet ItemGroupLineRet *' => 								[ 'VendorCredit_ItemGroupLine', '*' ],

            'VendorCreditRet DataExtRet' => 	[ null, null ],
            'VendorCreditRet DataExtRet *' => 	[ 'DataExt', '*' ],
            'VendorCreditRet *' => 				[ 'VendorCredit', '*' ],

            'VendorTypeRet' => 						[ 'VendorType', null ],
            'VendorTypeRet ParentRef' => 			[ null, null ],
            'VendorTypeRet ParentRef *' => 			[ 'VendorType', 'Parent_*' ],
            'VendorTypeRet *' => 					[ 'VendorType', '*' ],

            'WorkersCompCodeRet' => 				[ 'WorkersCompCode', null ],
            'WorkersCompCodeRet Desc' => 			[ 'WorkersCompCode', 'Descrip' ],
            'WorkersCompCodeRet RateHistory' => 	[ null, null ],
            'WorkersCompCodeRet RateHistory *' => 	[ 'WorkersCompCode_RateHistory', '*' ],
            'WorkersCompCodeRet *' => 				[ 'WorkersCompCode', '*' ],
            ];

        static $sql_to_xml = null;
        if (is_null($sql_to_xml)) {
            foreach ($xml_to_sql as $xml => $sql) {
                $sql_to_xml[$sql[0] . '.' . $sql[1]] = $xml;
            }
        }

        // Mapping of:
        //	XPATH => array(
        //		array( table => extra field ),
        //		array( another table => another extra field ),
        static $xml_to_sql_others = [
            'AccountRet TaxLineInfoRet' => 											[
                [ 'Account_TaxLineInfo', 'Account_ListID'],
                [ 'Account_TaxLineInfo', 'Account_FullName' ],
                ],
            'AccountRet DataExtRet' => 												[
                [ 'DataExt', 'EntityType' ],
                [ 'DataExt', 'TxnType' ],
                [ 'DataExt', 'Entity_ListID' ],
                [ 'DataExt', 'Txn_TxnID' ],
                ],
            'BillingRateRet BillingRatePerItemRet' => 								[
                [ 'BillingRate_BillingRatePerItem', 'BillingRate_ListID' ],
                [ 'BillingRate_BillingRatePerItem', 'BillingRate_FullName' ],
                ],
            'BillPaymentCheckRet' => [
                [ 'BillPaymentCheck', 'ExchangeRate' ],
                [ 'BillPaymentCheck', 'AmountInHomeCurrency' ],
                ],
            'BillPaymentCheckRet AppliedToTxnRet' => 								[
                [ 'BillPaymentCheck_AppliedToTxn', 'FromTxnID' ],
                [ 'BillPaymentCheck_AppliedToTxn', 'BillPaymentCheck_TxnID' ],
                ],
            'BillPaymentCreditCardRet AppliedToTxnRet' => 							[
                [ 'BillPaymentCreditCard_AppliedToTxn', 'FromTxnID' ],
                [ 'BillPaymentCreditCard_AppliedToTxn', 'BillPaymentCreditCard_TxnID' ],
                ],
            'BillRet' => 															[
                [ 'Bill', 'Tax1Total' ],
                [ 'Bill', 'Tax2Total' ],
                //array( 'Bill', 'ExchangeRate' ),
                ],
            'BillRet LinkedTxn' => 													[
                [ 'Bill_LinkedTxn', 'FromTxnID' ],
                [ 'Bill_LinkedTxn', 'Bill_TxnID' ],
                [ 'Bill_LinkedTxn', 'LinkType' ],
                ],
            'BillRet ExpenseLineRet' => 											[
                [ 'Bill_ExpenseLine', 'Bill_TxnID' ],
                [ 'Bill_ExpenseLine', 'SortOrder' ],
                ],
            'BillRet ItemLineRet' => 												[
                [ 'Bill_ItemLine', 'Bill_TxnID' ],
                [ 'Bill_ItemLine', 'SortOrder' ],
                ],
            'BillRet ItemGroupLineRet' => 											[
                [ 'Bill_ItemGroupLine', 'Bill_TxnID' ],
                [ 'Bill_ItemGroupLine', 'TxnLineID' ],
                [ 'Bill_ItemGroupLine', 'SortOrder' ],
                ],
            'BillRet ItemGroupLineRet ItemLineRet' => 								[
                [ 'Bill_ItemGroupLine_ItemLine', 'Bill_ItemGroupLine_TxnLineID' ],
                [ 'Bill_ItemGroupLine_ItemLine', 'Bill_TxnID' ],
                [ 'Bill_ItemGroupLine_ItemLine', 'SortOrder' ],
                ],
            'ChargeRet' => 															[
                [ 'Charge', 'IsPaid' ],
                ],
            'CheckRet ExpenseLineRet' => 											[
                [ 'Check_ExpenseLine', 'Check_TxnID' ],
                [ 'Check_ExpenseLine', 'SortOrder' ],
                ],
            'CheckRet ItemGroupLineRet' => 											[
                [ 'Check_ItemGroupLine', 'Check_TxnID' ],
                [ 'Check_ItemGroupLine', 'SortOrder' ],
                ],
            'CheckRet ItemGroupLineRet ItemLineRet' => 								[
                [ 'Check_ItemGroupLine_ItemLine', 'Check_TxnID' ],
                [ 'Check_ItemGroupLine_ItemLine', 'Check_ItemGroupLine_TxnLineID' ],
                [ 'Check_ItemGroupLine_ItemLine', 'SortOrder' ],
                ],
            'CheckRet ItemLineRet' => 												[
                [ 'Check_ItemLine', 'Check_TxnID' ],
                [ 'Check_ItemLine', 'SortOrder' ],
                ],
            'CheckRet LinkedTxn' => 												[
                [ 'Check_LinkedTxn', 'FromTxnID' ],
                [ 'Check_LinkedTxn', 'Check_TxnID' ],
                [ 'Check_LinkedTxn', 'LinkType' ],
                ],
            'CompanyRet SubscribedServices Service' => 								[
                [ 'Company_SubscribedServices_Service', 'Company_CompanyName' ],
                ],
            'CreditCardChargeRet ExpenseLineRet' => 								[
                [ 'CreditCardCharge_ExpenseLine', 'CreditCardCharge_TxnID' ],
                [ 'CreditCardCharge_ExpenseLine', 'SortOrder' ],
                ],
            'CreditCardChargeRet ItemLineRet' => 									[
                [ 'CreditCardCharge_ItemLine', 'CreditCardCharge_TxnID' ],
                [ 'CreditCardCharge_ItemLine', 'SortOrder' ],
                ],
            'CreditCardChargeRet ItemGroupLineRet' => 								[
                [ 'CreditCardCharge_ItemGroupLine', 'CreditCardCharge_TxnID' ],
                [ 'CreditCardCharge_ItemGroupLine', 'SortOrder' ],
                ],
            'CreditCardChargeRet ItemGroupLineRet ItemLineRet' => 					[
                [ 'CreditCardCharge_ItemGroupLine_ItemLine', 'CreditCardCharge_TxnID' ],
                [ 'CreditCardCharge_ItemGroupLine_ItemLine', 'CreditCardCharge_ItemGroupLine_TxnLineID' ],
                [ 'CreditCardCharge_ItemGroupLine_ItemLine', 'SortOrder' ],
                ],
            'CreditCardCreditRet ExpenseLineRet' => 								[
                [ 'CreditCardCredit_ExpenseLine', 'CreditCardCredit_TxnID' ],
                [ 'CreditCardCredit_ExpenseLine', 'SortOrder' ],
                ],
            'CreditCardCreditRet ItemLineRet' => 									[
                [ 'CreditCardCredit_ItemLine', 'CreditCardCredit_TxnID' ],
                [ 'CreditCardCredit_ItemLine', 'SortOrder' ],
                ],
            'CreditCardCreditRet ItemGroupLineRet' => 								[
                [ 'CreditCardCredit_ItemGroupLine', 'CreditCardCredit_TxnID' ],
                [ 'CreditCardCredit_ItemGroupLine', 'SortOrder' ],
                ],
            'CreditCardCreditRet ItemGroupLineRet ItemLineRet' => 					[
                [ 'CreditCardCredit_ItemGroupLine_ItemLine', 'CreditCardCredit_TxnID' ],
                [ 'CreditCardCredit_ItemGroupLine_ItemLine', 'CreditCardCredit_ItemGroupLine_TxnLineID' ],
                [ 'CreditCardCredit_ItemGroupLine_ItemLine', 'SortOrder' ],
                ],
            'CreditMemoRet CreditMemoLineRet' => 									[
                [ 'CreditMemo_CreditMemoLine', 'CreditMemo_TxnID' ],
                [ 'CreditMemo_CreditMemoLine', 'SortOrder' ],
                ],
            'CreditMemoRet CreditMemoLineGroupRet' => 								[
                [ 'CreditMemo_CreditMemoLineGroup', 'CreditMemo_TxnID' ],
                [ 'CreditMemo_CreditMemoLineGroup', 'SortOrder' ],
                ],
            'CreditMemoRet CreditMemoLineGroupRet CreditMemoLineRet' => 			[
                [ 'CreditMemo_CreditMemoLineGroup_CreditMemoLine', 'CreditMemo_TxnID' ],
                [ 'CreditMemo_CreditMemoLineGroup_CreditMemoLine', 'CreditMemo_CreditMemoLineGroup_TxnLineID' ],
                [ 'CreditMemo_CreditMemoLineGroup_CreditMemoLine', 'SortOrder' ],
                ],
            'CreditMemoRet LinkedTxn' => 											[
                [ 'CreditMemo_LinkedTxn', 'FromTxnID' ],
                [ 'CreditMemo_LinkedTxn', 'CreditMemo_TxnID' ],
                [ 'CreditMemo_LinkedTxn', 'LinkType' ],
                ],
            'DataExtDefRet AssignToObject' => 										[
                [ 'DataExtDef_AssignToObject', 'DataExtDef_OwnerID' ],
                [ 'DataExtDef_AssignToObject', 'DataExtDef_DataExtName' ],
                ],
            'DepositRet DepositLineRet' => 											[
                [ 'Deposit_DepositLine', 'Deposit_TxnID' ],
                [ 'Deposit_DepositLine', 'SortOrder' ],
                ],
            'EmployeeRet EmployeePayrollInfo Earnings' => 							[
                [ 'Employee_Earnings', 'Employee_ListID' ],
                ],
            'EstimateRet EstimateLineRet' => 										[
                [ 'Estimate_EstimateLine', 'Estimate_TxnID' ],
                [ 'Estimate_EstimateLine', 'SortOrder' ],
                ],
            'EstimateRet EstimateLineGroupRet' => 									[
                [ 'Estimate_EstimateLineGroup', 'Estimate_TxnID' ],
                [ 'Estimate_EstimateLineGroup', 'SortOrder' ],
                ],
            'EstimateRet EstimateLineGroupRet EstimateLineRet' => 					[
                [ 'Estimate_EstimateLineGroup_EstimateLine', 'Estimate_TxnID' ],
                [ 'Estimate_EstimateLineGroup_EstimateLine', 'Estimate_EstimateLineGroup_TxnLineID' ],
                [ 'Estimate_EstimateLineGroup_EstimateLine', 'SortOrder' ],
                ],
            'EstimateRet LinkedTxn' => 												[
                [ 'Estimate_LinkedTxn', 'FromTxnID' ],
                [ 'Estimate_LinkedTxn', 'Estimate_TxnID' ],
                [ 'Estimate_LinkedTxn', 'LinkType' ],
                ],
            'InventoryAdjustmentRet InventoryAdjustmentLineRet' => 					[
                [ 'InventoryAdjustment_InventoryAdjustmentLine', 'InventoryAdjustment_TxnID' ],
                [ 'InventoryAdjustment_InventoryAdjustmentLine', 'SortOrder' ],

                /*
                array( 'InventoryAdjustment_InventoryAdjustmentLine', 'QuantityAdjustment_NewQuantity' ),
                array( 'InventoryAdjustment_InventoryAdjustmentLine', 'QuantityAdjustment_QuantityDifference' ),

                array( 'InventoryAdjustment_InventoryAdjustmentLine', 'ValueAdjustment_NewQuantity' ),
                array( 'InventoryAdjustment_InventoryAdjustmentLine', 'ValueAdjustment_QuantityDifference' ),
                array( 'InventoryAdjustment_InventoryAdjustmentLine', 'ValueAdjustment_NewValue' ),
                array( 'InventoryAdjustment_InventoryAdjustmentLine', 'ValueAdjustment_ValueDifference' ),
                */
                ],
            'InvoiceRet InvoiceLineRet' => 											[
                [ 'Invoice_InvoiceLine', 'Invoice_TxnID' ],
                [ 'Invoice_InvoiceLine', 'SortOrder' ],
                ],
            'InvoiceRet InvoiceLineGroupRet' => 									[
                [ 'Invoice_InvoiceLineGroup', 'Invoice_TxnID' ],
                [ 'Invoice_InvoiceLineGroup', 'SortOrder' ],
                ],
            'InvoiceRet InvoiceLineGroupRet InvoiceLineRet' => 						[
                [ 'Invoice_InvoiceLineGroup_InvoiceLine', 'Invoice_TxnID' ],
                [ 'Invoice_InvoiceLineGroup_InvoiceLine', 'Invoice_InvoiceLineGroup_TxnLineID' ],
                [ 'Invoice_InvoiceLineGroup_InvoiceLine', 'SortOrder' ],
                ],
            'InvoiceRet LinkedTxn' => 												[
                [ 'Invoice_LinkedTxn', 'FromTxnID' ],
                [ 'Invoice_LinkedTxn', 'Invoice_TxnID' ],
                [ 'Invoice_LinkedTxn', 'LinkType' ],
                ],
            'ItemGroupRet ItemGroupLine' => 										[
                [ 'ItemGroup_ItemGroupLine', 'ItemGroup_ListID' ],
                [ 'ItemGroup_ItemGroupLine', 'SortOrder' ],
                ],
            'ItemInventoryAssemblyRet ItemInventoryAssemblyLine' => 				[
                [ 'ItemInventoryAssembly_ItemInventoryAssemblyLine', 'ItemInventoryAssembly_ListID' ],
                [ 'ItemInventoryAssembly_ItemInventoryAssemblyLine', 'SortOrder' ],
                ],
            'ItemReceiptRet ExpenseLineRet' => 										[
                [ 'ItemReceipt_ExpenseLine', 'ItemReceipt_TxnID' ],
                [ 'ItemReceipt_ExpenseLine', 'SortOrder' ],
                ],
            'ItemReceiptRet ItemLineRet' => 										[
                [ 'ItemReceipt_ItemLine', 'ItemReceipt_TxnID' ],
                [ 'ItemReceipt_ItemLine', 'SortOrder' ],
                ],
            'ItemReceiptRet ItemGroupLineRet' => 									[
                [ 'ItemReceipt_ItemGroupLine', 'ItemReceipt_TxnID' ],
                [ 'ItemReceipt_ItemGroupLine', 'SortOrder' ],
                ],
            'ItemReceiptRet ItemGroupLineRet ItemLineRet' => 						[
                [ 'ItemReceipt_ItemGroupLine_ItemLine', 'ItemReceipt_TxnID' ],
                [ 'ItemReceipt_ItemGroupLine_ItemLine', 'ItemReceipt_ItemGroupLine_TxnLineID' ],
                [ 'ItemReceipt_ItemGroupLine_ItemLine', 'SortOrder' ],
                ],
            'ItemReceiptRet LinkedTxn' => 											[
                [ 'ItemReceipt_LinkedTxn', 'FromTxnID' ],
                [ 'ItemReceipt_LinkedTxn', 'ItemReceipt_TxnID' ],
                [ 'ItemReceipt_LinkedTxn', 'LinkType' ],
                ],
            'ItemSalesTaxGroupRet ItemSalesTaxRef' => 								[
                [ 'ItemSalesTaxGroup_ItemSalesTax', 'ItemSalesTaxGroup_ListID' ],
                ],
            'JournalEntryRet JournalDebitLine' => 									[
                [ 'JournalEntry_JournalDebitLine', 'JournalEntry_TxnID' ],
                [ 'JournalEntry_JournalDebitLine', 'SortOrder' ],
                ],
            'JournalEntryRet JournalCreditLine' => 									[
                [ 'JournalEntry_JournalCreditLine', 'JournalEntry_TxnID' ],
                [ 'JournalEntry_JournalCreditLine', 'SortOrder' ],
                ],
            'PriceLevelRet PriceLevelPerItemRet' => 								[
                [ 'PriceLevel_PriceLevelPerItem', 'PriceLevel_ListID' ],
                ],
            'PurchaseOrderRet PurchaseOrderLineRet' => 								[
                [ 'PurchaseOrder_PurchaseOrderLine', 'PurchaseOrder_TxnID' ],
                [ 'PurchaseOrder_PurchaseOrderLine', 'SortOrder' ],
                ],
            'PurchaseOrderRet PurchaseOrderLineGroupRet' => 						[
                [ 'PurchaseOrder_PurchaseOrderLineGroup', 'PurchaseOrder_TxnID' ],
                [ 'PurchaseOrder_PurchaseOrderLineGroup', 'SortOrder' ],
                ],
            'PurchaseOrderRet PurchaseOrderLineGroupRet PurchaseOrderLineRet' => 	[
                [ 'PurchaseOrder_PurchaseOrderLineGroup_PurchaseOrderLine', 'PurchaseOrder_TxnID' ],
                [ 'PurchaseOrder_PurchaseOrderLineGroup_PurchaseOrderLine', 'PurchaseOrder_PurchaseOrderLineGroup_TxnLineID' ],
                [ 'PurchaseOrder_PurchaseOrderLineGroup_PurchaseOrderLine', 'SortOrder' ],
                ],
            'PurchaseOrderRet LinkedTxn' => 										[
                [ 'PurchaseOrder_LinkedTxn', 'FromTxnID' ],
                [ 'PurchaseOrder_LinkedTxn', 'PurchaseOrder_TxnID' ],
                [ 'PurchaseOrder_LinkedTxn', 'LinkType' ],
                ],
            'ReceivePaymentRet AppliedToTxnRet' => 									[
                [ 'ReceivePayment_AppliedToTxn', 'FromTxnID' ],
                [ 'ReceivePayment_AppliedToTxn', 'ReceivePayment_TxnID' ],
                ],
            'SalesOrderRet SalesOrderLineRet' => 									[
                [ 'SalesOrder_SalesOrderLine', 'SalesOrder_TxnID' ],
                [ 'SalesOrder_SalesOrderLine', 'SortOrder' ],
                ],
            'SalesOrderRet SalesOrderLineGroupRet' => 								[
                [ 'SalesOrder_SalesOrderLineGroup', 'SalesOrder_TxnID' ],
                [ 'SalesOrder_SalesOrderLineGroup', 'SortOrder' ],
                ],
            'SalesOrderRet SalesOrderLineGroupRet SalesOrderLineRet' => 			[
                [ 'SalesOrder_SalesOrderLineGroup_SalesOrderLine', 'SalesOrder_TxnID' ],
                [ 'SalesOrder_SalesOrderLineGroup_SalesOrderLine', 'SalesOrder_SalesOrderLineGroup_TxnLineID' ],
                [ 'SalesOrder_SalesOrderLineGroup_SalesOrderLine', 'SortOrder' ],
                ],
            'SalesOrderRet LinkedTxn' => 											[
                [ 'SalesOrder_LinkedTxn', 'FromTxnID' ],
                [ 'SalesOrder_LinkedTxn', 'SalesOrder_TxnID' ],
                [ 'SalesOrder_LinkedTxn', 'LinkType' ],
                ],
            'SalesReceiptRet SalesReceiptLineRet' => 								[
                [ 'SalesReceipt_SalesReceiptLine', 'SalesReceipt_TxnID' ],
                [ 'SalesReceipt_SalesReceiptLine', 'SortOrder' ],
                ],
            'SalesReceiptRet SalesReceiptLineGroupRet' => 							[
                [ 'SalesReceipt_SalesReceiptLineGroup', 'SalesReceipt_TxnID' ],
                [ 'SalesReceipt_SalesReceiptLineGroup', 'SortOrder' ],
                ],
            'SalesReceiptRet SalesReceiptLineGroupRet SalesReceiptLineRet' => 		[
                [ 'SalesReceipt_SalesReceiptLineGroup_SalesReceiptLine', 'SalesReceipt_TxnID' ],
                [ 'SalesReceipt_SalesReceiptLineGroup_SalesReceiptLine', 'SalesReceipt_SalesReceiptLineGroup_TxnLineID' ],
                [ 'SalesReceipt_SalesReceiptLineGroup_SalesReceiptLine', 'SortOrder' ],
                ],
            'UnitOfMeasureSetRet RelatedUnit' => 									[
                [ 'UnitOfMeasureSet_RelatedUnit', 'UnitOfMeasureSet_ListID' ],
                ],
            'UnitOfMeasureSetRet DefaultUnit' => 									[
                [ 'UnitOfMeasureSet_DefaultUnit', 'UnitOfMeasureSet_ListID' ],
                ],
            'VendorCreditRet ExpenseLineRet' => 									[
                [ 'VendorCredit_ExpenseLine', 'VendorCredit_TxnID' ],
                [ 'VendorCredit_ExpenseLine', 'SortOrder' ],
                ],
            'VendorCreditRet ItemLineRet' => 										[
                [ 'VendorCredit_ItemLine', 'VendorCredit_TxnID' ],
                [ 'VendorCredit_ItemLine', 'SortOrder' ],
                ],
            'VendorCreditRet ItemGroupLineRet' => 									[
                [ 'VendorCredit_ItemGroupLine', 'VendorCredit_TxnID' ],
                [ 'VendorCredit_ItemGroupLine', 'SortOrder' ],
                ],
            'VendorCreditRet ItemGroupLineRet ItemLineRet' => 						[
                [ 'VendorCredit_ItemGroupLine_ItemLine', 'VendorCredit_TxnID' ],
                [ 'VendorCredit_ItemGroupLine_ItemLine', 'VendorCredit_ItemGroupLine_TxnLineID' ],
                [ 'VendorCredit_ItemGroupLine_ItemLine', 'SortOrder' ],
                ],
            'VendorCreditRet LinkedTxn' => 											[
                [ 'VendorCredit_LinkedTxn', 'FromTxnID' ],
                [ 'VendorCredit_LinkedTxn', 'VendorCredit_TxnID' ],
                [ 'VendorCredit_LinkedTxn', 'LinkType' ],
                ],
            'WorkersCompCodeRet RateHistory' => 									[
                [ 'WorkersCompCode_RateHistory', 'WorkersCompCode_ListID' ],
                ],
            ];

        if ($mode == QUICKBOOKS_SQL_SCHEMA_MAP_TO_SQL) {		// map the QuickBooks XML tags to SQL schema
            $path = trim($path_or_tablefield);
            $spaces = substr_count($path, ' ');
            $map = [ null, null ];		// default map

            // @todo Can we break out of this big loop early to improve performance?

            foreach ($xml_to_sql as $pattern => $table_and_field) {
                if (substr_count($pattern, ' ') == $spaces and 		// check path depth
                    false !== strpos($pattern, '*')) {
                    if (QuickBooks_SQL_Schema::_fnmatch($pattern, $path)) { 	// check it to see if this pattern matches
                        foreach (explode(' ', $pattern) as $kpart => $vpart) {
                            if ($vpart == '*') {
                                $xml = explode(' ', $path);
                                $match = $xml[$kpart];

                                /*
                                if ($options['uppercase_tables'])
                                {
                                    $table_and_field[0] = strtoupper($table_and_field[0]);
                                }
                                else if ($options['lowercase_tables'])
                                {
                                    $table_and_field[0] = strtolower($table_and_field[0]);
                                }

                                if ($options['uppercase_fields'])
                                {
                                    $table_and_field[1] = strtoupper($table_and_field[1]);
                                }
                                else if ($options['lowercase_fields'])
                                {
                                    $table_and_field[1] = strtolower($table_and_field[1]);
                                }
                                */

                                $map = [
                                    $table_and_field[0],
                                    str_replace('*', $match, $table_and_field[1]),
                                    ];

                                QuickBooks_SQL_Schema::_applyOptions($map, QUICKBOOKS_SQL_SCHEMA_MAP_TO_SQL, $options);

                                break;
                            }
                        }
                    }
                } elseif ($pattern == $path) {
                    $map = $table_and_field;
                    QuickBooks_SQL_Schema::_applyOptions($map, QUICKBOOKS_SQL_SCHEMA_MAP_TO_SQL, $options);

                    if (isset($xml_to_sql_others[$pattern])) {
                        $others = $xml_to_sql_others[$pattern];
                        foreach ($others as $key => $other) {
                            QuickBooks_SQL_Schema::_applyOptions($other, QUICKBOOKS_SQL_SCHEMA_MAP_TO_SQL, $options);
                            $others[$key] = $other;
                        }
                    }

                    break;
                }
            }

            //print_r($map);
            //print_r($others);
        } else {		// mode = QUICKBOOKS_SQL_SCHEMA_MAP_TO_XML		map the SQL schema back to QuickBooks qbXML tags
            $tablefield = trim($path_or_tablefield);
            $tablefield_compare = strtolower($tablefield);

            $underscores = substr_count($tablefield, '_');
            $map = '';

            foreach ($sql_to_xml as $pattern => $path) {
                $pattern_compare = strtolower($pattern);
                if ($pattern_compare == $tablefield_compare) {
                    $map = $path;
                    break;
                } elseif (substr_count($pattern, '_') == $underscores and
                    false !== strpos($pattern, '*')) {
                    if (QuickBooks_SQL_Schema::_fnmatch($pattern_compare, $tablefield_compare)) {
                        $tmp_pattern = explode('.', $pattern);
                        if (count($tmp_pattern) == 2 and
                            $tmp_pattern[1] == '*') {
                            // table.* pattern
                            $tmp_tablefield = explode('.', $tablefield);

                            $map = str_replace('*', $tmp_tablefield[1], $path);
                            break;
                        } else {
                            //print('matched ' . $tablefield . ' to ' . $path . ' (' . $pattern . ') ' . "\n");

                            $pos = strpos($pattern, '*');
                            $field = substr($tablefield, $pos);

                            $map = str_replace('*', $field, $path);
                            break;
                        }
                    }
                }
            }
        }
    }

    protected static function _applyOptions(&$path_or_arrtablefield, $mode, $options)
    {
        $applied = 0;

        $defaults = [
            'desc_to_descrip' => 			true,
            'uppercase_tables' => 			false,
            'lowercase_tables' => 			true,
            'uppercase_fields' => 			false,
            'lowercase_fields' => 			false,
            'prepend_parent' => 			true,
            ];

        $options = array_merge($defaults, $options);

        if ($mode == QUICKBOOKS_SQL_SCHEMA_MAP_TO_SQL) {

            if ($options['uppercase_tables']) {
                $path_or_arrtablefield[0] = strtoupper($path_or_arrtablefield[0]);
                $applied++;
            } elseif ($options['lowercase_tables']) {
                $path_or_arrtablefield[0] = strtolower($path_or_arrtablefield[0]);
                $applied++;
            }

            if ($options['uppercase_fields']) {
                $path_or_arrtablefield[1] = strtoupper($path_or_arrtablefield[1]);
                $applied++;
            } elseif ($options['lowercase_fields']) {
                $path_or_arrtablefield[1] = strtolower($path_or_arrtablefield[1]);
                $applied++;
            }

            return $applied;
        } else {

        }
    }

    /**
     * Map a qbXML XML field type to it's SQL type definition
     *
     * @param string $object_type
     * @param string $field
     * @param string $qb_type
     * @return array
     * @TODO We case the input to lowercase, and so the array has to be in lowercase. Is there a better way to do this?
     */
    public static function mapFieldToSQLDefinition($object_type, $field, $qb_type)
    {
        // array( type, length, default )

        static $overrides = [
            'billpaymentcheck' => [
                'istobeprinted' => [ null, null, 'null' ],
                ],
            'check' => [
                'istobeprinted' => [ null, null, 'null' ],
                ],
            'creditmemo' => [
                'ispending' => [ null, null, 'null' ],
                ],
            'creditmemo_creditmemoline' => [
                'creditcardtxninputinfo_expirationmonth' => [ null, null, 'null' ],
                'creditcardtxninputinfo_expirationyear' => [ null, null, 'null' ],
                'creditcardtxnresultinfo_resultcode' => [ null, null, 'null' ],
                'creditcardtxnresultinfo_paymentgroupingcode' => [ null, null, 'null' ],
                'creditcardtxnresultinfo_txnauthorizationstamp' => [ null, null, 'null' ],
                ],
            'creditmemo_creditmemolinegroup_creditmemoline' => [
                'creditcardtxninfo_creditcardtxninputinfo_expirationmonth' => [ null, null, 'null' ],
                'creditcardtxninfo_creditcardtxninputinfo_expirationyear' => [ null, null, 'null' ],
                'creditcardtxninfo_creditcardtxnresultinfo_resultcode' => [ null, null, 'null' ],
                'creditcardtxninfo_creditcardtxnresultinfo_paymentgroupingcode' => [ null, null, 'null' ],
                'creditcardtxninfo_creditcardtxnresultinfo_txnauthorizationstamp' => [ null, null, 'null' ],
                ],
            'customer' => [
                'creditcardinfo_expirationmonth' => [ null, null, 'null' ],
                'creditcardinfo_expirationyear' => [ null, null, 'null' ]
                ],
            'employee' => [
                'employeepayrollinfo_clearearnings' => [ null, null, 'null' ],
                'employeepayrollinfo_isusingtimedatatocreatepaychecks' => [ null, null, 'null' ],
                'employeepayrollinfo_sickhours_isresettinghourseachnewyear' => [ null, null, 'null' ],
                'employeepayrollinfo_vacationhours_isresettinghourseachnewyear' => [ null, null, 'null' ],
                ],
            'estimate' => [
                'istobeemailed' => [ null, null, 'null' ],
                ],
            'estimate_estimateline' => [
                'quantity' => [ null, null, 'null' ],
                ],
            'itemnoninventory' => [
                'salesorpurchase_price' => [ null, null, 'null' ],
                'salesorpurchase_pricepercent' => [ null, null, 'null' ],
                'salesorpurchase_salesprice' => [ null, null, 'null' ],
                'salesorpurchase_purchasecost' => [ null, null, 'null' ]
                ],
            'itemdiscount' => [
                'discountrate' => [ null, null, 'null' ],
                'discountratepercent' => [ null, null, 'null' ]
                ],
            'inventoryadjustment_inventoryadjustmentline' => [
                'quantityadjustment_newquantity' => [ null, null, 'null' ],
                'quantityadjustment_quantitydifference' => [ null, null, 'null' ],
                'valueadjustment_newquantity' => [ null, null, 'null' ],
                'valueadjustment_quantitydifference' => [ null, null, 'null' ],
                'valueadjustment_newvalue' => [ null, null, 'null' ],
                'valueadjustment_valuedifference' => [ null, null, 'null' ],
                ],
            'invoice' => [
                'ispending' => [ null, null, 'null' ],
                'isfinancecharge' => [ null, null, 'null' ],
                'ispaid' => [ null, null, 'null' ],
                'istobeprinted' => [ null, null, 'null' ],
                'istobeemailed' => [ null, null, 'null' ],
                ],
            'invoice_invoiceline' => [
                'quantity' => [ null, null, 'null' ]
                ],
            'purchaseorder' => [
                'ismanuallyclosed' => [ null, null, 'null' ],
                'isfullyreceived' => [ null, null, 'null' ],
                'istobeprinted' => [ null, null, 'null' ],
                'istobeemailed' => [ null, null, 'null' ],
                ],
            'purchaseorder_purchaseorderline' => [
                'ismanuallyclosed' => [ null, null, 'null' ],
                'receivedquantity' => [ null, null, 'null' ],
                'quantity' => [ null, null, 'null' ]
                ],
            'receivepayment' => [
                'creditcardtxninfo_creditcardtxninputinfo_expirationmonth' => [ null, null, 'null' ],
                'creditcardtxninfo_creditcardtxninputinfo_expirationyear' => [ null, null, 'null' ],
                'creditcardtxninfo_creditcardtxnresultinfo_resultcode' => [ null, null, 'null' ],
                'creditcardtxninfo_creditcardtxnresultinfo_paymentgroupingcode' => [ null, null, 'null' ],
                'creditcardtxninfo_creditcardtxnresultinfo_txnauthorizationstamp' => [ null, null, 'null' ],
                ],
            'salesorder' => [
                'ismanuallyclosed' => [ null, null, 'null' ],
                'isfullyinvoiced' => [ null, null, 'null' ],
                'istobeprinted' => [ null, null, 'null' ],
                'istobeemailed' => [ null, null, 'null' ],
                ],
            'salesorder_salesorderline' => [
                'quantity' => [ null, null, 'null' ],
                'invoiced' => [ null, null, 'null' ],
                'ismanuallyclosed' => [ null, null, 'null' ],
                ],
            'salesreceipt' => [
                'ispending' => [ null, null, 'null' ],
                'istobeprinted' => [ null, null, 'null' ],
                'istobeemailed' => [ null, null, 'null' ],
                'creditcardtxninfo_creditcardtxninputinfo_expirationmonth' => [ null, null, 'null' ],
                'creditcardtxninfo_creditcardtxninputinfo_expirationyear' => [ null, null, 'null' ],
                'creditcardtxninfo_creditcardtxnresultinfo_resultcode' => [ null, null, 'null' ],
                'creditcardtxninfo_creditcardtxnresultinfo_paymentgroupingcode' => [ null, null, 'null' ],
                'creditcardtxninfo_creditcardtxnresultinfo_txnauthorizationstamp' => [ null, null, 'null' ],
                ],
            'salesreceipt_salesreceiptline' => [
                'quantity' => [ null, null, 'null' ],
                'creditcardtxninfo_creditcardtxninputinfo_expirationmonth' => [ null, null, 'null' ],
                'creditcardtxninfo_creditcardtxninputinfo_expirationyear' => [ null, null, 'null' ],
                'creditcardtxninfo_creditcardtxnresultinfo_resultcode' => [ null, null, 'null' ],
                'creditcardtxninfo_creditcardtxnresultinfo_paymentgroupingcode' => [ null, null, 'null' ],
                'creditcardtxninfo_creditcardtxnresultinfo_txnauthorizationstamp' => [ null, null, 'null' ],
                ],
            ];

        $object_type = strtolower($object_type);
        $field = strtolower($field);

        $type = QUICKBOOKS_DRIVER_SQL_VARCHAR;
        $length = 32;
        $default = null;

        // Default mappings for types
        switch ($qb_type) {
            case 'AMTTYPE':

                $type = QUICKBOOKS_DRIVER_SQL_DECIMAL;
                $length = '10,2';
                $default = 'null';

                break;
            case 'PRICETYPE':

                $type = QUICKBOOKS_DRIVER_SQL_DECIMAL;
                $length = '13,5';
                $default = 'null';

                break;
            case 'PERCENTTYPE':

                $type = QUICKBOOKS_DRIVER_SQL_DECIMAL;
                $length = '12,5';
                $default = 'null';

                break;
            case 'DATETYPE':

                $type = QUICKBOOKS_DRIVER_SQL_DATE;
                $length = null;
                $default = 'null';

                break;
            case 'DATETIMETYPE':

                $type = QUICKBOOKS_DRIVER_SQL_DATETIME;
                $length = null;
                $default = 'null';

                break;
            case 'BOOLTYPE':

                $type = QUICKBOOKS_DRIVER_SQL_BOOLEAN;
                $length = null;
                $default = false;

                break;
            case 'INTTYPE':

                $type = QUICKBOOKS_DRIVER_SQL_INTEGER;
                $length = null;
                $default = 0;

                break;
            case 'QUANTYPE':

                $type = QUICKBOOKS_DRIVER_SQL_DECIMAL;
                $length = '12,5';
                $default = 0;

                break;
            case 'IDTYPE':

                $type = QUICKBOOKS_DRIVER_SQL_VARCHAR;
                $length = 40;
                $default = 'null';

                break;
            case 'ENUMTYPE':

                $type = QUICKBOOKS_DRIVER_SQL_VARCHAR;
                $length = 40;
                $default = 'null';

                break;
            case 'STRTYPE':
            default:

                //print('casting: ' . $object_type . "\n");
                //print('field: ' . $field . "\n");

                $x = str_repeat('x', 10000);
                $length = strlen(QuickBooks_Cast::cast($object_type, $field, $x));

                // All FullName and *_FullName fields should be VARCHAR(255) so we can add INDEXes to them
                if ($length > 255 and
                    strtolower(substr($field, -8)) == 'fullname') {
                    $length = 255;
                }

                // If the length is really long, put it in a TEXT field instead of a VARCHAR
                if ($length > 255) {
                    $type = QUICKBOOKS_DRIVER_SQL_TEXT;
                } else {
                    $type = QUICKBOOKS_DRIVER_SQL_VARCHAR;
                }

                $default = 'null';

                if ($field == 'EditSequence') {
                    $length = 16;
                } elseif (isset($overrides[$object_type][$field])) {
                    //

                    if (!is_null($overrides[$object_type][$field][2])) {
                        $default = $overrides[$object_type][$field][2];
                    }
                }

                break;
        }

        // Overrides for mappings that couldn't be done automatically
        /*switch ($object_type)
        {
            case 'invoice':
                switch ($field)
                {
                    default:
                        break;
                }
            default:

                switch ($field)
                {
                    case 'isactive':
                        $default = true;
                        break;
                    default:

                        break;
                }

                break;
        }*/

        // @TODO -- Keith, is this a good way to accomplish converting all txnid/listid fields to varchar? ~Garrett
        if (stripos($field, 'listid') !== false or stripos($field, 'txnid') !== false) {
            $type = QUICKBOOKS_DRIVER_SQL_VARCHAR;
            $length = 40;
            $default = 'null';
        } elseif (strtolower($field) == 'sortorder') {
            $type = QUICKBOOKS_DRIVER_SQL_INTEGER;
            $length = null;
            $default = 0;
        }

        if (isset($overrides[$object_type][$field])) {
            if (!is_null($overrides[$object_type][$field][0])) {
                $type = $overrides[$object_type][$field][0];
            }

            if (!is_null($overrides[$object_type][$field][1])) {
                $length = $overrides[$object_type][$field][1];
            }

            if (!is_null($overrides[$object_type][$field][2])) {
                $default = $overrides[$object_type][$field][2];
            }
        }

        return [ $type, $length, $default ];
    }
}
