<div class="content-header wow slideInDown">
    <div class="container">
        <h3>Supply Chain and Bill of Material Forecast</h3>
    </div>
</div>

<div class="container wow zoomIn">
    <?php $chart = $this->widget('application.widgets.BomWidget', $chartOptions); ?>
</div>

<?php $this->renderPartial('//common/feedback'); ?>