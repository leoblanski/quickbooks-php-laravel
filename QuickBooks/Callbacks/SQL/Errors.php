<?php

/**
 *
 *
 * Copyright (c) 2010 Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage SQL
 */

// For DEBUGGING only
//require_once '/Users/kpalmer/Projects/QuickBooks/QuickBooks.php';

/**
 *
 */
class QuickBooks_Callbacks_SQL_Errors
{
    /**
     * @TODO Change this to return false by default, and only catch the specific errors we're concerned with.
     *
     */
    public static function catchall($requestID, $user, $action, $ident, $extra, &$err, $xml, $errnum, $errmsg, $config)
    {
        $quickBooksDriver = QuickBooks_Driver_Singleton::getInstance();

        $ignore = [
            QUICKBOOKS_IMPORT_DELETEDTXNS => true,
            QUICKBOOKS_QUERY_DELETEDTXNS => true,
            QUICKBOOKS_IMPORT_DELETEDLISTS => true,
            QUICKBOOKS_QUERY_DELETEDLISTS => true,
            QUICKBOOKS_VOID_TRANSACTION => true,
            QUICKBOOKS_DELETE_TRANSACTION => true,
            QUICKBOOKS_DELETE_LIST => true,
            ];

        if (isset($ignore[$action])) {
            // Ignore errors for these requests
            return true;
        }

        /*
        $Parser = new QuickBooks_XML($xml);
        $errnumTemp = 0;
        $errmsgTemp = '';
        $Doc = $Parser->parse($errnumTemp, $errmsgTemp);
        $Root = $Doc->getRoot();
        $emailStr = var_export($Root->children(), true);

        $List = $Root->getChildAt('QBXML QBXMLMsgsRs '.QuickBooks_Utilities::actionToResponse($action));
        $Node = current($List->children());
        */

        $map = [];
        $others = [];
        QuickBooks_SQL_Schema::mapToSchema(trim(QuickBooks_Utilities::actionToXMLElement($action)), QUICKBOOKS_SQL_SCHEMA_MAP_TO_SQL, $map, $others);
        $quickBooksSQLObject = new QuickBooks_SQL_Object($map[0], trim(QuickBooks_Utilities::actionToXMLElement($action)));
        $table = $quickBooksSQLObject->table();

        $existing = null;

        if ($table && is_numeric($ident)) {
            $multipart = [
                QUICKBOOKS_DRIVER_SQL_FIELD_ID => $ident
                ];

            $existing = $quickBooksDriver->get(QUICKBOOKS_DRIVER_SQL_PREFIX_SQL . $table, $multipart);
        }

        switch ($errnum) {
            case 1:		// These errors occur when we search for something and it doesn't exist
            case 500:	// 	i.e. we query for invoices modified since xyz, but there are none that have been modified since then

                // This isn't really an error, just ignore it

                if ($action == QUICKBOOKS_DERIVE_CUSTOMER) {
                    // Tried to derive, doesn't exist, add it
                    $quickBooksDriver->queueEnqueue(
                        $user,
                        QUICKBOOKS_ADD_CUSTOMER,
                        $ident,
                        true,
                        QuickBooks_Utilities::priorityForAction(QUICKBOOKS_ADD_CUSTOMER)
                    );
                } elseif ($action == QUICKBOOKS_DERIVE_INVOICE) {
                    // Tried to derive, doesn't exist, add it
                    $quickBooksDriver->queueEnqueue(
                        $user,
                        QUICKBOOKS_ADD_INVOICE,
                        $ident,
                        true,
                        QuickBooks_Utilities::priorityForAction(QUICKBOOKS_ADD_INVOICE)
                    );
                } elseif ($action == QUICKBOOKS_DERIVE_RECEIVEPAYMENT) {
                    // Tried to derive, doesn't exist, add it
                    $quickBooksDriver->queueEnqueue(
                        $user,
                        QUICKBOOKS_ADD_RECEIVEPAYMENT,
                        $ident,
                        true,
                        QuickBooks_Utilities::priorityForAction(QUICKBOOKS_ADD_RECEIVEPAYMENT)
                    );
                }

                return true;
            case 1000:
            case 3250:
            // Insufficient permission level to perform this action.
            case 3261:
            case '0x8004040D':	// The ticket parameter is invalid  (how does this happen!?!)

                return true;
                //case 3120:			// 3120 errors are handled in the 3210 error handler section
                //	break;
            case 3170:	// This list has been modified by another user.
            case 3175:
            case 3176:
            case 3180:

                // This error can occur in several different situations, so we test per situation
                if (false !== strpos($errmsg, 'list has been modified by another user') || false !== strpos($errmsg, 'internals could not be locked') || false !== strpos($errmsg, 'failed to acquire the lock') || false !== strpos($errmsg, 'list element is in use')) {
                    // This is *not* an error, we can just send the request again, and it'll go through just fine
                    return true;
                }

                break;
            case 3200:
                // Ignore EditSequence errors (the record will be picked up and a conflict reported next time it runs... maybe?)

                if ($action == QUICKBOOKS_MOD_CUSTOMER && $existing) {
                    // Queue up a derive customer request
                    // Tried to derive, doesn't exist, add it
                    $quickBooksDriver->queueEnqueue(
                        $user,
                        QUICKBOOKS_DERIVE_CUSTOMER,
                        $ident,
                        true,
                        9999,
                        [ 'ListID' => $existing['ListID'] ]
                    );
                } elseif ($action == QUICKBOOKS_MOD_INVOICE && $existing) {
                    // Queue up a derive customer request
                    // Tried to derive, doesn't exist, add it
                    $quickBooksDriver->queueEnqueue(
                        $user,
                        QUICKBOOKS_DERIVE_INVOICE,
                        $ident,
                        true,
                        9999,
                        [ 'TxnID' => $existing['TxnID'] ]
                    );
                }

                return true;
            case 3120:
            case 3210:

                //print_r($existing);
                //print('TXNID: [' . $existing['TxnID'] . ']');

                // 3210: The &quot;AppliedToTxnAdd payment amount&quot; field has an invalid value &quot;129.43&quot;.  QuickBooks error message: You cannot pay more than the amount due.
                if ($action == QUICKBOOKS_ADD_RECEIVEPAYMENT && (false !== strpos($errmsg, 'pay more than the amount due') || false !== strpos($errmsg, 'cannot be found')) && $existing) {
                    // If this happens, we're going to try to re-submit the payment, *without* the AppliedToTxn element

                    $db_errnum = null;
                    $db_errmsg = null;

                    $quickBooksDriver->query(
                        '
						UPDATE 
							' . QUICKBOOKS_DRIVER_SQL_PREFIX_SQL . "receivepayment_appliedtotxn 
						SET 
							qbsql_to_skip = 1 
						WHERE 
							ReceivePayment_TxnID = '%s' ",
                        $db_errnum,
                        $db_errmsg,
                        null,
                        null,
                        [ $existing['TxnID'] ]
                    );

                    return true;
                }

                break;
            case 3260:
            case 3100: // Name of List Element is already in use.

                break;
        }

        // This is our catch-all which marks the item as errored out
        if (strstr($xml, 'statusSeverity="Info"') === false) { // If it's NOT just an Info message.
            $multipart = [ QUICKBOOKS_DRIVER_SQL_FIELD_ID => $ident ];
            $quickBooksSQLObject->set(QUICKBOOKS_DRIVER_SQL_FIELD_ERROR_NUMBER, $errnum);
            $quickBooksSQLObject->set(QUICKBOOKS_DRIVER_SQL_FIELD_ERROR_MESSAGE, $errmsg);

            // Do not set the resync field, we want resync and modified timestamps to be different
            $update_resync_field = false;
            $update_discov_field = false;
            $update_derive_field = false;

            if ($table && is_numeric($ident)) {		// This catches cases where errors occur on IMPORT requests with ap9y8ag random idents
                // Set the error message
                $quickBooksDriver->update(
                    QUICKBOOKS_DRIVER_SQL_PREFIX_SQL . $table,
                    $quickBooksSQLObject,
                    [ $multipart ],
                    $update_resync_field,
                    $update_discov_field,
                    $update_derive_field
                );
            }
        }

        // Please don't change this, it stops us from knowing what's actually
        //	going wrong. If an error occurs, we should either catch it if it's
        //	recoverable, or treated as a fatal error so we know about it and
        //	can address it later.
        //return false;

        // I'm changing it because otherwise the sync never completes if a
        //	single error occurs... we need a way to skip errored-out records
        return true;
    }
}

