<?php
$form = $this->beginWidget('CActiveForm', array(
    'method' => 'post',
));
?>

<div class="content-header">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h3>Edit <?php echo ucfirst($type); ?></h3>
            </div>
            <div class="col-sm-6 text-right">
                <?php echo CHtml::link('<i class="fa fa-times"></i> Delete ' . ucfirst($type), array('permission/delete', 'name' => $item->name), array('class' => 'btn btn-danger delete-confirm')); ?>
                <?php echo CHtml::link('<i class="fa fa-chain"></i> Assign child', array('permission/addChild', 'parent' => $item->name), array('class' => 'btn btn-primary')); ?>
                <button class="btn btn-primary" type="submit" aria-label="Save"><i class="fa fa-save"></i> Save <?php echo ucfirst($type); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <?php echo CHtml::label('Name', ucfirst($type) . '_name', array('class' => 'control-label')); ?>
                <?php echo CHtml::textField(ucfirst($type) . '[name]', $item->name, array('class' => 'form-control')); ?>
            </div>

            <div class="form-group">
                <?php echo CHtml::label('Description', ucfirst($type) . '_description', array('class' => 'control-label')); ?>
                <?php echo CHtml::textArea(ucfirst($type) . '[description]', $item->description, array('class' => 'form-control', 'rows' => 4)); ?>
            </div>
        </div>
        <div class="col-sm-8">
            <?php
            $children = $item->children;
            sort($children);
            $this->widget('zii.widgets.grid.CGridView', array(
                'dataProvider' => new CArrayDataProvider($children, array(
                    'keyField' => false,
                    'pagination' => array(
                        'pageSize' => count($children),
                    ),
                )),
                'enableHistory' => true,
                'enablePagination' => false,
                'emptyText' => 'No children found',
                'summaryText' => 'Total {count} children.',
                'columns' => array(
                    array(
                        'header' => 'Type',
                        'type' => 'raw',
                        'value' => function ($data) {
                            switch ($data->type) {
                                case 0:
                                    return "Operation";
                                case 1:
                                    return "Task";
                                case 2:
                                    return "Role";
                            }
                        }
                    ),
                    array(
                        'header' => 'Name',
                        'type' => 'raw',
                        'value' => 'CHtml::link($data->name.(in_array($data->name,Yii::app()->authManager->defaultRoles) ? " <i class=\"fa fa-asterisk\"></i>" : false),array("permission/edit","name"=>$data->name,"type"=>"role"))',
                    ),
                    array(
                        'header' => 'Description',
                        'type' => 'raw',
                        'value' => 'nl2br($data->description)',
                    ),
                    array(
                        'buttons' => array(
                            'delete' => array(
                                'options' => array(
                                    'class' => 'btn btn-danger delete-confirm',
                                ),
                                'imageUrl' => null,
                                'label' => '<i class="fa fa-chain-broken"></i> Unassign',
                                'url' => 'Yii::app()->createUrl("permission/deleteChild", array("parent"=>$_GET["name"], "child"=>$data->name))',
                            ),

                        ),
                        'class' => 'CButtonColumn',
                        'template' => '{delete}',
                    ),
                ),
            )); ?>
        </div>
    </div>
</div>

<?php $this->endWidget(); ?>