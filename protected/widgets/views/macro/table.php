<?php
$items = array();
$cols = [
    '<th></th>',
    '<th>Indicator</th>',
    '<th>Units</th>',
];
if (!empty($data['rows'])) {
    foreach ($data['rows'] as $row) {
        if (!empty($row['c'])) {
            foreach ($row['c'] as $id => $item) {
                if ($id == 0) {
                    $cols[] = '<th class="text-right">' . $item['v'] . '</th>';
                } else {
                    if(empty($items[$id]) && !empty($data['cols'][$id])){
                        $items[$id] = array(
                            'ref' => $data['cols'][$id],
                            'values' => array(),
                        );
                    }
                    $items[$id]['values'][] = $item['v'];
                }
            }
        }
    }
}
$cols[] = '<th>Source</th>';

$rows = [];
$i = 0;
foreach ($items as $id => $row) {
    $item = [];

    $color = '';
    if (!empty($row['ref']['color'])) {
        $colorType = 'Country';
        if(!empty($this->compare)){
            $colorType = 'Macro';
        }
        $color = '<span class="fa fa-circle" style="color: #' . $row['ref']['color'] . '"  data-toggle="tooltip" data-placement="bottom" title="'.$colorType.' Reference Color"></span> ';
    }

    $label = '';
    if (!empty($row['ref']['country'])) {
        $label = $row['ref']['country'];
    }
    if (!empty($row['ref']['note'])) {
        $label .= '<sup title="Click to show/hide notes" data-toggle="tooltip" data-placement="right"><a href="#" class="chart-notes-btn">' . $row['ref']['note'] . '</a></sup>';
    }
    $item[] = '<th>' . $color . $label . '</th>';

    $item[] = '<th>' . (!empty($row['ref']['macro']) ? ActiveRecordMacroData::model()->getListLabel("listMacro", $row['ref']['macro']) : '-') . '</th>';

    $variant = '';
    if (!empty($row['ref']['variant'])) {
        $variant = $this->getListLabel('listVariant', $row['ref']['variant']);
        if ($variant == 'Index points') {
            $variant = 'Index';
        }
    }
    $item[] = '<th>' . $variant . '</th>';

    foreach ($row['values'] as $cell) {
        $td = '<td class="text-right">';
        if(is_null($cell)){
            $td .= '-';
        }else if ($this->variant == 'Percent') {
            $td .= number_format($cell, 4) . '%';
        } else {
            if ($this->getListLabel('listVariant', $this->variant) == 'US Dollars') {
                //echo Yii::app()->format->currency($cell);
                $td .= '$' . number_format(floatval($cell), 4);
            } else {
                $td .= number_format($cell, 4);
            }
        }
        $td .= '</td>';
        $item[] = $td;
    }

    $item[] = '<th>' . (!empty($row['ref']['source']) ? $row['ref']['source'] : '-') . '</th>';

    $rows[] = $item;
    $i++;
}

if($this->view == 'pdf'){

    $vertical = [];
    foreach ($cols as $j => $cell) {
        $vertical[$j] = [$cell];
    }
    foreach ($rows as $i => $row) {
        foreach ($row as $j => $cell) {
            $vertical[$j][] = $cell;
        }
    }
    $this->render('//chart/tableVer', [
        'vertical' => $vertical,
    ]);

}else{
    $this->render('//chart/tableHor', [
        'cols' => $cols,
        'rows' => $rows,
    ]);
}

?>

<?php
if (!empty($this->notes['notes'])) {
    echo '<p class="chart-notes"><small>' . implode('<br />', $this->notes['notes']) . '</small></p>';
}
?>