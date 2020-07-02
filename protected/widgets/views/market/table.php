<?php
$forecastId = 0;
$items = $dataCols = array();
$cols = [
    '<th></th>',
    '<th>Units</th>',
    '<th class="text-right">Confidence</th>',
    //'<th class="text-right">Correlations</th>',
    //'<th class="text-right">R-Squared</th>',
];
$forecastHeader = '';
if (!empty($data['rows'])) {
    foreach ($data['rows'] as $row) {
        if (!empty($row['c'])) {
            foreach ($row['c'] as $id => $item) {
                if ($id == 0) {
                    $dataCols[] = $item['v'];
                    if ($item['v'] == $forecastDate) {
                        $forecastId = count($dataCols) - 1;
                        $forecastHeader = ' <span class="fa fa-info-circle" data-toggle="tooltip" title="Forecasted Data"></span>';
                    }
                    $cols[] = '<th class="text-right">' . $item['v'] . $forecastHeader . '</th>';
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
$cols[] = '<th>Notes</th>';
$cols[] = '<th>Source</th>';

$rows = [];
$i = 0;
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

        $correlationTop = '';
        /*if(!empty($row['ref']['correlations-top']) && $this->view != 'pdf'){
            $correlationTopItems = [
                $row['ref']['name'] . ': Correlations = '.number_format($row['ref']['correlations'], 4).' | R-Squared = '.number_format($row['ref']['rsq'], 4).'<br>',
                'Top Correlated Assets for '.$row['ref']['name']
            ];
            $count = 0;
            foreach($row['ref']['correlations-top'] as $key => $val){
                $count++;
                if(empty($val)){
                    $correlationTopItems[] = $count . '. ' . str_replace('_', ' ', $key);
                }else {
                    $correlationTopItems[] = $count . '. ' . str_replace('_', ' ', $key) . ': ' . number_format($val, 4);
                }
            }
            $correlationTop = ' <span class="tooltip-lg tooltip-text-left"><i class="fa fa-info-circle tooltip-lg" data-toggle="tooltip" data-placement="bottom" title="'.implode('<br>', $correlationTopItems).'"></i></span>';
        }*/

        $item[] = '<th>' . $color . $label . $correlationTop . '</th>';

        $variant = '';
        if (!empty($row['ref']['variant'])) {
            $variant = $this->getVariant($row['ref']['variant']);
            if ($variant == 'Index points') {
                $variant = 'Index';
            }
        }
        $item[] = '<th>' . $variant . '</th>';

        $confidence = '';
        if (!empty($row['ref']['confidence'])) {
            switch ($row['ref']['confidence']) {
                case 1:
                    $confidenceClass = 'table-confidence-1';
                    $confidenceTitle = 'Confidence: Low';
                    break;
                case 2:
                default:
                    $confidenceClass = 'table-confidence-2';
                    $confidenceTitle = 'Confidence: Average';
                    break;
                case 3:
                    $confidenceClass = 'table-confidence-3';
                    $confidenceTitle = 'Confidence: High';
                    break;
            }

            //$confidenceTitle .= '<br>Correlations: '.$row['ref']['correlations'].'<br>R-Squared: '.$row['ref']['rsq'];

            if ($this->view == 'pdf') {
                $confidence = str_replace(' Confidence', '', $confidenceTitle);
            } else {
                $confidence = '<span class="fa fa-circle ' . $confidenceClass . '" data-toggle="tooltip" data-placement="bottom" title="' . $confidenceTitle . '"></span>';
                $confidence .= ' <span class="fa fa-info-circle" data-toggle="tooltip" data-placement="bottom" title="We indicate red, amber and green for low, average and high levels of confidence, respectively. These levels are based on the statistical correlations as outlined below - High: 0.851-0.99 | Mid: 0.70-0.85 | Low: <0.70"></span>';
            }
        }
        $item[] = '<th class="text-center">' . $confidence . '</th>';

        /*$correlations = '';
        if (!empty($row['ref']['correlations'])) {
            $correlations = $row['ref']['correlations'];
        }*/
        //$item[] = '<th class="text-right">' . $correlations . '</th>';

//        $rsq = '';
//        if (!empty($row['ref']['rsq'])) {
//            $rsq = $row['ref']['rsq'];
//        }
        //$item[] = '<th class="text-right">' . $rsq . '</th>';

        foreach ($row['values'] as $j => $cell) {
            $td = '<td class="text-right ' . ($j >= $forecastId ? 'text-forecast' : '') . '">';

            if (is_null($cell)) {
                $td .= '-';
            } else if ($this->variant == 'Percent') {
                $td .= number_format($cell, 4) . '%';
            } else {
                if ($this->getVariant($this->variant) == 'US Dollars') {
                    $td .= '$' . number_format(floatval($cell), 4);
                } else if ($cell) {
                    $td .= number_format($cell, 4);
                }
            }
            $td .= '</td>';

            $item[] = $td;
        }

        $item[] = '<td>Monthly Average of the End of Day Closing Price</td>';
        $item[] = '<td>' . (!empty($row['ref']['source']) ? $row['ref']['source'] : '-') . '</td>';

        $colCount = count($item);
        $rows[] = $item;

        if (!empty($this->correlations) && !empty($this->correlations[$i]) && $this->view != 'pdf') {
            $item = [];
            $correlations = $this->correlations[$i];
            $correlationTopItems = [
                $row['ref']['name'] . ': Correlations = ' . number_format($correlations['correlations'], 4) . ' | R-Squared = ' . number_format($correlations['rsq'], 4)
            ];
            $count = 0;

            $correlationTopItems[] = '<br>';
            if (!empty($correlations['correlations-top'])) {
                foreach ($correlations['correlations-top'] as $key1 => $val1) {
                    if (!empty($val1)) {
                        $count = 0;
                        $temp = [];
                        if($key1 > 0) {
                            $temp[] = 'Top Correlated Assets (forecast from ' . $key1 . ' months ago)';
                        }else{
                            $temp[] = 'Top Correlated Assets';
                        }
                        foreach ($val1 as $key2 => $val2) {
                            $count++;
                            if (empty($val2)) {
                                $temp[] = $count . '. ' . str_replace('_', ' ', $key2);
                            } else {
                                $temp[] = $count . '. ' . str_replace('_', ' ', $key2) . ': ' . number_format($val2, 4);
                            }
                        }
                        $correlationTopItems[] = '<span class="correlation-row-top">'.implode('<br>', $temp).'</span>';
                    }
                }


//                $correlationTopItems[] = '';
//                $correlationTopItems[] = 'Top Correlated Assets for ' . $row['ref']['name'];
//                foreach ($correlations['correlations-top'] as $key => $val) {
//                    $count++;
//                    if (empty($val)) {
//                        $correlationTopItems[] = $count . '. ' . str_replace('_', ' ', $key);
//                    } else {
//                        $correlationTopItems[] = $count . '. ' . str_replace('_', ' ', $key) . ': ' . number_format($val, 4);
//                    }
//                }
                $item[] = '<th class="text-italic small correlation-row correlation-row-toggle">Correlations and top assets</th>';
            } else {
                $item[] = '<th class="text-italic small correlation-row correlation-row-toggle">Correlations</th>';
            }


            $item[] = '<td class="text-italic small" colspan="' . ($colCount - 1) . '"><span class="correlation-row-info">' . implode('', $correlationTopItems) . '</span></td>';
            $rows[] = $item;
        }

        $momToggle = '';
        if (!empty($this->percentage) && !empty($this->percentage[$i])) {
            $momToggle = 'mom-row';
        }

        if (!empty($this->mom) && !empty($this->mom[$i])) {
            $item = [];
            if ($this->view == 'pdf') {
                $item[] = '<th class="text-italic small">Month on Month Change</th>';
                $item[] = '<th></th>';
                $item[] = '<th></th>';
            } else {
                $item[] = '<th class="text-italic small ' . $momToggle . ' mom-row-toggle" data-group="' . $id . '" colspan="3">Month on Month Change</th>';
            }
            foreach ($this->mom[$i] as $cell) {
                if (empty($cell) && $cell != 0) {
                    $cell = '-';
                } else if ($cell < 0) {
                    $cell = '<span class="text-danger">' . number_format(round($cell, 4), 4) . '%</span>';
                } else {
                    $cell = number_format(round($cell, 4), 4) . '%';
                }
                $item[] = '<td class="text-right text-italic small">' . $cell . '</td>';
            }
            $item[] = '<td></td>';
            $item[] = '<td></td>';
            $rows[] = $item;
        }

        if (!empty($this->qoq) && !empty($this->qoq[$i])) {
            $item = [];
            if ($this->view == 'pdf') {
                $item[] = '<th class="text-italic small">Year on Quarter Change</th>';
                $item[] = '<th></th>';
                $item[] = '<th></th>';
            } else {
                $item[] = '<th class="text-italic small ' . $momToggle . ' mom-row-toggle" data-group="' . $id . '" colspan="3">Year on Quarter Change</th>';
            }
            foreach ($this->qoq[$i] as $cell) {
                if (empty($cell) && $cell != 0) {
                    $cell = '-';
                } else if ($cell < 0) {
                    $cell = '<span class="text-danger">' . number_format(round($cell, 4), 4) . '%</span>';
                } else {
                    $cell = number_format(round($cell, 4), 4) . '%';
                }
                $item[] = '<td class="text-right text-italic small">' . $cell . '</td>';
            }
            $item[] = '<td></td>';
            $item[] = '<td></td>';
            $rows[] = $item;
        }

        if (!empty($this->yoy) && !empty($this->yoy[$i])) {
            $item = [];
            if ($this->view == 'pdf') {
                $item[] = '<th class="text-italic small">Year on Year Change</th>';
                $item[] = '<th></th>';
                $item[] = '<th></th>';
            } else {
                $item[] = '<th class="text-italic small ' . $momToggle . ' mom-row-toggle" data-group="' . $id . '" colspan="3">Year on Year Change</th>';
            }
            foreach ($this->yoy[$i] as $cell) {
                if (empty($cell) && $cell != 0) {
                    $cell = '-';
                } else if ($cell < 0) {
                    $cell = '<span class="text-danger">' . number_format(round($cell, 4), 4) . '%</span>';
                } else {
                    $cell = number_format(round($cell, 4), 4) . '%';
                }
                $item[] = '<td class="text-right text-italic small">' . $cell . '</td>';
            }
            $item[] = '<td></td>';
            $item[] = '<td></td>';
            $rows[] = $item;
        }


        if (!empty($this->percentage) && !empty($this->percentage[$i])) {
            foreach ($this->percentage[$i] as $month => $percentage) {
                $item = [];
                if ($this->view == 'pdf') {
                    $item[] = '<th class="text-italic small">% change from previous forecast (' . $month . ' month)</th>';
                    $item[] = '<th></th>';
                    $item[] = '<th></th>';
                } else {
                    $item[] = '<th class="text-italic small mom-row" data-group="' . $id . '" colspan="3">% change from previous forecast (' . $month . ' month)</th>';
                }
                foreach ($percentage as $cell) {
                    if (empty($cell) && $cell != 0) {
                        $cell = '-';
                    } else if ($cell < 0) {
                        $cell = '<span class="text-danger">' . number_format(round($cell, 4), 4) . '%</span>';
                    } else {
                        $cell = number_format(round($cell, 4), 4) . '%';
                    }
                    $item[] = '<td class="text-right text-italic small">' . $cell . '</td>';
                }
                $item[] = '<td></td>';
                $item[] = '<td></td>';
                $rows[] = $item;
            }
        }

        $i++;
    }
}

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