<div class="row">
    <div class="col-md-6">
        <?php $this->renderPartial('//site/dashboard/_spark', ['data' => $sparklines]); ?>
    </div>

    <div class="col-md-6">
        <?php $this->renderPartial('//site/dashboard/_forecast', ['data' => $lastYear]); ?>
    </div>

</div>
<div class="row">

    <div class="col-md-6">
        <?php $this->renderPartial('//site/dashboard/_g10'); ?>
    </div>

    <div class="col-md-6">
        <?php $this->renderPartial('//site/dashboard/_outlook', ['data' => $catOutlook]); ?>
    </div>
</div>