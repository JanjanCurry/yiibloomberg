<?php
/* @var $this SiteController */
/* @var $error array */

if(YII_DEBUG && Yii::app()->user->checkAccess('dev')){
    $this->renderPartial('//site/error/dev', array('error' => $error));
}else if($code = 404){
    $this->renderPartial('//site/error/404', array('error' => $error));
}else{
    $this->renderPartial('//site/error/default', array('error' => $error));
}

?>