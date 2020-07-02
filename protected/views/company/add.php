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
                <h3>Add Company</h3>
            </div>
            <div class="col-sm-6 text-right">
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

</div>
<?php $this->endWidget(); ?>
