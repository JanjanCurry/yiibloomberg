<div class="content-header">
    <div class="container">
        <h3>Account Login Management</h3>
    </div>
</div>

<div class="container">

    <div class="alert alert-info">
        <h4>Information</h4>
        <p>This feature stops users logging into the same account from multiple devices/browsers.</p>
        <ul>
            <li>This feature does not affect to developer accounts.</li>
            <li>If the user logs out then the record shown below will be removed.</li>
            <li>If the login session is idle for 30+ min then the record below will be ignored and overwritten upon the users next successful login attempt.</li>
            <li>
                Manually removing the record below will simulate the conditions of the active login session being idle for 30+ min.
                <ul>
                    <li>If the new device then logs in, it will be granted access and become the active session. The old device will be sent to the login page upon its next attempt to load a page.</li>
                    <li>If the old device loads a page before the user logs in with their new device then the old device will be retained as the active session and the new device will continue to be denied access.</li>
                </ul>
            </li>
        </ul>

        <h4>Statuses</h4>
        <ul>
            <li>Locked: The account has a login session that is currently active. Logins from other devices are disabled.</li>
            <li>Idle: The account has an login session that has not been active for 30+ min. Logins from other devices are permitted.</li>
            <li>Open (account not shown below): The account has no active login session. Logins from other devices are permitted.</li>
        </ul>
    </div>

    <?php $this->widget('zii.widgets.grid.CGridView', array(
        'dataProvider' => $lock->search([
            'paginationSize' => 'all',
            'sort' => 'updated DESC',
        ]),
        'columns' => array(
            array(
                'header' => 'Name',
                'type' => 'raw',
                'value' => '$data->user->company."<br><strong>".$data->user->fullName."</strong>"',
            ),
            array(
                'header' => 'Account Type',
                'type' => 'raw',
                'value' => '$data->user->getListLabel("listType", $data->user->type)',
            ),
            array(
                'header' => 'Current IP',
                'type' => 'raw',
                'value' => '$data->ipAddress',
            ),
            array(
                'header' => 'Last Updated',
                'type' => 'raw',
                'value' => 'Yii::app()->format->datetime($data->updated)',
            ),
            array(
                'header' => 'Status',
                'type' => 'raw',
                'value' => '($data->updated > strtotime("-30 min") ? "Locked" : "Idle")',
            ),
            array(
                'header' => '',
                'type' => 'raw',
                'value' => 'CHtml::link("<span class=\"fa fa-times\"></span>", array("user/logins", "id"=>$data->user->id), array("class" => "btn btn-danger"))',
            ),
        ),
    )); ?>
</div>
