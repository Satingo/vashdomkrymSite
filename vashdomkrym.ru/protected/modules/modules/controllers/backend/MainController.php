<?php
/**********************************************************************************************
*	copyright			:	(c) 2015 Monoray
*	website				:	http://www.monoray.ru/
*	contact us			:	http://www.monoray.ru/contact
***********************************************************************************************/

class MainController extends ModuleAdminController {
	public $defaultAction='admin';

	public function accessRules(){
		return array(
			array('allow',
				'expression'=> "Yii::app()->user->checkAccess('all_settings_admin')",
			),
			array('deny',
				'users'=>array('*'),
			),
		);
	}

	public function actionAdmin(){
		$this->render('modules');
	}

	public function actionManipulate($type, $module){
		if($type == 'enable'){
			ConfigurationModel::updateValue('module_enabled_'.$module, 1);
			if ($module == 'seasonalprices') {
				# копирование информации в таблицу сезонных цен
				$sql = '
					INSERT INTO {{seasonal_prices}} (`apartment_id`, `price`, `price_type`, `date_start`, `month_start`, `date_end`, `month_end`, `sorter`, `date_created`)
						SELECT `id`, `price`, `price_type`, 1, 1, 31, 12, 1, NOW()
						FROM {{apartment}} a
						WHERE is_price_poa = 0
						AND type = '.Apartment::TYPE_RENT.'
						AND price_type > 1
						AND NOT EXISTS (SELECT 1 from {{seasonal_prices}} b WHERE b.apartment_id = a.id)
				';
				Yii::app()->db->createCommand($sql)->execute();
			}
		} else {
			ConfigurationModel::updateValue('module_enabled_'.$module, 0);
		}
		$this->redirect(array('admin'));
	}

}
