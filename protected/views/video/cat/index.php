<div class="content-header">
    <div class="container">
        <h3>Video Category List</h3>
    </div>
</div>

<div class="container">
    <?php $this->widget('zii.widgets.grid.CGridView', array(
        'dataProvider' => $model->search(['paginationSize' => 50]),
        'columns' => array(
            'name',
            array(
                'header' => '',
                'type' => 'raw',
                'value' => 'CHtml::link("<span class=\"fa fa-pencil\"></span>", array("video/editCat", "id"=>$data->id), array("class" => "btn btn-primary"))',
            ),
        ),
    )); ?>
</div>
