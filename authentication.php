<?php

require_once INCLUDE_DIR . 'class.plugin.php';
require_once 'config.php';

require_once 'SamlStaffAuthenticationBackend.class.php';


class SamlAuthPlugin extends Plugin
{

    var $config_class = "SamlAuthConfig";

    function bootstrap()
    {
        $config = $this->getConfig();
        if ($config->get('auth-staff'))
            StaffAuthenticationBackend::register(new SamlStaffAuthenticationBackend($this->getConfig()));
        if ($config->get('auth-client'))
            UserAuthenticationBackend::register(new SamlUserAuthenticationBackend($this->getConfig()));

    }
}