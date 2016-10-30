<?php

//This is variable is an example - Just make sure that the urls in the 'idp' config are ok.
$sp_host = 'http://tilvekst.dev:8000/saml2';

return $settings = array(
    /*****
     * Cosmetic settings - controller routes
     **/
    'useRoutes' => true, //include library routes and controllers


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
    'debug' => false, //@todo: make this depend on laravel config

    // Service Provider Data that we are deploying
    'sp' => array(

        // Specifies constraints on the name identifier to be used to
        // represent the requested subject.
        // Take a look on lib/Saml2/Constants.php to see the NameIdFormat supported
        'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient',

        // Usually x509cert and privateKey of the SP are provided by files placed at
        // the certs folder. But we can also provide them with the following parameters
        //'x509cert' => '',
        //'privateKey' => '',

        //LARAVEL - You don't need to change anything else on the sp
        // Identifier of the SP entity  (must be a URI)
        //'entityId' => $sp_host . '/metadata', //LARAVEL: This would be set to saml_metadata route
        // Specifies info about where and how the <AuthnResponse> message MUST be
        // returned to the requester, in this case our SP.
        'assertionConsumerService' => array(
            // URL Location where the <Response> from the IdP will be returned
            //'url' => $sp_host . '/acs', //LARAVEL: This would be set to saml_acs route
        ),
        // Specifies info about where and how the <Logout Response> message MUST be
        // returned to the requester, in this case our SP.
        'singleLogoutService' => array(
            // URL Location where the <Response> from the IdP will be returned
            //'url' => $sp_host . '/logout', //LARAVEL: This would be set to saml_sls route
        ),
    ),

    // Identity Provider Data that we want connect with our SP
    'idp' => array(
        'entityId' => 'https://idp.ssocircle.com',
        'singleSignOnService' => array(
            'url' =>  'https://idp.ssocircle.com/sso/SSORedirect/metaAlias/publicidp',
        ),
        'singleLogoutService' => array(
            'url' => 'https://idp.ssocircle.com/sso/IDPSloRedirect/metaAlias/publicidp',
        ),
        'x509cert' => 'MIIEYzCCAkugAwIBAgIDIAZmMA0GCSqGSIb3DQEBCwUAMC4xCzAJBgNVBAYTAkRF
MRIwEAYDVQQKDAlTU09DaXJjbGUxCzAJBgNVBAMMAkNBMB4XDTE2MDgwMzE1MDMy
M1oXDTI2MDMwNDE1MDMyM1owPTELMAkGA1UEBhMCREUxEjAQBgNVBAoTCVNTT0Np
cmNsZTEaMBgGA1UEAxMRaWRwLnNzb2NpcmNsZS5jb20wggEiMA0GCSqGSIb3DQEB
AQUAA4IBDwAwggEKAoIBAQCAwWJyOYhYmWZF2TJvm1VyZccs3ZJ0TsNcoazr2pTW
cY8WTRbIV9d06zYjngvWibyiylewGXcYONB106ZNUdNgrmFd5194Wsyx6bPvnjZE
ERny9LOfuwQaqDYeKhI6c+veXApnOfsY26u9Lqb9sga9JnCkUGRaoVrAVM3yfghv
/Cg/QEg+I6SVES75tKdcLDTt/FwmAYDEBV8l52bcMDNF+JWtAuetI9/dWCBe9VTC
asAr2Fxw1ZYTAiqGI9sW4kWS2ApedbqsgH3qqMlPA7tg9iKy8Yw/deEn0qQIx8Gl
VnQFpDgzG9k+jwBoebAYfGvMcO/BDXD2pbWTN+DvbURlAgMBAAGjezB5MAkGA1Ud
EwQCMAAwLAYJYIZIAYb4QgENBB8WHU9wZW5TU0wgR2VuZXJhdGVkIENlcnRpZmlj
YXRlMB0GA1UdDgQWBBQhAmCewE7aonAvyJfjImCRZDtccTAfBgNVHSMEGDAWgBTA
1nEA+0za6ppLItkOX5yEp8cQaTANBgkqhkiG9w0BAQsFAAOCAgEAAhC5/WsF9ztJ
Hgo+x9KV9bqVS0MmsgpG26yOAqFYwOSPmUuYmJmHgmKGjKrj1fdCINtzcBHFFBC1
maGJ33lMk2bM2THx22/O93f4RFnFab7t23jRFcF0amQUOsDvltfJw7XCal8JdgPU
g6TNC4Fy9XYv0OAHc3oDp3vl1Yj8/1qBg6Rc39kehmD5v8SKYmpE7yFKxDF1ol9D
KDG/LvClSvnuVP0b4BWdBAA9aJSFtdNGgEvpEUqGkJ1osLVqCMvSYsUtHmapaX3h
iM9RbX38jsSgsl44Rar5Ioc7KXOOZFGfEKyyUqucYpjWCOXJELAVAzp7XTvA2q55
u31hO0w8Yx4uEQKlmxDuZmxpMz4EWARyjHSAuDKEW1RJvUr6+5uA9qeOKxLiKN1j
o6eWAcl6Wr9MreXR9kFpS6kHllfdVSrJES4ST0uh1Jp4EYgmiyMmFCbUpKXifpsN
WCLDenE3hllF0+q3wIdu+4P82RIM71n7qVgnDnK29wnLhHDat9rkC62CIbonpkVY
mnReX0jze+7twRanJOMCJ+lFg16BDvBcG8u0n/wIDkHHitBI7bU1k6c6DydLQ+69
h8SCo6sO9YuD+/3xAGKad4ImZ6vTwlB4zDCpu6YgQWocWRXE+VkOb+RBfvP755PU
aLfL63AFVlpOnEpIio5++UjNJRuPuAA=',
        /*
         *  Instead of use the whole x509cert you can use a fingerprint
         *  (openssl x509 -noout -fingerprint -in "idp.crt" to generate it)
         */
        // 'certFingerprint' => 'BD:EB:FB:FB:A7:86:C2:D9:7F:21:25:27:47:93:E3:26:43:35:8E:81',
    ),

//     // Identity Provider Data that we want connect with our SP
//     // https://weblogin-test.uio.no/simplesaml/saml2/idp/metadata.php?output=xhtml
//     'idp' => array(
//         'entityId' => 'https://weblogin-test.uio.no',
//         'singleSignOnService' => array(
//             'url' => 'https://weblogin-test.uio.no/simplesaml/saml2/idp/SSOService.php',
//         ),
//         'singleLogoutService' => array(
//             'url' => 'https://weblogin-test.uio.no/simplesaml/saml2/idp/SingleLogoutService.php',
//         ),
//         // Public x509 certificate of the IdP
//         'x509cert' => 'MIIFHTCCBAWgAwIBAgICA0UwDQYJKoZIhvcNAQEEBQAwga0xCzAJBgNVBAYTAk5PMQ0wCwYDVQQHEwRPc2xvMRswGQYDVQQKExJVbml2ZXJzaXR5IG9mIE9zbG8xOjA4BgNVBAsTMUNlbnRlciBmb3IgSW5mb3JtYXRpb24gVGVjaG5vbG9neSBTZXJ2aWNlcyAoVVNJVCkxEDAOBgNVBAMTB1VTSVQgQ0ExJDAiBgkqhkiG9w0BCQEWFXdlYm1hc3RlckB1c2l0LnVpby5ubzAeFw0xMzEwMjgxMjUxMzlaFw0yMzEwMjYxMjUxMzlaMIGrMQswCQYDVQQGEwJOTzEbMBkGA1UEChMSVW5pdmVyc2l0eSBvZiBPc2xvMTowOAYDVQQLEzFDZW50ZXIgZm9yIEluZm9ybWF0aW9uIFRlY2hub2xvZ3kgU2VydmljZXMgKFVTSVQpMR0wGwYDVQQDExR3ZWJsb2dpbi10ZXN0LnVpby5ubzEkMCIGCSqGSIb3DQEJARYVd2VibWFzdGVyQHVzaXQudWlvLm5vMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEArditxDo2pV0pKddtUo1yH7Znjwkf+PSYMMiI+W1EaSAQ3zyayNnF/xGCK0FmPIs0eZACs/0mODn9flhyINjWb224GS45Ry592u6Ta9HTyWrnPvAgYw0TMs/evc76B+XATiQcw4xNFFhqG1hPGYaNHwZaWmngG2F+B5xY5twN/lMwwuD+Q3sJ/B39pfHy+Y6jy0bEDpM2RrqF5tARKnU1iikwViHI0bWlFEAF2piuj/M4Cha5seIxEZhZtLLMfFX7Q7JTwprisL3pwtALNPSm9sZRLCcpFIFRNUzpgf3HNFvsYdyDw/1gXj/2RBzLBImDG1QQxg67tT/OQpL9gqO2CwIDAQABo4IBRTCCAUEwCQYDVR0TBAIwADA4BglghkgBhvhCAQ0EKxYpVzNDQS1zaWduZWQgT3BlblNTTCBHZW5lcmF0ZWQgQ2VydGlmaWNhdGUwHQYDVR0OBBYEFNrZ2Qv6rFnLBBtjKZ9sm8eiWzc8MIHaBgNVHSMEgdIwgc+AFC9SOGQmVepyVHRb5nI8z/GwjeYHoYGzpIGwMIGtMQswCQYDVQQGEwJOTzENMAsGA1UEBxMET3NsbzEbMBkGA1UEChMSVW5pdmVyc2l0eSBvZiBPc2xvMTowOAYDVQQLEzFDZW50ZXIgZm9yIEluZm9ybWF0aW9uIFRlY2hub2xvZ3kgU2VydmljZXMgKFVTSVQpMRAwDgYDVQQDEwdVU0lUIENBMSQwIgYJKoZIhvcNAQkBFhV3ZWJtYXN0ZXJAdXNpdC51aW8ubm+CAQAwDQYJKoZIhvcNAQEEBQADggEBAFfb5ednPCcwA/U6/v4JIHEOREQlXcpcKsQHT9dNjKWSiXUxF1N3KlKRCrdOSe4DVS1BkmgnAUY1GSnT1acxvsBmW1m0qu6cFlr4K8qgkDio2nPQtIv608+e51Iop6JN1B9m1UX14DXxDjozH3bLO95mChhJ00jKdIFtAXOpjZJS8LC/ii/GjKrPUl8Yz9gcmxykkryr+HdZtBUpcLDCnPhkv5Qqkr0SZQBlsr2XzCydll4ZkYUYYLRG/wxlKop9PY3dKMXLf+jlNiVH9YbiRoa1NdxDsFKTpfhnzVNbGbNp4Gkrn4lut007fhMfcq1ZbATR39NzU84WkMjbhGaisNA=',
//         /*
//          *  Instead of use the whole x509cert you can use a fingerprint
//          *  (openssl x509 -noout -fingerprint -in "idp.crt" to generate it)
//          */
//         // 'certFingerprint' => 'BD:EB:FB:FB:A7:86:C2:D9:7F:21:25:27:47:93:E3:26:43:35:8E:81',
//     ),





//     // Identity Provider Data that we want connect with our SP
//     // https://weblogin.uio.no/simplesaml/saml2/idp/metadata.php?output=xhtml
//     'idp' => array(
//         'entityId' => 'https://weblogin.uio.no',
//         'singleSignOnService' => array(
//             'url' => 'https://weblogin.uio.no/simplesaml/saml2/idp/SSOService.php',
//         ),
//         'singleLogoutService' => array(
//             'url' => 'https://weblogin.uio.no/simplesaml/saml2/idp/SingleLogoutService.php',
//         ),
//         // Public x509 certificate of the IdP
//         'x509cert' => 'MIIFGDCCBACgAwIBAgICA0YwDQYJKoZIhvcNAQEEBQAwga0xCzAJBgNVBAYTAk5PMQ0wCwYDVQQHEwRPc2xvMRswGQYDVQQKExJVbml2ZXJzaXR5IG9mIE9zbG8xOjA4BgNVBAsTMUNlbnRlciBmb3IgSW5mb3JtYXRpb24gVGVjaG5vbG9neSBTZXJ2aWNlcyAoVVNJVCkxEDAOBgNVBAMTB1VTSVQgQ0ExJDAiBgkqhkiG9w0BCQEWFXdlYm1hc3RlckB1c2l0LnVpby5ubzAeFw0xMzExMTUwOTU2NTZaFw0zMzExMTAwOTU2NTZaMIGmMQswCQYDVQQGEwJOTzEbMBkGA1UEChMSVW5pdmVyc2l0eSBvZiBPc2xvMTowOAYDVQQLEzFDZW50ZXIgZm9yIEluZm9ybWF0aW9uIFRlY2hub2xvZ3kgU2VydmljZXMgKFVTSVQpMRgwFgYDVQQDEw93ZWJsb2dpbi51aW8ubm8xJDAiBgkqhkiG9w0BCQEWFXdlYm1hc3RlckB1c2l0LnVpby5ubzCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBALbX2nZYBbUO5tdeG3lVaF/1kdcf1HrGfE/IITNyMqogHT6VPbQxzL5mN1IcHZPDi6Wi+Lg8V9RcXvgP7X6ajUk0PTPmqwil/izS3fUtyKnyulZn/M3+JUW9BHIazhpAsifbqkp5YHt+bFQ/FlStb8ZVzVRIHJZ+GmLKPV+DJ1MsnPyC1InSsdMHEBpt6tYPk3n5qZ7gU3CqI/b/rGP8ECGszRuf8RiL2zO5tlg9mgQ4I+xryIRVf963b538xq+OzGTbIr6eo4+5DolNDsEStOYfAQhnKaZ7QV6PEzS0lcMIXj5L6vZ4aGXEbESB5r/iWUX0HF8qpuMKzw6OgPddEOkCAwEAAaOCAUUwggFBMAkGA1UdEwQCMAAwOAYJYIZIAYb4QgENBCsWKVczQ0Etc2lnbmVkIE9wZW5TU0wgR2VuZXJhdGVkIENlcnRpZmljYXRlMB0GA1UdDgQWBBSCTt0rB5AtfBv0H+svAC8CtdlQHzCB2gYDVR0jBIHSMIHPgBQvUjhkJlXqclR0W+ZyPM/xsI3mB6GBs6SBsDCBrTELMAkGA1UEBhMCTk8xDTALBgNVBAcTBE9zbG8xGzAZBgNVBAoTElVuaXZlcnNpdHkgb2YgT3NsbzE6MDgGA1UECxMxQ2VudGVyIGZvciBJbmZvcm1hdGlvbiBUZWNobm9sb2d5IFNlcnZpY2VzIChVU0lUKTEQMA4GA1UEAxMHVVNJVCBDQTEkMCIGCSqGSIb3DQEJARYVd2VibWFzdGVyQHVzaXQudWlvLm5vggEAMA0GCSqGSIb3DQEBBAUAA4IBAQA+0egBeRpeEC5aDJaJqgtDMMn1y9LmybNTjxKB6/7ICRt3Uhf3EII/oJeuAs99voK96OEu+IEffq4hXSjMnxbHrJWlfEvcIrF9kQfbJKZpXv7jCz8BoDDp4N8mUGeq7xEdWhQsH5+o5aj4WUPMeuZjTPnxNt/cGTJxh95v2Gm0JQ/XmwrUZ41xOxPPsfwQjGIqjWjIsDIvpSlFSqphH967uwWB84GLKHj3jvESUUIhz5Y0TBDcXpF1o0gD4RXBwJEES8cZlYk2pzpH2n6ZpUInfshq2MVCHCPkLN5Iykx+53WmUzeAzFRY4NkuSJxy+poRpbtygxvGateGiFEkm+dD',
//         /*
//          *  Instead of use the whole x509cert you can use a fingerprint
//          *  (openssl x509 -noout -fingerprint -in "idp.crt" to generate it)
//          */
//         // 'certFingerprint' => 'BD:EB:FB:FB:A7:86:C2:D9:7F:21:25:27:47:93:E3:26:43:35:8E:81',
//     ),

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
