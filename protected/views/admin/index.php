<div class="content-header wow slideInDown">
    <div class="container">
        <h3>Admin Dashboard</h3>
    </div>
</div>

<div class="container wow zoomIn">
    <div class="row">
        <div class="col-sm-4">
            <h4>User Accounts</h4>
            <div class="list-group">
                <?php echo CHtml::link('<span class="fa fa-plus"></span> Add new user', array('user/add'), array('class' => 'list-group-item')); ?>
                <?php echo CHtml::link('<span class="fa fa-list"></span> Account list', array('user/index'), array('class' => 'list-group-item')); ?>
                <?php echo CHtml::link('<span class="fa fa-plus"></span> Add new company', array('company/add'), array('class' => 'list-group-item')); ?>
                <?php echo CHtml::link('<span class="fa fa-list"></span> Company list', array('company/index'), array('class' => 'list-group-item')); ?>
                <?php echo CHtml::link('<span class="fa fa-lock"></span> Account Login Management', array('user/logins'), array('class' => 'list-group-item')); ?>
            </div>
        </div>

        <div class="col-sm-4">
            <h4>User Data</h4>
            <div class="list-group">
                <?php echo CHtml::link('<span class="fa fa-plus"></span> Add Notifications', array('alert/add'), array('class' => 'list-group-item')); ?>
                <?php echo CHtml::link('<span class="fa fa-list"></span> List Notifications', array('alert/indexAll'), array('class' => 'list-group-item')); ?>
                <?php echo CHtml::link('<span class="fa fa-download"></span> User Export', array('user/export'), array('class' => 'list-group-item')); ?>
                <?php echo CHtml::link('<span class="fa fa-list"></span> Activity Log', array('user/log'), array('class' => 'list-group-item')); ?>
            </div>
        </div>

        <div class="col-sm-4">
            <h4>Report Data</h4>
            <div class="list-group">
                <?php echo CHtml::link('<span class="fa fa-refresh"></span> Update Available Data', array('admin/reporterAvailability'), array('class' => 'list-group-item')); ?>
                <?php echo CHtml::link('<span class="fa fa-calendar"></span> Database Dates', array('admin/dbDates'), array('class' => 'list-group-item')); ?>
                <?php echo CHtml::link('<span class="fa fa-eyedropper"></span> Country Colors', array('admin/countryColor'), array('class' => 'list-group-item')); ?>
                <?php echo CHtml::link('<span class="fa fa-eyedropper"></span> Macro Colors', array('admin/macroColor'), array('class' => 'list-group-item')); ?>
                <?php echo CHtml::link('<span class="fa fa-sitemap"></span> Partner Groups', array('admin/partnerGroups', 'page' => 'a'), array('class' => 'list-group-item')); ?>
                <?php echo CHtml::link('<span class="fa fa-sitemap"></span> Visible Reporters', array('admin/reporterGroups'), array('class' => 'list-group-item')); ?>
                <?php echo CHtml::link('<span class="fa fa-database"></span> Market Confidence', array('admin/confidence'), array('class' => 'list-group-item')); ?>
                <?php echo CHtml::link('<span class="fa fa-database"></span> Update Top/Worst Assets', array('admin/topAssets'), array('class' => 'list-group-item')); ?>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="col-sm-4">
            <h4>Videos</h4>
            <div class="list-group">
                <?php echo CHtml::link('<span class="fa fa-plus"></span> Add new video', array('video/add'), array('class' => 'list-group-item')); ?>
                <?php echo CHtml::link('<span class="fa fa-list"></span> Video List', array('video/index'), array('class' => 'list-group-item')); ?>
                <?php echo CHtml::link('<span class="fa fa-plus"></span> Add new video category', array('video/addCat'), array('class' => 'list-group-item')); ?>
                <?php echo CHtml::link('<span class="fa fa-list"></span> Video category list', array('video/indexCat'), array('class' => 'list-group-item')); ?>
            </div>
        </div>

        <div class="col-sm-4">
            <h4>Mailer</h4>
            <div class="list-group">
                <?php echo CHtml::link('<span class="fa fa-inbox"></span> Outbox', array('mail/outbox'), array('class' => 'list-group-item')); ?>
                <?php if(Yii::app()->user->checkAccess('dev')){ ?>
                    <?php echo CHtml::link('<span class="fa fa-plus"></span> Add new trigger', array('mail/triggerAdd'), array('class' => 'list-group-item')); ?>
                    <?php echo CHtml::link('<span class="fa fa-bolt"></span> Trigger List', array('mail/triggers'), array('class' => 'list-group-item')); ?>
                <?php } ?>
            </div>
        </div>

        <?php if(Yii::app()->user->checkAccess('dev')){ ?>
        <div class="col-sm-4">
            <h4>Developer Tools</h4>
            <div class="list-group">
                <?php echo CHtml::link('<span class="fa fa-lock"></span> Access Manager', array('permission/index'), array('class' => 'list-group-item')); ?>
                <?php echo CHtml::link('<span class="fa fa-bug"></span> Error Log', array('admin/errorLog'), array('class' => 'list-group-item')); ?>
                <?php echo CHtml::link('<span class="fa fa-trash"></span> Clear Broken Favorites', array('admin/cleanFavorites'), array('class' => 'list-group-item')); ?>
                <?php echo CHtml::link('<span class="fa fa-trash"></span> Clear Cache', array('admin/flushCache'), array('class' => 'list-group-item')); ?>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
