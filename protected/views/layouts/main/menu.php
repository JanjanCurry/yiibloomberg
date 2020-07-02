<?php
$upgrade = true;

//$upgradeUrl = 'https://www.completeintel.com/index.php/subscription/';
$upgradeUrl = 'https://www.completeintel.com/index.php/contact/';
$tool = DbUserService::convertTool(Yii::app()->controller->id);
if (!empty($tool)) {
    if (Yii::app()->user->checkToolAccess($tool, 'service-pro')) {
        $upgrade = false;
    }

    if ($tool == 'tra') {
        $upgradeUrl = 'https://www.completeintel.com/economics';
    } else {
        $upgradeUrl = 'https://www.completeintel.com/markets';
    }
} else {
    $upgrade = false;
    foreach (DbUserService::model()->listTool() as $key => $val) {
        if (!Yii::app()->user->checkToolAccess($key, 'service-pro')) {
            $upgrade = true;
        }
    }
}

?>

<div class="navbar navbar-fixed-top" role="navigation">
    <div class="container-fluid">

        <div class="row">
            <div class="col-xs-9 col-sm-3 navbar-logo">
                <?php echo CHtml::link(CHtml::image(Yii::app()->baseUrl . '/images/ci-logo.svg'), array('site/index'), array('aria-label' => Yii::app()->name)); ?>
            </div>

            <div class="<?php echo(!$upgrade ? 'col-xs-3 col-sm-push-6' : 'col-xs-3 col-sm-9 col-md-4 col-md-push-5') ?>">
                <ul class="nav navbar-nav navbar-right navbar-standard hidden-xs">

                    <li class="user-alert-list dropdown">
                        <?php $this->renderPartial('//layouts/main/alerts'); ?>
                    </li>

                    <li class="user-name"><?php echo CHtml::link($this->user->fName, array('user/view', 'id' => Yii::app()->user->id)); ?></li>
                    <li class="<?php echo($upgrade ? '' : 'hidden') ?>">
                        <?php echo CHtml::link('<span class="fa fa-plus-circle"></span> Upgrade To Pro', $upgradeUrl, array(
                            'class' => 'btn btn-accent',
                            'target' => '_blank',
                            'rel' => 'noopener',
                        )); ?>
                    </li>
                    <li>
                        <a href="#" class="navbar-toggle main-menu-toggle" aria-label="Toggle site navigation">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </a>
                    </li>
                </ul>

                <div class="visible-xs">
                    <a href="#" class="navbar-toggle main-menu-toggle" aria-label="Toggle site navigation">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                </div>
            </div>

            <div class="<?php echo($upgrade ? 'col-xs-12 col-md-5 col-md-pull-4' : 'col-xs-12 col-sm-6 col-sm-pull-3') ?> navbar-search">
                <?php $form = $this->beginWidget('CActiveForm', array(
                    'id' => 'search_form',
                    'action' => Yii::app()->createUrl('site/search'),
                    'method' => 'post',
                )); ?>

                <div id="tour-step-1" class="typeahead-scrollable">
                    <?php echo CHtml::textField('search', null, array(
                        'class' => 'form-control',
                        'placeholder' => 'Country name/ISO code and/or indicator/macro',
                        'maxlength' => 255,
                        'aria-label' => 'Search for reports',
                    )); ?>
                </div>

                <?php $this->endWidget(); ?>

            </div>
        </div>

    </div>
</div>

<div class="wrap-menu">
    <div class="wrap-menu-inner">
        <ul class="list-unstyled main-menu">
            <?php if (Yii::app()->user->checkAccess('menu-admin')) { ?>
                <li><?php echo CHtml::link('<span class="fa fa-power-off"></span> Admin', array('admin/index')); ?></li>
            <?php } ?>
            <li><?php echo CHtml::link('<span class="fa fa-dashboard"></span> Dashboard <span class="badge badge-primary">BETA</span>', array('site/dashboard')); ?></li>

            <li class="sep"></li>
            <li>
                <?php echo CHtml::link('<span class="fa fa-tint"></span> Commodities', array('commodity/index')); ?>
                <?php //echo CHtml::link('<span class="fa fa-tint"></span> Commodities', array('site/commodities')); ?>
            </li>
            <li>
                <?php echo CHtml::link('<span class="fa fa-usd"></span> Currencies', array('currency/index')); ?>
                <?php //echo CHtml::link('<span class="fa fa-usd"></span> Currencies', array('site/currencies')); ?>
            </li>
            <li>
                <?php echo CHtml::link('<span class="fa fa-balance-scale"></span> Equities', array('equity/index')); ?>
                <?php //echo CHtml::link('<span class="fa fa-balance-scale"></span> Equities', array('site/equities')); ?>
            </li>
            <li><?php echo CHtml::link('<span class="fa fa-cubes"></span> Market Reports <span class="badge badge-primary">BETA</span>', array('report/markets')); ?></li>
            <li class="sep"></li>
            <div id="tour-step-14">
                <li><?php echo CHtml::link('<span class="fa fa-info"></span> FAQ', array('site/faq')); ?></li>
                <li><?php echo CHtml::link('<span class="fa fa-info"></span> Methodology', array('site/methodology')); ?></li>

                <li><?php echo CHtml::link('<span class="fa fa-question"></span> How To', array('site/howTo')); ?></li>

                <li><?php echo CHtml::link('<span class="fa fa-support"></span> Support', 'https://www.completeintel.com/contact/', array('rel' => 'noopener')); ?></li>
            </div>
            <li class="sep"></li>
            <li><?php echo CHtml::link('<span class="fa fa-user"></span> Account', array('user/view')); ?></li>
            <li><?php echo CHtml::link('<span class="fa fa-home"></span> Home', array('site/index')); ?></li>
            <li class="sep"></li>
            <li><?php echo CHtml::link('<span class="fa fa-sign-out"></span> Logout', array('site/logout')); ?></li>
        </ul>
    </div>
</div>