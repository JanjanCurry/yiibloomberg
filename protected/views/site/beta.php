<?php
/* @var $this SiteController */
/* @var $form CActiveForm */
?>

<?php $form = $this->beginWidget('CActiveForm'); ?>

<?php echo $form->errorSummary($this->user, '', null, array('class' => 'alert alert-danger text-left')); ?>

<h3>Complete Intelligence Markets (Beta)</h3>
<ul class="list-unstyled text-left">
    <li><i class="fa fa-circle text-primary"></i> Spot global market trends and inflection points</li>
    <li><i class="fa fa-circle text-primary"></i> Plan materials purchases for global supply chains</li>
    <li><i class="fa fa-circle text-primary"></i> Support currency hedging and treasury efforts</li>
    <li><i class="fa fa-circle text-primary"></i> Identify cross asset correlations and risk</li>
</ul>
<p class="text-italic">Sign me up to the exclusive beta trial!</p>

<div class="sep sep-md margin-top-30 margin-bottom-30"></div>

<h4>Beta Terms of Use</h4>
<div class="form-group">
    <label class="control-label" style="line-height: 20px">
        <?php echo $form->checkBox($this->user, 'termsBeta'); ?>
        I agree to the Terms of Use for <?php echo Yii::app()->params['name']; ?> beta features &amp; data
    </label>
</div>

<div class="form-group">
    <?php echo CHtml::link('View Beta Terms of Use', 'https://www.completeintel.com/index.php/terms-of-use/', array('class' => 'btn btn-sm btn-danger', 'target' => '_blank', 'rel' => 'noopener')); ?>
</div>

<button type="submit" class="btn btn-primary btn-block" aria-label="Continue">Continue</button>

<?php $this->endWidget(); ?>