<?php

/**
 * This is the model class for table "db_market_percent".
 *
 * The followings are the available columns in table 'db_market_percent':
 * @property integer $SLNO
 * @property integer $months
 * @property string $Asset_ID
 * @property string $Asset_Name
 * @property string $Type
 * @property string $Variant
 * @property integer $Confidence
 * @property string $Correlations
 * @property string $RSQ
 * @property integer $Source_ID
 * @property string $M201001
 * @property string $M201002
 * @property string $M201003
 * @property string $M201004
 * @property string $M201005
 * @property string $M201006
 * @property string $M201007
 * @property string $M201008
 * @property string $M201009
 * @property string $M201010
 * @property string $M201011
 * @property string $M201012
 * @property string $M201101
 * @property string $M201102
 * @property string $M201103
 * @property string $M201104
 * @property string $M201105
 * @property string $M201106
 * @property string $M201107
 * @property string $M201108
 * @property string $M201109
 * @property string $M201110
 * @property string $M201111
 * @property string $M201112
 * @property string $M201201
 * @property string $M201202
 * @property string $M201203
 * @property string $M201204
 * @property string $M201205
 * @property string $M201206
 * @property string $M201207
 * @property string $M201208
 * @property string $M201209
 * @property string $M201210
 * @property string $M201211
 * @property string $M201212
 * @property string $M201301
 * @property string $M201302
 * @property string $M201303
 * @property string $M201304
 * @property string $M201305
 * @property string $M201306
 * @property string $M201307
 * @property string $M201308
 * @property string $M201309
 * @property string $M201310
 * @property string $M201311
 * @property string $M201312
 * @property string $M201401
 * @property string $M201402
 * @property string $M201403
 * @property string $M201404
 * @property string $M201405
 * @property string $M201406
 * @property string $M201407
 * @property string $M201408
 * @property string $M201409
 * @property string $M201410
 * @property string $M201411
 * @property string $M201412
 * @property string $M201501
 * @property string $M201502
 * @property string $M201503
 * @property string $M201504
 * @property string $M201505
 * @property string $M201506
 * @property string $M201507
 * @property string $M201508
 * @property string $M201509
 * @property string $M201510
 * @property string $M201511
 * @property string $M201512
 * @property string $M201601
 * @property string $M201602
 * @property string $M201603
 * @property string $M201604
 * @property string $M201605
 * @property string $M201606
 * @property string $M201607
 * @property string $M201608
 * @property string $M201609
 * @property string $M201610
 * @property string $M201611
 * @property string $M201612
 * @property string $M201701
 * @property string $M201702
 * @property string $M201703
 * @property string $M201704
 * @property string $M201705
 * @property string $M201706
 * @property string $M201707
 * @property string $M201708
 * @property string $M201709
 * @property string $M201710
 * @property string $M201711
 * @property string $M201712
 * @property string $M201801
 * @property string $M201802
 * @property string $M201803
 * @property string $M201804
 * @property string $M201805
 * @property string $M201806
 * @property string $M201807
 * @property string $M201808
 * @property string $M201809
 * @property string $M201810
 * @property string $M201811
 * @property string $M201812
 * @property string $M201901
 * @property string $M201902
 * @property string $M201903
 * @property string $M201904
 * @property string $M201905
 * @property string $M201906
 * @property string $M201907
 * @property string $M201908
 * @property string $M201909
 * @property string $M201910
 * @property string $M201911
 * @property string $M201912
 * @property string $M202001
 * @property string $M202002
 * @property string $M202003
 * @property string $M202004
 * @property string $M202005
 * @property string $M202006
 * @property string $M202007
 * @property string $M202008
 * @property string $M202009
 * @property string $M202010
 * @property string $M202011
 * @property string $M202012
 */
