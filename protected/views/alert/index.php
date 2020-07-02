<div class="content-header">
    <div class="container">
        <h3 class="text-center"><i class="fa fa-bell"></i> Your Notifications</h3>
    </div>
</div>

<div class="container alert-items-full">
    <?php
    $this->widget('zii.widgets.CListView', array(
        'dataProvider'=>$model->search(['paginationSize' => 'all']),
        'emptyText' => 'No Notifications',
        'summaryText' => '',
        'itemView' => 'index/_item',
    ));
    ?>
</div>
