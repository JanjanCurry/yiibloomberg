<?php
/* @var $this SiteController */
/* @var $login LoginForm */
/* @var $form CActiveForm */

?>

<?php $form = $this->beginWidget('CActiveForm'); ?>

<?php echo $form->errorSummary($this->user, '', null, array('class' => 'alert alert-danger text-left')); ?>

    <h3>Terms of Use</h3>
    <div class="form-group">
        <label class="control-label">
            <?php echo $form->checkBox($this->user, 'terms'); ?>
            I agree to the Terms of Use
        </label>
    </div>

    <div class="form-group">
        <?php echo CHtml::link('View Terms of Use', 'https://www.completeintel.com/index.php/terms-of-use/', array('class' => 'btn btn-sm btn-danger', 'target' => '_blank', 'rel' => 'noopener')); ?>
    </div>

    <button type="submit" class="btn btn-primary btn-block" aria-label="Continue">Continue</button>

<?php $this->endWidget(); ?>