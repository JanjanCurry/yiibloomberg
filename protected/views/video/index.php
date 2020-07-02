<div class="content-header">
    <div class="container">
        <h3>Video List</h3>
    </div>
</div>

<div class="container">
    <?php $this->widget('zii.widgets.grid.CGridView', array(
        'dataProvider' => $model->search(['paginationSize' => 50]),
        'columns' => array(
            'video',
            'title',
            array(
                'header' => '',
                'type' => 'raw',
                'value' => 'CHtml::link("<span class=\"fa fa-pencil\"></span>", array("video/edit", "id"=>$data->id), array("class" => "btn btn-primary"))',
            ),
        ),
    )); ?>
</div>
