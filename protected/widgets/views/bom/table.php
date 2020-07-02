<?php
$forecastId = 0;
$items = $dataCols = array();
$cols = [
    '<th></th>',
    '<th>Weighting</th>',
];
$forecastHeader = '';
if (!empty($data['rows'])) {
    foreach ($data['rows'] as $row) {
        if (!empty($row['c'])) {
            foreach ($row['c'] as $id => $item) {
                if ($id == 0) {
                    $dataCols[] = $item['v'];
                    $cols[] = '<th class="text-right">' . ucfirst(str_replace('_', ' ', $item['v'])) . '</th>';
                } else {
                    if (empty($items[$id]) && !empty($data['cols'][$id])) {
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

$rows = [];
$i = 0;
$totals = [
    'weight' => 0,
    'months' => [],
];
foreach ($items as $id => $row) {
    if (empty($row['ref']['role'])) {
        $item = [];

        $color = '';
        if (!empty($row['ref']['color'])) {
            $color = '<span class="fa fa-circle" style="color: #' . $row['ref']['color'] . '"  data-toggle="tooltip" data-placement="bottom" title="Reference Color"></span> ';
        }

        $label = '';
        if (!empty($row['ref']['label'])) {
            $label = $row['ref']['label'];
        }

        $item[] = '<th>' . $color . $label . '</th>';

        $weighting = '';
        if (!empty($row['ref']['weighting'])) {
            $weighting = $row['ref']['weighting'] . '%';
            $totals['weight'] += $row['ref']['weighting'];
        }
        $item[] = '<th class="text-right">' . $weighting . '</th>';

        foreach ($row['values'] as $j => $cell) {
            $td = '<td class="text-right">';

            if (is_null($cell)) {
                $td .= '-';
            } else {
                $td .= number_format($cell, 2);
            }
            $td .= '</td>';

            $item[] = $td;

            if (empty($totals['months'][$j])) {
                $totals['months'][$j] = 0;
            }
            $totals['months'][$j] += $cell;
        }

        $rows[] = $item;
        $i++;
    }
}

$item = [
    '<th class="text-italic small">Weighted BOM Total</th>',
    '<th class="text-right text-italic small">' . $totals['weight'] . '%</th>',
];
foreach ($totals['months'] as $val) {
    $item[] = '<td class="text-right text-italic small">' . number_format($val, 2) . '</td>';
}
$rows[] = $item;


if ($this->view == 'pdf') {

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

} else {
    $this->render('//chart/tableHor', [
        'cols' => $cols,
        'rows' => $rows,
    ]);
}


?>