<?php

//This is variable is for simplesaml example only.
// For real IdP, you must set the url values in the 'idp' config to conform to the IdP's real urls.
$idp_host = 'https://weblogin.uio.no/simplesaml';

return $settings = array(

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

    // Service Provider Data that we are deploying
    'sp' => array(

        // Specifies constraints on the name identifier to be used to
        // represent the requested subject.
        // Take a look on lib/Saml2/Constants.php to see the NameIdFormat supported
        'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent',

        // Usually x509cert and privateKey of the SP are provided by files placed at
        // the certs folder. But we can also provide them with the following parameters
        'x509cert' => '',
        'privateKey' => '',

        // Identifier (URI) of the SP entity.
        // Leave blank to use the '{idpName}_metadata' route, e.g. 'test_metadata'.
        'entityId' => '',

        // Specifies info about where and how the <AuthnResponse> message MUST be
        // returned to the requester, in this case our SP.
        'assertionConsumerService' => array(
            // URL Location where the <Response> from the IdP will be returned,
            // using HTTP-POST binding.
            // Leave blank to use the '{idpName}_acs' route, e.g. 'test_acs'
            'url' => '',
        ),
        // Specifies info about where and how the <Logout Response> message MUST be
        // returned to the requester, in this case our SP.
        // Remove this part to not include any URL Location in the metadata.
        'singleLogoutService' => array(
            // URL Location where the <Response> from the IdP will be returned,
            // using HTTP-Redirect binding.
            // Leave blank to use the '{idpName}_sls' route, e.g. 'test_sls'
            'url' => '',
        ),
    ),

    // Identity Provider Data that we want connect with our SP
    'idp' => array(
        // Identifier of the IdP entity  (must be a URI)
        'entityId' => $idp_host . '/saml2/idp/metadata.php',
        // SSO endpoint info of the IdP. (Authentication Request protocol)
        'singleSignOnService' => array(
            // URL Target of the IdP where the SP will send the Authentication Request Message,
            // using HTTP-Redirect binding.
            'url' => $idp_host . '/saml2/idp/SSOService.php',
        ),
        // SLO endpoint info of the IdP.
        'singleLogoutService' => array(
            // URL Location of the IdP where the SP will send the SLO Request,
            // using HTTP-Redirect binding.
            'url' => $idp_host . '/saml2/idp/SingleLogoutService.php',
        ),
        // Public x509 certificate of the IdP
        'x509cert' => 'MIIFGDCCBACgAwIBAgICA0YwDQYJKoZIhvcNAQEEBQAwga0xCzAJBgNVBAYTAk5PMQ0wCwYDVQQHEwRPc2xvMRswGQYDVQQKExJVbml2ZXJzaXR5IG9mIE9zbG8xOjA4BgNVBAsTMUNlbnRlciBmb3IgSW5mb3JtYXRpb24gVGVjaG5vbG9neSBTZXJ2aWNlcyAoVVNJVCkxEDAOBgNVBAMTB1VTSVQgQ0ExJDAiBgkqhkiG9w0BCQEWFXdlYm1hc3RlckB1c2l0LnVpby5ubzAeFw0xMzExMTUwOTU2NTZaFw0zMzExMTAwOTU2NTZaMIGmMQswCQYDVQQGEwJOTzEbMBkGA1UEChMSVW5pdmVyc2l0eSBvZiBPc2xvMTowOAYDVQQLEzFDZW50ZXIgZm9yIEluZm9ybWF0aW9uIFRlY2hub2xvZ3kgU2VydmljZXMgKFVTSVQpMRgwFgYDVQQDEw93ZWJsb2dpbi51aW8ubm8xJDAiBgkqhkiG9w0BCQEWFXdlYm1hc3RlckB1c2l0LnVpby5ubzCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBALbX2nZYBbUO5tdeG3lVaF/1kdcf1HrGfE/IITNyMqogHT6VPbQxzL5mN1IcHZPDi6Wi+Lg8V9RcXvgP7X6ajUk0PTPmqwil/izS3fUtyKnyulZn/M3+JUW9BHIazhpAsifbqkp5YHt+bFQ/FlStb8ZVzVRIHJZ+GmLKPV+DJ1MsnPyC1InSsdMHEBpt6tYPk3n5qZ7gU3CqI/b/rGP8ECGszRuf8RiL2zO5tlg9mgQ4I+xryIRVf963b538xq+OzGTbIr6eo4+5DolNDsEStOYfAQhnKaZ7QV6PEzS0lcMIXj5L6vZ4aGXEbESB5r/iWUX0HF8qpuMKzw6OgPddEOkCAwEAAaOCAUUwggFBMAkGA1UdEwQCMAAwOAYJYIZIAYb4QgENBCsWKVczQ0Etc2lnbmVkIE9wZW5TU0wgR2VuZXJhdGVkIENlcnRpZmljYXRlMB0GA1UdDgQWBBSCTt0rB5AtfBv0H+svAC8CtdlQHzCB2gYDVR0jBIHSMIHPgBQvUjhkJlXqclR0W+ZyPM/xsI3mB6GBs6SBsDCBrTELMAkGA1UEBhMCTk8xDTALBgNVBAcTBE9zbG8xGzAZBgNVBAoTElVuaXZlcnNpdHkgb2YgT3NsbzE6MDgGA1UECxMxQ2VudGVyIGZvciBJbmZvcm1hdGlvbiBUZWNobm9sb2d5IFNlcnZpY2VzIChVU0lUKTEQMA4GA1UEAxMHVVNJVCBDQTEkMCIGCSqGSIb3DQEJARYVd2VibWFzdGVyQHVzaXQudWlvLm5vggEAMA0GCSqGSIb3DQEBBAUAA4IBAQA+0egBeRpeEC5aDJaJqgtDMMn1y9LmybNTjxKB6/7ICRt3Uhf3EII/oJeuAs99voK96OEu+IEffq4hXSjMnxbHrJWlfEvcIrF9kQfbJKZpXv7jCz8BoDDp4N8mUGeq7xEdWhQsH5+o5aj4WUPMeuZjTPnxNt/cGTJxh95v2Gm0JQ/XmwrUZ41xOxPPsfwQjGIqjWjIsDIvpSlFSqphH967uwWB84GLKHj3jvESUUIhz5Y0TBDcXpF1o0gD4RXBwJEES8cZlYk2pzpH2n6ZpUInfshq2MVCHCPkLN5Iykx+53WmUzeAzFRY4NkuSJxy+poRpbtygxvGateGiFEkm+dD',
        /*
         *  Instead of use the whole x509cert you can use a fingerprint
         *  (openssl x509 -noout -fingerprint -in "idp.crt" to generate it)
         */
        // 'certFingerprint' => '',
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
            'givenName' => 'name',
            'emailAddress' => 'no@reply.com'
        ),
        'support' => array(
            'givenName' => 'Support',
            'emailAddress' => 'no@reply.com'
        ),
    ),

    // Organization information template, the info in en_US lang is recomended, add more if required
    'organization' => array(
        'en-US' => array(
            'name' => 'Name',
            'displayname' => 'Display Name',
            'url' => 'http://url'
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
