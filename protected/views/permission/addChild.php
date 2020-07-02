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
                <h3>Assign Child</h3>
            </div>
            <div class="col-sm-6 text-right">
                <button class="btn btn-primary" type="submit" aria-label="Save"><i class="fa fa-chain"></i> Save</button>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="form-group">
        <?php echo CHtml::label('Child', 'Item', array('class' => 'col-sm-3 control-label')); ?>
        <div class="col-sm-9">
            <?php echo CHtml::dropDownList('Item', '', Yii::app()->authManager->getPossibleChildren($parent), array('class' => 'form-control', 'empty' => '')); ?>
        </div>
    </div>>
</div>
<?php $this->endWidget(); ?>

