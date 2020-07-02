<?php
$items = array();
$rawData = array(
    'commodity' => DbCommodities::model()->findAll(),
    'currency' => DbCurrencies::model()->findAll(),
    'equity' => DbEquities::model()->findAll(),
    'macro' => DbMacroList::model()->findAll(),
    'partner' => DbPartners::model()->findAll(),
    'reporter' => DbReporters::model()->findAll(),
    'sector' => DbSectors::model()->findAll(),
);
foreach($rawData as $group => $models){
    $items[$group] = array();
    if(!empty($models)){
        $i = 0;
        $total = ceil(count($models) / 3);
        $letter = '';
        $newRow = false;
        foreach($models as $model){
            if ($i == $total || $i == ($total * 2)) {
                $newRow = true;
            }
            $i++;

            if($group == 'sector') {
                $key = $model->code[0] . $model->code[1];
            }else{
                $key = $model->name[0];
            }

            if ($letter != $key) {
                if ($newRow) {
                    $newRow = false;
                    $items[$group][] = '</div><div class="col-md-4">';
                }
                $letter = $key;
                $items[$group][] = '<h3>' . strtoupper($letter) . '</h3>';
            }

            $items[$group][] = '<a href="#" class="list-group-item list-group-item-' . $group . '-' . $model->code . '" data-ref="' . $model->code . '" data-label="' . $model->code . ', ' . $model->name . '">' . $model->code . ': ' . $model->name . '</a>';
        }
    }
}

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
                <h3>Data Availability</h3>
            </div>
            <div class="col-sm-6 text-right">
                <a href="#" class="btn btn-primary startUpdate"><i class="fa fa-save"></i> Start Update</a>
            </div>
        </div>
    </div>
</div>


