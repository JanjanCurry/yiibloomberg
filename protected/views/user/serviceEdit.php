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
                <h3>Edit Subscription</h3>
            </div>
            <div class="col-sm-6 text-right">
                <?php echo CHtml::link('<i class="fa fa-chevron-left"></i> User', array('user/edit', 'id' => $service->userId), array('class' => 'btn btn-primary')); ?>
                <?php echo CHtml::link('<i class="fa fa-trash"></i> Delete', array('user/serviceDelete', 'id' => $service->id), array('class' => 'btn btn-danger delete-confirm-link')); ?>
                <button type="submit" class="btn btn-primary" aria-label="Save"><i class="fa fa-save"></i> Save</button>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <?php echo $form->errorSummary($service, '', null, array('class' => 'alert alert-primary')); ?>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <?php echo $form->labelEx($service, 'tool', array('class' => 'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->dropDownList($service, 'tool', $service->listTool(), array('class' => 'form-control selectpicker',)); ?>
                    <?php echo $form->error($service, 'tool'); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($service, 'level', array('class' => 'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->dropDownList($service, 'level', $service->listLevel(), array('class' => 'form-control selectpicker',)); ?>
                    <?php echo $form->error($service, 'level'); ?>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                <?php echo $form->labelEx($service, 'expire', array('class' => 'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <span class="input-group">
                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                        <?php
                        $service->convertTime('expire', 'string');
                        echo $form->textField($service, 'expire', array('class' => 'form-control datepicker')); ?>
                    </span>
                    <?php echo $form->error($service, 'expire'); ?>
                </div>
            </div>
        </div>

    </div>

    <hr/>
    <h4>Subscriptions</h4>

    <?php
    $services = new DbUserService();
    $services->unsetAttributes();
    $services->userId = $service->userId;

    $this->widget('zii.widgets.grid.CGridView', array(
        'dataProvider' => $services->search(),
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
                'header' => 'Tool',
                'type' => 'raw',
                'value' => '$data->getListLabel("listTool",$data->tool)',
            ),
            array(
                'header' => 'Service level',
                'type' => 'raw',
                'value' => '$data->getListLabel("listLevel",$data->level)',
            ),
            array(
                'header' => 'Date Added',
                'type' => 'raw',
                'value' => 'Yii::app()->format->date($data->created)',
            ),
            array(
                'header' => 'Expiry Date',
                'type' => 'raw',
                'value' => '(!empty($data->expire) ? Yii::app()->format->date($data->expire) : "-")',
            ),
            array(
                'header' => '',
                'type' => 'raw',
                'value' => 'CHtml::link("<span class=\"fa fa-pencil\"></span>", array("user/serviceEdit", "id"=>$data->id), array("class" => "btn btn-primary")).CHtml::link("<span class=\"fa fa-trash\"></span>", array("user/serviceDelete", "id"=>$data->id), array("class" => "btn btn-danger"))',
            ),

        ),
        'htmlOptions' => array(//'class' => 'table table-hover items'
        ),
    )); ?>


</div>
<?php $this->endWidget(); ?>
