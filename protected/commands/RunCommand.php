<?php

class RunCommand extends CConsoleCommand {

    protected $debug = false;

    /*
     * example CPanel cron command: php /home/CPANEL_ACCOUNT_NAME/public_html/admin/cron.php run sendPendingMail
     * example local Windows Command Line command: php cron.php run sendPendingMail
     */

    public function run($args) {
        foreach ($args as $arg) {
            $mail = new Mail();
            $maintenance = new Maintenance();

            if ($arg == 'test') {
                var_dump('start');
                //$maintenance->migratePermissions();
                //$maintenance->migratePermissions2();
                var_dump('end');
                exit;
            }

            if (!YII_DEBUG) {
                $this->cronCollision($arg, 'processing');
            }

            switch ($arg) {

                //Time based commands
                case 'minutely':
                    $mail->sendPendingMail();
                    $maintenance->cleanUserLog();
                    break;

                case 'hourly':
                    $maintenance->cleanImages();
                    $maintenance->cleanCsv();
                    $maintenance->cleanReports();
                    break;

                case 'daily':
                    $maintenance->cleanFavorites();
                    break;

                case 'weekly':
                    break;

                case 'monthly':
                    $maintenance->topAssets();
                    $maintenance->dateAvailability();
                    $maintenance->cleanLog();
                    break;
            }

            $this->cronCollision($arg, 'sleep');
        }
    }

    protected function cronCollision($arg, $status) {
        //get reusable cron flag
        $log = DbVar::model()->findByAttributes(array(
            'name' => $arg,
            'type' => 'cronCollision',
        ));

        if (empty($log)) {
            //must be a new cron, create a flag model
            $log = new DbVar();
            $log->name = $arg;
            $log->type = 'cronCollision';

        } elseif (!empty($log->data['status']) && $log->data['status'] == $status && $log->data['status'] == 'processing') {
            //the cron is already running
            if ($log->updated > strtotime('-15 min')) {
                //kill this instance of the cron to avoid a collision
                exit;
            }
            //its possible that the cron hit a fatal error, reset the flag and allow processing to continue
            $log->updated = time();
        }

        $length = '';
        if ($status == 'sleep' && !empty($log->updated)) {
            $length = Yii::app()->format->age($log->updated, time(), 'double');
        }


        //switch the cron flag on and off (processing / asleep)
        $log->data = array(
            'status' => $status,
            'length' => $length,
        );
        $log->save();
    }

}