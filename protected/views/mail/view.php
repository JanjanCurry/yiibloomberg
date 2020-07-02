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
                <h3>View Mail Item</h3>
            </div>
            <div class="col-sm-6 text-right">
            <?php
            if($outbox->status == 'pending') {
                echo CHtml::link('<i class="fa fa-times"></i> Cancel Mail', array('mail/cancel', 'id' => $outbox->id), array('class' => 'btn btn-danger'));
            }
            ?>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <?php echo $form->errorSummary($outbox, '', null, array('class'=>'alert alert-danger')); ?>

    <h4>Settings</h4>

    <div class="form-group">
        <?php echo $form->label($outbox, 'runTime', array('class'=>'col-xs-3 control-label')); ?>
        <div class="col-xs-9">
            <p class="form-control"><?php echo Yii::app()->format->datetime($outbox->runTime); ?></p>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label($outbox, 'sentTime', array('class'=>'col-xs-3 control-label')); ?>
        <div class="col-xs-9">
            <p class="form-control"><?php echo Yii::app()->format->datetime($outbox->sentTime); ?></p>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label($outbox, 'status', array('class'=>'col-xs-3 control-label')); ?>
        <div class="col-xs-9">
            <p class="form-control"><?php echo $outbox->getListLabel('listStatus',$outbox->status); ?></p>
        </div>
    </div>


    <h4>Recipients</h4>

    <div class="form-group">
        <?php echo $form->label($outbox, 'sendTo', array('class'=>'col-xs-3 control-label')); ?>
        <div class="col-xs-9">
            <p class="form-control"><?php echo $outbox->sendTo; ?></p>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label($outbox, 'sendCc', array('class'=>'col-xs-3 control-label')); ?>
        <div class="col-xs-9">
            <p class="form-control"><?php echo $outbox->sendCc; ?></p>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label($outbox, 'sendBcc', array('class'=>'col-xs-3 control-label')); ?>
        <div class="col-xs-9">
            <p class="form-control"><?php echo $outbox->sendBcc; ?></p>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label($outbox, 'sendFrom', array('class'=>'col-xs-3 control-label')); ?>
        <div class="col-xs-9">
            <p class="form-control"><?php echo $outbox->sendFrom; ?></p>
        </div>
    </div>

    <h4>Email Content</h4>

    <div class="form-group">
        <?php echo $form->label($outbox, 'subject', array('class'=>'col-xs-3 control-label')); ?>
        <div class="col-xs-9">
            <p class="form-control"><?php echo $outbox->subject; ?></p>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label($outbox, 'attach', array('class'=>'col-xs-3 control-label')); ?>
        <div class="col-xs-9">
            <p class="form-control">
                <?php
                $outbox->formatCommaSeparated('attach','string');
                echo $outbox->attach;
                ?>
            </p>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label($outbox, 'body', array('class'=>'col-xs-3 control-label')); ?>
        <div class="col-sm-9">
            <iframe class="iframe-mail" src="<?php echo Yii::app()->createUrl('mail/viewBody',array('id'=>$outbox->id)); ?>"></iframe>
        </div>
    </div>
</div>

<?php $this->endWidget(); ?>