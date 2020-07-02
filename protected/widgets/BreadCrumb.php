<?php
class BreadCrumb extends CWidget {
 
    public $details = array();
	public $controller;
	public $action;
    public $start = array();
    public $middle = array();
    public $end = array();
    public $showHome = true;
    public $showController = true;
    public $showAction = true;
	
    public function run() {
        $this->render('breadCrumb');
    } 
}
?>