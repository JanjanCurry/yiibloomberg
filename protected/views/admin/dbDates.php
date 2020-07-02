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
                <h3>Database Dates</h3>
            </div>
            <div class="col-sm-6 text-right">
                <button type="submit" class="btn btn-primary" aria-label="Save"><i class="fa fa-save"></i> Save</button>
            </div>
        </div>
    </div>
</div>

<div class="container">

    <div class="form-group">
        <?php echo CHtml::label('Forecast Date', 'forecastDate', array('class' => 'col-sm-4 control-label')); ?>
        <div class="col-sm-8">
            <span class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <?php echo CHtml::textField('forecastDate', (!empty($model->data['forecastDate']) ? $model->data['forecastDate'] : ''), array('class' => 'form-control')); ?>
            </span>
            <p class="small">This date defines when foretasted data (i.e. dotted line) should begin from in Market reports.</p>
        </div>
    </div>

</div>
<?php $this->endWidget(); ?>
