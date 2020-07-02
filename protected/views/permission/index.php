<div class="content-header">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h3>Access Manager</h3>
            </div>
            <div class="col-sm-6">
                <ul class="nav nav-tabs navbar-right">
                    <li class="active"><a href="#roles" data-toggle="tab">Roles</a></li>
                    <li><a href="#tasks" data-toggle="tab">Tasks</a></li>
                    <li><a href="#operations" data-toggle="tab">Operations</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="tab-content" style="padding: 1em;">

        <div class="tab-pane active" id="roles">
            <div class="row">
                <div class="col-sm-6">
                    <h4>Roles</h4>
                </div>
                <div class="col-sm-6 text-right">
                    <?php echo CHtml::link('<i class="fa fa-plus"></i> Add New Role', array('permission/add', 'type' => 'role'), array('class' => 'btn btn-primary')); ?>
                </div>
            </div>
            <?php
            $roles = Yii::app()->authManager->roles;
            sort($roles);
            $this->widget('zii.widgets.grid.CGridView', array(
                'dataProvider' => new CArrayDataProvider($roles, array(
                    'keyField' => false,
                    'pagination' => array(
                        'pageSize' => count($roles),
                    ),
                )),
                'enableHistory' => true,
                'enablePagination' => false,
                'emptyText' => 'No roles found',
                'summaryText' => 'Total {count} roles.',
                'columns' => array(
                    array(
                        'header' => 'Role',
                        'type' => 'raw',
                        'value' => 'CHtml::link($data->name,array("permission/edit","name"=>$data->name,"type"=>"role"))',
                    ),
                    array(
                        'header' => 'Description',
                        'type' => 'raw',
                        'value' => 'nl2br($data->description)',
                    ),
                    array(
                        'header' => 'Children',
                        'type' => 'raw',
                        'value' => 'sizeof($data->children)',
                    ),
                ),
            )); ?>
            <div class="alert alert-info">
                <p class="small">
                    <i class="fa fa-info-circle"></i> Roles are parents of Tasks.
                </p>
            </div>
        </div>

        <div class="tab-pane" id="tasks">
            <div class="row">
                <div class="col-sm-6">
                    <h4>Tasks</h4>
                </div>
                <div class="col-sm-6 text-right">
                    <?php echo CHtml::link('<i class="fa fa-plus"></i> Add New Task', array('permission/add', 'type' => 'task'), array('class' => 'btn btn-primary')); ?>
                </div>
            </div>
            <?php
            $tasks = Yii::app()->authManager->tasks;
            sort($tasks);
            $this->widget('zii.widgets.grid.CGridView', array(
                'dataProvider' => new CArrayDataProvider($tasks, array(
                    'keyField' => false,
                    'pagination' => array(
                        'pageSize' => count($tasks),
                    ),
                )),
                'enableHistory' => true,
                'enablePagination' => false,
                'emptyText' => 'No tasks found',
                'summaryText' => 'Total {count} tasks.',
                'columns' => array(
                    array(
                        'header' => 'Task',
                        'type' => 'raw',
                        'value' => 'CHtml::link($data->name,array("permission/edit","name"=>$data->name,"type"=>"task"))',
                    ),
                    array(
                        'header' => 'Description',
                        'type' => 'raw',
                        'value' => '$data->description',
                    ),
                    array(
                        'header' => 'Children',
                        'type' => 'raw',
                        'value' => 'sizeof($data->children)',
                    ),
                ),
            )); ?>
            <div class="alert alert-info">
                <p class="small">
                    <i class="fa fa-info-circle"></i> Tasks are children of Roles and parents of Operations.
                </p>
            </div>
        </div>
        <div class="tab-pane" id="operations">
            <div class="row">
                <div class="col-sm-6">
                    <h4>Operations</h4>
                </div>
                <div class="col-sm-6 text-right">
                    <?php echo CHtml::link('<i class="fa fa-plus"></i> Add New Operation', array('permission/add', 'type' => 'operation'), array('class' => 'btn btn-primary')); ?>
                </div>
            </div>
            <?php
            $operations = Yii::app()->authManager->operations;
            sort($operations);
            $this->widget('zii.widgets.grid.CGridView', array(
                'dataProvider' => new CArrayDataProvider($operations, array(
                    'keyField' => false,
                    'pagination' => array(
                        'pageSize' => count($operations),
                    ),
                )),
                'enableHistory' => true,
                'enablePagination' => false,
                'emptyText' => 'No operations found',
                'summaryText' => 'Total {count} operations.',
                'columns' => array(
                    array(
                        'header' => 'Operation',
                        'type' => 'raw',
                        'value' => 'CHtml::link($data->name,array("permission/edit","name"=>$data->name,"type"=>"task"))',
                    ),
                    array(
                        'header' => 'Description',
                        'type' => 'raw',
                        'value' => '$data->description',
                    ),
                    array(
                        'header' => 'Children',
                        'type' => 'raw',
                        'value' => 'sizeof($data->children)',
                    ),
                ),
            )); ?>
            <div class="alert alert-info">
                <p class="small">
                    <i class="fa fa-info-circle"></i> Operations are children of Tasks.
                </p>
            </div>
        </div>
    </div>
</div>


