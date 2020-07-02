<div class="well dash-group dash-group-g10">
    <div class="row">
        <div class="col-xs-6">
            <h3 class="mt-0">Economic Indicators <span class="badge badge-primary">BETA</span></h3>
        </div>
        <div class="col-xs-6 text-right">
            <a href="#" class="btn btn-sm macro-edit-btn"><i class="fa fa-pencil"></i></a>
        </div>
    </div>
    <div class="dash-g10 item">
        <?php
        $criteria = new CDbCriteria();
        $criteria->compare('userId', $this->user->id);
        $criteria->compare('type', 'g10');
        $favorite = DbUserDash::model()->find($criteria);
        if(!empty($favorite)) {
            $data = [];
            $data['init'] = true;
            $data['session'] = 'ignore';
            $data['ignorePermissions'] = true;
            $data['period'] = 'annual';
            $data['showTable'] = false;
            $data['chartType'] = 'bar';
            $data['macro'] = $favorite->data['macro'];
            $data['reporter'] = ['BEL', 'CAN', 'FRA', 'DEU', 'ITA', 'JPN', 'NLD', 'SWE', 'CHE', 'GBR', 'USA'];
            $data['report'] = 'dash-change';
            $data['startTime'] = strtotime('1 Jan '.date('Y'));
            $data['endTime'] = strtotime('31 Dec '.date('Y'));
            $data['view'] = 'group';
            $this->widget('application.widgets.MacroWidget', $data);
        }
        ?>
    </div>
</div>