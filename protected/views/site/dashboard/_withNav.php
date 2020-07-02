<div class="form-group">
    <?php echo CHtml::dropDownList('dash-nav', 'spark', [
        'spark' => 'Performance',
        'forecast' => '3 Month Forecast vs Last Year',
        'g10' => 'Economic Indicators',
        'outlook' => 'Category Outlook',
    ], array(
        'class' => 'form-control selectpicker',
    )); ?>
</div>

<div class="dash-tabs tab-content">
    <div class="tab-pane active" id="spark">
        <?php $this->renderPartial('//site/dashboard/_spark', ['data' => $sparklines]); ?>
    </div>

    <div class="tab-pane" id="forecast">
        <?php $this->renderPartial('//site/dashboard/_forecast', ['data' => $lastYear]); ?>
    </div>

    <div class="tab-pane" id="g10">
        <?php $this->renderPartial('//site/dashboard/_g10'); ?>
    </div>

    <div class="tab-pane" id="outlook">
        <?php $this->renderPartial('//site/dashboard/_outlook', ['data' => $catOutlook]); ?>
    </div>
</div>