<?php
/* @var $this SiteController */
/* @var $login LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle=Yii::app()->name . ' - Unsubscribe';
?>

<?php if(!$valid){ ?>


<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'login-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array(
        'validateOnSubmit'=>true,
    ),
    'htmlOptions' => array('class'=>'m-t'),
)); ?>

<h4>Unsubscribe</h4>
<p>Enter your email address to unsubscribe from our mailing lists.</p>

<?php echo $form->errorSummary($login, '', null, array('class'=>'alert alert-danger text-left')); ?>

<div class="form-group">
    <?php echo $form->emailField($login,'email', array('class'=>'form-control', 'placeholder'=>'Email', 'required'=>'')); ?>
</div>

<button type="submit" class="btn btn-primary btn-block" aria-label="Send">Unsubscribe</button>
    <?php echo CHtml::link('<small>Login</small>', array('site/login')); ?>

<?php $this->endWidget(); ?>

<?php }else{ ?>
    <h4>You have been unsubscribed</h4>
    <p>Please allow up to 30 days for this request to be fully processed.</p>
    <?php echo CHtml::link('Login', array('site/login'), array('class' => 'btn btn-primary btn-block')); ?>
<?php } ?>

