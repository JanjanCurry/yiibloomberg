<?php
//Import OneLogin Library
$libDir = __DIR__ . '/lib/Saml2/';
$extlibDir = __DIR__ . '/extlib/';
require_once $extlibDir . 'xmlseclibs/xmlseclibs.php';
$folderInfo = scandir($libDir);
foreach ($folderInfo as $element) {
    if (is_file($libDir . $element) && (substr($element, -4) === '.php')) {
        include_once $libDir . $element;
    }
}

class SAML extends OneLogin_Saml2_Auth {
    public $auth;

    function __construct () {
        return parent::__construct($this->config());
    }

    public function config () {
        $spBaseUrl = YiiBase::getPathOfAlias('website');

        return array(
            'sp' => array(
                'entityId' => $spBaseUrl . '/saml/metadata',
                'assertionConsumerService' => array(
                    'url' => $spBaseUrl . '/saml/acs',
                ),
                'singleLogoutService' => array(
                    'url' => $spBaseUrl . '/saml/sls',
                ),
                'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified',
            ),
            'idp' => array(
                'entityId' => 'https://app.onelogin.com/saml/metadata/c11e414b-ade5-4627-a155-2ffd33d98dfe',
                'singleSignOnService' => array(
                    'url' => 'https://complete-intelligence-dev.onelogin.com/trust/saml2/http-post/sso/957764',
                ),
                'singleLogoutService' => array(
                    'url' => 'https://complete-intelligence-dev.onelogin.com/trust/saml2/http-redirect/slo/957764',
                ),
                'x509cert' => '-----BEGIN CERTIFICATE-----
    MIIECDCCAvCgAwIBAgIUKOrMUHJurRT4JkEHB1ozHuYNYzUwDQYJKoZIhvcNAQEF
    BQAwUzEeMBwGA1UECgwVQ29tcGxldGUgSW50ZWxsaWdlbmNlMRUwEwYDVQQLDAxP
    bmVMb2dpbiBJZFAxGjAYBgNVBAMMEU9uZUxvZ2luIEFjY291bnQgMB4XDTE5MDcw
    OTE2MTIwM1oXDTI0MDcwOTE2MTIwM1owUzEeMBwGA1UECgwVQ29tcGxldGUgSW50
    ZWxsaWdlbmNlMRUwEwYDVQQLDAxPbmVMb2dpbiBJZFAxGjAYBgNVBAMMEU9uZUxv
    Z2luIEFjY291bnQgMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA23yT
    qMHBor6w3/FqstnEOAetkX/ZMLcOod15azIubDUdiPAgTpMGB8ARZppL4ETEoySj
    aToCDIh9kbJA61GpyoQyFDQMDbJuDdbyvhDp5pV+Ka2+bt7+ygAgkNWU4VQQY+WC
    pOQrqLJBQ2BrwshItVMVCbF1qunwUQ9nDQ49p2UShNavoLfSkG0t15wmT1uX/6Fu
    ZEKBF+heCOku+dNIAliDphOQDt5huKWohW8wfLgyyaBDN9jbgH8Bg6Jy9kBGlyrM
    EgHPqBbSVMWx9ynHefJ/UqZObfkSsn/D8te/MGCKJ5yLQaCBlFb6rM9DoWIEdyZW
    1P0UuRIoTtiUpVwowwIDAQABo4HTMIHQMAwGA1UdEwEB/wQCMAAwHQYDVR0OBBYE
    FEteBp8Mzvxr1eMHr+ZuC9SllptpMIGQBgNVHSMEgYgwgYWAFEteBp8Mzvxr1eMH
    r+ZuC9SllptpoVekVTBTMR4wHAYDVQQKDBVDb21wbGV0ZSBJbnRlbGxpZ2VuY2Ux
    FTATBgNVBAsMDE9uZUxvZ2luIElkUDEaMBgGA1UEAwwRT25lTG9naW4gQWNjb3Vu
    dCCCFCjqzFBybq0U+CZBBwdaMx7mDWM1MA4GA1UdDwEB/wQEAwIHgDANBgkqhkiG
    9w0BAQUFAAOCAQEAeAImjt8ySDeLEl0SlxOEAFh/QX6jKT2r+Mz2B56WBxOumPVH
    M6REtIGpd3Zlbc0PGQQyB8vNAuMhpbztgtZ8ZL+2XDXJmrVM4JCqqZchKVIsD9zZ
    HkdwGX1kcUVvz4j/UpunN8g7dKaGmEge1h/kOHzifP1thAMomeAtIsmLnNz1zrV+
    E/pSDcmRzflSWenDa8HNDKT5UbKEVHoZGjQPA9nmLb580IUVxcakdx410EunP8ZV
    wpTaKFqte5rtN52Bwk7RRTkqg1kEWS6BiaHGBEhGe7D8/I33HHFKhYbYmqrymJQ6
    5d/Hc8DsL6ZrXPK5mOtayHbWOzaScWIIQx/PMA==
    -----END CERTIFICATE-----
    ',
            ),
        );
    }
}
