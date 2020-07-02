<?php
require_once './protected/extensions/saml/Saml.php';
require_once './protected/extensions/saml/Meta.php';

class SamlLoader extends CApplicationComponent {

    public function getAuth () {
        return $auth = new SAML();
    }

    public function getMeta () {
        return $auth = new Meta();
    }
}
