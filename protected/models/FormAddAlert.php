<?php

class FormAddAlert extends FormModel {

    public $save = 0;
    public $accountType;
    public $message;
    public $search;
    public $status;
    public $subject;
    public $subscription;

    public function rules () {
        return array(
            array('accountType, message, save, search, subscription, status, subject', 'safe'),
        );
    }

    public function attributeLabels () {
        return array(
            'save' => 'Save',
            'accountType' => 'Account Type',
            'message' => 'Notification',
            'search' => 'Search',
            'status' => 'Account Status',
            'subscription' => 'Subscription',
            'subject' => 'Subject',
        );
    }

    public function listAccountType () {
        return DbUser::model()->listType();
    }

    public function listStatus () {
        return DbUser::model()->listStatus();
    }

    public function listSubscription () {
        $list = [];
        foreach (DbUserService::model()->listTool() as $key1 => $val1) {
            $list[$val1] = [];
            foreach (DbUserService::model()->listLevel() as $key2 => $val2) {
                $list[$val1][$key1 . '-' . $key2] = $val1 . ': ' . $val2;
            }
        }

        return $list;
    }

    public function save(){
        $valid = false;
        if(!empty($this->save)){
            $message = new DbMessage();
            $message->setDefaults();
            $message->subject = $this->subject;
            $message->message = $this->message;

            $users = $this->search(['returnFormat' => 'array']);
            if(!empty($users)){
                $valid = true;
                foreach($users as $user){
                    $clone = clone $message;
                    $clone->userId = $user->id;

                    $clone->message = str_replace('[FirstName]', $user->fName, $clone->message);

                    $clone->save();
                }
            }
        }

        return $valid;
    }

    public function search($options = null){
        $user = new DbUser();
        $user->unsetAttributes();

        $userIds = [];
        if(!empty($this->subscription)) {
            $criteria = new CDbCriteria();
            $criteria->select = 'userId';
            $criteria->group = 'userId';
            foreach($this->subscription as $subscription) {
                $parts = explode('-', $subscription);
                $criteria2 = new CDbCriteria();
                $criteria2->compare('t.tool', $parts[0]);
                $criteria2->compare('t.level', $parts[1]);
                $criteria->mergeWith($criteria2, 'or');
            }
            $services = DbUserService::model()->findAll($criteria);
            if(!empty($services)){
                foreach ($services as $service){
                    $userIds[] = $service->userId;
                }
            }

        }

        $user->search = $this->search;
        $user->searchTerms = $user->explodeTerms($user->search);

        $criteria = new CDbCriteria();
        $criteria->with[] = 'services';
        $criteria->compare('unsubscribe', '0');
        if(!empty($userIds)){
            $criteria->compare('t.id', $userIds);
        }
        if(!empty($this->accountType)) {
            $criteria->compare('t.type', $this->accountType);
        }
        if(!empty($this->status)) {
            $criteria->compare('t.status', $this->status);
        }
        $options['criteria'] = $criteria;

        return $user->search($options);
    }

}