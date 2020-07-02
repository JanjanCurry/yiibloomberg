<?php $url = Yii::app()->createAbsoluteUrl('mail/link', array('hash' => $hash->hash)); ?>

<table class="row">
    <tbody>
    <tr>
        <th class="small-12 large-12 columns first last">
            <table>
                <tr>
                    <th>
                        <?php echo $mailer->renderView('application.views.mail.snippets.common.spacer', array('size'=>32)); ?>
                        <h1 class="text-center">Forgot Your Password?</h1>
                        <?php echo $mailer->renderView('application.views.mail.snippets.common.spacer'); ?>

                        <p class="text-center">It happens. Click the link below to reset your password.</p>

                        <?php echo $mailer->renderView('application.views.mail.snippets.common.button', array(
                                'label' => 'Reset Password',
                                'url' => $url)
                        ); ?>
                    </th>
                    <th class="expander"></th>
                </tr>
            </table>
        </th>
    </tr>
    </tbody>
</table>