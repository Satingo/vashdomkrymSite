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

class HUser {
	const UPLOAD_MAIN = 'main';
	const UPLOAD_PORTFOLIO = 'portfolio';
	const UPLOAD_AVA = 'ava';

	private static $_model;

	public static function getUploadDirectory(User $user, $category = self::UPLOAD_MAIN) {
		$DS = DIRECTORY_SEPARATOR;
		$root = ROOT_PATH . $DS . 'uploads' . $DS . $category;
		self::genDir($root);

		$year = date('Y', strtotime($user->date_created));
		$path = $root . $DS . $year;
		self::genDir($path);

		$month = date('m', strtotime($user->date_created));
		$path = $path . $DS . $month;
		self::genDir($path);

		return $path;
	}

	public static function getUploadUrl(User $user, $category = self::UPLOAD_MAIN){
		$DS = '/';
		$root = 'uploads' . $DS . $category;

		$year = date('Y', strtotime($user->date_created));
		$path = $root . $DS . $year;

		$month = date('m', strtotime($user->date_created));
		$path = $path . $DS . $month;

		return Yii::app()->baseUrl . $DS . $path;
	}

	public static function genDir($path){
		if(!is_dir($path)){
			if(!mkdir($path)){
				throw new CException('HUser невозможно создать директорию ' . $path);
			}
		}
	}

	public static function getModel()
	{
		if(!isset(self::$_model)){
			self::$_model = User::model()->findByPk(Yii::app()->user->id);
		}

		return self::$_model;
	}

	public static function getListAgency(){
		$sql = "SELECT id, agency_name FROM {{users}} WHERE active = 1 AND type=:type ORDER BY agency_name";
		$all = Yii::app()->db->createCommand($sql)->queryAll(true, array(':type' => User::TYPE_AGENCY));
		$list = CHtml::listData($all, 'id', 'agency_name');

		return CMap::mergeArray(array(0 => ''), $list);
	}

	public static function getLinkDelAgent(User $user){
		return CHtml::link(tc('Delete'), Yii::app()->createUrl('/usercpanel/main/deleteAgent', array('id' => $user->id)));
	}

	public static function getAgentBalanceAndLink(User $user){
		return CHtml::link(tc('Add balance').' ('.$user->balance.' ' . Currency::getDefaultCurrencyName().')',
			Yii::app()->createUrl('/paidservices/main/index', array('paid_id' => PaidServices::ID_ADD_FUNDS_TO_AGENT, 'agent_id'=>$user->id)), array('class' => 'fancy mgp-open-ajax'));
	}

	public static function returnStatusHtml($data, $tableId){
		$statuses = User::getAgentStatusList();

		$options = array(
			'onclick' => 'ajaxSetAgentStatus(this, "'.$tableId.'", "'.$data->id.'"); return false;',
		);

		return '<div align="center" class="editable_select" id="editable_select-'.$data->id.'">'.CHtml::link($statuses[$data->agent_status], '#' , $options).'</div>';
	}

	public static function getCountAwaitingAgent($agencyUserID){
		$sql = "SELECT COUNT(id) FROM {{users}} WHERE agency_user_id = :user_id AND agent_status = :status AND active = 1";
		return Yii::app()->db->createCommand($sql)->queryScalar(array(
			':user_id' => $agencyUserID,
			':status' => User::AGENT_STATUS_AWAIT_VERIFY,
		));
	}

