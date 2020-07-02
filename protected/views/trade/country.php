<div class="content-header wow slideInDown">
    <div class="container">
        <h3><span class="fa fa-circle" style="color: #<?php echo $country->color; ?>"  data-toggle="tooltip" data-placement="bottom" title="Country Reference Color"></span> <?php echo $country->country; ?> (<?php echo $country->ccode3; ?>)</h3>
    </div>
</div>

<div class="container wow zoomIn">
    <?php $chart = $this->widget('application.widgets.TradeWidget', $chartOptions); ?>

    <div class="padding-top-30">
        <?php $this->renderPartial('//common/helpBox', ['cat' => 'economics']); ?>
    </div>
</div>
