<?php
$form = $this->beginWidget('CActiveForm', array(
    'method' => 'post',
    'htmlOptions' => array('class' => 'form-horizontal'),
));
?>

<div class="content-header">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h3>Access Manager</h3>
            </div>
            <div class="col-sm-6 text-right">
                <button class="btn btn-primary" type="submit" aria-label="Save"><i class="fa fa-plus"></i> Save <?php echo ucfirst($type); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="form-group">
        <?php echo CHtml::label('Name', ucfirst($type) . '_name', array('class' => 'col-sm-3 control-label')); ?>
        <div class="col-sm-9">
            <?php echo CHtml::textField(ucfirst($type) . '[name]', '', array('class' => 'form-control')); ?>
        </div>
    </div>

    <div class="form-group">
        <?php echo CHtml::label('Description', ucfirst($type) . '_description', array('class' => 'col-sm-3 control-label')); ?>
        <div class="col-sm-9">
            <?php echo CHtml::textArea(ucfirst($type) . '[description]', '', array('class' => 'form-control')); ?>
        </div>
    </div>

    <div class="form-group">
        <?php echo CHtml::label('Business Rule', ucfirst($type) . '_bizRule', array('class' => 'col-sm-3 control-label')); ?>
        <div class="col-sm-9">
            <?php echo CHtml::textArea(ucfirst($type) . '[bizRule]', '', array('class' => 'form-control')); ?>
        </div>
    </div>
</div>
<?php $this->endWidget(); ?>
