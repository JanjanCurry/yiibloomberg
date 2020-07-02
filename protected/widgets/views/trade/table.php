<?php
$labels = $colors = $items = $partners = array();
$cols = [
    '<th class="table-freeze"></th>',
    '<th class="table-freeze">Indicator</th>',
    '<th class="table-freeze">Partner / Commodity</th>',
];
if(!empty($data['cols'])) {
    foreach ($data['cols'] as $id => $col) {
        if ($id > 0) {

            $partner = 'None';
            if ($this->reportIndicator == 'top10') {
                $partner = '<span class="fa fa-circle" style="color: #' . $this->reporter->color . '"  data-toggle="tooltip" data-placement="bottom" title="Country Reference Color"></span> ' . $this->reporter->country;
            } else if (!empty($this->partner) || strpos($this->reportType, 'top10') !== false) {
                $partner = '<span class="fa fa-circle" style="color: #' . $col['color'] . '"  data-toggle="tooltip" data-placement="bottom" title="Country Reference Color"></span> ' . $col['label'];
            } else if (!empty($this->sector)) {
                $partner = $col['label'];
            }

            if(!empty($col['table'])){
                $col = $col['table'];
            }

            $temp = array();
            $temp['partner'] = $partner;
            $temp['label'] = $col['label'];
            if (!empty($col['color'])) {
                $temp['color'] = $col['color'];
            }
            $labels[$id] = $temp;
        }
    }

    if(!empty($data['rows'])) {
        foreach ($data['rows'] as $i => $row) {
            if (!empty($row['c'])) {
                foreach ($row['c'] as $id => $item) {
                    if ($id == 0) {
                        $cols[] = '<th class="text-right">' . $item['v'] . '</th>';
                    } else if (!empty($labels[$id])) {
                        $key = $labels[$id]['label'].'###'.$id;
                        if(empty($items[$key])){
                            $items[$key] = array();
                        }

                        $items[$key][] = $item['v'];
                        if(!empty($labels[$id]['color'])) {
                            $colors[$key] = $labels[$id]['color'];
                        }

                        $temp = explode(' (',$labels[$id]['partner']);
                        $partners[$key] = $temp[0];
                    }
                }
            }
        }
    }
}

$rows = [];
$i = 0;
foreach ($items as $ref => $row) {
    $item = [];
    $temp = explode('###',$ref);
    $label = $temp[0];
    $color = '';
    if(!empty($colors[$ref])){
        $color = '<span class="fa fa-circle" style="color: #'.$colors[$ref].'"  data-toggle="tooltip" data-placement="bottom" title="Country Reference Color"></span> ';
    }

    if($this->reportIndicator == 'top10') {
        $item[] = '<th>' . ($i + 1) . '</th>';
        $item[] = '<th>' . $indicator . '</th>';
        $item[] = '<th>' . $color . $label . '</th>';
    }else if(strpos($this->reportType, 'top10') !== false){
        $item[] = '<th>' . ($i + 1) . '</th>';
        $item[] = '<th>' . $indicator . '</th>';
        $item[] = '<th>' . $partners[$ref] . '</th>';
    }else{
        $item[] = '<th>' . $color . $label . '</th>';
        $item[] = '<th>' . $indicator . '</th>';
        $item[] = '<th>' . $partners[$ref] . '</th>';
    }
    foreach ($row as $cell) {
        $item[] = '<td class="text-right">' . (is_null($cell) ? '-' :  Yii::app()->format->currency($cell)) . '</td>';
    }

    $rows[] = $item;

    if(!empty($this->mom) && !empty($this->mom[$i])){
        $item = [];
        if($this->view == 'pdf') {
            $item[] = '<th class="text-italic small">Month on Month Change</th>';
            $item[] = '<th></th>';
            $item[] = '<th></th>';
        }else{
            $item[] = '<th class="text-italic small" colspan="3">Month on Month Change</th>';
        }
        foreach ($this->mom[$i] as $cell) {
            if (empty($cell) && $cell != 0) {
                $cell = '-';
            }else if ($cell < 0){
                $cell = '<span class="text-danger">'.number_format(round($cell,2), 2).'%</span>';
            }else{
                $cell = number_format(round($cell,2), 2).'%';
            }
            $item[] = '<td class="text-right text-italic small">'.$cell.'</td>';
        }
        $rows[] = $item;
    }

    if(!empty($this->qoq) && !empty($this->qoq[$i])){
        $item = [];
        if($this->view == 'pdf') {
            $item[] = '<th class="text-italic small">Quarter on Quarter Change</th>';
            $item[] = '<th></th>';
            $item[] = '<th></th>';
        }else{
            $item[] = '<th class="text-italic small" colspan="3">Quarter on Quarter Change</th>';
        }
        foreach ($this->qoq[$i] as $cell) {
            if (empty($cell) && $cell != 0) {
                $cell = '-';
            }else if ($cell < 0){
                $cell = '<span class="text-danger">'.number_format(round($cell,2), 2).'%</span>';
            }else{
                $cell = number_format(round($cell,2), 2).'%';
            }
            $item[] = '<td class="text-right text-italic small">'.$cell.'</td>';
        }
        $rows[] = $item;
    }

    if(!empty($this->yoy) && !empty($this->yoy[$i])){
        $item = [];
        if($this->view == 'pdf') {
            $item[] = '<th class="text-italic small">Year on Year Change</th>';
            $item[] = '<th></th>';
            $item[] = '<th></th>';
        }else{
            $item[] = '<th class="text-italic small" colspan="3">Year on Year Change</th>';
        }
        foreach ($this->yoy[$i] as $cell) {
            if (empty($cell) && $cell != 0) {
                $cell = '-';
            }else if ($cell < 0){
                $cell = '<span class="text-danger">'.number_format(round($cell,2), 2).'%</span>';
            }else{
                $cell = number_format(round($cell,2), 2).'%';
            }
            $item[] = '<td class="text-right text-italic small">'.$cell.'</td>';
        }
        $rows[] = $item;
    }

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
