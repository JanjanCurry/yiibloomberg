<?php $form = $this->beginWidget('CActiveForm', array(
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
                <h3>User Export</h3>
            </div>
            <div class="col-sm-6 text-right">
                <button type="submit" class="btn btn-primary" aria-label="Search"><i class="fa fa-list"></i> Preview</button>
                <button type="submit" class="btn btn-primary export-btn" aria-label="Export"><i class="fa fa-download"></i> Export</button>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <?php echo $form->hiddenField($model, 'export'); ?>

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
