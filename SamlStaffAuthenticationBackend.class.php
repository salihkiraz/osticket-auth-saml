<?php

require_once 'lib/xmlseclibs/xmlseclibs.php';
require_once 'lib/Saml2/Settings.php';
require_once 'lib/Saml2/AuthnRequest.php';
require_once 'lib/Saml2/Error.php';
require_once 'lib/Saml2/Utils.php';
require_once 'lib/Saml2/Response.php';
require_once 'lib/Saml2/Constants.php';


class SamlStaffAuthenticationBackend extends ExternalStaffAuthenticationBackend
{

    static $id = "saml";
    static $name = "SAML";
    static $service_name = "SAML";

    private $_config;

    function __construct($config)
    {
        $this->_config = $config;

        $self = $this;
        self::$service_name=$this->_config->get('login_page_sso_name');
        Signal::connect('api', function ($dispatcher) use ($self) {
            $dispatcher->append(
                url('^/auth/saml$', function () use ($self) {
                    if (isset($_POST['SAMLResponse'])) {
                        $response = $self->getSamlResponse($_POST['SAMLResponse']);

                        if ($response->isValid()) {
                            $_SESSION['saml']['nameId'] = $response->getNameId();
                            $_SESSION['saml']['name'] = $response->getAttributes()["givenName"][0];
                            $_SESSION['saml']['surname'] = $response->getAttributes()["sn"][0];


                            if ($_SESSION['saml']['type'] == "staff") {
                                Http::redirect(ROOT_PATH . 'scp');
                            } else {
                                Http::redirect(ROOT_PATH . 'open.php');
                            }

                        }
                    }
                })
            );
        });
    }

    private function getSamlConfiguration()
    {
        $settings = array(
            'idp' => array(
                'entityId' => $this->_config->get('idp_entity_id'),
                'singleSignOnService' => array(
                    'url' => $this->_config->get('idp_sso_target_url'),
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
                ),
                'x509cert' => $this->_config->get('idp_x509cert')
            ),

            'sp' => array(
                'entityId' => $this->_config->get('entity_id'),
                'assertionConsumerService' => array(
                    'url' => $this->_config->get('assertion_consumer_service_url'),
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
                ),
                'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient'
            ),
            "debug"=>$this->_config->get('debug'),
        );

        return new OneLogin_Saml2_Settings($settings);
    }

    public function createSignInURL()
    {
        $_SESSION['saml']['type'] = "staff";
        $settings = $this->getSamlConfiguration();
        $authRequest = new OneLogin_Saml2_AuthnRequest($settings);
        $samlRequest = $authRequest->getRequest();

        $parameters = array('SAMLRequest' => $samlRequest);

        $idpData = $settings->getIdPData();
        $ssoUrl = $idpData['singleSignOnService']['url'];
        $url = OneLogin_Saml2_Utils::redirect($ssoUrl, $parameters, true);

        return $url;
    }

    public function getSamlResponse($saml_response)
    {
        return new OneLogin_Saml2_Response($this->getSamlConfiguration(), $saml_response);
    }

    function signOn()
    {
        $_SESSION['saml']['type'] = "staff";
        if (isset($_SESSION['saml']['nameId'])) {
            $staff_id = StaffSession::getIdByEmail($_SESSION['saml']['nameId']);

            if ($staff_id) {
                if (method_exists(StaffSession, 'lookup')) {
                    // Assholes
                    $staff_session = StaffSession::lookup($staff_id);
                } else {
                    $staff_session = new StaffSession($staff_id);
                }

                return $staff_session;
            } else {
                $_SESSION['_staff']['auth']['msg'] = /* trans */ 'Have your administrator create a local account';
            }
        }
    }

    function triggerAuth()
    {
        # TODO : Set location header
        parent::triggerAuth();

        Http::redirect($this->createSignInURL());
    }
}

class SamlUserAuthenticationBackend extends ExternalUserAuthenticationBackend
{

    static $id = "saml.client";
    static $name = "SAML";
    static $service_name = "SAML";

    private $_config;