/*
$requestID = 'asdf';
$user = 'quickbooks';
$action = QUICKBOOKS_ADD_RECEIVEPAYMENT;
$ident = 1;
$extra = array();
$err = null;

$xml = '<?xml version="1.0" ?>
<QBXML>
<QBXMLMsgsRs>
<ReceivePaymentAddRs
requestID="U2hpcE1ldGhvZEltcG9ydHxmMmMyNzk1OGQ5Y2UwMTZiYzViN2RmYTZlMDJlODM5NA=="
statusCode="3210" statusSeverity="Info" statusMessage="The &quot;AppliedToTxnAdd payment amount&quot; field has an invalid value &quot;129.43&quot;.  QuickBooks error message: You cannot pay more than the amount due." />
</QBXMLMsgsRs>
</QBXML>';

$errnum = 3210;
$errmsg = 'The &quot;AppliedToTxnAdd payment amount&quot; field has an invalid value &quot;129.43&quot;.  QuickBooks error message: You cannot pay more than the amount due.';
$config = array();

$tmp = QuickBooks_Driver_Singleton::getInstance('mysql://root:root@localhost/quickbooks_sql', array(), array(), QUICKBOOKS_LOG_DEVELOP);
QuickBooks_Callbacks_SQL_Errors::catchall($requestID, $user, $action, $ident, $extra, $err, $xml, $errnum, $errmsg, $config);
*/
