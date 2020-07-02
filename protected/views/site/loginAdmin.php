<?php
/* @var $this SiteController */
/* @var $login LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle=Yii::app()->name . ' - Login';
?>

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'login-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array(
        'validateOnSubmit'=>true,
    ),
)); ?>

<?php echo $form->errorSummary($login, '', null, array('class'=>'alert alert-danger text-left')); ?>

<div class="form-group">
    <?php echo $form->emailField($login,'email', array('class'=>'form-control', 'placeholder'=>'Email address')); ?>
</div>

<div class="form-group">
    <?php echo $form->passwordField($login,'password', array('class'=>'form-control', 'placeholder'=>'Password')); ?>
</div>

<button type="submit" class="btn btn-primary btn-block" aria-label="Login">Login</button>

<?php $this->endWidget(); ?>

<?php echo CHtml::link('<small>Forgot password?</small>', array('site/forgotPassword')); ?>

<?php /*?><div class="sep sep-md sep-blue"></div>

<div class="margin-top-20">
    <h4>Don't have an Account Yet?</h4>
    <?php echo CHtml::link('Sign Up', 'https://www.completeintel.com/index.php/subscription/', array('class' => 'btn btn-accent')); ?>
</div><?php */?>
