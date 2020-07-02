<?php
/* @var $this SiteController */
/* @var $error array */
?>

<div class="container">
    <div class="row">
        <div class="col-sm-4 col-sm-push-4 text-center">
            <h2>404<br /><small>Page Not Found</small></h2>
            <p>The page you are looking for could not be found. Please use our websites navigation to find the page you are looking for.</p>
            <?php echo CHtml::link(Yii::app()->name.' Home', array('site/index'), array('class' => 'btn btn-primary btn-lg')); ?>
        </div>
    </div>
</div>