class DbMarketPercent extends ActiveRecordMarketData {
	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'db_market_percent';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		return array(
			array('months, Confidence, Source_ID', 'numerical', 'integerOnly'=>true),
			array('Asset_ID, Asset_Name', 'length', 'max'=>255),
			array('Type', 'length', 'max'=>4),
			array('Variant', 'length', 'max'=>5),
			array('Correlations, RSQ, M201001, M201002, M201003, M201004, M201005, M201006, M201007, M201008, M201009, M201010, M201011, M201012, M201101, M201102, M201103, M201104, M201105, M201106, M201107, M201108, M201109, M201110, M201111, M201112, M201201, M201202, M201203, M201204, M201205, M201206, M201207, M201208, M201209, M201210, M201211, M201212, M201301, M201302, M201303, M201304, M201305, M201306, M201307, M201308, M201309, M201310, M201311, M201312, M201401, M201402, M201403, M201404, M201405, M201406, M201407, M201408, M201409, M201410, M201411, M201412, M201501, M201502, M201503, M201504, M201505, M201506, M201507, M201508, M201509, M201510, M201511, M201512, M201601, M201602, M201603, M201604, M201605, M201606, M201607, M201608, M201609, M201610, M201611, M201612, M201701, M201702, M201703, M201704, M201705, M201706, M201707, M201708, M201709, M201710, M201711, M201712, M201801, M201802, M201803, M201804, M201805, M201806, M201807, M201808, M201809, M201810, M201811, M201812, M201901, M201902, M201903, M201904, M201905, M201906, M201907, M201908, M201909, M201910, M201911, M201912, M202001, M202003, M202004, M202005, M202006, M202007, M202008, M202009, M202010, M202011, M202012', 'length', 'max'=>32),
			array('M202002', 'length', 'max'=>25),
			array('SLNO, months, Asset_ID, Asset_Name, Type, Variant, Confidence, Correlations, RSQ, Source_ID, M201001, M201002, M201003, M201004, M201005, M201006, M201007, M201008, M201009, M201010, M201011, M201012, M201101, M201102, M201103, M201104, M201105, M201106, M201107, M201108, M201109, M201110, M201111, M201112, M201201, M201202, M201203, M201204, M201205, M201206, M201207, M201208, M201209, M201210, M201211, M201212, M201301, M201302, M201303, M201304, M201305, M201306, M201307, M201308, M201309, M201310, M201311, M201312, M201401, M201402, M201403, M201404, M201405, M201406, M201407, M201408, M201409, M201410, M201411, M201412, M201501, M201502, M201503, M201504, M201505, M201506, M201507, M201508, M201509, M201510, M201511, M201512, M201601, M201602, M201603, M201604, M201605, M201606, M201607, M201608, M201609, M201610, M201611, M201612, M201701, M201702, M201703, M201704, M201705, M201706, M201707, M201708, M201709, M201710, M201711, M201712, M201801, M201802, M201803, M201804, M201805, M201806, M201807, M201808, M201809, M201810, M201811, M201812, M201901, M201902, M201903, M201904, M201905, M201906, M201907, M201908, M201909, M201910, M201911, M201912, M202001, M202002, M202003, M202004, M202005, M202006, M202007, M202008, M202009, M202010, M202011, M202012', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'SLNO' => 'Slno',
			'months' => 'Months',
			'Asset_ID' => 'Asset',
			'Asset_Name' => 'Asset Name',
			'Type' => 'Type',
			'Variant' => 'Variant',
			'Confidence' => 'Confidence',
			'Correlations' => 'Correlations',
			'RSQ' => 'Rsq',
			'Source_ID' => 'Source',
			'M201001' => 'M201001',
			'M201002' => 'M201002',
			'M201003' => 'M201003',
			'M201004' => 'M201004',
			'M201005' => 'M201005',
			'M201006' => 'M201006',
			'M201007' => 'M201007',
			'M201008' => 'M201008',
			'M201009' => 'M201009',
			'M201010' => 'M201010',
			'M201011' => 'M201011',
			'M201012' => 'M201012',
			'M201101' => 'M201101',
			'M201102' => 'M201102',
			'M201103' => 'M201103',
			'M201104' => 'M201104',
			'M201105' => 'M201105',
			'M201106' => 'M201106',
			'M201107' => 'M201107',
			'M201108' => 'M201108',
			'M201109' => 'M201109',
			'M201110' => 'M201110',
			'M201111' => 'M201111',
			'M201112' => 'M201112',
			'M201201' => 'M201201',
			'M201202' => 'M201202',
			'M201203' => 'M201203',
			'M201204' => 'M201204',
			'M201205' => 'M201205',
			'M201206' => 'M201206',
			'M201207' => 'M201207',
			'M201208' => 'M201208',
			'M201209' => 'M201209',
			'M201210' => 'M201210',
			'M201211' => 'M201211',
			'M201212' => 'M201212',
			'M201301' => 'M201301',
			'M201302' => 'M201302',
			'M201303' => 'M201303',
			'M201304' => 'M201304',
			'M201305' => 'M201305',
			'M201306' => 'M201306',
			'M201307' => 'M201307',
			'M201308' => 'M201308',
			'M201309' => 'M201309',
			'M201310' => 'M201310',
			'M201311' => 'M201311',
			'M201312' => 'M201312',
			'M201401' => 'M201401',
			'M201402' => 'M201402',
			'M201403' => 'M201403',
			'M201404' => 'M201404',
			'M201405' => 'M201405',
			'M201406' => 'M201406',
			'M201407' => 'M201407',
			'M201408' => 'M201408',
			'M201409' => 'M201409',
			'M201410' => 'M201410',
			'M201411' => 'M201411',
			'M201412' => 'M201412',
			'M201501' => 'M201501',
			'M201502' => 'M201502',
			'M201503' => 'M201503',
			'M201504' => 'M201504',
			'M201505' => 'M201505',
			'M201506' => 'M201506',
			'M201507' => 'M201507',
			'M201508' => 'M201508',
			'M201509' => 'M201509',
			'M201510' => 'M201510',
			'M201511' => 'M201511',
			'M201512' => 'M201512',
			'M201601' => 'M201601',
			'M201602' => 'M201602',
			'M201603' => 'M201603',
			'M201604' => 'M201604',
			'M201605' => 'M201605',
			'M201606' => 'M201606',
			'M201607' => 'M201607',
			'M201608' => 'M201608',
			'M201609' => 'M201609',
			'M201610' => 'M201610',
			'M201611' => 'M201611',
			'M201612' => 'M201612',
			'M201701' => 'M201701',
			'M201702' => 'M201702',
			'M201703' => 'M201703',
			'M201704' => 'M201704',
			'M201705' => 'M201705',
			'M201706' => 'M201706',
			'M201707' => 'M201707',
			'M201708' => 'M201708',
			'M201709' => 'M201709',
			'M201710' => 'M201710',
			'M201711' => 'M201711',
			'M201712' => 'M201712',
			'M201801' => 'M201801',
			'M201802' => 'M201802',
			'M201803' => 'M201803',
			'M201804' => 'M201804',
			'M201805' => 'M201805',
			'M201806' => 'M201806',
			'M201807' => 'M201807',
			'M201808' => 'M201808',
			'M201809' => 'M201809',
			'M201810' => 'M201810',
			'M201811' => 'M201811',
			'M201812' => 'M201812',
			'M201901' => 'M201901',
			'M201902' => 'M201902',
			'M201903' => 'M201903',
			'M201904' => 'M201904',
			'M201905' => 'M201905',
			'M201906' => 'M201906',
			'M201907' => 'M201907',
			'M201908' => 'M201908',
			'M201909' => 'M201909',
			'M201910' => 'M201910',
			'M201911' => 'M201911',
			'M201912' => 'M201912',
			'M202001' => 'M202001',
			'M202002' => 'M202002',
			'M202003' => 'M202003',
			'M202004' => 'M202004',
			'M202005' => 'M202005',
			'M202006' => 'M202006',
			'M202007' => 'M202007',
			'M202008' => 'M202008',
			'M202009' => 'M202009',
			'M202010' => 'M202010',
			'M202011' => 'M202011',
			'M202012' => 'M202012',
		);
	}

