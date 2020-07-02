<div class="content-header wow slideInDown">
    <div class="container">
        <h3>Trade</h3>
    </div>
</div>

<div class="container wow zoomIn">
    <?php $chart = $this->widget('application.widgets.TradeWidget', $chartOptions); ?>

    <div class="padding-top-30">
        <?php $this->renderPartial('//common/helpBox', ['cat' => 'economics']); ?>
    </div>
</div>
