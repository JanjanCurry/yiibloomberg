<div class="content-header wow slideInDown">
    <div class="container">
        <h3>Currencies<?php echo (!empty($currency) ? ': '.$currency->name: '');?></h3>
    </div>
</div>

<div class="container wow zoomIn">
    <?php $chart = $this->widget('application.widgets.MarketWidget', $chartOptions); ?>

    <div class="padding-top-30">
        <?php $this->renderPartial('//common/helpBox', ['cat' => 'markets']); ?>
    </div>
</div>

<?php $this->renderPartial('//common/feedback'); ?>