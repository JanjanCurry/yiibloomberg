<?php class DbConnection extends CDbConnection{
    public function open(){
        try {
            parent::open();
        } catch(CDbException $e) {
            echo 'Your connection to the system has worked but the system was unable to load due to the server and/or database experiencing technical issues, this issue is company-wide.'; exit;
        }
    }
}