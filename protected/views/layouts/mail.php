<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width"/>
    <title><?php echo Yii::app()->params['name']; ?></title>
    <?php
    echo '<link rel="stylesheet" type="text/css" href="'.YiiBase::getPathOfAlias('website') . '/css/foundation' . (YII_DEBUG ? '' : '.min') . '.css'.'">';
    echo '<link rel="stylesheet" type="text/css" href="'.YiiBase::getPathOfAlias('website') . '/css/foundation-emails' . (YII_DEBUG ? '' : '.min') . '.css'.'">';
    echo '<link rel="stylesheet" type="text/css" href="'.YiiBase::getPathOfAlias('website') . '/css/mail' . (YII_DEBUG ? '' : '.min') . '.css'.'">';
    ?>
</head>

<body>
<!-- <style> -->
<table class="body" data-made-with-foundation>
    <tr>
        <td class="float-center" align="center" valign="top">
            <center>
                <body>
                <!-- <style> -->
                <table class="body" data-made-with-foundation="">
                    <tr>
                        <td class="float-center" align="center" valign="top">
                            <center data-parsed="">
                                <table class="spacer float-center">
                                    <tbody>
                                    <tr>
                                        <td height="16px" style="font-size:16px;line-height:16px;">&#xA0;</td>
                                    </tr>
                                    </tbody>
                                </table>
                                <table align="center" class="container float-center">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <table class="row header">
                                                <tbody>
                                                <tr>
                                                    <th class="small-12 large-12 columns first last">
                                                        <table>
                                                            <tr>
                                                                <th>
                                                                    <table class="spacer">
                                                                        <tbody>
                                                                        <tr>
                                                                            <td height="16px" style="font-size:16px;line-height:16px;">&#xA0;</td>
                                                                        </tr>
                                                                        </tbody>
                                                                    </table>
                                                                    <h1 class="text-center logo">
                                                                        <a href="<?php echo Yii::getPathOfAlias('website'); ?>" title="<?php echo Yii::app()->params['name']; ?>">
                                                                            <center data-parsed="">
                                                                                <img src="<?php echo Yii::getPathOfAlias('website'); ?>/images/ci-logo.png" alt="<?php echo Yii::app()->params['name']; ?>"  align="center" class="float-center" />
                                                                            </center>
                                                                        </a>
                                                                    </h1>
                                                                </th>
                                                                <th class="expander"></th>
                                                            </tr>
                                                        </table>
                                                    </th>
                                                </tr>
                                                </tbody>
                                            </table>

                                            <?php echo $content; ?>

                                            <table class="row footer">
                                                <tbody>
                                                <tr>
                                                    <th class="small-12 large-12 columns first last">
                                                        <table>
                                                            <tr>
                                                                <th>
                                                                    <table class="spacer">
                                                                        <tbody>
                                                                        <tr>
                                                                            <td height="16px" style="font-size:16px;line-height:16px;">&#xA0;</td>
                                                                        </tr>
                                                                        </tbody>
                                                                    </table>
                                                                    <hr>
                                                                    <p><small>This E-mail has been sent by <?php echo Yii::app()->params['name']; ?>. This message (and any associated files) is intended only for the use of the individual to which it is addressed and may contain information that is confidential. If you are not the intended recipient you are hereby notified that any dissemination, copying or distribution of this message, or files associated with this message, is strictly prohibited.</small></p>

                                                                    <p class="text-center"><small>&copy; <?php echo Yii::app()->name. ' ' . date('Y'); ?>, All Rights Reserved</small></p>
                                                                </th>
                                                                <th class="expander"></th>
                                                            </tr>
                                                        </table>
                                                    </th>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <table class="spacer">
                                                <tbody>
                                                <tr>
                                                    <td height="16px" style="font-size:16px;line-height:16px;">&#xA0;</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </center>
                        </td>
                    </tr>
                </table>
                </body>
            </center>
        </td>
    </tr>
</table>
</body>
</html>