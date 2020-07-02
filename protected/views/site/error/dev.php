<?php
/* @var $this SiteController */
/* @var $error array */
?>

<div class="content-header">
    <div class="container">
        <h3>Error: <?php echo $error['code']; ?></h3>
    </div>
</div>

<div class="container">
    <?php
    echo '<p>Error Message: <strong>'.$error['message'].'</strong></p>';
    echo '<p><strong>'.$error['file'].' (Line: '.$error['line'].')</strong></p>';
    echo '<hr />';
    echo '<p>'.nl2br($error['trace']).'</p>';
    ?>
</div>
