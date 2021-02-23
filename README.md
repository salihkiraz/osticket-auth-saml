# osticket-auth-saml
Osticket Saml Login Plugin


this reposity based https://code.tf/thefarm/osticket-auth-saml


#versions support

Osticket 1.12.x  and Osticket 1.14.x and 1.15.x ( Other verisons no tested)

#Settings 

Example setting:

Login Page Button Name :

Saml 

Identity Provider Entity ID: 

http://example.com/saml2/idp/metadata.php

Identity Provider X509 Cert: 

4EdjCCAt4CCQDTEiMCAOgINfEsJqKyvgDy........

Identity Provider SSO URL: 

http://example.com/saml2/idp/SSOService.php

Service Provider Entity ID: 

http://localhost:8888/osTicket125/

Assertion Consumer Service URL: 

http://localhost:8888/osTicket125/api/auth/saml

Attribute Mapping

Attribute Mapping Options

Name: 

givenName

Surname: 

sn

Authentication Modes

Staff Authentication: 

 Enable authentication of staff members
 
Client Authentication: 

 Enable authentication and discovery of clients
 

 
 