    function __construct($config)
    {
        $this->_config = $config;

        $self = $this;

        self::$service_name=$this->_config->get('login_page_sso_name');

        Signal::connect('api', function ($dispatcher) use ($self) {
            $dispatcher->append(
                url('^/auth/saml$', function () use ($self) {
                    if (isset($_POST['SAMLResponse'])) {
                        $response = $self->getSamlResponse($_POST['SAMLResponse']);

                        if ($response->isValid()) {
                            $_SESSION['saml']['nameId'] = $response->getNameId();
                            $_SESSION['saml']['name'] = $response->getAttributes()[$this->_config->get('attribute_mapping_name')][0];
                            $_SESSION['saml']['surname'] = $response->getAttributes()[$this->_config->get('attribute_mapping_surname')][0];

                            Http::redirect(ROOT_PATH . '');
                        }
                    }
                })
            );
        });
    }


    private function getSamlConfiguration()
    {
        $url=(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

        $settings = array(
            'idp' => array(
                'entityId' => $this->_config->get('idp_entity_id'),
                'singleSignOnService' => array(
                    'url' => $this->_config->get('idp_sso_target_url'),
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
                ),
                'x509cert' => $this->_config->get('idp_cert_fingerprint')
            ),

            'sp' => array(
                'entityId' => (($this->_config->get('entity_id')=="") ? $url:$this->_config->get('entity_id')),
                'assertionConsumerService' => array(
                    'url' => $this->_config->get('assertion_consumer_service_url'),
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
                ),
                'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient'
            ),
            "debug"=>$this->_config->get('debug'),
        );

        return new OneLogin_Saml2_Settings($settings);
    }

    public function createSignInURL()
    {
        $_SESSION['saml']['type'] = "user";
        $settings = $this->getSamlConfiguration();
        $authRequest = new OneLogin_Saml2_AuthnRequest($settings);
        $samlRequest = $authRequest->getRequest();

        $parameters = array('SAMLRequest' => $samlRequest);

        $idpData = $settings->getIdPData();
        $ssoUrl = $idpData['singleSignOnService']['url'];
        $url = OneLogin_Saml2_Utils::redirect($ssoUrl, $parameters, true);

        return $url;
    }

    public function getSamlResponse($saml_response)
    {
        return new OneLogin_Saml2_Response($this->getSamlConfiguration(), $saml_response);
    }

    function signOn()
    {
        $_SESSION['saml']['type'] = "client";
        if (isset($_SESSION['saml']['nameId'])) {

            $acct = ClientAccount::lookupByUsername($_SESSION['saml']['nameId']);
            if ($acct = ClientAccount::lookupByUsername($_SESSION['saml']['nameId'])) {
                if (($client = new ClientSession(new EndUser($acct->getUser()))) && $client->getId()) {
                    $user = $acct->getUser();
                    $oldAddress = $user->getDefaultEmailAddress();
                    $userID = $client->getId();

                    // Has their email changed?
                    if (strcasecmp($oldAddress, $_SESSION['saml']['nameId']) != 0) {
                        // Let's check if this email exists, first of all.
                        $newEmail = UserEmailModel::lookup(array("address" => $_SESSION['saml']['nameId']));

                        if ($newEmail) {
                            // Let's update the user_id for this email!
                            $newEmail->set("user_id", $userID);
                            $newEmail->save();
                        } else {
                            // Let's add the new email.
                            $newEmail = UserEmailModel::create();
                            $newEmail->set("user_id", $userID);
                            $newEmail->set("address", $_SESSION['saml']['nameId']);
                            $newEmail->save();
                        }

                        // Update the default email ID.
                        $user->set("default_email_id", $newEmail->get("id"));
                        $user->save();
                    }

                    return $client;
                }
            } else { // Doesn't exist, so let's make one?
                // IF the user has previously used helpdesk to submit a ticket via email (without an account) this will sync, based on email address.
                $client = new ClientCreateRequest($this, $_SESSION['saml']['nameId'], ["email" => $_SESSION['saml']['nameId'], "name" => $_SESSION['saml']['name'] . " " .
                    $_SESSION['saml']['surname']]);
                return $client->attemptAutoRegister();
            }
        }
    }

    function triggerAuth()
    {
        # TODO : Set location header
        parent::triggerAuth();


        Http::redirect($this->createSignInURL());
    }
}
