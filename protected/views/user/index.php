<div class="content-header">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h3>Account List</h3>
            </div>
            <div class="col-sm-6">
                <?php $form = $this->beginWidget('CActiveForm', array(
                    'action' => array(Yii::app()->controller->id . '/' . Yii::app()->controller->action->id),
                    'method' => 'GET'
                )); ?>
                <div class="input-group">
                    <?php echo $form->textField($user, 'search', array('class' => 'form-control', 'placeholder' => 'Search...')); ?>
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="submit" aria-label="Search"><i class="fa fa-search"></i></button>
                    </span>
                </div>
                <?php $this->endWidget(); ?>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <?php $this->widget('zii.widgets.grid.CGridView', array(
        'dataProvider' => $user->search(['paginationSize' => 50]),
        'columns' => array(
            array(
                'header' => 'Name',
                'type' => 'raw',
                'value' => '$data->company."<br><strong>".$data->fullName."</strong>"',
            ),
            array(
                'header' => 'Contact',
                'type' => 'raw',
                'value' => '$data->email."<br>".$data->phone',
            ),
            array(
                'header' => 'Account',
                'type' => 'raw',
                'value' => '$data->gridAccount()',
            ),
            array(
                'header' => 'Subscriptions',
                'type' => 'raw',
                'value' => 'DbUserService::model()->gridTool($data->id)',
            ),
            array(
                'header' => 'created',
                'type' => 'raw',
                'value' => 'Yii::app()->format->date($data->created)',
            ),
            array(
                'header' => '',
                'type' => 'raw',
                'value' => 'CHtml::link("<span class=\"fa fa-pencil\"></span>", array("user/edit", "id"=>$data->id), array("class" => "btn btn-primary"))',
            ),
        ),
    )); ?>
</div>