	/**
	 * @param array $options formatSearch() options
     * @return CActiveDataProvider
	 */
    public function search ($options = null) {
		$criteria=new CDbCriteria;

		$criteria->compare('t.SLNO',$this->SLNO);
		$criteria->compare('t.months',$this->months);
		$criteria->compare('t.Asset_ID',$this->Asset_ID,true);
		$criteria->compare('t.Asset_Name',$this->Asset_Name,true);
		$criteria->compare('t.Type',$this->Type,true);
		$criteria->compare('t.Variant',$this->Variant,true);
		$criteria->compare('t.Confidence',$this->Confidence);
		$criteria->compare('t.Correlations',$this->Correlations,true);
		$criteria->compare('t.RSQ',$this->RSQ,true);
		$criteria->compare('t.Source_ID',$this->Source_ID);
		$criteria->compare('t.M201001',$this->M201001,true);
		$criteria->compare('t.M201002',$this->M201002,true);
		$criteria->compare('t.M201003',$this->M201003,true);
		$criteria->compare('t.M201004',$this->M201004,true);
		$criteria->compare('t.M201005',$this->M201005,true);
		$criteria->compare('t.M201006',$this->M201006,true);
		$criteria->compare('t.M201007',$this->M201007,true);
		$criteria->compare('t.M201008',$this->M201008,true);
		$criteria->compare('t.M201009',$this->M201009,true);
		$criteria->compare('t.M201010',$this->M201010,true);
		$criteria->compare('t.M201011',$this->M201011,true);
		$criteria->compare('t.M201012',$this->M201012,true);
		$criteria->compare('t.M201101',$this->M201101,true);
		$criteria->compare('t.M201102',$this->M201102,true);
		$criteria->compare('t.M201103',$this->M201103,true);
		$criteria->compare('t.M201104',$this->M201104,true);
		$criteria->compare('t.M201105',$this->M201105,true);
		$criteria->compare('t.M201106',$this->M201106,true);
		$criteria->compare('t.M201107',$this->M201107,true);
		$criteria->compare('t.M201108',$this->M201108,true);
		$criteria->compare('t.M201109',$this->M201109,true);
		$criteria->compare('t.M201110',$this->M201110,true);
		$criteria->compare('t.M201111',$this->M201111,true);
		$criteria->compare('t.M201112',$this->M201112,true);
		$criteria->compare('t.M201201',$this->M201201,true);
		$criteria->compare('t.M201202',$this->M201202,true);
		$criteria->compare('t.M201203',$this->M201203,true);
		$criteria->compare('t.M201204',$this->M201204,true);
		$criteria->compare('t.M201205',$this->M201205,true);
		$criteria->compare('t.M201206',$this->M201206,true);
		$criteria->compare('t.M201207',$this->M201207,true);
		$criteria->compare('t.M201208',$this->M201208,true);
		$criteria->compare('t.M201209',$this->M201209,true);
		$criteria->compare('t.M201210',$this->M201210,true);
		$criteria->compare('t.M201211',$this->M201211,true);
		$criteria->compare('t.M201212',$this->M201212,true);
		$criteria->compare('t.M201301',$this->M201301,true);
		$criteria->compare('t.M201302',$this->M201302,true);
		$criteria->compare('t.M201303',$this->M201303,true);
		$criteria->compare('t.M201304',$this->M201304,true);
		$criteria->compare('t.M201305',$this->M201305,true);
		$criteria->compare('t.M201306',$this->M201306,true);
		$criteria->compare('t.M201307',$this->M201307,true);
		$criteria->compare('t.M201308',$this->M201308,true);
		$criteria->compare('t.M201309',$this->M201309,true);
		$criteria->compare('t.M201310',$this->M201310,true);
		$criteria->compare('t.M201311',$this->M201311,true);
		$criteria->compare('t.M201312',$this->M201312,true);
		$criteria->compare('t.M201401',$this->M201401,true);
		$criteria->compare('t.M201402',$this->M201402,true);
		$criteria->compare('t.M201403',$this->M201403,true);
		$criteria->compare('t.M201404',$this->M201404,true);
		$criteria->compare('t.M201405',$this->M201405,true);
		$criteria->compare('t.M201406',$this->M201406,true);
		$criteria->compare('t.M201407',$this->M201407,true);
		$criteria->compare('t.M201408',$this->M201408,true);
		$criteria->compare('t.M201409',$this->M201409,true);
		$criteria->compare('t.M201410',$this->M201410,true);
		$criteria->compare('t.M201411',$this->M201411,true);
		$criteria->compare('t.M201412',$this->M201412,true);
		$criteria->compare('t.M201501',$this->M201501,true);
		$criteria->compare('t.M201502',$this->M201502,true);
		$criteria->compare('t.M201503',$this->M201503,true);
		$criteria->compare('t.M201504',$this->M201504,true);
		$criteria->compare('t.M201505',$this->M201505,true);
		$criteria->compare('t.M201506',$this->M201506,true);
		$criteria->compare('t.M201507',$this->M201507,true);
		$criteria->compare('t.M201508',$this->M201508,true);
		$criteria->compare('t.M201509',$this->M201509,true);
		$criteria->compare('t.M201510',$this->M201510,true);
		$criteria->compare('t.M201511',$this->M201511,true);
		$criteria->compare('t.M201512',$this->M201512,true);
		$criteria->compare('t.M201601',$this->M201601,true);
		$criteria->compare('t.M201602',$this->M201602,true);
		$criteria->compare('t.M201603',$this->M201603,true);
		$criteria->compare('t.M201604',$this->M201604,true);
		$criteria->compare('t.M201605',$this->M201605,true);
		$criteria->compare('t.M201606',$this->M201606,true);
		$criteria->compare('t.M201607',$this->M201607,true);
		$criteria->compare('t.M201608',$this->M201608,true);
		$criteria->compare('t.M201609',$this->M201609,true);
		$criteria->compare('t.M201610',$this->M201610,true);
		$criteria->compare('t.M201611',$this->M201611,true);
		$criteria->compare('t.M201612',$this->M201612,true);
		$criteria->compare('t.M201701',$this->M201701,true);
		$criteria->compare('t.M201702',$this->M201702,true);
		$criteria->compare('t.M201703',$this->M201703,true);
		$criteria->compare('t.M201704',$this->M201704,true);
		$criteria->compare('t.M201705',$this->M201705,true);
		$criteria->compare('t.M201706',$this->M201706,true);
		$criteria->compare('t.M201707',$this->M201707,true);
		$criteria->compare('t.M201708',$this->M201708,true);
		$criteria->compare('t.M201709',$this->M201709,true);
		$criteria->compare('t.M201710',$this->M201710,true);
		$criteria->compare('t.M201711',$this->M201711,true);
		$criteria->compare('t.M201712',$this->M201712,true);
		$criteria->compare('t.M201801',$this->M201801,true);
		$criteria->compare('t.M201802',$this->M201802,true);
		$criteria->compare('t.M201803',$this->M201803,true);
		$criteria->compare('t.M201804',$this->M201804,true);
		$criteria->compare('t.M201805',$this->M201805,true);
		$criteria->compare('t.M201806',$this->M201806,true);
		$criteria->compare('t.M201807',$this->M201807,true);
		$criteria->compare('t.M201808',$this->M201808,true);
		$criteria->compare('t.M201809',$this->M201809,true);
		$criteria->compare('t.M201810',$this->M201810,true);
		$criteria->compare('t.M201811',$this->M201811,true);
		$criteria->compare('t.M201812',$this->M201812,true);
		$criteria->compare('t.M201901',$this->M201901,true);
		$criteria->compare('t.M201902',$this->M201902,true);
		$criteria->compare('t.M201903',$this->M201903,true);
		$criteria->compare('t.M201904',$this->M201904,true);
		$criteria->compare('t.M201905',$this->M201905,true);
		$criteria->compare('t.M201906',$this->M201906,true);
		$criteria->compare('t.M201907',$this->M201907,true);
		$criteria->compare('t.M201908',$this->M201908,true);
		$criteria->compare('t.M201909',$this->M201909,true);
		$criteria->compare('t.M201910',$this->M201910,true);
		$criteria->compare('t.M201911',$this->M201911,true);
		$criteria->compare('t.M201912',$this->M201912,true);
		$criteria->compare('t.M202001',$this->M202001,true);
		$criteria->compare('t.M202002',$this->M202002,true);
		$criteria->compare('t.M202003',$this->M202003,true);
		$criteria->compare('t.M202004',$this->M202004,true);
		$criteria->compare('t.M202005',$this->M202005,true);
		$criteria->compare('t.M202006',$this->M202006,true);
		$criteria->compare('t.M202007',$this->M202007,true);
		$criteria->compare('t.M202008',$this->M202008,true);
		$criteria->compare('t.M202009',$this->M202009,true);
		$criteria->compare('t.M202010',$this->M202010,true);
		$criteria->compare('t.M202011',$this->M202011,true);
		$criteria->compare('t.M202012',$this->M202012,true);

		$options['sortDefault'] = 't.SLNO ASC';

        $searchAttrs = array();
        $criteria = $this->addSearchTerms($criteria, $searchAttrs);

        return $this->formatSearch($this, $criteria, $options);
	}

	/**
	 * @param string $className active record class name.
	 * @return DbMarketPercent the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
