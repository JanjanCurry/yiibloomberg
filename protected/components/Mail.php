<?php class Mail extends CComponent {

    protected $log; //log of errors
    public $options; //options that control this class
    protected $mail; //new DbMailOutbox used to build the email
    protected $mailer; //swift mailer extension for physically sending emails
    protected $related; //models relating to the email (property, user, etc)
    protected $source; //ActiveRecord model on which related data will be gathered from
    protected $trigger; //DbMail Trigger in use

    protected $mailHashViews = array( //array is checked to see if view file needs $this->related['hash'] (DbMailHash)
        //DbMailTrigger->viewFile => $this->related
        'create-password' => array('ref' => 'user', 'action' => 'passwordCreate'),
        'forgot-password' => array('ref' => 'user', 'action' => 'passwordReset'),
    );

    /*
     * Merge given options with default options
     * @param array $options
     */
    protected function setOptions ($options = null) {
        $default = array(
            'addFlash' => true, //toggle for disabling the email summary popup
            'debug' => false, //don't send email
            'debug-show' => true, //when debug is set to true, script will stop and show errors after addMail()
            'debug-live' => false, //send email to admin email
            'debug-email' => Yii::app()->params['devEmail'], //admin email address uses with debug-live option (requires debug option to be set to false)
            'default-email' => Yii::app()->params['email'],
            'layout' => 'mail', // layout view file
            'related' => array(), //append the contents of this to $this->related and user for template keyword replacement
            'time' => time(), //the current time (use this when using time() in case seconds changes during lengthy processes)
        );
        $this->options = Yii::app()->format->options($options, $default);
    }


    ///////////////////////////////////////
    // INIT
    ///////////////////////////////////////

    /*
     * Init add mail
     * @param ActiveRecord $source
     * @param string $triggerRef
     * @param array $options
     * @return boolean
     */
    public function addMail ($source, $triggerRef, $options = null) {
        $valid = false;
        $this->runSetup($source, $triggerRef, $options);
        if (!$this->hasErrors()) {
            $this->getRelatedData();
            $this->setMailHash();
            $this->setRecipients();
            $this->setContent();
            $this->setAttachments();
            $this->setRunTime();
        }
        if ($this->saveMail()) {
            $valid = true;
        }

        //when in debug mode, kill process so that errors can be seen
        if ($this->options['debug'] && $this->options['debug-show']) {
            if ($this->hasErrors()) {
                Yii::app()->format->debug($this->log);
                exit;
            }
        }

        return $valid;
    }

    public function testMail ($source, $triggerRef, $options = null) {
        $valid = false;
        $this->runSetup($source, $triggerRef, $options);
        $this->options['debug'] = true;

        if (!$this->hasErrors()) {
            $this->getRelatedData();
            $this->setMailHash();
            $this->setRecipients();
            $this->setContent();
            $this->setAttachments();
            $this->setRunTime();
        }

        if ($this->saveMail()) {
            $valid = true;
        }

        //when in debug mode, kill process so that errors can be seen
        if ($this->options['debug'] && $this->options['debug-show']) {
            if ($this->hasErrors()) {
                Yii::app()->format->debug($this->log);
                exit;
            }
        }

        return $valid;
    }

    ///////////////////////////////////////
    // SETUP
    ///////////////////////////////////////

    /*
     * Using $this->source relations, get the data that will be used to construct the email
     */
    public function getRelatedData () {

        $this->related = $this->options['related'];
        $this->related['trigger'] = $this->trigger;
        $this->related['source'] = $this->source;

        $company = new DbUser();
        $company->company = Yii::app()->params['name'];
        $company->address = Yii::app()->params['address'];
        $company->email = Yii::app()->params['email'];
        $company->phone = Yii::app()->params['phone'];
        $this->related['company'] = $company;

        if (!empty(Yii::app()->user) && !empty(Yii::app()->user->id)) {
            $user = DbUser::Model()->findByPk(Yii::app()->user->id);
            if (!empty($user)) {
                $this->related['sender'] = $user;
            }
        }
        if (empty($this->related['sender'])) {
            $this->related['sender'] = $company;
        }

        $other = array(
            'baseUrl' => Yii::app()->baseUrl,
            'webUrl' => Yii::getPathOfAlias('website'),
        );
        if (!empty($this->related['other'])) {
            $this->related['other'] = Yii::app()->format->options($other, $this->related['other']);
        } else {
            $this->related['other'] = $other;
        }

        switch (get_class($this->source)) {
            case 'DbUser':
                $this->related['user'] = $this->source;
                break;
        }

        //log errors for missing data
        if (!empty($this->related)) {
            foreach ($this->related as $key => $val) {
                if (!empty($val)) {
                    if (is_array($val)) {
                        foreach ($val as $subVal) {
                            if (empty($subVal)) {
                                $this->addError('Setup: missing some related data found for ' . $key);
                            }
                        }
                    }
                } else {
                    $this->addError('Setup: no related data found for ' . $key);
                }
            }
        } else {
            $this->addError('Setup: no related data found');
        }
    }

    /*
     * Setup class ready for processing
     * @param ActiveRecord $source
     * @param string $triggerRef
     * @param array $options
     */
    protected function runSetup ($source, $triggerRef, $options = null) {
        $this->setOptions($options);
        $this->source = $source;
        $this->related = array();
        $this->mail = new DbMailOutbox();
        Yii::import('ext.YiiMailer.YiiMailer');
        $this->mailer = new YiiMailer();

        if (!empty($triggerRef) && is_object($triggerRef) && get_class($triggerRef) == 'DbMailTrigger') {
            $this->trigger = $triggerRef;
        } else {
            $this->trigger = DbMailTrigger::model()->findByAttributes(array('ref' => $triggerRef));
        }

        if (!empty($this->trigger)) {
            if ($this->trigger->status == 'inactive') {
                $this->addError('Setup: Trigger is not turned on');
            }
        } elseif (!empty($triggerRef)) {
            $this->addError('Setup: Trigger not found');
        }
    }

    protected function setMailHash () {
        if (!empty($this->trigger) && !empty($this->trigger->viewFile) && array_key_exists($this->trigger->viewFile, $this->mailHashViews)) {
            $data = $this->mailHashViews[$this->trigger->viewFile];
            if (!empty($this->related[$data['ref']])) {
                $hash = DbMailHash::add($this->related[$data['ref']], $data['action'], $data);
                if (!empty($hash)) {
                    $this->related['hash'] = $hash;
                }
            }
            if (empty($this->related['hash'])) {
                $this->addError('Setup: Filed to generate mail hash');
            }
        }
    }


    ///////////////////////////////////////
    // FORMAT
    ///////////////////////////////////////

    public function emogrify ($content) {
        //////////////////////////////
        //Emogrifier - convert css file to inline styling for better css support in mail clients
        //get full html (layout file + body from DB) and then unset the layout so it wont be re-inserted when sent
        $mailer = new YiiMailer();
        $fullHtml = $mailer->MsgHTML($mailer->renderView('application.views.layouts.mail', array('content' => $content)), Yii::getPathOfAlias('webroot.images.mail'));

        //minify html
        $search = array(
            '/\>[^\S ]+/s',  // strip whitespaces after tags, except space
            '/[^\S ]+\</s',  // strip whitespaces before tags, except space
            '/(\s)+/s'       // shorten multiple whitespace sequences
        );
        $replace = array(
            '>',
            '<',
            '\\1'
        );
        //$fullHtml = preg_replace($search, $replace, $fullHtml);

        $inkCss = file_get_contents(YiiBase::getPathOfAlias('root') . '/css/foundation.css');
        $inkCss .= file_get_contents(YiiBase::getPathOfAlias('root') . '/css/foundation-emails.css');
        $inkCss .= file_get_contents(YiiBase::getPathOfAlias('root') . '/css/mail.css');
        //merge the css into the html and update the mail body with the merged html
        //Yii::import('root.vendor.emogrifier.Emogrifier');
        $emogrifier = new Emogrifier();
        $emogrifier->disableStyleBlocksParsing();
        $emogrifier->enableCssToHtmlMapping();
        $emogrifier->setHtml($fullHtml);
        $emogrifier->setCss($inkCss);

        return $emogrifier->emogrify();
        //////////////////////////////
    }

    /*
     * Replace all keywords in template data and update mail record
     */
    protected function setContent () {
        $subject = $this->mail->subject;
        if(!empty($this->options['subject'])){
            $subject = $this->options['subject'];
        }
        if(empty($subject) && !empty($this->trigger)) {
            $subject = $this->trigger->subject;
        }
        if (!empty($subject) && preg_match_all("/\[(.*?)?\]/", $subject, $keywords)) {
            if (!empty($keywords[1])) {
                foreach ($keywords[1] as $keyword) {
                    $subject = str_replace('[' . $keyword . ']', $this->replaceKeyword($keyword), $subject);
                }
            }
        }
        $this->mail->subject = $subject;

        if(!empty($this->options['body'])){
            $this->mail->body = $this->options['body'];
        }else if(!empty($this->trigger) && !empty($this->trigger->viewFile)) {
            $this->mail->body = $this->mailer->renderView('application.views.mail.snippets.' . $this->trigger->viewFile, $this->related);
        }
    }

    /*
     * convert a single keyword placeholder to real data
     * @param string $keyword
     * @return mixed
     */
    protected function replaceKeyword ($keyword) {
        $attrName = '';
        $format = '';
        $modelRef = '';
        $result = null;

        if (strpos($keyword, ':') !== false) {
            $parts = explode(':', $keyword);
            $format = $parts[0];
            $keyword = $parts[1];
        }

        $parts = explode('_', $keyword);
        if (!empty($parts[0]) && !empty($parts[1])) {
            $modelRef = $parts[0];
            $attrName = $parts[1];
        }

        if (!empty($modelRef)) {
            switch ($modelRef) {

                case 'other':
                    $model = new stdClass();
                    if (!empty($this->related['other'])) {
                        foreach ($this->related['other'] as $key => $attribute) {
                            $model->$key = $attribute;
                        }
                    }
                    break;

                default:
                    if (!empty($this->related[$modelRef])) {
                        if (is_array($this->related[$modelRef])) {
                            $model = $this->related[$modelRef][0];
                        } else {
                            $model = $this->related[$modelRef];
                        }
                    }
            }
            if (!empty($model)) {
                if (isset($model->$attrName)) {
                    $methodName = 'list' . ucfirst($attrName);
                    if (method_exists($model, $methodName) && is_array($model->$methodName())) {
                        $result = $model->getListLabel($methodName, $model->$attrName);
                    } else {
                        $result = $model->$attrName;
                    }
                } else {
                    $this->addError('Build: Can\'t find attribute in model : ' . $modelRef . '_' . $attrName);
                }
            } else {
                $this->addError('Build: Can\'t find model: ' . $modelRef);
            }
        }

        if (!empty($result) || $result === 0) {
            if (!empty($format)) {
                switch ($format) {
                    case 'boolean':
                        $result = Yii::app()->format->boolean($result);
                        break;
                    case 'camelCase':
                        $result = Yii::app()->format->camelCase($result);
                        break;
                    case 'currency':
                        $result = Yii::app()->format->currency($result);
                        break;
                    case 'date':
                        $result = date('D j F', $result);
                        break;
                    case 'datetime':
                        $result = date('D j F @ g:i a', $result);
                        break;
                    case 'number':
                        $result = Yii::app()->format->number(floatval($result));
                        break;
                    case 'shortCurrency':
                        $result = Yii::app()->format->shortCurrency($result);
                        break;
                    case 'time':
                        $result = date('g:i a', $result);
                        break;
                    case 'uppercase':
                        $result = ucfirst($result);
                        break;
                    case 'url':
                        $hash = 'unknown';
                        if (!empty($this->related[$modelRef]->hash)) {
                            $hash = $this->related[$modelRef]->hash;
                        }
                        $result = YiiBase::getPathOfAlias('website') . 'external/email-' . $hash . '/' . $keyword;
                        break;
                }
            }
        }

        return $result;
    }

    /*
     * get file path for attachment keywords
     */
    protected function setAttachments () {
        $attachments = array();
        if (!empty($this->options['attach'])) {
            if (is_array($this->options['attach'])) {
                foreach ($this->options['attach'] as $val) {
                    $attachments[] = $val;
                }
            }else{
                $attachments[] = $this->options['attach'];
            }
        }
        if (!empty($attachments)) {
            $this->mail->attach = $attachments;
        }
    }

    /*
     * replace keywords and validate email addresses for each recipient group
     */
    protected function setRecipients () {
        $types = array('sendTo', 'sendCc', 'sendBcc', 'sendFrom');
        $groups = array();
        $recipients = array();
        $emailValidator = new CEmailValidator;
        $emailValidator->allowName = true;

        //split and clean comma separated recipients
        foreach ($types as $type) {
            $groups[$type]  = array();
            //add recipient's set in DbMailTrigger
            if (!empty($this->trigger->$type)) {
                $temp = Yii::app()->format->commaSeparated($this->trigger->$type, 'array');
                if (!empty($temp)) {
                    foreach ($temp as $val) {
                        if (!empty($val)) {
                            $groups[$type][] = $val;
                        }
                    }
                }
            }

            //add recipient's set in $this->options
            if (!empty($this->options[$type])) {
                if (is_array($this->options[$type])) {
                    foreach ($this->options[$type] as $val) {
                        $groups[$type][] = $val;
                    }
                }else{
                    $groups[$type][] = $this->options[$type];
                }
            }
        }

        //replace keywords
        if (!empty($groups)) {
            foreach ($groups as $type => $keywords) {
                if (!empty($keywords)) {
                    foreach ($keywords as $keyword) {
                        $keyword = trim($keyword);
                        switch ($keyword) {
                            case 'sender':
                                if (!empty($this->related['sender']) && !empty($this->related['sender']->email)) {
                                    $recipients[$type][] = $this->related['sender']->email;
                                }
                                break;

                            default:
                                if (!empty($this->related[$keyword])) {
                                    if (is_array($this->related[$keyword])) {
                                        foreach ($this->related[$keyword] as $user) {
                                            if (!empty($user->email)) {
                                                $recipients[$type][] = $user->email;
                                            }
                                        }
                                    } else if (!empty($this->related[$keyword]->email)) {
                                        $recipients[$type][] = $this->related[$keyword]->email;
                                    }
                                } else {
                                    $recipients[$type][] = $keyword;
                                }
                        }
                    }
                }
            }
        }

        //validate email addresses
        if (!empty($recipients)) {
            $temp = array();
            foreach ($recipients as $type => $emails) {
                $temp[$type] = array();
                if (!empty($emails)) {
                    foreach ($emails as $email) {
                        $valid = true;

                        //validate email address
                        if (!$emailValidator->validateValue($email)) {
                            $valid = false;
                        }

                        //force removal from marketing emails if unsubscribed
                        //@todo add unsubscribe functionality

                        if ($valid) {
                            $temp[$type][] = $email;
                        }
                    }
                }
            }

            $recipients = $temp;
        }

        //comma separate formatted and valid recipients
        if (!empty($recipients)) {
            foreach ($recipients as $type => $emails) {
                if (!empty($emails)) {
                    if (is_array($emails)) {
                        $this->mail->$type = implode(',', $emails);
                    } else {
                        $this->mail->$type = $emails;
                    }
                }
            }
        }

        if (empty($this->mail->sendFrom)) {
            $this->mail->sendFrom = $this->options['default-email'];
        }
    }

    protected function setRunTime () {
        if (!empty($this->trigger->timeDelay)) {
            $delay = $this->trigger->timeDelay;
        }
        if (!empty($this->options['time'])) {
            $delay = $this->options['time'];
        }
        if (empty($delay)) {
            $delay = strtotime('+2 min');
        }
        if (is_numeric($delay)) {
            $this->mail->runTime = $delay;
        } else {
            $this->mail->runTime = strtotime($delay, $this->options['time']);
        }
    }


    ///////////////////////////////////////
    // SAVE / SEND
    ///////////////////////////////////////

    protected function saveMail () {
        $valid = false;

        //get related model id's used to build the email and keep for re-use/debugging
        if ($this->options['debug-live']) {
            $this->mail->sendTo = $this->options['debug-email'];
            $this->mail->sendCc = null;
            $this->mail->sendBcc = null;
        }

        //var_dump($this->mail->attributes);exit;
        if ($this->options['debug']) {
            if ($this->mail->validate()) {
                $valid = true;
            }
        } else {
            if ($this->mail->save()) {
                $valid = true;

                if ($this->options['addFlash']) {
                    $this->addFlash($this->mail->id);
                }
            }
        }

        if (!$valid && !empty($this->mail->errors)) {
            foreach ($this->mail->errors as $attr => $errors) {
                foreach ($errors as $error) {
                    $this->addError($attr . ': ' . $error);
                }
            }
        }

        return $valid;
    }

    /*
     * get all pending mail from the database and attempt to send it
     */
    public function sendPendingMail () {
        //get pending mail
        $criteria = new CDbCriteria();
        $criteria->compare('runTime', '<' . time());
        $criteria->compare('status', 'pending');
        $criteria->limit = 100;
        $criteria->order = 'runTime ASC';
        $emails = DbMailOutbox::Model()->findAll($criteria);
        if (!empty($emails)) {

            //get mailer extension
            Yii::import('ext.YiiMailer.YiiMailer');
            $this->mailer = new YiiMailer();

            foreach ($emails as $i => $email) {
                //clear mailer data from previous email
                $this->mailer->clear();

                //set recipients
                if(strpos($email->sendFrom,'@completeintel.com') !== false){
                    $this->mailer->setFrom($email->sendFrom, Yii::app()->params['name']);
                } else {
                    $this->mailer->setFrom($email->sendFrom, $email->sendFrom);
                }
                $this->mailer->Sender = $email->sendFrom;
                $this->mailer->ReturnPath = $email->sendFrom;

                if (strpos($email->sendTo, ',') !== false) {
                    $this->mailer->setTo(explode(',', $email->sendTo));
                } else {
                    $this->mailer->setTo($email->sendTo);
                }

                if (strpos($email->sendCc, ',') !== false) {
                    $this->mailer->setCc(explode(',', $email->sendCc));
                } else {
                    $this->mailer->setCc($email->sendCc);
                }

                if (strpos($email->sendBcc, ',') !== false) {
                    $this->mailer->setBcc(explode(',', $email->sendBcc));
                } else {
                    $this->mailer->setBcc($email->sendBcc);
                }
               

                //attach files
                if (!empty($email->attach)) {
                    $email->formatSerialised('attach', 'array');
                    foreach ($email->attach as $attachment) {
                        $path = $attachment;
                        if (!file_exists($path) || !is_file($path)) {
                            $path = YiiBase::getPathOfAlias('root') . '/' . $attachment;
                        }
                        $this->mailer->setAttachment($path);
                    }
                }

                //set email content
                $this->mailer->setBody($this->emogrify($email->body));
                $this->mailer->setSubject($email->subject);

                $this->mailer->isHTML(true);
                $this->mailer->clearLayout();
                $this->mailer->clearView();

                //send and log outcome
                if ($this->mailer->send()) {
                    $email->status = 'sent';
                    $email->sentTime = time();
                } else {
                    $email->status = 'failed';
                }
                $email->save();
            }
        }
    }

    ///////////////////////////////////////
    // LOGGING
    ///////////////////////////////////////

    public function clearOldMail () {
        $criteria = new CDbCriteria();
        $criteria->compare('updated', '<' . strtotime('-3 months'));
        $models = DbMailOutbox::Model()->findAll($criteria);
        foreach ($models as $model) {
            $model->delete();
        }

        $criteria = new CDbCriteria();
        $criteria->compare('expire', '<' . time());
        $criteria->compare('updated', '<' . strtotime('-2 weeks'));
        $models = DbMailHash::Model()->findAll($criteria);
        foreach ($models as $model) {
            if (empty($model->expire) || $model->validateExpiry()) {
                $model->delete();
            }
        }
    }

    /*
     * Add error to the log
     * @param string $error
     */
    protected function addError ($error) {
        $this->log[] = $error;
        if ($this->options['debug'] && $this->options['debug-show']) {
            echo '<pre>';
            var_dump($error);
            echo '</pre>';
        }
    }

    /*
     * Add email to the sent mail popup
     * @param int $id - DbMail->id
     */
    public static function addFlash ($id) {
        if (isset(Yii::app()->session)) {
            $ids = array();
            if (!empty(Yii::app()->session['pending-mail'])) {
                $ids = Yii::app()->session['pending-mail'];
            }
            $ids[] = $id;
            Yii::app()->session['pending-mail'] = $ids;
        }
    }

    /*
     * clear log of existing data
     */
    public function clearLog () {
        $this->log = null;
    }

    /*
     * check if any errors exist
     * @return boolean
     */
    protected function hasErrors () {
        if (!empty($this->log)) {
            return true;
        }

        return false;
    }


    ///////////////////////////////////////
    // GETTERS
    ///////////////////////////////////////

    public function getLog () {
        return $this->log;
    }

    public function getBody () {
        if (!empty($this->mail)) {
            return $this->mail->body;
        }
    }

    public function getMail () {
        return $this->mail;
    }

    public function getSource () {
        return $this->source;
    }
}