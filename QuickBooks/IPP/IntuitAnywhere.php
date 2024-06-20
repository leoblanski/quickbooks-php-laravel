<?php

/**
 * QuickBooks PHP DevKit
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
 */

class QuickBooks_IPP_IntuitAnywhere
{
    protected $_oauth_version;
    
    protected $_oauth_scope;

    protected $_sandbox;

    protected $_this_url;
    
    protected $_that_url;

    protected $_consumer_key;
    
    protected $_consumer_secret;

    protected $_client_id;
    
    protected $_client_secret;

    protected $_errnum;
    
    protected $_errmsg;

    protected $_debug = false;

    protected $_dsn;
    
    protected $_driver;

    protected $_crypt;

    protected $_key;

    protected $_last_request;
    
    protected $_last_response;

    public const URL_REQUEST_TOKEN = 'https://oauth.intuit.com/oauth/v1/get_request_token';
    
    public const URL_ACCESS_TOKEN = 'https://oauth.intuit.com/oauth/v1/get_access_token';
    
    public const URL_CONNECT_BEGIN = 'https://appcenter.intuit.com/Connect/Begin';
    
    public const URL_CONNECT_DISCONNECT = 'https://developer.api.intuit.com/v2/oauth2/tokens/revoke';
    
    public const URL_CONNECT_RECONNECT = 'https://appcenter.intuit.com/api/v1/Connection/Reconnect';
    
    public const URL_APP_MENU = 'https://appcenter.intuit.com/api/v1/Account/AppMenu';

    public const URL_DISCOVERY_SANDBOX = 'https://developer.api.intuit.com/.well-known/openid_sandbox_configuration';
    
    public const URL_DISCOVERY_PRODUCTION = 'https://developer.api.intuit.com/.well-known/openid_configuration';

    public const EXPIRY_EXPIRED = 'expired';
    
    public const EXPIRY_NOTYET = 'notyet';
    
    public const EXPIRY_SOON = 'soon';
    
    public const EXPIRY_UNKNOWN = 'unknown';

    public const OAUTH_V1 = 'oauthv1';
    
    public const OAUTH_V2 = 'oauthv2';

    /**
     *
     *
     * @param string $consumer_key		The OAuth consumer key Intuit gives you
     * @param string $consumer_secret	The OAuth consumer secret Intuit gives you
     * @param string $this_url			The URL of your QuickBooks_IntuitAnywhere class instance
     * @param string $that_url			The URL the user should be sent to after authenticated
     */
    public function __construct($oauth_version, $sandbox, $scope, $dsn, $encryption_key, $consumer_key_or_client_id, $consumer_secret_or_client_secret, $this_url = null, $that_url = null)
    {
        $this->_dsn = $dsn;
        $this->_driver = QuickBooks_Driver_Factory::create($dsn);

        $this->_key = $encryption_key;

        $this->_this_url = $this_url;
        $this->_that_url = $that_url;

        $this->_oauth_version = $oauth_version;
        $this->_oauth_scope = $scope;

        $this->_sandbox = (bool) $sandbox;
        $this->_consumer_key = $consumer_key_or_client_id;
        $this->_client_id = $consumer_key_or_client_id;
        $this->_consumer_secret = $consumer_secret_or_client_secret;
        $this->_client_secret = $consumer_secret_or_client_secret;
    }

    /**
     * Turn on/off debug mode
     *
     * @param boolean $true_or_false
     */
    public function useDebugMode($true_or_false)
    {
        $this->_debug = (bool) $true_or_false;
    }

    /**
     * Get the last error number
     *
     * @return integer
     */
    public function errorNumber()
    {
        return $this->_errnum;
    }

    /**
     * Get the last error message
     *
     * @return string
     */
    public function errorMessage()
    {
        return $this->_errmsg;
    }

    /**
     * Set an error message
     *
     * @param integer $errnum	The error number/code
     * @param string $errmsg	The text error message
     * @return void
     */
    protected function _setError($errnum, $errmsg = '')
    {
        $this->_errnum = $errnum;
        $this->_errmsg = $errmsg;
    }

    public function lastRequest()
    {
        return $this->_last_request;
    }

    public function lastResponse()
    {
        return $this->_last_response;
    }

    /**
     * Returns TRUE if an OAuth token exists for this user, FALSE otherwise
     *
     * @param   string  $app_tenant   The tenant to check to see if they are connected/auth'd
     * @return  bool
     */
    public function check($app_tenant)
    {
        return (bool) ($arr = $this->load($app_tenant));
    }

