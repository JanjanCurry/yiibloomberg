<div class="content-header">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h3>All User Notifications</h3>
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
                <?php $this->endWidget(); ?>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <?php $this->widget('zii.widgets.grid.CGridView', array(
        'dataProvider' => $model->search(['paginationSize' => 50]),
        'columns' => array(
            array(
                'header' => 'Added',
                'type' => 'raw',
                'value' => 'Yii::app()->format->date($data->created)',
            ),
            array(
                'header' => 'Read',
                'type' => 'raw',
                'value' => '($data->status == 3 ? "<i class=\'fa fa-circle-o\' data-toggle=\'tooltip\' title=\'Read\'></i>" : 
                    ($data->status == 2 ? "<i class=\'fa fa-circle\' data-toggle=\'tooltip\' title=\'Seen\'></i>" : 
                    "<i class=\'fa fa-circle text-accent\' data-toggle=\'tooltip\' title=\'Unseen\'></i>" ))',
            ),
            array(
                'header' => 'User',
                'type' => 'raw',
                'value' => '$data->user->company."<br><strong>".$data->user->fullName."</strong>"',
            ),
            array(
                'header' => 'Notification',
                'type' => 'raw',
                'value' => 'nl2br($data->message)',
            ),
        ),
    )); ?>
</div>
