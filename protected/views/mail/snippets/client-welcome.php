<?php $url = Yii::app()->createAbsoluteUrl('site/login'); ?>

<table class="row">
    <tbody>
    <tr>
        <th class="small-12 large-12 columns first last">
            <table>
                <tr>
                    <th>
                        <?php echo $mailer->renderView('application.views.mail.snippets.common.spacer', array('size'=>32)); ?>
                        <h1 class="text-center">Thank You &amp; Welcome</h1>
                        <?php echo $mailer->renderView('application.views.mail.snippets.common.spacer'); ?>

                        <p>Hi <?php echo $user->fName; ?>,</p>

                        <p>I just wanted to drop a short note to thank and welcome you. We’re so thrilled you’re here! </p>

                        <p>Our interface is simple and concise. And our intention is to allow you to easily find relevant, comparable and highly visual data to address whatever needs you have - in your business, at your university, with government planning or to understand the environment around your investments. </p>

                        <p>Three things you need to know before getting started:</p>

                        <ol>
                            <li>As part of your subscription, you will receive a weekly info graphic via email. You’ll love those fancy charts!</li>
                            <li>We prepared a short tutorial to walk you through the interface, which will be automatically triggered on your first three visits. If you need a refresher in the future, you can always open the tutorial again from the main menu.</li>
                            <li>Inside the interface, you will have access to our helpful customer support. </li>
                        </ol>

                        <p>I’d love to continue the chit-chat. But I know you’re as excited as I am! So without further ado, here’s your special access: </p>

                        <?php echo $mailer->renderView('application.views.mail.snippets.common.spacer'); ?>

                        <?php echo $mailer->renderView('application.views.mail.snippets.common.button', array(
                                'label' => 'Login',
                                'url' => $url,
                        )); ?>

                        <?php echo $mailer->renderView('application.views.mail.snippets.common.spacer'); ?>

                        <p>Like you, we love data!</p>

                        <p>
                            Tony Nash<br />
                            CEO and Founder, Complete Intelligence
                        </p>
                    </th>
                    <th class="expander"></th>
                </tr>
            </table>
        </th>
    </tr>
    </tbody>
</table>