    /**
     * Test to see if a connection actually works (make sure you haven't been disconnected on Intuit's end)
     *
     * @param string   $app_tenant
     *
     */
    public function test($app_tenant)
    {
        if (($creds = $this->load($app_tenant)) && !empty($creds['oauth_access_token'])) {
            $quickBooksIPP = new QuickBooks_IPP($this->_dsn, $this->_key);

            if ($this->_oauth_version == self::OAUTH_V1) {
                $authmode = QuickBooks_IPP::AUTHMODE_OAUTHV1;
            } elseif ($this->_oauth_version == self::OAUTH_V2) {
                $authmode = QuickBooks_IPP::AUTHMODE_OAUTHV2;
            }

            $quickBooksIPP->authMode(
                $authmode,
                $creds
            );

            if ($this->_sandbox) {
                $quickBooksIPP->sandbox(true);
            }

            if ($Context = $quickBooksIPP->context()) {
                // Set the IPP flavor
                $quickBooksIPP->flavor($creds['qb_flavor']);
                // Get the base URL if it's QBO
                if ($creds['qb_flavor'] == QuickBooks_IPP_IDS::FLAVOR_ONLINE) {
                    $cur_version = $quickBooksIPP->version();

                    $quickBooksIPP->version(QuickBooks_IPP_IDS::VERSION_3);		// Need v3 for this

                    $quickBooksIPPServiceCustomer = new QuickBooks_IPP_Service_Customer();
                    $customers = $quickBooksIPPServiceCustomer->query($Context, $creds['qb_realm'], 'SELECT * FROM Customer MAXRESULTS 1');

                    $quickBooksIPP->version($cur_version);		// Revert back to whatever they set
                } else {
                    $companies = $quickBooksIPP->getAvailableCompanies($Context);
                }

                // Check the last error code now...
                // but for some stupid reason the getAvailableCompanies call returns this
                return !($quickBooksIPP->errorCode() == 401 || $quickBooksIPP->errorCode() == 3100 || $quickBooksIPP->errorCode() == 3200);
            }
        }

        return false;
    }

    /**
     * Load OAuth credentials from the database
     *
     * @param string $app_username
     * @return array
     */
    public function load($app_tenant)
    {
        if ($this->_oauth_version == self::OAUTH_V1) {
            if (($arr = $this->_driver->oauthLoadV1($this->_key, $app_tenant)) && strlen($arr['oauth_access_token']) > 0 && strlen($arr['oauth_access_token_secret']) > 0) {
                $arr['oauth_consumer_key'] = $this->_consumer_key;
                $arr['oauth_consumer_secret'] = $this->_consumer_secret;

                return $arr;
            }
        } elseif ($this->_oauth_version == self::OAUTH_V2) {
            if (($arr = $this->_driver->oauthLoadV2($this->_key, $app_tenant)) && strlen($arr['oauth_access_token']) > 0 && strlen($arr['oauth_refresh_token']) > 0) {
                $arr['oauth_client_id'] = $this->_client_id;
                $arr['oauth_client_secret'] = $this->_client_secret;

                $arr['qb_flavor'] = QuickBooks_IPP_IDS::FLAVOR_ONLINE;

                return $arr;
            }
        }

        return false;
    }

