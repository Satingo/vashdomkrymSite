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

class SocialauthModel extends ParentModel {
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{socialauth}}';
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

	public function rules() {
		return array(
			array('name, value', 'required'),
			array('name, value', 'length', 'max' => 255),
		);
	}

    public function getTitle(){
        return tt($this->name);
    }

	public function attributeLabels() {
		return array(
			//'title_ru' => SocialauthModule::t('Name'),
			'value' => SocialauthModule::t('Value'),
		);
	}

	public function search() {
		$criteria = new CDbCriteria;
		$criteria->compare('value', $this->value, true);

        $section_filter = Yii::app()->request->getQuery('section_filter', 'all');

        if($section_filter != 'all'){
            $criteria->compare('section', $section_filter);
        }

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => 'section',
			),
			'pagination' => array(
				'pageSize' => param('adminPaginationPageSize', 20),
			),
		));
	}

    public static function getAdminValue($model){
        if($model->type == 'bool') {
            $url = Yii::app()->controller->createUrl("activate",
                array(
                    'id' => $model->id,
                    'action' => ($model->value == 1 ? 'deactivate' : 'activate'),
                ));
            $img = CHtml::image(
				Yii::app()->theme->baseUrl.'/images/'.($model->value ? '' : 'in').'active.png',
                Yii::t('common', $model->value ? 'Inactive' : 'Active'),
                array('title' => Yii::t('common', $model->value ? 'Deactivate' : 'Activate'))
            );

            $options = array(
                'onclick' => 'ajaxSetStatus(this, "socialauth-table"); return false;',
            );

            return '<div align="left">'.CHtml::link($img, $url, $options).'</div>';
        } else {
			if(demo()){
				return tc('Hidden in demo mode');
			} else {
            	return utf8_substr($model->value, 0, 55);
			}
        }
    }

    public static function getVisible($type){
        return $type == 'text';
    }

	public static function getSocialParamValue($param = '') {
		if ($param) {
			return Yii::app()->db->createCommand()
					->select('value')
					->from('{{socialauth}}')
					->where('name = "'.$param.'"')
					->queryScalar();
		}
	}
}