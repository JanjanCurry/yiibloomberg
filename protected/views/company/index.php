<div class="content-header">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h3>Company List</h3>
            </div>
            <div class="col-sm-6">
                <?php $form = $this->beginWidget('CActiveForm', array(
                    'action' => array(Yii::app()->controller->id . '/' . Yii::app()->controller->action->id),
                    'method' => 'GET'
                )); ?>
                <div class="input-group">
                    <?php echo $form->textField($model, 'search', array('class' => 'form-control', 'placeholder' => 'Search...')); ?>
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="submit" aria-label="Search"><i class="fa fa-search"></i></button>
                    </span>
                </div>
            </div>
            <?php $this->endWidget(); ?>
        </div>
    </div>
</div>

<div class="container">
    <?php $this->widget('zii.widgets.grid.CGridView', array(
        'dataProvider' => $model->search(['paginationSize' => 50]),
        'columns' => array(
            array(
                'header' => 'Name',
                'type' => 'raw',
                'value' => '$data->name',
            ),
            array(
                'header' => 'Accounts',
                'type' => 'raw',
                'value' => '$data->calcAccounts($data->name)',
            ),
            array(
                'header'=>'',
                'type'=>'raw',
                'value' => 'CHtml::link("<span class=\"fa fa-pencil\"></span>", array("company/edit", "id"=>$data->id), array("class" => "btn btn-primary"))',
            ),
        ),
    )); ?>
</div>
