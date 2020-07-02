<?php

/**
 * This is the model class for table "db_user".
 *
 * The followings are the available columns in table 'db_user':
 * @property integer           $id
 * @property string            $status
 * @property string            $type
 * @property string            $email
 * @property string            $password
 * @property string            $company
 * @property integer           $companyId
 * @property string            $fName
 * @property string            $sName
 * @property string            $phone
 * @property string            $address
 * @property string            $hash
 * @property string            $preferences
 * @property string            $recentSearch
 * @property integer           $verifyEmail
 * @property integer           $tour
 * @property integer           $tourLogin
 * @property integer           $terms
 * @property integer           $termsBeta
 * @property integer           $expire
 * @property string            $tools
 * @property integer           $unsubscribe
 * @property integer           $allowMultiLogin
 * @property integer           $updated
 * @property integer           $created
 *
 * @property DbUserFavorites[] $favorites
 * @property DbUserFavorites[] $favoritesCommodity
 * @property DbUserFavorites[] $favoritesCurrency
 * @property DbUserFavorites[] $favoritesEquity
 * @property DbUserFavorites[] $favoritesMacro
 * @property DbUserFavorites[] $favoritesTrade
 * @property DbUserService[]   $services
 */
class DbUser extends ActiveRecord {

    public $passwordNew;
    public $passwordConfirm;

    protected function afterConstruct () {
        parent::afterConstruct();
        $this->generateHash();
        $this->formatCommaSeparated('tools', 'array');
        $this->formatSerialised('preferences', 'array');
        $this->formatSerialised('recentSearch', 'array');
    }

    protected function afterFind () {
        parent::afterFind();
        $this->formatCommaSeparated('tools', 'array');
        $this->formatSerialised('preferences', 'array');
        $this->formatSerialised('recentSearch', 'array');
        $this->setDefaults();
        //$this->setDefaultFavorites();
    }

    protected function beforeValidate () {
        $this->updatePassword();
        $this->setDefaults();
        $this->convertTime('expire', 'integer');
        $this->formatCommaSeparated('tools', 'string');
        $this->formatSerialised('preferences', 'string');
        $this->formatSerialised('recentSearch', 'string');

        return parent::beforeValidate();
    }

    protected function afterValidate () {
        parent::afterValidate();
        $this->formatCommaSeparated('tools', 'array');
        $this->formatSerialised('preferences', 'array');
        $this->formatSerialised('recentSearch', 'array');
    }

    protected function beforeSave () {
        $this->formatCommaSeparated('tools', 'string');
        $this->formatSerialised('preferences', 'string');
        $this->formatSerialised('recentSearch', 'string');

        return parent::beforeSave();
    }

    protected function afterSave () {
        parent::afterSave();
        $this->passwordNew = null;
        $this->passwordConfirm = null;
        $this->formatCommaSeparated('tools', 'array');
        $this->formatSerialised('preferences', 'array');
        $this->formatSerialised('recentSearch', 'array');
        $this->updateRoles();
        //$this->setDefaultFavorites();
    }

    protected function beforeDelete () {
        if (!empty($this->watchlist)) {
            foreach ($this->watchlist as $item) {
                $item->delete();
            }
        }
        if (!empty($this->favorites)) {
            foreach ($this->favorites as $item) {
                $item->delete();
            }
        }

        foreach (array_keys(Yii::app()->authManager->getAuthAssignments($this->id)) as $role) {
            Yii::app()->authManager->revoke($role, $this->id);
        }

        return parent::beforeSave();
    }

