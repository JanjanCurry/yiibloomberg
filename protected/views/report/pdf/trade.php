<?php $pageCount = 0; ?>

<htmlpagefooter name="footerFront" class="pdf-footerFront">
    <table width="100%">
        <tr>
            <td width="45%" class="pdf-footer-left"></td>
            <td width="55%" class="pdf-footer-right">This document contains confidential IP of Complete Intelligence. No part of it may be circulated, quoted, or reproduced for distribution without prior
                written approval from Complete Intelligence.
            </td>
        </tr>
    </table>
</htmlpagefooter>

<htmlpagefooter name="footerCopy" class="pdf-footerCopy">
    <table width="100%">
        <tr>
            <td width="25%" class="pdf-footer-left"></td>
            <td width="50%" class="pdf-footer-middle">
                Superforecasting Streamlined <sup>&trade;</sup>
                <br>
                <b>www.completeintel.com</b>
            </td>
            <td width="25%" class="pdf-footer-right">&copy; Complete Intelligence <?php echo date('Y'); ?></td>
        </tr>
    </table>
</htmlpagefooter>

<htmlpagefooter name="footerPager" class="pdf-footerPager">
    <table width="100%">
        <tr>
            <td width="25%" class="pdf-footer-left"></td>
            <td width="50%" class="pdf-footer-middle">
                Superforecasting Streamlined <sup>&trade;</sup>
                <br>
                <b>www.completeintel.com</b>
            </td>
            <td width="25%" class="pdf-footer-right">{PAGENO}</td>
        </tr>
    </table>
</htmlpagefooter>

<htmlpagefooter name="footer1" class="pdf-footer1">
    <table width="100%">
        <tr>
            <td width="25%" class="pdf-footer-left">&copy; Complete Intelligence <?php echo date('Y'); ?></td>
            <td width="50%" class="pdf-footer-right">{PAGENO}</td>
        </tr>
    </table>
</htmlpagefooter>

<htmlpagefooter name="footer2" class="pdf-footer2">
    <table width="100%">
        <tr>
            <td width="100%" class="pdf-footer-left">&copy; Complete Intelligence <?php echo date('Y'); ?></td>
        </tr>
    </table>
</htmlpagefooter>

<div class="pdf-landscape report-country pdf-download">
    <div class="page page-front">
        <div class="page-content">
            <h1>Country Report Forecast Update</h1>
            <h2><?php echo date('F Y'); ?></h2>
        </div>
    </div>

    <?php
    foreach ($charts as $group) {
        if(!empty($group)) {
            if (is_array($group)) {
                foreach ($group as $chart) {
                    if (!empty($chart)) {
                        if (is_array($chart) && !empty($chart['title'])) {
                            echo '<div class="page page-logo-3 page-title"><h1>' . $chart['title'] . '</h1></div>';
                        } else {
                            echo $chart->pdf;
                        }
                    }
                }
            } else {
                echo $group->pdf;
            }
        }
    }
    ?>


   <?php /*?> <div class="page page-icon page-text">
        <h1>Helpful definitions</h1>
        <?php
        $links = array(
            'Consumer credit' => 'https://en.wikipedia.org/wiki/Credit_(finance)',
            'Consumer price index' => 'https://en.wikipedia.org/wiki/Consumer_price_index',
            'Consumer spending' => 'https://en.wikipedia.org/wiki/Consumer_spending',
            'Core inflation' => 'https://en.wikipedia.org/wiki/Core_inflation',
            'Disposable income' => 'https://en.wikipedia.org/wiki/Disposable_and_discretionary_income',
            'Export' => 'https://en.wikipedia.org/wiki/Export',
            'GDP' => 'https://en.wikipedia.org/wiki/Gross_domestic_product',
            'Goods and Services' => 'https://en.wikipedia.org/wiki/Goods_and_services',
            'Government revenues' => 'https://en.wikipedia.org/wiki/Government_revenue',
            'Government spending' => 'https://en.wikipedia.org/wiki/Government_spending',
            'Gross fixed capital formation' => 'https://en.wikipedia.org/wiki/Gross_fixed_capital_formation',
            'Imports' => 'https://en.wikipedia.org/wiki/Import',
            'Industrial production' => 'https://en.wikipedia.org/wiki/Industrial_production',
            'Inflation' => 'https://en.wikipedia.org/wiki/Inflation',
            'Producer price index' => 'https://en.wikipedia.org/wiki/Producer_price_index',
            'Trade' => 'https://en.wikipedia.org/wiki/Trade',
            'Trade balance' => 'https://en.wikipedia.org/wiki/Balance_of_trade',
            'Unemployment' => 'https://en.wikipedia.org/wiki/Unemployment',
            'Wages' => 'https://en.wikipedia.org/wiki/Wage',
        );
        $temp = array();

        echo '<div class="list-group" style="float: left; width: 50%;">';
        $i = 1;
        $break = ceil(count($links) / 2);
        foreach ($links as $name => $url) {
            if($i == $break){
                echo '</div><div class="list-group" style="float: right; width: 50%;">';
            }
            echo '<div class="list-group-item">' . $name . ': <a href="' . $url . '">' . $url . '</a></div>';
            $i++;
        }
        echo '</div>';
        ?>
    </div><?php */?>

    <div class="page page-back">

    </div>
</div>