<div class="container">
    <div class="alert alert-info">
        <h4>How calculations are done</h4>
        <ol>
            <li>Names and codes of report subjects are gathered from the database <small>(e.g. Database tables db_reporters and db_currencies)</small></li>
            <li>Each item is tested to see in an entry exists in the corresponding results table. If a results entry exists, it is assumed there is data <small>(e.g. Database tables all_mon_ttp_results, all_mrk_cur_results)</small></li>
            <li>Access permissions of each item are tested <small>(i.e. check is country is available to 'Essentials' accounts)</small></li>
            <li>Data is saved to db_reports so that it can be quickly searched to check the availability of individual reports</li>
            <li>Totals shown below are the total number of tested reports logged in db_reports</li>
        </ol>
    </div>

    <h4>Other</h4>
    <table class="table table-striped reporterAvailability">
        <tr>
            <th></th>
            <th class="text-center">Updated</th>
            <th class="text-center">Total</th>
        </tr>
        <tr class="item" data-ref="dates">
            <th>Dates</th>
            <th class="text-center icon"><span class="fa fa-minus"></span></th>
            <td class="text-center dates" data-type='dates'>N/A</td>
        </tr>

        <tr class="item" data-ref="commodity">
            <th>Commodities</th>
            <th class="text-center icon"><span class="fa fa-minus"></span></th>
            <?php
            $criteria = new CDbCriteria();
            $criteria->compare('t.group', 'commodity');
            $criteria->compare('t.type', 'asset');
            $criteria->compare('t.hasData', 1);
            $count = DbReports::model()->count($criteria);
            echo "<td class='text-center item-type commodity' data-type='commodity' data-group='commodity'>".(empty($count) ? '-' : $count).'</td>';
            ?>
        </tr>

        <tr class="item" data-ref="currency">
            <th>Currencies</th>
            <th class="text-center icon"><span class="fa fa-minus"></span></th>
            <?php
            $criteria = new CDbCriteria();
            $criteria->compare('t.group', 'currency');
            $criteria->compare('t.type', 'asset');
            $criteria->compare('t.hasData', 1);
            $count = DbReports::model()->count($criteria);
            echo "<td class='text-center item-type currency' data-type='currency' data-group='currency'>".(empty($count) ? '-' : $count).'</td>';
            ?>
        </tr>

        <tr class="item" data-ref="equity">
            <th>Equities</th>
            <th class="text-center icon"><span class="fa fa-minus"></span></th>
            <?php
            $criteria = new CDbCriteria();
            $criteria->compare('t.group', 'equity');
            $criteria->compare('t.type', 'asset');
            $criteria->compare('t.hasData', 1);
            $count = DbReports::model()->count($criteria);
            echo "<td class='text-center item-type equity' data-type='equity' data-group='equity'>".(empty($count) ? '-' : $count).'</td>';
            ?>
        </tr>
    </table>

    <h4>Countries</h4>
    <table class="table table-striped reporterAvailability">
        <tr>
            <th>Country</th>
            <th class="text-center">Updated</th>

            <th class="text-center">Macro: Ann</th>
            <th class="text-center">Macro: Qtr</th>
            <th class="text-center">Macro: Mon</th>

            <th class="text-center">Trade: TT</th>
            <th class="text-center">Trade: TTP</th>
            <th class="text-center">Trade: TTCC</th>
            <th class="text-center">Trade: TTFP</th>
            <th class="text-center">Trade: TTFC</th>
        </tr>
        <?php
        $cols = array(
            array('group' => 'macro', 'part1' => 'macro', 'part2' => 'annual', 'type' => 'macro-annual'),
            array('group' => 'macro', 'part1' => 'macro', 'part2' => 'quarter', 'type' => 'macro-quarter'),
            array('group' => 'macro', 'part1' => 'macro', 'part2' => 'month', 'type' => 'macro-month'),

            array('group' => 'reporter', 'part1' => 'trade', 'part2' => 'tt', 'type' => 'tt'),
            array('group' => 'partner', 'part1' => 'trade', 'part2' => 'ttp', 'type' => 'ttp'),
            array('group' => 'sector', 'part1' => 'trade', 'part2' => 'ttcc', 'type' => 'ttcc'),
            array('group' => 'partner', 'part1' => 'trade', 'part2' => 'ttf-partner', 'type' => 'ttf-partner'),
            array('group' => 'sector', 'part1' => 'trade', 'part2' => 'ttf-sector', 'type' => 'ttf-sector'),
        );

        if(!empty($reporters)){
            $log = DbVar::model()->findByAttributes([
                'name' => 'raCachedTable',
                'type' => 'reporterAvailability',
            ]);
            $cache = [];
            foreach($reporters as $i => $reporter){
                echo '<tr class="item" data-ref="'.$reporter->code.'">';
                echo '<th><a href="#" class="btn btn-primary startUpdateSingle"><i class="fa fa-refresh"></i></a> '.$reporter->code.': '.$reporter->name.'</th>';
                echo '<th class="text-center icon"><span class="fa fa-minus"></span></th>';

                $cache[$reporter->code] = [];
                foreach($cols as $colData){
                    $group = $colData['group'];
                    $key = $colData['part1'];
                    $val = $colData['part2'];
                    $type = $colData['type'];


                    if(!empty($log) && !empty($log->data) && isset($log->data[$reporter->code][$key.'-'.$val])){
                        $count = $log->data[$reporter->code][$key.'-'.$val];
                    }else {
                        $criteria = new CDbCriteria();
                        $criteria->compare('t.group', $key);
                        $criteria->compare('t.type', $val);
                        $criteria->compare('t.code', $reporter->code);
                        $criteria->compare('t.hasData', 1);
                        $count = DbReports::model()->count($criteria);
                    }
                    echo "<td class='text-center item-type ".$key."-".$val."' data-type='".$type."' data-group='".$group."'>".(empty($count) ? '-' : $count).'</td>';

                    $cache[$reporter->code][$key.'-'.$val] = $count;
                }

                echo '</tr>';
                /*if($i > 10){
                    break;
                }*/
            }

            DbVar::add('raCachedTable', 'reporterAvailability', $cache);
        }
        ?>

    </table>

    <?php //Yii::app()->format->debug($log->data); ?>

</div>
<?php $this->endWidget(); ?>

<div class="modal fade-scale" id="availability-inspector" tabindex="-1">
    <div class="modal-dialog modal-fw" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>


                <?php
                if (!empty($items)) {
                    foreach ($items as $group => $groupItems) {
                        echo '<div class="list-group list-group-'.$group.' hidden">';
                        echo '<h3>'.ucfirst($group).'</h3>';
                        echo '<div class="row"><div class="col-md-4">';
                        if(!empty($groupItems)){
                            foreach($groupItems as $item){
                                echo $item;
                            }
                        }
                        echo '</div></div></div>';
                    }
                }
                ?>


            </div>
        </div>
    </div>
</div>

