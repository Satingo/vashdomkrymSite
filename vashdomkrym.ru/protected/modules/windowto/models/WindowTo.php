<?php
/* * ********************************************************************************************
 *								Open Real Estate
 *								----------------
 * 	version				:	V1.16.1
 * 	copyright			:	(c) 2015 Monoray
 * 							http://monoray.net
 *							http://monoray.ru
 *
 * 	website				:	http://open-real-estate.info/en
 *
 * 	contact us			:	http://open-real-estate.info/en/contact-us
 *
 * 	license:			:	http://open-real-estate.info/en/license
 * 							http://open-real-estate.info/ru/license
 *
 * This file is part of Open Real Estate
 *
 * ********************************************************************************************* */

class WindowTo extends ParentModel {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{apartment_window_to}}';
	}

    public function rules() {
   		return array(
   			array('title', 'i18nLength', 'max' => 255),
   			array('title', 'i18nRequired'),
   			array('id', 'safe', 'on' => 'search'),
			array($this->getI18nFieldSafe(), 'safe'),
   		);
   	}

   public function i18nFields(){
       return array(
           'title' => 'varchar(255) not null',
       );
   }

	public function relations() {
		return array(
		);
	}

	public function behaviors(){
		return array(
			'AutoTimestampBehavior' => array(
				'class' => 'zii.behaviors.CTimestampBehavior',
				'createAttribute' => null,
				'updateAttribute' => 'date_updated',
			),
		);
	}

	public function attributeLabels() {
		return array(
			'title' => tt('Value'),
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('title_'.Yii::app()->language, $this->{'title_'.Yii::app()->language}, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'pagination' => array(
				'pageSize' => param('adminPaginationPageSize', 20),
			),
		));
	}

	public function getTitle() {
        $title = 'title_' . Yii::app()->language;
        return $this->$title;
	}

	public function afterDelete(){
		$sql = 'UPDATE {{apartment}} SET window_to="0" WHERE window_to="'.$this->id.'"';
		Yii::app()->db->createCommand($sql)->execute();

		return parent::afterDelete();
	}

	static function getWindowTo(){
		$sql = 'SELECT id, title_'.Yii::app()->language.' as title FROM {{apartment_window_to}}';
		$results = Yii::app()->db->createCommand($sql)->queryAll();
		$return = array();
		$return[0] = '';
		if($results){
			foreach($results as $result){
				$return[$result['id']] = $result['title'];
			}
		}
		return $return;
	}
}