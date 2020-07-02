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

    <button type="submit" class="btn btn-primary btn-block" aria-label="Login">Login</button>

<?php $this->endWidget(); ?>