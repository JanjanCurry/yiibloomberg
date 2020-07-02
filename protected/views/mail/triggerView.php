<?php $form=$this->beginWidget('CActiveForm', array(
    'enableAjaxValidation'=> true,
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
                    <h3>Edit Mail Trigger</h3>
                </div>
                <div class="col-sm-6 text-right">
                    <button type="submit" class="btn btn-primary" aria-label="Save"><i class="fa fa-save"></i> Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <?php echo $form->errorSummary($trigger, '', null, array('class'=>'alert alert-danger')); ?>

        <h4>Name</h4>

        <div class="form-group">
            <?php echo $form->labelEx($trigger, 'name', array('class'=>'col-xs-3 control-label')); ?>
            <div class="col-xs-9">
                <?php echo $form->textField($trigger, 'name', array('class'=>'form-control')); ?>
                <?php echo $form->error($trigger, 'name'); ?>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->labelEx($trigger, 'group', array('class'=>'col-xs-3 control-label')); ?>
            <div class="col-xs-9">
                <?php echo $form->textField($trigger, 'group', array('class'=>'form-control')); ?>
                <?php echo $form->error($trigger, 'group'); ?>
            </div>
        </div>

        <h4>Recipients</h4>

        <div class="form-group">
            <?php echo $form->labelEx($trigger, 'sendTo', array('class'=>'col-xs-3 control-label')); ?>
            <div class="col-xs-9">
                <?php echo $form->textField($trigger, 'sendTo', array('class'=>'form-control')); ?>
                <?php echo $form->error($trigger, 'sendTo'); ?>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->labelEx($trigger, 'sendCc', array('class'=>'col-xs-3 control-label')); ?>
            <div class="col-xs-9">
                <?php echo $form->textField($trigger, 'sendCc', array('class'=>'form-control')); ?>
                <?php echo $form->error($trigger, 'sendCc'); ?>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->labelEx($trigger, 'sendBcc', array('class'=>'col-xs-3 control-label')); ?>
            <div class="col-xs-9">
                <?php echo $form->textField($trigger, 'sendBcc', array('class'=>'form-control')); ?>
                <?php echo $form->error($trigger, 'sendBcc'); ?>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->labelEx($trigger, 'sendFrom', array('class'=>'col-xs-3 control-label')); ?>
            <div class="col-xs-9">
                <?php echo $form->textField($trigger, 'sendFrom', array('class'=>'form-control')); ?>
                <?php echo $form->error($trigger, 'sendFrom'); ?>
            </div>
        </div>

        <h4>Email Content</h4>

        <div class="form-group">
            <?php echo $form->labelEx($trigger, 'subject', array('class'=>'col-xs-3 control-label')); ?>
            <div class="col-xs-9">
                    <span class="input-group">
                        <?php echo $form->textField($trigger, 'subject', array('class'=>'form-control')); ?>
                        <span class="input-group-addon">: <?php echo Yii::app()->params['name']; ?></span>
                    </span>
                <?php echo $form->error($trigger, 'subject'); ?>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->labelEx($trigger, 'attach', array('class'=>'col-xs-3 control-label')); ?>
            <div class="col-xs-9">
                <?php echo $form->textField($trigger, 'attach', array('class'=>'form-control')); ?>
                <?php echo $form->error($trigger, 'attach'); ?>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->labelEx($trigger, 'marketing', array('class'=>'col-xs-3 control-label')); ?>
            <div class="col-xs-9">
                <?php echo $form->dropDownList($trigger, 'marketing', $trigger->listBoolean(), array(
                    'class'=>'form-control',
                    'data-toggle' => 'select',
                )); ?>
                <?php echo $form->error($trigger, 'marketing'); ?>
            </div>
        </div>

        <h4>Settings</h4>

        <div class="form-group">
            <?php echo $form->labelEx($trigger, 'status', array('class'=>'col-xs-3 control-label')); ?>
            <div class="col-xs-9">
                <?php echo $form->dropDownList($trigger, 'status', $trigger->listStatus(), array(
                    'class'=>'form-control',
                    'data-toggle' => 'select',
                )); ?>
                <?php echo $form->error($trigger, 'status'); ?>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->labelEx($trigger, 'viewFile', array('class'=>'col-xs-3 control-label')); ?>
            <div class="col-xs-9">
                <?php echo $form->textField($trigger, 'viewFile', array('class'=>'form-control')); ?>
                <?php echo $form->error($trigger, 'viewFile'); ?>
            </div>
        </div>


        <div class="form-group">
            <?php echo $form->labelEx($trigger, 'ref', array('class'=>'col-xs-3 control-label')); ?>
            <div class="col-xs-9">
                <?php echo $form->textField($trigger, 'ref', array('class'=>'form-control')); ?>
                <?php echo $form->error($trigger, 'ref'); ?>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->labelEx($trigger, 'timeDelay', array('class'=>'col-xs-3 control-label')); ?>
            <div class="col-xs-9">
                <?php echo $form->textField($trigger, 'timeDelay', array('class'=>'form-control')); ?>
                <?php echo $form->error($trigger, 'timeDelay'); ?>
            </div>
        </div>

    </div>

<?php $this->endWidget(); ?>