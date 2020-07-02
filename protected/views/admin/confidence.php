<?php $form = $this->beginWidget('CActiveForm', array(
    'enableAjaxValidation' => false,
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
                <h3>Market Confidence</h3>
            </div>
            <div class="col-sm-6 text-right">
                <button type="submit" class="btn btn-primary" aria-label="Save"><i class="fa fa-save"></i> Save</button>
            </div>
        </div>
    </div>
</div>

<div class="container">

    <div class="alert alert-info">
        <p>
            Calculations will be carried out in the follow format:<br>
            [Correlations] [Symbol] [Limit]<br><br>

            Correlations = Data from the Correlations column in the database table<br>
            Symbol = The selected symbol below<br>
            Limit = The value entered below
        </p>
        <hr />
        <p>
            Unless BOTH high and low limits are set, they will default to:<br>
            Low: 0.7<br>
            High: 0.851
        </p>
    </div>

    <h4>Commodities</h4>
    <h5><span class="fa fa-circle text-danger"></span> Low Confidence</h5>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <?php echo CHtml::label('Symbol', 'data[commodity][low][symbol]', array('class' => 'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->dropDownList($model, 'data[commodity][low][symbol]', [
                        '<=' => '<=',
                        '<' => '<',
                    ], array('class' => 'form-control selectpicker',)); ?>
                    <?php echo $form->error($model, 'data[commodity][low][symbol]'); ?>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <?php echo CHtml::label('Limit', 'data[commodity][low][limit]', array('class' => 'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->textField($model, 'data[commodity][low][limit]', array('class' => 'form-control')); ?>
                    <?php echo $form->error($model, 'data[commodity][low][limit]'); ?>
                </div>
            </div>
        </div>
    </div>

    <h5><span class="fa fa-circle text-success"></span> High Confidence</h5>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <?php echo CHtml::label('Symbol', 'data[commodity][high][symbol]', array('class' => 'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->dropDownList($model, 'data[commodity][high][symbol]', [
                        '>=' => '>=',
                        '>' => '>',
                    ], array('class' => 'form-control selectpicker',)); ?>
                    <?php echo $form->error($model, 'data[commodity][high][symbol]'); ?>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <?php echo CHtml::label('Limit', 'data[commodity][high][limit]', array('class' => 'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->textField($model, 'data[commodity][high][limit]', array('class' => 'form-control')); ?>
                    <?php echo $form->error($model, 'data[commodity][high][limit]'); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="sep"></div>

    <h4>Currencies</h4>
    <h5><span class="fa fa-circle text-danger"></span> Low Confidence</h5>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <?php echo CHtml::label('Symbol', 'data[currency][low][symbol]', array('class' => 'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->dropDownList($model, 'data[currency][low][symbol]', [
                        '<=' => '<=',
                        '<' => '<',
                    ], array('class' => 'form-control selectpicker',)); ?>
                    <?php echo $form->error($model, 'data[currency][low][symbol]'); ?>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <?php echo CHtml::label('Limit', 'data[currency][low][limit]', array('class' => 'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->textField($model, 'data[currency][low][limit]', array('class' => 'form-control')); ?>
                    <?php echo $form->error($model, 'data[currency][low][limit]'); ?>
                </div>
            </div>
        </div>
    </div>

    <h5><span class="fa fa-circle text-success"></span> High Confidence</h5>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <?php echo CHtml::label('Symbol', 'data[currency][high][symbol]', array('class' => 'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->dropDownList($model, 'data[currency][high][symbol]', [
                        '>=' => '>=',
                        '>' => '>',
                    ], array('class' => 'form-control selectpicker',)); ?>
                    <?php echo $form->error($model, 'data[currency][high][symbol]'); ?>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <?php echo CHtml::label('Limit', 'data[currency][high][limit]', array('class' => 'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->textField($model, 'data[currency][high][limit]', array('class' => 'form-control')); ?>
                    <?php echo $form->error($model, 'data[currency][high][limit]'); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="sep"></div>

    <h4>Equities</h4>
    <h5><span class="fa fa-circle text-danger"></span> Low Confidence</h5>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <?php echo CHtml::label('Symbol', 'data[equity][low][symbol]', array('class' => 'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->dropDownList($model, 'data[equity][low][symbol]', [
                        '<=' => '<=',
                        '<' => '<',
                    ], array('class' => 'form-control selectpicker',)); ?>
                    <?php echo $form->error($model, 'data[equity][low][symbol]'); ?>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <?php echo CHtml::label('Limit', 'data[equity][low][limit]', array('class' => 'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->textField($model, 'data[equity][low][limit]', array('class' => 'form-control')); ?>
                    <?php echo $form->error($model, 'data[equity][low][limit]'); ?>
                </div>
            </div>
        </div>
    </div>

    <h5><span class="fa fa-circle text-success"></span> High Confidence</h5>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <?php echo CHtml::label('Symbol', 'data[equity][high][symbol]', array('class' => 'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->dropDownList($model, 'data[equity][high][symbol]', [
                        '>=' => '>=',
                        '>' => '>',
                    ], array('class' => 'form-control selectpicker',)); ?>
                    <?php echo $form->error($model, 'data[equity][high][symbol]'); ?>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <?php echo CHtml::label('Limit', 'data[equity][high][limit]', array('class' => 'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->textField($model, 'data[equity][high][limit]', array('class' => 'form-control')); ?>
                    <?php echo $form->error($model, 'data[equity][high][limit]'); ?>
                </div>
            </div>
        </div>
    </div>


</div>
<?php $this->endWidget(); ?>
