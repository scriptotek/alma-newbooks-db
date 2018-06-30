<?php

$idp_host = 'https://weblogin.uio.no/simplesaml';

return $settings = array(

    /**
     * If 'useRoutes' is set to true, the package defines five new routes:
     *
     *    Method | URI                      | Name
     *    -------|--------------------------|------------------
     *    POST   | {routesPrefix}/acs       | saml_acs
     *    GET    | {routesPrefix}/login     | saml_login
     *    GET    | {routesPrefix}/logout    | saml_logout
     *    GET    | {routesPrefix}/metadata  | saml_metadata
     *    GET    | {routesPrefix}/sls       | saml_sls
     */
    'useRoutes' => true,

    'routesPrefix' => '/saml2',

    /**
     * which middleware group to use for the saml routes
     * Laravel 5.2 will need a group which includes StartSession
     */
    'routesMiddleware' => ['saml'],

    /**
     * Indicates how the parameters will be
     * retrieved from the sls request for signature validation
     */
    'retrieveParametersFromServer' => false,

    /**
     * Where to redirect after logout
     */
    'logoutRoute' => '/',

    /**
     * Where to redirect after login if no other option was provided
     */
    'loginRoute' => '/',


    /**
     * Where to redirect after login if no other option was provided
     */
    'errorRoute' => '/saml2/error',




    /*****
     * One Login Settings
     */



    // If 'strict' is True, then the PHP Toolkit will reject unsigned
    // or unencrypted messages if it expects them signed or encrypted
    // Also will reject the messages if not strictly follow the SAML
    // standard: Destination, NameId, Conditions ... are validated too.
    'strict' => true, //@todo: make this depend on laravel config

    // Enable debug mode (to print errors)
    'debug' => env('APP_DEBUG', false),

    // If 'proxyVars' is True, then the Saml lib will trust proxy headers
    // e.g X-Forwarded-Proto / HTTP_X_FORWARDED_PROTO. This is useful if
    // your application is running behind a load balancer which terminates
    // SSL.
    'proxyVars' => true,

    // Service Provider Data that we are deploying
    'sp' => array(

        // Specifies constraints on the name identifier to be used to
        // represent the requested subject.
        // Take a look on lib/Saml2/Constants.php to see the NameIdFormat supported
        'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient',

        // Usually x509cert and privateKey of the SP are provided by files placed at
        // the certs folder. But we can also provide them with the following parameters
        'x509cert' => '',
        'privateKey' => '',

        //LARAVEL - You don't need to change anything else on the sp
        // Identifier of the SP entity  (must be a URI)
        'entityId' => env('SAML2_SP_ENTITYID',''),
        // Specifies info about where and how the <AuthnResponse> message MUST be
        // returned to the requester, in this case our SP.
        'assertionConsumerService' => array(
            // URL Location where the <Response> from the IdP will be returned
            'url' => '', //  $sp_host . '/acs', //LARAVEL: This would be set to saml_acs route
        ),
        // Specifies info about where and how the <Logout Response> message MUST be
        // returned to the requester, in this case our SP.
        'singleLogoutService' => array(
            // URL Location where the <Response> from the IdP will be returned
            //'url' => $sp_host . '/logout', //LARAVEL: This would be set to saml_sls route
            'url' => '', // $sp_host . '/logout',
        ),
    ),

     // Identity Provider Data that we want connect with our SP
     // https://weblogin.uio.no/simplesaml/saml2/idp/metadata.php?output=xhtml
     'idp' => array(
         'entityId' => $idp_host . '/saml2/idp/metadata.php',
         'singleSignOnService' => array(
             'url' => $idp_host . '/saml2/idp/SSOService.php',
         ),
         'singleLogoutService' => array(
             'url' => $idp_host . '/saml2/idp/SingleLogoutService.php',
         ),
         // Public x509 certificate of the IdP
         'x509cert' => 'MIIFGDCCBACgAwIBAgICA0YwDQYJKoZIhvcNAQEEBQAwga0xCzAJBgNVBAYTAk5PMQ0wCwYDVQQHEwRPc2xvMRswGQYDVQQKExJVbml2ZXJzaXR5IG9mIE9zbG8xOjA4BgNVBAsTMUNlbnRlciBmb3IgSW5mb3JtYXRpb24gVGVjaG5vbG9neSBTZXJ2aWNlcyAoVVNJVCkxEDAOBgNVBAMTB1VTSVQgQ0ExJDAiBgkqhkiG9w0BCQEWFXdlYm1hc3RlckB1c2l0LnVpby5ubzAeFw0xMzExMTUwOTU2NTZaFw0zMzExMTAwOTU2NTZaMIGmMQswCQYDVQQGEwJOTzEbMBkGA1UEChMSVW5pdmVyc2l0eSBvZiBPc2xvMTowOAYDVQQLEzFDZW50ZXIgZm9yIEluZm9ybWF0aW9uIFRlY2hub2xvZ3kgU2VydmljZXMgKFVTSVQpMRgwFgYDVQQDEw93ZWJsb2dpbi51aW8ubm8xJDAiBgkqhkiG9w0BCQEWFXdlYm1hc3RlckB1c2l0LnVpby5ubzCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBALbX2nZYBbUO5tdeG3lVaF/1kdcf1HrGfE/IITNyMqogHT6VPbQxzL5mN1IcHZPDi6Wi+Lg8V9RcXvgP7X6ajUk0PTPmqwil/izS3fUtyKnyulZn/M3+JUW9BHIazhpAsifbqkp5YHt+bFQ/FlStb8ZVzVRIHJZ+GmLKPV+DJ1MsnPyC1InSsdMHEBpt6tYPk3n5qZ7gU3CqI/b/rGP8ECGszRuf8RiL2zO5tlg9mgQ4I+xryIRVf963b538xq+OzGTbIr6eo4+5DolNDsEStOYfAQhnKaZ7QV6PEzS0lcMIXj5L6vZ4aGXEbESB5r/iWUX0HF8qpuMKzw6OgPddEOkCAwEAAaOCAUUwggFBMAkGA1UdEwQCMAAwOAYJYIZIAYb4QgENBCsWKVczQ0Etc2lnbmVkIE9wZW5TU0wgR2VuZXJhdGVkIENlcnRpZmljYXRlMB0GA1UdDgQWBBSCTt0rB5AtfBv0H+svAC8CtdlQHzCB2gYDVR0jBIHSMIHPgBQvUjhkJlXqclR0W+ZyPM/xsI3mB6GBs6SBsDCBrTELMAkGA1UEBhMCTk8xDTALBgNVBAcTBE9zbG8xGzAZBgNVBAoTElVuaXZlcnNpdHkgb2YgT3NsbzE6MDgGA1UECxMxQ2VudGVyIGZvciBJbmZvcm1hdGlvbiBUZWNobm9sb2d5IFNlcnZpY2VzIChVU0lUKTEQMA4GA1UEAxMHVVNJVCBDQTEkMCIGCSqGSIb3DQEJARYVd2VibWFzdGVyQHVzaXQudWlvLm5vggEAMA0GCSqGSIb3DQEBBAUAA4IBAQA+0egBeRpeEC5aDJaJqgtDMMn1y9LmybNTjxKB6/7ICRt3Uhf3EII/oJeuAs99voK96OEu+IEffq4hXSjMnxbHrJWlfEvcIrF9kQfbJKZpXv7jCz8BoDDp4N8mUGeq7xEdWhQsH5+o5aj4WUPMeuZjTPnxNt/cGTJxh95v2Gm0JQ/XmwrUZ41xOxPPsfwQjGIqjWjIsDIvpSlFSqphH967uwWB84GLKHj3jvESUUIhz5Y0TBDcXpF1o0gD4RXBwJEES8cZlYk2pzpH2n6ZpUInfshq2MVCHCPkLN5Iykx+53WmUzeAzFRY4NkuSJxy+poRpbtygxvGateGiFEkm+dD',
     ),

    /***
     *
     *  OneLogin advanced settings
     *
     *
     */
    // Security settings
    'security' => array(

        /** signatures and encryptions offered */

        // Indicates that the nameID of the <samlp:logoutRequest> sent by this SP
        // will be encrypted.
        'nameIdEncrypted' => false,

        // Indicates whether the <samlp:AuthnRequest> messages sent by this SP
        // will be signed.              [The Metadata of the SP will offer this info]
        'authnRequestsSigned' => false,

        // Indicates whether the <samlp:logoutRequest> messages sent by this SP
        // will be signed.
        'logoutRequestSigned' => false,

        // Indicates whether the <samlp:logoutResponse> messages sent by this SP
        // will be signed.
        'logoutResponseSigned' => false,

        /* Sign the Metadata
         False || True (use sp certs) || array (
                                                    keyFileName => 'metadata.key',
                                                    certFileName => 'metadata.crt'
                                                )
        */
        'signMetadata' => false,


        /** signatures and encryptions required **/

        // Indicates a requirement for the <samlp:Response>, <samlp:LogoutRequest> and
        // <samlp:LogoutResponse> elements received by this SP to be signed.
        'wantMessagesSigned' => false,

        // Indicates a requirement for the <saml:Assertion> elements received by
        // this SP to be signed.        [The Metadata of the SP will offer this info]
        'wantAssertionsSigned' => false,

        // Indicates a requirement for the NameID received by
        // this SP to be encrypted.
        'wantNameIdEncrypted' => false,

        // Authentication context.
        // Set to false and no AuthContext will be sent in the AuthNRequest,
        // Set true or don't present thi parameter and you will get an AuthContext 'exact' 'urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport'
        // Set an array with the possible auth context values: array ('urn:oasis:names:tc:SAML:2.0:ac:classes:Password', 'urn:oasis:names:tc:SAML:2.0:ac:classes:X509'),
        'requestedAuthnContext' => true,
    ),

    // Contact information template, it is recommended to suply a technical and support contacts
    'contactPerson' => array(
        'technical' => array(
            'givenName' => 'UiO Realfagsbiblioteket IT-drift',
            'emailAddress' => 'ureal-it-av-drift@ub.uio.no'
        ),
    ),

    // Organization information template, the info in en_US lang is recomended, add more if required
    'organization' => array(
        'en-US' => array(
            'name' => 'UiO Universitetsbiblioteket',
            'displayname' => 'UiO Universitetsbiblioteket',
            'url' => 'http://ub.uio.no/'
        ),
    ),

/* Interoperable SAML 2.0 Web Browser SSO Profile [saml2int]   http://saml2int.org/profile/current

   'authnRequestsSigned' => false,    // SP SHOULD NOT sign the <samlp:AuthnRequest>,
                                      // MUST NOT assume that the IdP validates the sign
   'wantAssertionsSigned' => true,
   'wantAssertionsEncrypted' => true, // MUST be enabled if SSL/HTTPs is disabled
   'wantNameIdEncrypted' => false,
*/

);
