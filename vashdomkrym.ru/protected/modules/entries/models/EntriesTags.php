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

class EntriesTags extends ParentModel {
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
	
	public function tableName() {
		return '{{entries_all_tags}}';
	}

	public function rules() {
		return array(
			array('name', 'required'),
			array('frequency', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>128),
		);
	}

	public function relations() {
		return array(
		);
	}
	
	public function behaviors() {
		$arr = array();
		if (issetModule('historyChanges')) {
			$arr['ArLogBehavior'] = array(
				'class' => 'application.modules.historyChanges.components.ArLogBehavior',
			);
		}

		return $arr;
	}

	public function attributeLabels() {
		return array(
			'id' => tt('Id', 'entries'),
			'name' => tc('Name'),
			'frequency' => tt('Frequency', 'entries'),
		);
	}

	public function findTagWeights($limit=20) {
		$models=$this->findAll(array(
			'order'=>'frequency DESC',
			'limit'=>$limit,
		));

		$total=0;
		foreach($models as $model)
			$total+=$model->frequency;

		$tags=array();
		if($total>0)
		{
			foreach($models as $model)
				$tags[$model->name]=8+(int)(16*$model->frequency/($total+10));
			ksort($tags);
		}
		return $tags;
	}

	public function suggestTags($keyword,$limit=20) {
		$tags=$this->findAll(array(
			'condition'=>'name LIKE :keyword',
			'order'=>'frequency DESC, Name',
			'limit'=>$limit,
			'params'=>array(
				':keyword'=>'%'.strtr($keyword,array('%'=>'\%', '_'=>'\_', '\\'=>'\\\\')).'%',
			),
		));
		$names=array();
		foreach($tags as $tag)
			$names[]=$tag->name;
		return $names;
	}

	public static function string2array($tags) {
		return preg_split('/\s*,\s*/',trim($tags),-1,PREG_SPLIT_NO_EMPTY);
	}

	public static function array2string($tags) {
		return implode(', ',$tags);
	}

	public function updateFrequency($oldTags, $newTags) {
		$oldTags=self::string2array($oldTags);
		$newTags=self::string2array($newTags);
		$this->addTags(array_values(array_diff($newTags,$oldTags)));
		$this->removeTags(array_values(array_diff($oldTags,$newTags)));
	}

	public function addTags($tags) {
		$criteria=new CDbCriteria;
		$criteria->addInCondition('name',$tags);
		$this->updateCounters(array('frequency'=>1),$criteria);
		foreach($tags as $name) {
			if(!$this->exists('name=:name',array(':name'=>$name))) {
				$tag=new EntriesTags;
				$tag->name=$name;
				$tag->frequency=1;
				$tag->save();
			}
		}
	}

	public function removeTags($tags) {
		if(empty($tags))
			return;
		$criteria=new CDbCriteria;
		$criteria->addInCondition('name',$tags);
		$this->updateCounters(array('frequency'=>-1),$criteria);
		$this->deleteAll('frequency<=0');
	}
}