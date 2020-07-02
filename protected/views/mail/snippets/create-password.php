<?php $url = Yii::app()->createAbsoluteUrl('mail/link', array('hash' => $hash->hash)); ?>

<table class="row">
    <tbody>
    <tr>
        <th class="small-12 large-12 columns first last">
            <table>
                <tr>
                    <th>
                        <?php echo $mailer->renderView('application.views.mail.snippets.common.spacer', array('size'=>32)); ?>
                        <h1 class="text-center">Creating Your Account</h1>
                        <?php echo $mailer->renderView('application.views.mail.snippets.common.spacer'); ?>

                        <p class="text-center">Your account is almost ready to use. All that is left to do is set the password for your account. Click on the link below and you will be sent to a page where you can create your password.</p>

                        <?php echo $mailer->renderView('application.views.mail.snippets.common.button', array(
                                'label' => 'Create Password',
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