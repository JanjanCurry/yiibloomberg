<table class="row">
    <tbody>
    <tr>
        <th class="small-12 large-12 columns first last">
            <table>
                <tr>
                    <th>
                        <?php echo $mailer->renderView('application.views.mail.snippets.common.spacer', array('size'=>32)); ?>
                        <h1 class="text-center">Chart Export: <?php echo $source->title; ?></h1>
                        <?php echo $mailer->renderView('application.views.mail.snippets.common.spacer'); ?>

                        <p class="text-center"><?php echo nl2br($source->message); ?></p>
                    </th>
                    <th class="expander"></th>
                </tr>
            </table>
        </th>
    </tr>
    </tbody>
</table>