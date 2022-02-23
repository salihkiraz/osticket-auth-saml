<?php

// set_include_path( get_include_path() . PATH_SEPARATOR . dirname(__file__) . '/include' );

return array(
    'id' => 'auth:saml',
    'version' => '1.3',
    'name' => 'SAML Authentication and Lookup',
    'author' => 'Salih KİRAZ salihk06@gmail.com',
    'description' => 'Provides an authentication backend for SAML identity providers.',
    'plugin' => 'authentication.php:SamlAuthPlugin'
);