    /**
     * @return string the associated database table name
     */
    public function tableName () {
        return 'db_user';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules () {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('status, type, email, fName, password, hash', 'required'),

            array('companyId, verifyEmail, tour, tourLogin, terms, termsBeta, expire, unsubscribe, allowMultiLogin, updated, created', 'numerical', 'integerOnly' => true),
            array('status, type, email, password, company, fName, sName, phone, hash, tools', 'length', 'max' => 45),
            array('company', 'length', 'max' => 100),

            //array('phone', 'match', 'pattern' => '/^([+]?[0-9 ]+)$/'),
            array('email', 'email'),
            array('email', 'unique', 'allowEmpty' => true),
            array('updated', 'customValidation'),

            array('updated', 'default', 'value' => time(), 'setOnEmpty' => false),
            array('created', 'default', 'value' => time(), 'setOnEmpty' => false, 'on' => 'insert'),

            array('status, type', 'unsafe', 'on' => 'limitAccess'),

            array('preferences, recentSearch, address, passwordNew, passwordConfirm', 'safe'),
            array('id, status, type, email, password, company, companyId, fName, sName, phone, address, hash, verifyEmail, tour, tourLogin, terms, termsBeta, expire, tools, unsubscribe, allowMultiLoginupdated, created', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations () {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'companyModel' => array(self::BELONGS_TO, 'DbCompany', 'companyId'),
            'logs' => array(self::HAS_MANY, 'DbUserLog', 'userId'),
            'favorites' => array(self::HAS_MANY, 'DbUserFavorites', 'userId'),
            'favoritesCommodity' => array(self::HAS_MANY, 'DbUserFavorites', 'userId', 'on' => 'favoritesCommodity.type="commodity"'),
            'favoritesCurrency' => array(self::HAS_MANY, 'DbUserFavorites', 'userId', 'on' => 'favoritesCurrency.type="currency"'),
            'favoritesEquity' => array(self::HAS_MANY, 'DbUserFavorites', 'userId', 'on' => 'favoritesEquity.type="equity"'),
            'favoritesMacro' => array(self::HAS_MANY, 'DbUserFavorites', 'userId', 'on' => 'favoritesMacro.type="macro"'),
            'favoritesTrade' => array(self::HAS_MANY, 'DbUserFavorites', 'userId', 'on' => 'favoritesTrade.type="trade"'),
            'services' => array(self::HAS_MANY, 'DbUserService', 'userId'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels () {
        return array(
            'id' => 'ID',
            'status' => 'Login Status',
            'type' => 'Account Type',
            'email' => 'Email',
            'password' => 'Password',
            'Company' => 'Company Name',
            'companyId' => 'Company Name',
            'fName' => 'First Name',
            'sName' => 'Last Name',
            'phone' => 'Phone',
            'address' => 'Postal Address',
            'hash' => 'Hash',
            'updated' => 'Updated',
            'created' => 'Created',
            'passwordNew' => 'New Password',
            'passwordConfirm' => 'Confirm New Password',
            'preferences' => 'Display Preferences',
            'recentSearch' => 'Recent Searches',
            'verifyEmail' => 'Verified Email Address',
            'tour' => 'Tutorial Status',
            'tourLogin' => 'Tutorial Status', //Show tutorial on next login
            'terms' => 'Agreed to Terms',
            'termsBeta' => 'Agreed to Beta Terms',
            'expire' => 'Account expiry date',
            'allowMultiLogin' => 'Allow Multiple Login Sessions',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search ($options = null) {
        $criteria = new CDbCriteria;
        $criteria->with = ['companyModel'];

        $criteria->compare('id', $this->id);
        $criteria->compare('status', $this->status);
        $criteria->compare('email', $this->email);
        $criteria->compare('password', $this->password);
        $criteria->compare('company', $this->company, true);
        $criteria->compare('companyId', $this->companyId);
        $criteria->compare('fName', $this->fName, true);
        $criteria->compare('sName', $this->sName, true);
        $criteria->compare('phone', $this->phone, true);
        $criteria->compare('address', $this->address, true);
        $criteria->compare('hash', $this->hash);
        $criteria->compare('preferences', $this->preferences);
        $criteria->compare('recentSearch', $this->recentSearch);
        $criteria->compare('tour', $this->tour);
        $criteria->compare('verifyEmail', $this->verifyEmail);
        $criteria->compare('tourLogin', $this->tourLogin);
        $criteria->compare('terms', $this->terms);
        $criteria->compare('termsBeta', $this->termsBeta);
        $criteria->compare('expire', $this->expire);
        $criteria->compare('unsubscribe', $this->unsubscribe);
        $criteria->compare('allowMultiLogin', $this->allowMultiLogin);
        $criteria->compare('updated', $this->updated);
        $criteria->compare('created', $this->created);

        $criteria = $this->compareCommaSeparated($criteria, 'type', $this->type);

        if (!Yii::app()->user->checkAccess('dev')) {
            $criteria->addNotInCondition('t.type', ['dev']);
        }

        $options['sortDefault'] = 'companyModel.name ASC, fName ASC, sName ASC';

        $options['sortAttributes']['fullName'] = array(
            'asc' => 'companyModel.name, fName, sName',
            'desc' => 'companyModel.name DESC, fName DESC, sName DESC',
        );

        $searchAttrs = array(
            'companyModel.name',
            't.fName',
            't.sName',
            't.email',
            't.phone',
        );
        $criteria = $this->addSearchTerms($criteria, $searchAttrs);

        return $this->formatSearch($this, $criteria, $options);
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return DbUser the static model class
     */
    public static function model ($className = __CLASS__) {
        return parent::model($className);
    }

    public function customValidation ($attribute, $params) {
        if (empty($this->company) && empty($this->fName) && empty($this->sName)) {
            $this->addError($attribute, 'You must complete at least 1 name field');
        }
    }

    public function hasRole ($role) {
        if (empty($this->id) || empty($role)) {
            return false;
        }

        return Yii::app()->authManager->isAssigned($role, $this->id);
    }

    public function hasType ($type) {
        if (!empty($this->type)) {
            if (!is_array($this->type)) {
                $this->formatCommaSeparated('type', 'array');
            }
            if (in_array($type, $this->type)) {
                return true;
            }
        }

        return false;

        return Yii::app()->authManager->isAssigned($role, $this->id);
    }

    public function isExpired () {
        $valid = false;
        if (!empty($this->expire) && $this->expire < time()) {
            $valid = true;
        }

        return $valid;
    }

    public function getRoles () {
        $list = array();

        $roles = array_keys(array_filter(Yii::app()->authManager->roles, function ($data) {
            return !in_array($data->name, Yii::app()->authManager->defaultRoles);
        }));
        foreach ($roles as $i => $roleName) {
            if (Yii::app()->authManager->isAssigned($roleName, $this->id)) {
                $list[] = $roleName;
            }
        }

        return $list;
    }

    public function generateHash () {
        if (empty($this->hash)) {
            $salt = Yii::app()->params['privateSalt'];
            $str = time() . $this->email . rand(111111, 999999);
            $encrypted = base64_encode(md5($salt . md5(hash('SHA512', $str . $salt) . $salt)));
            $this->hash = substr($encrypted, 0, 40);

            return true;
        }

        return false;
    }

    public function getAlerts ($limit = null) {
        $criteria = new CDbCriteria();
        $criteria->compare('userId', $this->id);
        $criteria->limit = $limit;

        return DbMessage::model()->findAll($criteria);
    }

    public function gridAccount () {
        $return = '';
        $return .= ' <span class="fa fa-power-off ' . ($this->status == 'inactive' ? 'text-danger' : 'text-success') . '" data-toggle="tooltip" title="' . $this->getAttributeLabel('status') . '"></span>';
        $return .= ' <span class="fa fa-lock ' . (!in_array($this->type, ['admin', 'dev']) ? 'text-danger' : 'text-success') . '" data-toggle="tooltip" title="Admin Access"></span>';
        $return .= ' <span class="fa fa-envelope ' . (empty($this->verifyEmail) ? 'text-danger' : 'text-success') . '" data-toggle="tooltip" title="' . $this->getAttributeLabel('verifyEmail') . '"></span>';
        $return .= ' <span class="fa fa-check ' . (empty($this->terms) ? 'text-danger' : 'text-success') . '" data-toggle="tooltip" title="' . $this->getAttributeLabel('terms') . '"></span>';
        $return .= ' <span class="fa fa-cog ' . (empty($this->termsBeta) ? 'text-danger' : 'text-success') . '" data-toggle="tooltip" title="' . $this->getAttributeLabel('termsBeta') . '"></span>';
        $return .= ' <span class="fa fa-info ' . (empty($this->tourLogin) ? 'text-danger' : 'text-success') . '" data-toggle="tooltip" title="' . $this->getAttributeLabel('tourLogin') . '"></span>';

        return $return;
    }

    public function listCompany () {
        $list = [];
        $models = DbCompany::model()->findAll();
        if (!empty($models)) {
            foreach ($models as $model) {
                $list[$model->id] = $model->name;
            }
            asort($list);
        }

        return $list;
    }

    public function listTour () {
        return array(
            0 => 'Don\'t Show tutorial on next login',
            1 => 'Show tutorial on next login',
        );
    }

    public function listType () {
        return [
            'user' => 'User',
            'admin' => 'Admin',
            'dev' => 'Developer',
        ];
        /*$list = array();
        $roles = Yii::app()->authManager->roles;
        if (!empty($roles)) {
            foreach ($roles as $key => $role) {
                switch ($role->name) {
                    case 'dev':
                        $name = 'Developer';
                        break;
                    case 'premium':
                        $name = 'Premium Account';
                        break;
                    case 'trial':
                        $name = 'Free Account';
                        break;
                    default:
                        $name = ucfirst($role->name);
                }
                $list[$key] = $name;
            }
            asort($list);
        }

        return $list;*/
    }

    public function listTools () {
        return array(
            'economic' => 'Economic Inditators',
            'market' => 'Markets',
        );
    }

    public function setDefaults () {
        $this->generateHash();

        if (empty($this->password)) {
            $ui = new UserIdentity(null, null);
            $ui->user = $this;
            $this->password = $ui->generatePassword();
        }

        if (empty($this->status)) {
            $this->status = 'active';
        }

        if (empty($this->type)) {
            $this->type = 'user';
        }

        $preferences = array(
            'home-macro-fav-tab' => 'open',
            'home-trade-fav-tab' => 'open',
            'home-commodity-fav-tab' => 'open',
        );
        $temp = $this->preferences;
        foreach ($preferences as $key => $val) {
            if (!isset($temp[$key])) {
                $temp[$key] = $val;
            }
        }
        $this->preferences = $temp;

        if (empty($this->tools) && empty($this->id)) {
            $this->tools = [
                'economic',
                'market',
            ];
        }
    }

    public function setDefaultFavorites ($type = null) {
        $maintenance = new Maintenance();
        $maintenance->cleanFavorites($this->id);

        if (!empty($this->id) && $this->status == 'active') {
            $dash = [];

            $companyId = [0];
            $user = Yii::app()->controller->user;
            if(!empty($user->companyId)){
                $companyId[] = $user->companyId;
            }

            $defaults = DbReporters::model()->findAllByAttributes(array(
                'searchDef' => 1
            ));
            $dash['trade'] = $defaults;
            if ((empty($type) || $type == 'tra') && Yii::app()->authManager->checkAccess('tool-tra', $this->id)) {
                if (!empty($defaults)) {
                    foreach ($defaults as $default) {
                        DbUserFavorites::model()->assign($this->id, 'trade', array(
                            'reporter' => $default->ccode3,
                            'reportValue' => 'TT',
                            'reportType' => 'trade',
                            'indicator' => 'trade-none-tt',
                        ));
                    }
                }
            }

            $defaults = DbCommodities::model()->findAllByAttributes(array(
                'searchDef' => 1,
                'companyId' => $companyId,
            ));
            $dash['commodity'] = $defaults;
            if ((empty($type) || $type == 'com') && Yii::app()->authManager->checkAccess('tool-com', $this->id)) {
                if (!empty($defaults)) {
                    foreach ($defaults as $default) {
                        DbUserFavorites::model()->assign($this->id, 'commodity', array(
                            'item' => $default->code,
                            'compare' => '',
                        ));
                    }
                }
            }

            $defaults = DbCurrencies::model()->findAllByAttributes(array(
                'searchDef' => 1,
                'companyId' => $companyId,
            ));
            $dash['currency'] = $defaults;
            if ((empty($type) || $type == 'cur') && Yii::app()->authManager->checkAccess('tool-cur', $this->id)) {
                if (!empty($defaults)) {
                    foreach ($defaults as $default) {
                        DbUserFavorites::model()->assign($this->id, 'currency', array(
                            'item' => $default->code,
                            'compare' => '',
                        ));
                    }
                }
            }

            $defaults = DbEquities::model()->findAllByAttributes(array(
                'searchDef' => 1,
                'companyId' => $companyId,
            ));
            $dash['equity'] = $defaults;
            if ((empty($type) || $type == 'equ') && Yii::app()->authManager->checkAccess('tool-equ', $this->id)) {
                if (!empty($defaults)) {
                    foreach ($defaults as $default) {
                        DbUserFavorites::model()->assign($this->id, 'equity', array(
                            'item' => $default->code,
                            'compare' => '',
                        ));
                    }
                }
            }

            if((empty($type) || $type == 'dash') && !empty($dash)){
                $defaults = [
                    'spark' => [
//                        'SCF_CME_GC1' => 'commodity',
//                        'EURUSD' => 'currency',
//                        'INDEXBOM_SENSEX' => 'equity',
//                        'SCF_ICE_B1' => 'commodity',
//                        'SCF_CME_C1' => 'commodity',
                    ],
                    'yoy' => [
                        'SCF_SHFE_RB1' => 'commodity',
                        'USDJPY' => 'currency',
                        //'INDEXDJX_DJI' => 'equity',
                        'SENSEX_IND' => 'equity'
                    ],
                    'g10' => [
                        'GDP_AGR' => 'macro',
                    ],
                    'outlook' => [],
                ];

                foreach (['commodity', 'currency', 'equity'] as $group){
                    if(!empty($dash[$group])){
                        $j = 0;
                        foreach($dash[$group] as $i => $default){
                            if($i <= 4) {
                                $defaults['outlook'][$default->code] = $group;
                            }
                            if($j <= 4){
                                $defaults['spark'][$default->code] = $group;
                                $j++;
                            }
                        }
                    }
                }

                foreach($defaults as $key => $items){
                    foreach($items as $ref => $group){
                        $default = null;
                        switch ($key){
                            case 'outlook':
                            case 'spark':
                            case 'yoy':
                                DbUserDash::model()->assign($this->id, $key, [
                                    'item' => $ref,
                                    'market' => $group,
                                ]);
                                break;

                            case 'g10':
                                DbUserDash::model()->assign($this->id, $key, [
                                    'macro' => $ref,
                                ]);
                                break;
                        }
                    }
                }
            }
        }
    }

    public function setDefaultWatchlist () {
        if (!empty($this->id) && $this->status == 'active' && empty($this->watchCountries)) {
            $countries = DbReporters::model()->findAllByAttributes(array(
                'searchDef' => 1
            ));
            if (!empty($countries)) {
                foreach ($countries as $country) {
                    DbUserWatchlist::assign($country, $this->id, 'country');
                }
            }
        }
    }

    public function getFullName () {

        if (!empty($this->fName) && !empty($this->sName)) {
            return trim($this->fName . ' ' . $this->sName);
        } elseif (!empty($this->fName)) {
            return trim($this->fName);
        } elseif (!empty($this->sName)) {
            return trim($this->sName);
        } elseif (!empty($this->company)) {
            return trim($this->company);
        } elseif (!empty($this->email)) {
            return trim($this->email);
        }
    }

    public function getName () {
        if (!empty($this->fName)) {
            return trim($this->fName);
        } else {
            return $this->fullName;
        }
    }

    public function updatePassword () {
        if (!empty($this->passwordNew) || !empty($this->passwordConfirm)) {
            $ui = new UserIdentity(null, null);
            $ui->user = $this;

            $tempNew = $ui->encryption(str_replace(' ', '', $this->passwordNew));
            $tempConfirm = $ui->encryption(str_replace(' ', '', $this->passwordConfirm));

            $valid = true;

            if ($valid && (empty($tempNew) || empty($tempConfirm) || strlen($this->passwordConfirm) < 4 || strlen($this->passwordConfirm) > 24)) {
                $valid = false;
                $this->addError('passwordConfirm', 'Password must be between 4 and 24 characters long');
            }

            if ($valid && $tempNew != $tempConfirm) {
                $valid = false;
                $this->addError('passwordConfirm', 'Passwords do not match, please retype them');
            }

            if ($valid) {
                $this->password = $tempNew;
            }
        }
    }

    public function updateRoles () {
        if (!empty($this->id) && !empty($this->type) && $this->type != $this->modelOld->type) {
            foreach ($this->listType() as $key => $val) {
                if (Yii::app()->authManager->isAssigned($key, $this->id)) {
                    if ($this->type != $key) {
                        Yii::app()->authManager->revoke($key, $this->id);
                    }
                } else {
                    if ($this->type == $key) {
                        Yii::app()->authManager->assign($key, $this->id);
                    }
                }
            }
        }

        $roles = [];
        if (!empty($this->services)) {
            foreach ($this->services as $service) {
                if (empty($service->expire) || $service->expire > time()) {
                    $roles[] = 'sub-' . $service->tool . '-' . $service->level;
                }
            }
        }
        foreach (DbUserService::model()->listTool() as $key1 => $val1) {
            foreach (DbUserService::model()->listLevel() as $key2 => $val2) {
                $key = 'sub-' . $key1 . '-' . $key2;

                if (Yii::app()->authManager->isAssigned($key, $this->id)) {
                    if (!in_array($key, $roles)) {
                        Yii::app()->authManager->revoke($key, $this->id);
                    }
                } else {
                    if (in_array($key, $roles)) {
                        Yii::app()->authManager->assign($key, $this->id);
                    }
                }
            }
        }

    }
}
