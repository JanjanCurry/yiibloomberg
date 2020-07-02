<div class="content-header">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h3>Activity Log</h3>
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
            </div>
            <?php $this->endWidget(); ?>
        </div>
    </div>
</div>

<div class="container">
    <?php $this->widget('zii.widgets.grid.CGridView', array(
        'dataProvider'=>$user->search(),
        'columns'=>array(
            array(
                'header'=>'Time',
                'name'=>'',
                'type'=>'raw',
                'value'=>'Yii::app()->format->datetime($data->created)',
            ),
            array(
                'header'=>'Name',
                'name'=>'',
                'type'=>'raw',
                'value'=>'CHtml::link($data->user->fullName, array("user/edit", "id"=>$data->user->id))',
            ),
            array(
                'header' => 'Page',
                'name'=>'',
                'type'=>'raw',
                'value'=>'$data->controller."/".$data->action',
            ),
            array(
                'header' => 'URL',
                'name'=>'',
                'type'=>'raw',
                'value'=>'CHtml::link($data->url, $data->url, array("target" => "_blank"))',
            ),

        ),
    )); ?>
</div>
