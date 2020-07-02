<?php
/* @var $this SiteController */
/* @var $login LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle=Yii::app()->name . ' - Forgot Password';
?>




<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'login-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array(
        'validateOnSubmit'=>true,
    ),
    'htmlOptions' => array('class'=>'m-t'),
)); ?>

<p>Enter your email address and we will send you instructions for resetting your password.</p>

<?php echo $form->errorSummary($login, '', null, array('class'=>'alert alert-danger text-left')); ?>

<div class="form-group">
    <?php echo $form->emailField($login,'email', array('class'=>'form-control', 'placeholder'=>'Email', 'required'=>'')); ?>
</div>

<button type="submit" class="btn btn-primary block full-width m-b" aria-label="Send">Send</button>

<?php $this->endWidget(); ?>

<?php echo CHtml::link('<small>Login</small>', array('site/login')); ?>
