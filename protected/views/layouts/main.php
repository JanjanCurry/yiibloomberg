<!DOCTYPE html>
<html lang="en">
<?php $this->renderPartial('//layouts/main/head'); ?>

<body>
<div class="page-loading">
    <div class="page-loading-content">
        <div class="welcome <?php echo(!empty(Yii::app()->session['pageLoader']) ? '' : 'hidden'); ?>">
            <?php echo(!empty($this->user) ? '<h3>Hi ' . $this->user->fName . '</h3>' : ''); ?>
            <h4>Welcome to</h4>
            <?php echo CHtml::image(Yii::app()->baseUrl . '/images/ci-logo.svg'); ?>
            <h3>Superforecasting Streamlined<sup>&trade;</sup></h3>
            <div><i class="fa fa-spinner fa-spin"></i></div>
        </div>
        <div class="page-loading-default <?php echo(!empty(Yii::app()->session['pageLoader']) ? 'hidden' : ''); ?>">
            <?php /*
            <?php echo CHtml::image(Yii::app()->baseUrl . '/images/ci-logo.svg'); ?>
            <div><i class="fa fa-spinner fa-spin"></i></div>
 */ ?>
        </div>
    </div>
</div>
<?php unset(Yii::app()->session['pageLoader']); ?>

<div class="header">
    <?php $this->renderPartial('//layouts/main/menu'); ?>
</div>

<div class="content">
    <?php echo $content; ?>
    <div class="clearfix"></div>
</div>

<div class="footer">
    <div class="container">
        <div class="row">
            <div class="col-xs-6">
                <ul class="list-inline">
                    <li><?php echo CHtml::link('About', 'https://www.completeintel.com/about-us/'); ?></li>
                    <li><?php echo CHtml::link('Terms &amp; Conditions', 'https://www.completeintel.com/terms-of-use/'); ?></li>
                    <li><?php echo CHtml::link('Privacy Policy', 'https://www.completeintel.com/privacy/'); ?></li>
                    <li><?php echo CHtml::link('Contact', 'https://www.completeintel.com/contact/'); ?></li>
                </ul>
            </div>
            <div class="copyright col-xs-6 text-right">
                <p>&copy; <?php echo Yii::app()->name . ' ' . date('Y'); ?>, All Rights Reserved</p>
            </div>
        </div>
    </div>
</div>

<div class="ajaxLoading hidden">
    <div class="ajaxLoadingDisplay">
        <i class="fa fa-spinner fa-spin"></i> Loading...
    </div>
</div>

<div class="modal fade-scale" id="alertView" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                <div class="alert-content"></div>
            </div>
        </div>
    </div>
</div>

<?php $this->renderPartial('//layouts/main/flashMessage'); ?>

<span id="deferred-css" data-src='<?php echo (!empty($this->deferredCss) ? CJSON::encode($this->deferredCss) : ''); ?>'></span>

<span class="hidden" id="siteData"
      data-url="<?php echo Yii::app()->getBaseUrl(true); ?>"
      data-api="<?php echo Yii::app()->params['googleApiKey']; ?>"
      data-premium-url="<?php echo Yii::app()->params['url-pricing']; ?>"
      data-tour="<?php echo(!empty($this->user) ? $this->user->tour : 1); ?>"
      data-tour-login="<?php echo(!empty($this->user) ? $this->user->tourLogin : 1); ?>"
      data-adblock="1"
></span>


</body>
</html>