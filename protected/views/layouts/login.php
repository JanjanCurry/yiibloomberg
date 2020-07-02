<!DOCTYPE html>
<html lang="en">
<?php $this->renderPartial('//layouts/main/head'); ?>

<body>
<div class="container-fluid wow fadeIn">
    <div class="row">
        <div class="col-sm-4 col-sm-push-4 col-lg-2 col-lg-push-5 text-center">
            <h1 class="logo-name">
                <?php echo CHtml::link(
                    CHtml::image(Yii::app()->baseUrl.'/images/ci-logo.svg', Yii::app()->name, array('class'=>'l-fw')),
                    array('site/index'),
                    array('style'=>'padding:0;')
                ); ?>
            </h1>

            <?php echo $content; ?>

            <p><small><?php echo Yii::app()->name; ?> &copy; <?php echo date('Y'); ?></small></p>
        </div>
    </div>
</div>

<?php $this->renderPartial('//layouts/main/flashMessage'); ?>

<span id="deferred-css" data-src='<?php echo (!empty($this->deferredCss) ? CJSON::encode($this->deferredCss) : ''); ?>'></span>

<span class="hidden" id="siteData" data-url="<?php echo Yii::app()->getBaseUrl(true); ?>"></span>
</body>
</html>