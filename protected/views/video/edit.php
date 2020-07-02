<?php $form=$this->beginWidget('CActiveForm', array(
    'enableAjaxValidation'=> false,
    'enableClientValidation' => false,
    'htmlOptions' => array(
        'class'=>'form-horizontal',
        'autocomplete' => 'off',
    ),
)); ?>

<div class="content-header">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h3>Edit video</h3>
            </div>
            <div class="col-sm-6 text-right">
                <?php echo CHtml::link('<i class="fa fa-trash"></i> Delete', ['video/delete', 'id' => $model->id], ['class' => 'btn btn-danger delete-confirm-link']); ?>
                <?php echo CHtml::link('<i class="fa fa-list"></i> List', ['video/index'], ['class' => 'btn btn-primary']); ?>
                <button type="submit" class="btn btn-primary" aria-label="Save"><i class="fa fa-save"></i> Save</button>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <?php echo $form->errorSummary($model, '', null, array('class'=>'alert alert-danger')); ?>

    <div class="form-group">
        <?php echo $form->labelEx($model, 'video', array('class'=>'col-sm-4 control-label')); ?>
        <div class="col-sm-8">
            <?php echo $form->textField($model, 'video', array('class'=>'form-control')); ?>
            <?php echo $form->error($model, 'video'); ?>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->labelEx($model, 'title', array('class'=>'col-sm-4 control-label')); ?>
        <div class="col-sm-8">
            <?php echo $form->textField($model, 'title', array('class'=>'form-control')); ?>
            <?php echo $form->error($model, 'title'); ?>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->labelEx($model, 'description', array('class'=>'col-sm-4 control-label')); ?>
        <div class="col-sm-8">
            <?php echo $form->textArea($model, 'description', array('class'=>'form-control')); ?>
            <?php echo $form->error($model, 'description'); ?>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->labelEx($model, 'catIds', array('class'=>'col-sm-4 control-label')); ?>
        <div class="col-sm-8">
            <?php echo $form->dropDownlist($model, 'catIds', DbVideoCat::model()->listCategory(), array(
                'class'=>'form-control selectpicker',
                'multiple' => 'multiple',
            )); ?>
            <?php echo $form->error($model, 'catIds'); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-8 col-sm-offset-4">
            <div class="flex-video">
                <iframe src="https://www.youtube.com/embed/<?php echo $model->video; ?>?rel=0&amp;showinfo=0"
                        frameborder="0"
                        allow="autoplay; encrypted-media"
                        allowfullscreen
                        id="howto-video-player"></iframe>
            </div>
        </div>
    </div>

</div>
<?php $this->endWidget(); ?>
