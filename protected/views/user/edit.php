<?php
/* @var DbUser $user */
/* @var DbUserService $service */
?>

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
                <h3>Edit Account</h3>
            </div>
            <div class="col-sm-6 text-right">
                <?php echo CHtml::link('<i class="fa fa-trash"></i> Delete', array('user/delete', 'id' => $user->id), array('class' => 'btn btn-danger delete-confirm-link')); ?>
                <button type="submit" class="btn btn-primary" aria-label="Save"><i class="fa fa-save"></i> Save</button>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <?php echo $form->errorSummary($user, '', null, array('class' => 'alert alert-primary')); ?>

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
                <?php echo $form->labelEx($user, 'fName', array('class' => 'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->textField($user, 'fName', array('class' => 'form-control')); ?>
                    <?php echo $form->error($user, 'fName'); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($user, 'sName', array('class' => 'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->textField($user, 'sName', array('class' => 'form-control')); ?>
                    <?php echo $form->error($user, 'sName'); ?>
                </div>
            </div>

            <hr/>
            <h4>Contact Details</h4>

            <div class="form-group">
                <?php echo $form->labelEx($user, 'phone', array('class' => 'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->textField($user, 'phone', array('class' => 'form-control')); ?>
                    <?php echo $form->error($user, 'phone'); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($user, 'email', array('class' => 'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->emailField($user, 'email', array('class' => 'form-control')); ?>
                    <?php echo $form->error($user, 'email'); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($user, 'unsubscribe', array('class' => 'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->dropDownList($user, 'unsubscribe', $user->listBoolean(), array('class' => 'form-control selectpicker',)); ?>
                    <?php echo $form->error($user, 'unsubscribe'); ?>
                </div>
            </div>

            <hr/>
            <h4>Account Verification</h4>

            <div class="form-group">
                <?php echo $form->labelEx($user, 'verifyEmail', array('class' => 'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->dropDownList($user, 'verifyEmail', $user->listBoolean(), array('class' => 'form-control selectpicker',)); ?>
                    <?php echo $form->error($user, 'verifyEmail'); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($user, 'terms', array('class' => 'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->dropDownList($user, 'terms', $user->listBoolean(), array('class' => 'form-control selectpicker',)); ?>
                    <?php echo $form->error($user, 'terms'); ?>
                </div>
            </div>

            <?php /*
            <div class="form-group">
                <?php echo $form->labelEx($user, 'termsBeta', array('class' => 'col-sm-4 control-label selectpicker')); ?>
                <div class="col-sm-8">
                    <?php echo $form->dropDownList($user, 'termsBeta', $user->listBoolean(), array('class' => 'form-control',)); ?>
                    <?php echo $form->error($user, 'termsBeta'); ?>
                </div>
            </div>
            */ ?>

            <div class="form-group">
                <?php echo $form->labelEx($user, 'tourLogin', array('class' => 'col-sm-4 control-label selectpicker')); ?>
                <div class="col-sm-8">
                    <?php echo $form->dropDownList($user, 'tourLogin', $user->listTour(), array('class' => 'form-control', 'empty' => '')); ?>
                    <?php echo $form->error($user, 'tourLogin'); ?>
                </div>
            </div>

        </div>

        <div class="col-sm-6">
            <h4>Login Status</h4>

            <div class="form-group">
                <?php echo $form->labelEx($user, 'status', array('class' => 'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->dropDownList($user, 'status', $user->listStatus(), array('class' => 'form-control selectpicker',)); ?>
                    <?php echo $form->error($user, 'status'); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($user, 'allowMultiLogin', array('class' => 'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php echo $form->dropDownList($user, 'allowMultiLogin', $user->listBoolean(), array('class' => 'form-control selectpicker',)); ?>
                    <?php echo $form->error($user, 'allowMultiLogin'); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($user, 'expire', array('class' => 'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <span class="input-group">
                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                        <?php
                        $user->convertTime('expire', 'string');
                        echo $form->textField($user, 'expire', array('class' => 'form-control datepicker')); ?>
                    </span>
                    <?php echo $form->error($user, 'expire'); ?>
                </div>
            </div>

            <hr/>
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

            <?php
            if ($user->verifyEmail == 1) {
                echo CHtml::link('<span class="fa fa-envelope"></span> Send Password Reset Email', array('user/sendPasswordReset', 'id' => $user->id), array('class' => 'btn btn-primary btn-block'));
            } else {
                echo CHtml::link('<span class="fa fa-envelope"></span> Send Password Creation Email', array('user/sendPasswordCreate', 'id' => $user->id), array('class' => 'btn btn-primary btn-block'));
            }
            ?>

            <hr/>
            <h4>Account Type</h4>

            <div class="form-group">
                <?php echo $form->labelEx($user, 'type', array('class' => 'col-sm-4 control-label')); ?>
                <div class="col-sm-8">
                    <?php
                    $list = [];
                    foreach($user->listType() as $key => $val){
                        if(Yii::app()->user->checkAccess($key)){
                            $list[$key] = $val;
                        }
                    }
                    echo $form->dropDownList($user, 'type', $list, array('class' => 'form-control selectpicker')); ?>
                    <?php echo $form->error($user, 'type'); ?>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-4"><?php echo $service->getListLabel('listTool', 'com') ?></label>
                <div class="col-sm-8">
                    <p class="form-control" disabled><?php
                        $temp = $service->calcLevel('com', $user->id);
                        echo (!empty($temp) ? $service->getListLabel('listLevel', $temp) : 'Off');
                        ?></p>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-4"><?php echo $service->getListLabel('listTool', 'cur') ?></label>
                <div class="col-sm-8">
                    <p class="form-control" disabled><?php
                        $temp = $service->calcLevel('cur', $user->id);
                        echo (!empty($temp) ? $service->getListLabel('listLevel', $temp) : 'Off');
                        ?></p>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-4"><?php echo $service->getListLabel('listTool', 'equ') ?></label>
                <div class="col-sm-8">
                    <p class="form-control" disabled><?php
                        $temp = $service->calcLevel('equ', $user->id);
                        echo (!empty($temp) ? $service->getListLabel('listLevel', $temp) : 'Off');
                        ?></p>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-4"><?php echo $service->getListLabel('listTool', 'tra') ?></label>
                <div class="col-sm-8">
                    <p class="form-control" disabled><?php
                        $temp = $service->calcLevel('tra', $user->id);
                        echo (!empty($temp) ? $service->getListLabel('listLevel', $temp) : 'Off');
                        ?></p>
                </div>
            </div>


        </div>

        <div class="col-sm-6">
            <hr/>
            <h4>Favorites</h4>
            <p>Repairing and adding default favorites will not removed valid and working custom favorites.</p>
            <?php echo CHtml::link('Repair Favorites', array('user/cleanFavorites', 'id' => $user->id), array('class' => 'btn btn-primary btn-block')); ?>
            <?php echo CHtml::link('Add Default Favorites', array('user/defaultFavorites', 'id' => $user->id), array('class' => 'btn btn-primary btn-block')); ?>
        </div>

    </div>

    <hr/>
    <h4>Subscriptions</h4>

    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <label class="col-sm-4 control-label" for="DbUser_tools">Subscription</label>
                <div class="col-sm-8">
                    <?php
                    $list = [];
                    foreach (DbUserService::model()->listTool() as $key1 => $val1) {
                        $list[$val1] = [];
                        foreach (DbUserService::model()->listLevel() as $key2 => $val2) {
                            $list[$val1][$key1 . '-' . $key2] = $val1 . ': ' . $val2;
                        }
                    }

                    echo CHtml::dropDownList('subscription', null, $list, array('class' => 'form-control selectpicker', 'empty' => 'None', 'multiple' => 'multiple'));
                    ?>
                </div>
            </div>

        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label class="col-sm-4 control-label" for="DbUser_expire">Expiry date</label>
                <div class="col-sm-8">
                    <span class="input-group">
                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                        <?php echo CHtml::textField('subscription-expire', '', array('class' => 'form-control datepicker')); ?>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <button type="submit" class="btn btn-primary btn-block">Add Subscription</button>
        </div>
    </div>

    <?php
    $services = new DbUserService();
    $services->unsetAttributes();
    $services->userId = $user->id;

    $this->widget('zii.widgets.grid.CGridView', array(
        'dataProvider' => $services->search(),
        'summaryText' => '',
        'pager' => array(
            'header' => '',
            'htmlOptions' => array(
                'class' => 'pagination',
            ),
            'selectedPageCssClass' => 'active',
        ),
        'columns' => array(
            array(
                'header' => 'Tool',
                'type' => 'raw',
                'value' => '$data->getListLabel("listTool",$data->tool)',
            ),
            array(
                'header' => 'Service level',
                'type' => 'raw',
                'value' => '$data->getListLabel("listLevel",$data->level)',
            ),
            array(
                'header' => 'Date Added',
                'type' => 'raw',
                'value' => 'Yii::app()->format->date($data->created)',
            ),
            array(
                'header' => 'Expiry Date',
                'type' => 'raw',
                'value' => '(!empty($data->expire) ? Yii::app()->format->date($data->expire) : "-")',
            ),
            array(
                'header' => '',
                'type' => 'raw',
                'value' => 'CHtml::link("<span class=\"fa fa-pencil\"></span>", array("user/serviceEdit", "id"=>$data->id), array("class" => "btn btn-primary")).CHtml::link("<span class=\"fa fa-trash\"></span>", array("user/serviceDelete", "id"=>$data->id), array("class" => "btn btn-danger delete-confirm-link"))',
            ),

        ),
        'htmlOptions' => array(//'class' => 'table table-hover items'
        ),
    )); ?>


    <hr/>
    <h4>Emails</h4>

    <?php
    $criteria = new CDbCriteria();
    $criteria->compare('sendTo', $user->email, false, 'OR');
    $criteria->compare('sendFrom', $user->email, false, 'OR');
    $outbox = new DbMailOutbox();
    $outbox->unsetAttributes();

    $this->widget('zii.widgets.grid.CGridView', array(
        'dataProvider' => $outbox->search(array('criteria' => $criteria)),
        'summaryText' => '',
        'pager' => array(
            'header' => '',
            'htmlOptions' => array(
                'class' => 'pagination',
            ),
            'selectedPageCssClass' => 'active',
        ),
        'columns' => array(
            array(
                'header' => 'Sent',
                'type' => 'raw',
                'value' => 'CHtml::link(($data->sendTo == "' . $user->email . '" ? "To" : "From"), array("mail/view", "id"=>$data->id))',
            ),
            array(
                'name' => 'status',
                'type' => 'raw',
                'value' => 'CHtml::link($data->getListLabel("listStatus",$data->status), array("mail/view", "id"=>$data->id))',
            ),
            array(
                'header' => 'Times',
                'name' => 'runTime',
                'type' => 'raw',
                'value' => 'CHtml::link($data->gridTimes(), array("mail/view", "id"=>$data->id), array("class" => "gridMultiLine"))',
            ),
            array(
                'name' => 'subject',
                'type' => 'raw',
                'value' => 'CHtml::link($data->subject, array("mail/view", "id"=>$data->id))',
            ),
            array(
                'name' => 'email',
                'type' => 'raw',
                'value' => 'CHtml::link($data->gridEmails(true), array("mail/view", "id"=>$data->id), array("class" => "gridMultiLine"))',
            ),

        ),
        'htmlOptions' => array(//'class' => 'table table-hover items'
        ),
    )); ?>

</div>
<?php $this->endWidget(); ?>
