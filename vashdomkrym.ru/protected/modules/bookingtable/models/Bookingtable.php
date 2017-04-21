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

class Bookingtable extends ParentModel {
	const STATUS_NEW = 0;
	const STATUS_VIEWED = 1;
	const STATUS_CONFIRM = 2;
	const STATUS_NOT_CONFIRM = 3;
	const STATUS_NEED_PAY = 4;

	private static $_statuses_arr;

	public $dateStart = array();
	public $dateEnd = array();
	public $status = array();

	public $dateStartDb = array();
	public $dateEndDb = array();
	public $statusDb = array();

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{booking_table}}';
	}

	public function rules() {
		$arr = array(
			array('date_start, date_end', 'required'),
			array('apartment_id', 'required', 'on'=>'insert'),
			array('num_guest', 'numerical', 'integerOnly' => true, 'min' => 1, 'max' => param('booking_max_guest', 10)),
			array('username, email', 'length', 'max' => 128),
			array('phone', 'length', 'max' => 15),
			array('username, email, comment, phone','filter','filter'=>array(new CHtmlPurifier(),'purify')),
			array('user_ip, user_ip_ip2_long', 'length', 'max' => 60),
			array('id, active, apartment_id, username, email, phone, date_start, date_end, time_in, time_out, comment, date_created, num_guest', 'safe', 'on' => 'search'),
			array('comment_admin, details', 'safe'),

			array('active', 'required', 'on' => 'change_status'),
			array('active', 'validStatus', 'on' => 'change_status'),
			//array('amount', 'validAmount', 'on' => 'change_status'),
			//array('amount', 'numerical', 'min' => 1, 'on' => 'change_status'),
		);

		if(Yii::app()->user->checkAccess('bookingtable_admin')){
			$arr[] = array('amount, comment_admin', 'safe', 'on' => 'change_status');
		} else {
			$arr[] = array('amount', 'numerical', 'min' => 1, 'on' => 'change_status');
			$arr[] = array('active', 'numerical', 'min' => 1);
		}

		return $arr;
	}

	public function validStatus()
	{
		if($this->active == self::STATUS_NEED_PAY && !$this->sender){
			$this->addError('active', tt("This application has not registered user. Payment can not be unregistered user."));
		}
	}

	public function scopes(){
		return array(
			'scopeMy' => array(
				'condition' => 'sender_id = '.Yii::app()->user->id,
			),
		);
	}

	public function relations() {
		$relation = array();
		$relation['apartment'] = array(self::BELONGS_TO, 'Apartment', 'apartment_id');
		$relation['sender'] = array(self::BELONGS_TO, 'User', 'sender_id');
		$relation['timein'] = array(self::BELONGS_TO, 'TimesIn', 'time_in');
		$relation['timeout'] = array(self::BELONGS_TO, 'TimesOut', 'time_out');
		$relation['payment'] = array(self::HAS_ONE, 'Payments', 'booking_id');
		return $relation;
	}

	public function behaviors() {
		$arr = array();
		$arr['AutoTimestampBehavior'] = array(
			'class' => 'zii.behaviors.CTimestampBehavior',
			'createAttribute' => 'date_created',
			'updateAttribute' => 'date_updated',
		);
		if (issetModule('historyChanges')) {
			$arr['ArLogBehavior'] = array(
				'class' => 'application.modules.historyChanges.components.ArLogBehavior',
			);
		}

		return $arr;
	}

	public function attributeLabels() {
		return array(
			'active' => tc('Status'),
			'date_start' => tt('Check-in date', 'booking'),
			'date_end' => tt('Check-out date', 'booking'),
			'email' => Yii::t('common', 'E-mail'),
			'time_in' => tt('Check-in time', 'booking'),
			'time_out' => tt('Check-out time', 'booking'),
			'comment_admin' => tt('Admin comment', 'booking'),
			'comment' => tt('Comment', 'booking'),
			'username' => tt('User name', 'booking'),
			'date_created' => tt('Creation date', 'booking'),
			'dateCreated' => tt('Creation date', 'booking'),
			'apartment_id' => tt('Apartment ID', 'booking'),
			'id' => tt('ID', 'apartments'),
			'phone' => Yii::t('common', 'Phone number'),
			'verifyCode' => tc('Verify Code'),
			'user_ip' => tt('User IP', 'blockIp'),
			'amount' => tt('Advance payment', 'booking'),
			'num_guest' => tt('Number of guests', 'booking'),
		);
	}

	private static $_list;

	public static function getAllStatuses($checkPaidService = false, $checkUser = false){
		if(!isset(self::$_list)){
			$list = array(
				self::STATUS_NEW => tt('Status new', 'booking'),
				self::STATUS_VIEWED => tt('Status view', 'booking'),
				self::STATUS_CONFIRM => tt('Status confirm', 'booking'),
				self::STATUS_NOT_CONFIRM => tt('Status not confirm', 'booking'),
			);
			if(issetModule('bookingcalendar')){
				$list[self::STATUS_NEED_PAY] = tt('It is necessary to pay', 'booking');
			}
			self::$_list = $list;
		}

		// проверка нужна для выбора смена статуса в админке и юзерке
		if($checkPaidService || $checkUser){
			$list = self::$_list;

			$user = HUser::getModel();
			// если нет модуля платных услуг или это обычный пользователь
			if(!issetModule('paidservices') || !in_array($user->role, array(User::ROLE_MODERATOR, User::ROLE_ADMIN))){
				if(isset($list[Bookingtable::STATUS_NEED_PAY])){
					unset($list[Bookingtable::STATUS_NEED_PAY]);
				}
			} else {
				// если платная услуга не активна
				$paidService = PaidServices::model()->findByPk(PaidServices::ID_BOOKING_PAY);

				if(!$paidService->isActive() && isset($list[Bookingtable::STATUS_NEED_PAY])){
					unset($list[Bookingtable::STATUS_NEED_PAY]);
				}
			}

			return $list;
		}

		return self::$_list;
    }

	public static function getStatus($status){
        if(!isset(self::$_statuses_arr)){
            self::$_statuses_arr = self::getAllStatuses();
        }
        return self::$_statuses_arr[$status];
    }

	public function search($isUserView = false){
		$criteria = new CDbCriteria;

		if ($isUserView) {
			$criteria->addCondition('apartment.owner_id = :owner_id');
			$criteria->params[':owner_id'] = Yii::app()->user->id;

			$criteria->with['apartment'] = array(
				'select' => 'apartment.owner_id',
				'together' => true
			);
		}

		$criteria->compare($this->getTableAlias().'.id', $this->id);
		$criteria->compare($this->getTableAlias().'.active', $this->active);
		$criteria->compare($this->getTableAlias().'.apartment_id', $this->apartment_id, true);
		$criteria->compare($this->getTableAlias().'.username', $this->username, true);
		$criteria->compare($this->getTableAlias().'.email', $this->email, true);
		$criteria->compare($this->getTableAlias().'.phone', $this->phone, true);
		$criteria->compare($this->getTableAlias().'.date_start', $this->date_start, true);
		$criteria->compare($this->getTableAlias().'.date_end', $this->date_end, true);
		$criteria->compare($this->getTableAlias().'.time_in', $this->time_in);
		$criteria->compare($this->getTableAlias().'.time_out', $this->time_out);
		$criteria->compare($this->getTableAlias().'.comment', $this->comment, true);
		$criteria->compare($this->getTableAlias().'.num_guest', $this->num_guest, true);
		$criteria->compare($this->getTableAlias().'.date_created', $this->date_created, true);

		$criteria->order = $this->getTableAlias().'.id DESC';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>param('adminPaginationPageSize', 20),
			),
		));
	}

	public static function isUserAd($apartmentId = null, $ownerId = null) {
		if ($apartmentId && $ownerId) {
			if (Apartment::model()->findByAttributes(array('id' => $apartmentId, 'owner_id' => $ownerId)))
				return true;
			return false;

		}
		return false;
	}

	public static function addRecord(Booking $booking, User $user, $status = self::STATUS_NEW) {
		$dateStart = Yii::app()->dateFormatter->format('yyyy-MM-dd', CDateTimeParser::parse($booking->date_start, Booking::getYiiDateFormat()));
		$dateEnd = Yii::app()->dateFormatter->format('yyyy-MM-dd', CDateTimeParser::parse($booking->date_end, Booking::getYiiDateFormat()));

		$model = new Bookingtable;
		$model->active = $status;
		$model->apartment_id = $booking->apartment_id;
		$model->username = $booking->username;
		$model->email = $booking->useremail;
		$model->phone = $booking->phone;
		$model->date_start = $dateStart;
		$model->date_end = $dateEnd;
		$model->time_in = $booking->time_in;
		$model->time_out = $booking->time_out;
		$model->comment = $booking->comment;
		$model->user_ip = $booking->user_ip;
		$model->user_ip_ip2_long = $booking->user_ip_ip2_long;
		$model->sender_id = $user->id;
		$model->amount = $booking->amount;
		$model->num_guest = $booking->num_guest;

		$model->save(false);

		return $model;
	}

	public static function getCountNew($isUserView = false) {
		if ($isUserView) {
            $sql = "SELECT COUNT(b.id) FROM {{booking_table}} b "
                ." INNER JOIN {{apartment}} a ON b.apartment_id = a.id"
                ." WHERE (b.active = :status OR b.active = :statusNeedPay) AND a.owner_id = :owner_id";
			$params = array(
				':status' => self::STATUS_NEW,
				':statusNeedPay' => self::STATUS_NEED_PAY,
				':owner_id' => Yii::app()->user->id,
			);
		} else {
            $sql = "SELECT COUNT(id) FROM {{booking_table}} WHERE (active = :status OR active = :statusNeedPay)";
			$params = array(
				':status' => self::STATUS_NEW,
				':statusNeedPay' => self::STATUS_NEED_PAY,
			);
        }

        return (int) Yii::app()->db->createCommand($sql)->queryScalar($params);
	}

	public static function getCountLoggedOwner() {
		$sql = "SELECT COUNT(b.id) FROM {{booking_table}} b "
			." INNER JOIN {{apartment}} a ON b.apartment_id = a.id"
			." WHERE a.owner_id = :owner_id";

		return (int) Yii::app()->db->createCommand($sql)->queryScalar(array(
			':owner_id' => Yii::app()->user->id
		));
	}

	public static function getCountMyNeedPay() {
		$sql = "SELECT COUNT(id) FROM {{booking_table}} WHERE active = :status AND sender_id = :id";

		return (int) Yii::app()->db->createCommand($sql)->queryScalar(array(
			':status' => self::STATUS_NEED_PAY,
			':id' => Yii::app()->user->id
			));
	}

	public function getSenderName(){
		return isset($this->sender) ? $this->sender->username : '';
	}

	public function getApartmentUrl(){
		return isset($this->apartment) ? $this->apartment->getUrl() : '';
	}

	public function getApartmentTitle(){
		return isset($this->apartment) ? $this->apartment->getTitle() : '';
	}

	public function getCurrency()
	{
		return issetModule('currency') ? Currency::getDefaultCurrencyName() : param('siteCurrency');
	}

	public function getMyBookingButton(){
		$list = self::getAllStatuses();
		if($this->active == self::STATUS_NEED_PAY){
			$status = CHtml::link($list[$this->active], Yii::app()->createUrl('/paidservices/main/payForBooking', array('id' => $this->id)), array('class' => 'small_button'));
		}else{
			$status = isset($list[$this->active]) ? $list[$this->active] : '?';
		}
		if($this->details || $this->comment_admin){
			$status .= '<br>'.CHtml::link(tt('Details', 'booking'), Yii::app()->createUrl('/bookingtable/main/details', array('id' => $this->id)), array('class' => 'fancy'));
		}
		return $status;
	}

	public function getRequestLink() {
		$linkText = tc('from').' '.$this->username.' '.$this->getDateTimeInFormat('date_created');

		if (Yii::app()->params['useBootstrap'])		//Backend
			return CHtml::link($this->id.' '.$linkText,
				array('/bookingtable/backend/main/admin', 'Bookingtable[id]'=>$this->id));
		else
			return CHtml::link($linkText, array('/bookingtable/main/index', 'Bookingtable[id]'=>$this->id));
	}

	public function getCalcForMail()
	{
		$cost = HBooking::calculateAdvancePayment($this, true);
		if($this->amount == $cost){
			return HBooking::$calculateHtml;
		}
		return '<hr>';
	}
}
