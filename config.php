<?php

require_once INCLUDE_DIR . '/class.plugin.php';
require_once INCLUDE_DIR . '/class.forms.php';

class SamlAuthConfig extends PluginConfig {

    function getOptions() {
        return array(
            'login_page_sso_name' => new TextboxField(
                array(
                    'id' => 'login_page_sso_name',
                    'label' => 'Login Page Button Name  ',
                    'hint' =>"ex: Sign in with ....",
                    'configuration' => array(
                        'size' => 59,
                        'length' => 255
                    )
                )
            ),
            'idp_entity_id' => new TextboxField(
                array(
                    'id' => 'idp_entity_id',
                    'label' => 'Identity Provider Entity ID',
                    'configuration' => array(
                        'size' => 59,
                        'length' => 255
                    )
                )
            ),
            'idp_x509cert' => new TextareaField(
                array(
                    'id' => 'idp_x509cert',
                    'label' => 'Identity Provider X509 Cert',
                    'configuration' => array(
                        'size' => 59,
                        'length' => 5500,
                        "html" => false,
                    )
                )
            ),
            'idp_sso_target_url' => new TextboxField(
                array(
                    'id' => 'idp_sso_target_url',
                    'label' => 'Identity Provider SSO URL',
                    'configuration' => array(
                        'size' => 59,
                        'length' => 255
                    )
                )
            ),
            'entity_id' => new TextboxField(
                array(
                    'id' => 'entity_id',
                    'label' => 'Service Provider Entity ID',
                    'hint' => 'If left blank it will be detected automatically.',
                    'configuration' => array(
                        'size' => 59,
                        'length' => 255
                    )
                )
            ),
            'assertion_consumer_service_url' => new TextboxField(
                array(
                    'id' => 'assertion_consumer_service_url',
                    'label' => 'Assertion Consumer Service URL',
                    'configuration' => array(
                        'size' => 59,
                        'length' => 255
                    )
                )
            ),



            'attribute_mapping' => new SectionBreakField(array(
                'label' => 'Attribute Mapping',
                'hint' => 'Attribute Mapping Options',
            )),
            'attribute_mapping_name' => new TextboxField(array(
                'label' => 'Name',
                'default' => "givenName",

            )),
            'attribute_mapping_surname' => new TextboxField(array(
                'label' => 'Surname',
                'default' => "sn",
                'configuration' => array(
                    'desc' => 'Enable authentication and discovery of clients'
                )
            )),
            // Honestly adding this will help avoid trial and error when setting up this plugin.
            'attribute_mapping_email' => new TextboxField(array(
                'label' => 'Email',
                'default' => "",
                'configuration' => array(
                    'desc' => 'leave empty to use the default NameID response from SAML'
                )
            )),






            'auth' => new SectionBreakField(array(
                'label' => 'Authentication Modes',
                'hint' => 'Authentication modes for clients and staff
                    members can be enabled independently. Client discovery
                    can be supported via a separate backend (such as LDAP)',
            )),
            'auth-staff' => new BooleanField(array(
                'label' => 'Staff Authentication',
                'default' => true,
                'configuration' => array(
                    'desc' => 'Enable authentication of staff members'
                )
            )),
            'auth-client' => new BooleanField(array(
                'label' => 'Client Authentication',
                'default' => false,
                'configuration' => array(
                    'desc' => 'Enable authentication and discovery of clients'
                )
            )),
              'debug_help' => new SectionBreakField(array(
                'label' => 'Debug Modes',
                'hint' => 'Debug mode enable',
            )),
            'debug' => new BooleanField(array(
                'label' => 'Debug',
                'default' => false,
                'configuration' => array(
                    'desc' => 'Debug mode'
                )
            )),
        );
    }
}
