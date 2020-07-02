<?php
Yii::app()->controller->registerFile('js', 'https://cloud.tinymce.com/stable/tinymce.min.js');

$form = $this->beginWidget('CActiveForm', array(
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'htmlOptions' => array(
        'class' => 'form-horizontal',
    ),
)); ?>

<div class="content-header">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h3>Add User Notifcations</h3>
            </div>
            <div class="col-sm-6 text-right">
                <button type="submit" class="btn btn-primary" aria-label="Search"><i class="fa fa-filter"></i> Filter</button>
                <button type="submit" class="btn btn-primary save-btn" aria-label="Save"><i class="fa fa-save"></i> Save</button>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <?php echo $form->hiddenField($model, 'save'); ?>

    <h4>Notification</h4>
    <div class="form-group">
        <?php echo $form->label($model, 'subject', array('class' => 'col-xs-4 control-label')); ?>
        <div class="col-xs-8">
            <?php echo $form->textField($model, 'subject', array('class' => 'form-control')); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-xs-12">
            <?php echo $form->textArea($model, 'message', array(
                'class' => 'form-control',
                'rows' => 5,
            )); ?>
        </div>
    </div>

    <div class="sep"></div>

    <h4>Filter user list</h4>

    <div class="form-group">
        <?php echo $form->label($model, 'search', array('class' => 'col-xs-4 control-label')); ?>
        <div class="col-xs-8">
            <?php echo $form->textField($model, 'search', array('class' => 'form-control')); ?>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label($model, 'accountType', array('class' => 'col-xs-4 control-label')); ?>
        <div class="col-xs-8">
            <?php echo $form->dropDownList($model, 'accountType', $model->listAccountType(), array(
                'class' => 'form-control selectpicker',
                'multiple' => 'multiple',
            )); ?>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label($model, 'status', array('class' => 'col-xs-4 control-label')); ?>
        <div class="col-xs-8">
            <?php echo $form->dropDownList($model, 'status', $model->listStatus(), array(
                'class' => 'form-control selectpicker',
                'prompt' => 'NOTHING SELECTED',
            )); ?>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label($model, 'subscription', array('class' => 'col-xs-4 control-label')); ?>
        <div class="col-xs-8">
            <?php echo $form->dropDownList($model, 'subscription', $model->listSubscription(), array(
                'class' => 'form-control selectpicker',
                'multiple' => 'multiple',
            )); ?>
        </div>
    </div>

    <div class="sep"></div>

    <h4>Notification will be sent to the following users</h4>

    <?php $this->widget('zii.widgets.grid.CGridView', array(
        'dataProvider'=>$model->search(['paginationSize' => 'all']),
        'columns'=>array(
            array(
                'header'=>'Name',
                'type'=>'raw',
                'value'=>'$data->company."<br><strong>".$data->fullName."</strong>"',
            ),
            array(
                'header'=>'Email',
                'type'=>'raw',
                'value'=>'$data->email',
            ),
            array(
                'header'=>'Subscriptions',
                'type'=>'raw',
                'value'=>'DbUserService::model()->gridTool($data->id)',
            ),
        ),
    )); ?>
</div>
<?php $this->endWidget(); ?>
