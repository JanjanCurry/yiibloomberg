<?php $form = $this->beginWidget('CActiveForm', array(
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'htmlOptions' => array(
        'class' => 'form-horizontal',
        'autocomplete' => 'off',
    ),
)); ?>

<div class="content-header">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h3>Edit Company</h3>
            </div>
            <div class="col-sm-6 text-right">
                <?php echo CHtml::link('<i class="fa fa-trash"></i> Delete', array('company/delete', 'id' => $model->id), array('class' => 'btn btn-danger delete-confirm-link')); ?>
                <button type="submit" class="btn btn-primary" aria-label="Save"><i class="fa fa-save"></i> Save</button>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <?php echo $form->errorSummary($model, '', null, array('class' => 'alert alert-danger')); ?>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <?php echo $form->labelEx($model, 'name', array('class' => 'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->textField($model, 'name', array('class' => 'form-control')); ?>
                    <?php echo $form->error($model, 'name'); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="sep"></div>

    <?php
    $users = new DbUser();
    $users->unsetAttributes();
    $users->companyId = $model->id;

    $this->widget('zii.widgets.grid.CGridView', array(
        'dataProvider' => $users->search(),
        'summaryText' => '',
        'pager' => array(
            'header' => '',
            'htmlOptions' => array(
                'class' => 'pagination',
            ),
            'selectedPageCssClass' => 'active',
        ),
        'columns' => array(
            array(
                'header'=>'Name',
                'type'=>'raw',
                'value'=>'$data->company."<br><strong>".$data->fullName."</strong>"',
            ),
            array(
                'header'=>'Contact',
                'type'=>'raw',
                'value'=>'$data->email."<br>".$data->phone',
            ),
            array(
                'header'=>'Account',
                'type'=>'raw',
                'value'=>'$data->gridAccount()',
            ),
            array(
                'header'=>'Subscriptions',
                'type'=>'raw',
                'value'=>'DbUserService::model()->gridTool($data->id)',
            ),
            array(
                'header'=>'created',
                'type'=>'raw',
                'value'=>'Yii::app()->format->date($data->created)',
            ),
            array(
                'header'=>'',
                'type'=>'raw',
                'value' => 'CHtml::link("<span class=\"fa fa-pencil\"></span>", array("user/edit", "id"=>$data->id), array("class" => "btn btn-primary"))',
            ),

        ),
        'htmlOptions' => array(//'class' => 'table table-hover items'
        ),
    )); ?>

</div>
<?php $this->endWidget(); ?>