	public static function getMenu(){
		$user = HUser::getModel();

		if(param('useUserads')){
			$menu[] = array(
				'label' => tc('My listings'),
				'url' => Yii::app()->createUrl('/usercpanel/main/index'),
				'active' => Yii::app()->controller->menuIsActive('my_listings'),
			);

			$menu[] = array(
				'label' => tc('Add ad', 'apartments'),
				'url' => Yii::app()->createUrl('/userads/main/create'),
				'active' => Yii::app()->controller->menuIsActive('add_ad'),
			);
		}

		if (issetModule('bookingtable')) {
			$menu[] = array(
				'label' => tt('Booking applications', 'usercpanel')  . ' (' . Bookingtable::getCountNew(true) . ')',
				'url' => Yii::app()->createUrl('/bookingtable/main/index'),
				'active' => Yii::app()->controller->menuIsActive('booking_applications'),
				'visible' => Bookingtable::getCountLoggedOwner() || param('useUserads', 1),
			);
			$needPay = Bookingtable::getCountMyNeedPay();
			$menu[] = array(
				'label' => tt('My bookings', 'usercpanel') . self::brackets($needPay),
				'url' => Yii::app()->createUrl('/bookingtable/main/my'),
				'active' => Yii::app()->controller->menuIsActive('my_bookings'),
			);
		}

		if($user->type == User::TYPE_AGENCY){
			$countAwaitAgent = HUser::getCountAwaitingAgent($user->id);
			$bage = $countAwaitAgent ? ' (' .$countAwaitAgent. ')' : '';

			$menu[] = array(
				'label' => tt('My agents', 'usercpanel').$bage,
				'url' => Yii::app()->createUrl('/usercpanel/main/agents'),
				'active' => Yii::app()->controller->menuIsActive('my_agents'),
			);
		}

		if (issetModule('messages')) {
			$countMessagesUnread = Messages::getCountUnread(Yii::app()->user->id);
			$bageMessages = ($countMessagesUnread > 0) ? " ({$countMessagesUnread})" : '';

			$menu[] = array(
				'label' => tt('My mailbox', 'messages').$bageMessages,
				'url' => Yii::app()->createUrl('/messages/main/index'),
				'active' => Yii::app()->controller->menuIsActive('my_mailbox'),
			);

			if ($countMessagesUnread > 0){
				Yii::app()->clientScript->registerScript('init-cnt-unr-messages', '
					message("'.Yii::t('module_messages', 'You have {n} unread messages', $countMessagesUnread).'", "message", 4000);
				', CClientScript::POS_READY);
			}
		}

		$menu[] = array(
			'label' => tc('My data'),
			'url' => Yii::app()->createUrl('/usercpanel/main/data'),
			'active' => Yii::app()->controller->menuIsActive('my_data'),
		);
		$menu[] = array(
			'label' => tt('Change your password', 'usercpanel'),
			'url' => Yii::app()->createUrl('/usercpanel/main/changepassword'),
			'active' => Yii::app()->controller->menuIsActive('my_changepassword'),
		);

		if (issetModule('payment')) {
			if (issetModule('tariffPlans')) {
				$menu[] = array(
					'label' => tc('Tariff Plans'),
					'url' => Yii::app()->createUrl('/tariffPlans/main/index'),
					'active' => Yii::app()->controller->menuIsActive('tariff_plans'),
				);
			}
			$menu[] = array(
				'label' => tt('My payments', 'usercpanel'),
				'url' => Yii::app()->createUrl('/usercpanel/main/payments'),
				'active' => Yii::app()->controller->menuIsActive('my_payments'),
			);
			$menu[] = array(
				'label' => tc('My balance') . ' (' . $user->balance . ' ' . Currency::getDefaultCurrencyName() . ')',
				'url' => Yii::app()->createUrl('/usercpanel/main/balance'),
				'active' => Yii::app()->controller->menuIsActive('my_balance'),
			);
		}

		return $menu;
	}

	public static function brackets($count)
	{
		return $count ? ' (' . $count . ')' : '';
	}

    public static function getLinkForRecover(User $user){
        $restoreText = Yii::t('module_users', 'Restore password for {email}?', array('{email}' => $user->email));

        return CHtml::ajaxLink(
            tc('Recover password'),
            Yii::app()->createUrl('/users/backend/main/recover', array('id' => $user->id)),
            array (
                'type'=>'GET',
                'dataType'=>'json',
                'beforeSend' => "function( request ){
                    return confirm(".CJavaScript::encode($restoreText).");
                }",
                'success'=>'function(data){ message(data.msg, "message", 10000) }'
            ),
            array(
                'class' => 'btn btn-info',
				'id' => 'user-line-'.$user->id,
            )
        );
    }

    public static function getDataForListings($userId){
        $criteria = new CDbCriteria;
        $criteria->addCondition('active = '.Apartment::STATUS_ACTIVE);
        if (param('useUserads'))
            $criteria->addCondition('owner_active = '.Apartment::STATUS_ACTIVE);

        $criteria->addInCondition('type', HApartment::availableApTypesIds());
        $criteria->addInCondition('t.price_type', array_keys(HApartment::getPriceArray(Apartment::PRICE_SALE, true)));

        $userModel = User::model()->findByPk($userId);
        $userName = $userModel->getNameForType();

        if($userModel->type == User::TYPE_AGENCY){
            $userName = $userModel->getTypeName() . ' "' . $userName .'"';
            $sql = "SELECT id FROM {{users}} WHERE agency_user_id = :user_id AND agent_status=:status";
            $agentsId = Yii::app()->db->createCommand($sql)->queryColumn(array(':user_id' => $userId, ':status' => User::AGENT_STATUS_CONFIRMED));
            $agentsId[] = $userId;
            $criteria->compare('owner_id', $agentsId, false);
        } else {
            $criteria->compare('owner_id', $userId);
        }

        return array(
            'criteria' => $criteria,
            'userName' => $userName,
        );
    }
}