    /**
     * Disconnect from QuickBooks
     *
     * @param  string $app_tenant    The tenant/connection point to disconnect
     * @param  bool   $force         TRUE to remove the OAuth tokens from _your_ database regardless of the response from Intuit
     * @return bool                  TRUE on disconnect, FALSE on failure
     */
    public function disconnect($app_tenant, $force = false)
    {
        if ($creds = $this->_driver->oauthLoadV2($this->_key, $app_tenant)) {
            $quickBooksIPP = new QuickBooks_IPP($this->_dsn, $this->_key);

            $quickBooksIPP->authMode(
                QuickBooks_IPP::AUTHMODE_OAUTHV2,
                $creds
            );

            if ($this->_sandbox) {
                $quickBooksIPP->sandbox(true);
            }

            // Do we need to refresh?
            if ($quickBooksIPP->handleRenewal()) {
                // Reload creds
                $creds = $this->_driver->oauthLoadV2($this->_key, $app_tenant);
            }

            if ($creds['oauth_refresh_token']) {
                // Remove the access token
                $ch = curl_init(self::URL_CONNECT_DISCONNECT);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([ 'token' => $creds['oauth_refresh_token'] ]));
                curl_setopt($ch, CURLOPT_USERPWD, $this->_client_id . ':' . $this->_client_secret);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    ]);
                curl_exec($ch);
                $info = curl_getinfo($ch);
                curl_close($ch);
            }

            // Also try to revoke the refresh token
            $ch = curl_init(self::URL_CONNECT_DISCONNECT);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([ 'token' => $creds['oauth_access_token'] ]));
            curl_setopt($ch, CURLOPT_USERPWD, $this->_client_id . ':' . $this->_client_secret);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                ]);
            $retr = curl_exec($ch);
            $info = curl_getinfo($ch);
            curl_close($ch);

            if ($info['http_code'] == 200 || $force) {
                return $this->_driver->oauthAccessDelete($app_tenant);
            }
        }

        return false;
    }

    public function fudge($request_token, $access_token, $access_token_secret, $realm, $flavor)
    {
        $this->_driver->oauthAccessWrite(
            $this->_key,
            $request_token,
            $access_token,
            $access_token_secret,
            $realm,
            $flavor
        );
    }

    /**
     * Handle an OAuth request login thing
     *
     *
     */
    public function handle($app_tenant, $state = '')
    {
        if ($app_tenant && $this->check($app_tenant) && $this->test($app_tenant)) {
            // ... and they are valid
            // They are already logged in, send them on to exchange data
            $that_url = $this->_that_url;
            if (false === strpos($that_url, '?')) {
                $that_url .= '?oauth_testcheck=1&oauth_state=' . @$_GET['state'];
            } else {
                $that_url .= '&oauth_testcheck=1&oauth_state=' . @$_GET['state'];
            }

            header('Location: ' . $that_url);
            exit;
        }

        if ($this->_oauth_version == self::OAUTH_V1 && isset($_GET['oauth_token'])) {
            // We're in the middle of an OAuth v1 token session
            if ($arr = $this->_driver->oauthRequestResolveV1($_GET['oauth_token'])) {
                $info = $this->_getAccessToken(
                    $arr['oauth_request_token'],
                    $arr['oauth_request_token_secret'],
                    $_GET['oauth_verifier']
                );

                if ($info) {
                    $this->_driver->oauthAccessWriteV1(
                        $this->_key,
                        $arr['oauth_request_token'],
                        $info['oauth_token'],
                        $info['oauth_token_secret'],
                        $_GET['realmId'],
                        $_GET['dataSource']
                    );

                    header('Location: ' . $this->_that_url);
                    exit;
                }

                // Something went wrong when fetching the user token...?
                print('something went wrong fetching user token');
            } else {
                print('something went wrong... invalid oauth token?');
            }
        } elseif ($this->_oauth_version == self::OAUTH_V2 && !empty($_GET['code']) && !empty($_GET['state']) && ($info = $this->_driver->oauthRequestResolveV2($_GET['state']))) {
            // Try to get an access/refresh token here

            if ($discover = $this->_discover()) {
                $ch = curl_init($discover['token_endpoint']);

                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                    'code' => $_GET['code'],
                    'redirect_uri' => $this->_this_url,
                    'grant_type' => 'authorization_code',
                    ]));

                curl_setopt($ch, CURLOPT_USERPWD, $this->_client_id . ':' . $this->_client_secret);

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);   // Do not follow; security risk here
                $retr = curl_exec($ch);
                $info = curl_getinfo($ch);

                if ($info['http_code'] == 200) {
                    $json = json_decode($retr, true);

                    $this->_driver->oauthAccessWriteV2(
                        $this->_key,
                        $_GET['state'],
                        $json['access_token'],
                        $json['refresh_token'],
                        date('Y-m-d H:i:s', time() + (int) $json['expires_in']),
                        date('Y-m-d H:i:s', time() + (int) $json['x_refresh_token_expires_in']),
                        $_GET['realmId']
                    );

                    $that_url = $this->_that_url;
                    if (false === strpos($that_url, '?')) {
                        $that_url .= '?oauth_state=' . $_GET['state'];
                    } else {
                        $that_url .= '&oauth_state=' . $_GET['state'];
                    }

                    header('Location: ' . $that_url);
                    exit;
                }

                print('An error occurred fetching the access/refresh token.');
                return false;
            }

        }
        else {
            if ($this->_oauth_version == self::OAUTH_V1) {
                $auth_url = $this->_getAuthenticateURLV1($app_tenant, $this->_this_url);
            } else {
                $auth_url = $this->_getAuthenticateURLV2($app_tenant, $this->_this_url, $state);
            }

            if (!$auth_url) {
                print('Could not build an authorization URL.');
                return false;
            }

            // Forward them to the auth page
            header('Location: ' . $auth_url);
            exit;
        }

        return true;
    }

    protected function _getAuthenticateURLV2($app_tenant, $url, $state)
    {
        if ($discover = $this->_discover()) {
            if (!$state) {
                // Write the request to the database
                $state = md5(mt_rand() . microtime(true));
            }

            $this->_driver->oauthRequestWriteV2($app_tenant, $state);

            return $discover['authorization_endpoint'] . '?' . http_build_query([
                'client_id' => $this->_client_id,
                'scope' => $this->_oauth_scope,
                'redirect_uri' => $url,
                'response_type' => 'code',
                'state' => $state,
                ]);
        }

        return false;
    }

    protected function _discover()
    {
        return self::discover($this->_sandbox);
    }

    public static function discover($sandbox)
    {
        $url = self::URL_DISCOVERY_PRODUCTION;
        if ($sandbox) {
            $url = self::URL_DISCOVERY_SANDBOX;
        }

        // Make a request to the discovery URL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);   // Do not follow; security risk here
        $retr = curl_exec($ch);
        $info = curl_getinfo($ch);

        if ($info['http_code'] == 200) {
            return json_decode($retr, true);
        }

        return false;
    }

    /**
     *
     *
     * @param string $url
     * @return string
     */
    protected function _getAuthenticateURLV1($app_tenant, $url)
    {
        // Fetch a request token from the OAuth service
        $info = $this->_request(QuickBooks_IPP_OAuthv1::METHOD_GET, QuickBooks_IPP_IntuitAnywhere::URL_REQUEST_TOKEN, [ 'oauth_callback' => $url ]);

        $vars = [];
        parse_str($info, $vars);

        // Write the request tokens to the database
        $this->_driver->oauthRequestWriteV1($app_tenant, $vars['oauth_token'], $vars['oauth_token_secret']);

        // Return the auth URL
        return QuickBooks_IPP_IntuitAnywhere::URL_CONNECT_BEGIN . '?oauth_callback=' . urlencode($url) . '&oauth_consumer_key=' . $this->_consumer_key . '&oauth_token=' . $vars['oauth_token'];
    }

    protected function _getAccessToken($oauth_token, $oauth_token_secret, $verifier)
    {
        if ($str = $this->_request(
            QuickBooks_IPP_OAuthv1::METHOD_GET,
            QuickBooks_IPP_IntuitAnywhere::URL_ACCESS_TOKEN,
            [
                'oauth_token' => $oauth_token,
                'oauth_secret' => $oauth_token_secret,
                'oauth_verifier' => $verifier,
                ]
        )) {
            $info = [];
            parse_str($str, $info);

            return $info;
        }

        return false;
    }

    /**
     * This function returns the html for displaying the "Blue Dot" menu
     *
     * @deprecated No longer applicable with OAuthv2
     */
    public function widgetMenu($app_username, $app_tenant)
    {
        return '';
    }

    protected function _request($method, $url, $params = [], $token = null, $secret = null, $data = null)
    {
        $quickBooksIPPOAuthv1 = new QuickBooks_IPP_OAuthv1($this->_consumer_key, $this->_consumer_secret);

        // This returns a signed request
        //
        // 0 => signature base string
        // 1 => signature
        // 2 => normalized url
        // 3 => header string
        $signed = $quickBooksIPPOAuthv1->sign($method, $url, $token, $secret, $params);

        //print_r($signed);

        // Create the new HTTP object
        //$HTTP = new QuickBooks_HTTP($url);
        $quickBooksHTTP = new QuickBooks_HTTP($signed[2]);

        $headers = [
            //'Authorization' => $signed[3],
            ];

        $quickBooksHTTP->setHeaders($headers);

        //
        $quickBooksHTTP->setRawBody($data);

        // We need the headers back
        //$HTTP->returnHeaders(true);

        // Send the request
        $return = $quickBooksHTTP->GET();

        $errnum = $quickBooksHTTP->errorNumber();
        $errmsg = $quickBooksHTTP->errorMessage();

        $this->_last_request = $quickBooksHTTP->lastRequest();
        $this->_last_response = $quickBooksHTTP->lastResponse();

        if ($errnum) {
            // An error occurred!
            $this->_setError(QuickBooks_IPP::ERROR_HTTP, $errnum . ': ' . $errmsg);
            return false;
        }

        // Everything is good, return the data!
        $this->_setError(QuickBooks_IPP::ERROR_OK, '');
        return $return;
    }
}
