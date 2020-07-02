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

    <p>There was an error while trying to load the page, please see below for details. The the error is persistent then please <?php echo CHtml::link('Contact Us', array('site/contact')); ?> and let us know how you arrived at the error.</p>

    <hr />

    <p>
        <?php
        switch($error['code']){
            case 400:
                echo '<p><strong>Error 401:</strong> This error means that your web brower tried to access the page incorrectly or the page was corrupted during transfer. Please try reloading the page.</p>';

                break;
            case 401:
                echo '<p><strong>Error 401:</strong> This error means that you do not have access to view this page, this usually occurs when your login session expires, please try re-logging in.</p>';

                break;
            case 403:
                echo '<p><strong>Error 401:</strong> This error means that you do not have access to view this page.</p>';
                break;

            case 404:
                echo '<p><strong>Error 404:</strong> This error means the address you entered manually or via clicking a hyperlink was spelt wrong or the page no longer exists. Check the address or try access the page from elsewhere on the system.</p>';
                break;

            case 500:
                echo '<p><strong>Error 500:</strong> This error can have a multiple meanings and usually occur when submitting forms.</p>';
                break;
            default:
        }
        ?>
    </p>
</div>
