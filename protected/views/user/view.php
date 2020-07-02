<?php $form=$this->beginWidget('CActiveForm', array(
    'enableAjaxValidation'=> true,
    'enableClientValidation' => false,
    'htmlOptions' => array(
        'class'=>'form-horizontal',
        'autocomplete' => 'off',
    ),
)); ?>

<div class="content-header wow slideInDown">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h3>Your Account</h3>
            </div>
            <div class="col-sm-6 text-right">
                <button type="submit" class="btn btn-primary" aria-label="Save"><i class="fa fa-save"></i> Save</button>
            </div>
        </div>
    </div>
</div>

<div class="container wow zoomIn">
    <?php echo $form->errorSummary($user, '', null, array('class'=>'alert alert-danger')); ?>

    <div class="row">
        <div class="col-sm-6">
            <h4>Name</h4>

            <div class="form-group">
                <?php echo $form->labelEx($user, 'company', array('class'=>'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->textField($user, 'company', array('class'=>'form-control')); ?>
                    <?php echo $form->error($user, 'company'); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($user, 'fName', array('class'=>'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->textField($user, 'fName', array('class'=>'form-control')); ?>
                    <?php echo $form->error($user, 'fName'); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($user, 'sName', array('class'=>'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->textField($user, 'sName', array('class'=>'form-control')); ?>
                    <?php echo $form->error($user, 'sName'); ?>
                </div>
            </div>

            <hr/>
            <h4>Contact Details</h4>


            <div class="form-group">
                <?php echo $form->labelEx($user, 'email', array('class'=>'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->emailField($user, 'email', array('class'=>'form-control')); ?>
                    <?php echo $form->error($user, 'email'); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($user, 'phone', array('class'=>'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->textField($user, 'phone', array('class'=>'form-control')); ?>
                    <?php echo $form->error($user, 'phone'); ?>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <h4>Account Password</h4>

            <div class="form-group">
                <?php echo $form->labelEx($user, 'passwordNew', array('class' => 'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->passwordField($user, 'passwordNew', array('class' => 'form-control')); ?>
                    <?php echo $form->error($user, 'passwordNew'); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($user, 'passwordConfirm', array('class' => 'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->passwordField($user, 'passwordConfirm', array('class' => 'form-control')); ?>
                    <?php echo $form->error($user, 'passwordConfirm'); ?>
                </div>
            </div>
        </div>

    </div>

</div>
<?php $this->endWidget(); ?>
