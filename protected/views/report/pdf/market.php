<?php $pageCount = 0; ?>

<htmlpagefooter name="footerFront" class="pdf-footerFront">
    <table width="100%">
        <tr>
            <td width="100%" class="pdf-footer-left">&copy; Complete Intelligence <?php echo date('Y'); ?></td>
        </tr>
    </table>
</htmlpagefooter>

<htmlpagefooter name="footerFront2" class="pdf-footerFront-2">
    <table width="100%">
        <tr>
            <td width="100%" class="pdf-footer-left">&copy; Complete Intelligence <?php echo date('Y'); ?></td>
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
            <td width="75%" class="pdf-footer-left">&copy; Complete Intelligence <?php echo date('Y'); ?></td>
            <td width="25%" class="pdf-footer-right">{PAGENO}</td>
        </tr>
    </table>
</htmlpagefooter>

<htmlpagefooter name="footerPager2" class="pdf-footerPager-2">
    <table width="100%">
        <tr>
            <td width="75%" class="pdf-footer-left">&copy; Complete Intelligence <?php echo date('Y'); ?></td>
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

<div class="pdf-landscape pdf-download report-market">

    <div class="page page-front-2">
        <div class="page-content">
            <h1>Market Report Forecast Update</h1>
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

    <div class="page page-back-2">

    </div>

</div>