<?php
/* @var $this MailController */
/* @var $login LoginForm */
/* @var $form CActiveForm */
?>

<?php $form = $this->beginWidget('CActiveForm', array(
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
    ),
    'htmlOptions' => array('class' => 'm-t'),
)); ?>

<p>Enter your new password into the 2 fields below and then click save to change your password.</p>

<?php echo $form->errorSummary($login, '', null, array('class' => 'alert alert-danger text-left')); ?>

    <div class="form-group">
        <?php echo $form->passwordField($login, 'password', array('class' => 'form-control', 'placeholder' => 'Password', 'required' => '')); ?>
    </div>

    <div class="form-group">
        <?php echo $form->passwordField($login, 'passwordConfirm', array('class' => 'form-control', 'placeholder' => 'Confirm Password', 'required' => '')); ?>
    </div>

    <button type="submit" class="btn btn-primary btn-block" aria-label="Save">Save Password</button>

<?php $this->endWidget(); ?>

<?php echo CHtml::link('<small>Login</small>', array('site/login')); ?>