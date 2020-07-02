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
                <h3>Add Account</h3>
            </div>
            <div class="col-sm-6 text-right">
                <button type="submit" class="btn btn-primary" aria-label="Save"><i class="fa fa-save"></i> Save</button>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <?php echo $form->errorSummary($user, '', null, array('class'=>'alert alert-danger')); ?>

    <div class="row">
        <div class="col-sm-6">
            <h4>Name</h4>

            <div class="form-group">
                <?php echo $form->labelEx($user, 'companyId', array('class'=>'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->dropDownList($user, 'companyId', $user->listCompany(), array('class'=>'form-control', 'empty' => '')); ?>
                    <?php echo $form->error($user, 'companyId'); ?>
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
                <?php echo $form->labelEx($user, 'phone', array('class'=>'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->textField($user, 'phone', array('class'=>'form-control')); ?>
                    <?php echo $form->error($user, 'phone'); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($user, 'email', array('class'=>'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->emailField($user, 'email', array('class'=>'form-control')); ?>
                    <?php echo $form->error($user, 'email'); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($user, 'verifyEmail', array('class' => 'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->dropDownList($user, 'verifyEmail', $user->listBoolean(), array('class' => 'form-control',)); ?>
                    <?php echo $form->error($user, 'verifyEmail'); ?>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <h4>Account Settings</h4>

            <div class="form-group">
                <?php echo $form->labelEx($user, 'status', array('class' => 'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->dropDownList($user, 'status', $user->listStatus(), array('class' => 'form-control',)); ?>
                    <?php echo $form->error($user, 'status'); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($user, 'type', array('class' => 'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->dropDownList($user, 'type', $user->listType(), array('class' => 'form-control', 'empty' => '')); ?>
                    <?php echo $form->error($user, 'type'); ?>
                </div>
            </div>

            <hr />
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
