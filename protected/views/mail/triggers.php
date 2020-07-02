<div class="content-header">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h3>Mail Triggers</h3>
            </div>
            <div class="col-sm-6">
                <?php $form = $this->beginWidget('CActiveForm', array(
                    'action' => array(Yii::app()->controller->id . '/' . Yii::app()->controller->action->id),
                    'method' => 'GET'
                )); ?>
                <div class="input-group">
                    <?php echo $form->textField($trigger, 'search', array('class' => 'form-control', 'placeholder' => 'Search...')); ?>
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
        'dataProvider'=>$trigger->search(),
        'summaryText'=>'',
        'pager' => array(
            'header' => '',
            'htmlOptions'=>array(
                'class'=>'pagination',
            ),
            'selectedPageCssClass'=>'active',
        ),
        'columns'=>array(
            array(
                'name'=>'group',
                'type'=>'raw',
                'value'=>'CHtml::link($data->group, array("mail/triggerView", "id"=>$data->id))',
            ),
            array(
                'name'=>'name',
                'type'=>'raw',
                'value'=>'CHtml::link($data->name, array("mail/triggerView", "id"=>$data->id))',
            ),
            array(
                'name'=>'subject',
                'type'=>'raw',
                'value'=>'CHtml::link($data->subject, array("mail/triggerView", "id"=>$data->id))',
            ),
            array(
                'name'=>'status',
                'type'=>'raw',
                'value'=>'CHtml::link($data->getListLabel("listStatus",$data->status), array("mail/triggerView", "id"=>$data->id))',
            ),
            array(
                'name'=>'ref',
                'type'=>'raw',
                'value'=>'CHtml::link($data->ref, array("mail/triggerView", "id"=>$data->id))',
            ),
        ),
    )); ?>
</div>
