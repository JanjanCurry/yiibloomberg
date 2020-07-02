<?php
/* @var $this SiteController */
/* @var $login LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle=Yii::app()->name . ' - Forgot Password';
?>

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'login-form',
    'action' => array('site/login'),
    'enableClientValidation'=>true,
    'clientOptions'=>array(
        'validateOnSubmit'=>true,
    ),
    'htmlOptions' => array('class'=>'m-t'),
)); ?>

<p>Forgot password email sent. Instructions for resetting your password have been sent to you and should arrive within a few minutes time.</p>

<?php echo $form->errorSummary($login, '', null, array('class'=>'alert alert-danger text-left')); ?>

    <div class="form-group">
        <?php echo $form->textField($login,'email', array('class'=>'form-control', 'placeholder'=>'Email address', 'required'=>'')); ?>
    </div>

    <div class="form-group">
        <?php echo $form->passwordField($login,'password', array('class'=>'form-control', 'placeholder'=>'Password', 'required'=>'')); ?>
    </div>

    <button type="submit" class="btn btn-primary block full-width m-b" aria-label="Login">Login</button>

<?php $this->endWidget(); ?>

<?php echo CHtml::link('<small>Forgot password?</small>', array('site/forgotPassword')); ?>