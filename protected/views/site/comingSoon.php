<?php
/* @var $this SiteController */
?>

<div class="container">
    <div class="row">
        <div class="col-sm-4 col-sm-push-4 text-center">
            <h2>Coming Soon<br /><small>Under Development</small></h2>
            <p>The page you are looking for is currently in production. We hope to have it up and running soon.</p>
            <?php echo CHtml::link(Yii::app()->name.' Home', array('site/index'), array('class' => 'btn btn-primary btn-lg')); ?>
        </div>
    </div>
</div>
