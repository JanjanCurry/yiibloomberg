<?php

class FormDownload extends FormModel {

    protected $options;
    protected $image;

    public function downloadCsv ($charts, $filenameBase) {
        $data = array(
            'valid' => false,
        );

        if (!is_array($charts)) {
            $charts = array($charts);
        }

        $notes = $csvData = array();
        foreach ($charts as $chart) {
            if (!empty($chart->csvData)) {
                foreach ($chart->csvData as $key => $val) {
                    if ($key == 0) {
                        if (empty($csvData)) {
                            $csvData[] = $val;
                        }
                    } else {
                        $csvData[] = $val;
                    }
                }
            }

            if (!empty($chart->notes) && !empty($chart->notes['notes'])) {
                foreach ($chart->notes['notes'] as $key => $val) {
                    $notes[$key] = array($val);
                }
            }
        }

        if (!empty($csvData)) {
            $exists = true;
            $i = 0;
            while ($exists == true && $i < 1000) {
                $append = '';
                if ($i > 0) {
                    $append = '-' . $i;
                }
                $filename = 'CompleteIntelligence';
                $filename .= $filenameBase;
                $filename .= '-' . date('YmdHi');
                $filename .= $append . '.csv';
                $filename = Yii::app()->format->safeFileName($filename, 'csv');

                $newFile = '/images/csv/' . $filename;
                if (!file_exists(YiiBase::getPathOfAlias('root') . $newFile)) {
                    $exists = false;
                }
                $i++;
            }

            if (!empty($newFile)) {
                $csv = fopen(YiiBase::getPathOfAlias('root') . $newFile, 'w');
                foreach ($csvData as $cols) {
                    fputcsv($csv, $cols);
                }
                if (!empty($notes)) {
                    foreach ($notes as $cols) {
                        fputcsv($csv, $cols);
                    }
                }
                fclose($csv);
                if (file_exists(YiiBase::getPathOfAlias('root') . $newFile)) {
                    $data['valid'] = true;
                    $data['filename'] = $filename;
                    $data['url'] = YiiBase::getPathOfAlias('website') . $newFile;
                }
            }
        }

        return $data;
    }

    public function downloadImage ($base64Img, $options) {
        $this->options = Yii::app()->format->options($options, [
            'shortenUrl' => false,
            'reportType' => [],
            'legend' => null,
            'notes' => null,
            'source' => null,
            'title' => null,
            'color' => 'FFFFFF',
        ]);

        return $this->getImage($base64Img, $this->options);
    }

    public function downloadPdf ($charts, $filenameBase) {
        $valid = false;

        $exists = true;
        $i = 0;
        while ($exists == true && $i < 1000) {
            $append = '';
            if ($i > 0) {
                $append = '-' . $i;
            }
            $filename = 'CompleteIntelligence';
            $filename .= $filenameBase;
            $filename .= '-' . date('YmdHi');
            $filename .= $append . '.pdf';
            $filename = Yii::app()->format->safeFileName($filename, 'pdf');

            $newFile = '/images/charts/' . $filename;
            $path = YiiBase::getPathOfAlias('root') . $newFile;
            if (!file_exists(YiiBase::getPathOfAlias('root') . $newFile)) {
                $exists = false;
            }
            $i++;
        }

        if (!empty($charts)) {
            $mpdf = new \mPDF('utf-8', 'A4-L', 0, 'Open-Sans', 0, 0, 0, 0);
            $controller = Yii::app()->controller;
            $controller->registerLayout('pdf');
            $html = $controller->render('//report/single/pdf', [
                'title' => $charts[0]->getTypeLabel(),
                'subtitle' => date('F Y', $charts[0]->startTime) . ' - ' . date('F Y', $charts[0]->endTime),
                'charts' => $charts
            ], true);
            $css = file_get_contents(YiiBase::getPathOfAlias('root') . '/css/pdf.css');
            $mpdf->WriteHTML($css, 1);
            $mpdf->WriteHTML($html);
            //$mpdf->output();exit;
            $mpdf->Output($path, 'F');
            if (file_exists($path)) {
                $valid = true;
            }
        }

        return [
            'test' => $charts,
            'valid' => $valid,
            'filename' => $filename,
            'url' => YiiBase::getPathOfAlias('website') . $newFile,
        ];
    }

    private function getImage ($base64Img, $options) {
        $imgOptions = [
            'filenameData' => (!empty($options['reportType']) ? $options['reportType'] : null),
            'legend' => (!empty($options['legend']) ? $options['legend'] : null),
            'notes' => (!empty($options['notes']) ? $options['notes'] : null),
            'source' => (!empty($options['source']) ? $options['source'] : null),
            'title' => (!empty($options['title']) ? $options['title'] : null),
            'color' => (!empty($options['color']) ? $options['color'] : 'F1F1F1'),
        ];

        $image = new Image();
        $image->bgColor = $imgOptions['color'];
        if (is_array($base64Img)) {
            $valid = $image->createMultiImage($base64Img, $imgOptions);
        } else {
            $valid = $image->createImage($base64Img, $imgOptions);
        }

        if ($valid) {
            $url = Yii::app()->getBaseUrl(true) . $image->getUrlRelative();

            return [
                'filename' => $image->getFilename(),
                'shortUrl' => ($options['shortenUrl'] ? $this->getShortUrl($url) : ''),
                'url' => $url,
            ];
        }

        return false;
    }

    private function getShortUrl ($longurl) {
        // Bit.ly
        $url = "http://api.bit.ly/shorten?version=2.0.1&longUrl=$longurl&login=rm86&apiKey=R_daad42d22c06442bb42a17246052904e&format=json&history=1";

        $s = curl_init();
        curl_setopt($s, CURLOPT_URL, $url);
        curl_setopt($s, CURLOPT_HEADER, false);
        curl_setopt($s, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($s);
        curl_close($s);

        $obj = json_decode($result, true);
        if (!empty($obj["results"]["$longurl"]["shortUrl"])) {
            return $obj["results"]["$longurl"]["shortUrl"];
        }

        return '';
    }

}