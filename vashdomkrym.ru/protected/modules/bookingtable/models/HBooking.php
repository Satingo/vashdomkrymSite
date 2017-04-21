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

class HBooking {

	public static $calculateHtml = '';

	// the number of booked days
	public static $bookedDays;
	public static $calculateDay;

	private static $iRow = 0;

    public static function getChangeBookingStatus($data) {
        $changeUrl = Yii::app()->createUrl('/bookingtable/backend/main/changeStatus', array(
            'id' => $data->id,
        ));

        $link = CHtml::link(Bookingtable::getStatus($data->active), $changeUrl, array(
            'class' => 'tempModal',
            'data-original-title' => tt('Change status')
        ));

        $status = CHtml::tag('div', array('id' => 'cs_el_' . $data->id), $link);
		if($data->details || $data->comment_admin){
			$status .= '<br>'.CHtml::link(tt('Details', 'booking'),
					Yii::app()->createUrl('/bookingtable/backend/main/details', array('id' => $data->id)), array(
					'class' => 'tempModal',
					//'data-original-title' => tt('Booking details', 'booking'),
				));
		}
		return $status;
    }

	public static function calculateAdvancePayment($booking, $border = 0)
	{
		$amount = 0;

		if($booking instanceof Bookingtable){
			$apartment = $booking->apartment;
		}elseif($booking instanceof Booking){
			$apartment = Apartment::model()->findByPk($booking->apartment_id);
		}else{
			return 0;
		}
		if(!$apartment){
			return 0;
		}

		if (issetModule('paidservices')) {
			$paidService = PaidServices::model()->findByPk(PaidServices::ID_BOOKING_PAY);
			if($paidService && $paidService->isActive() && $apartment->id){
				$percent = $paidService->getFromJson('percent');

				$bookingStart = new DateTime($booking->date_start);
				$bookingEnd = new DateTime($booking->date_end);
				$interval = date_diff($bookingStart, $bookingEnd);
				self::$bookedDays = $interval->days+1;

				if (issetModule('seasonalprices')) {

					$priceRows = Yii::app()->db
						->createCommand("SELECT name_".Yii::app()->language." AS name, month_start, date_start, month_end, date_end, price FROM {{seasonal_prices}}
						WHERE price_type = :t AND (min_rental_period <= :days OR min_rental_period = 0) AND apartment_id=:id ORDER BY price ASC")
						->queryAll(true, array(
							':t' => Apartment::PRICE_PER_DAY,
							':days' => self::$bookedDays,
							':id' => $apartment->id,
						));
					if(!$priceRows){
						return 0;
					}

					$price = 0;

					$yearStart = date('Y', strtotime($booking->date_start));
					$yearEnd = date('Y', strtotime($booking->date_end));

					self::$calculateHtml = '<div class="grid-view">';
					self::$calculateHtml .= '<table class="items booking-calculate table table-striped" '.($border ? 'border="1"' : '').'>';
					self::$calculateHtml .= '<tr><th colspan="2">'.tt('Name', 'seasonalprices').'</th><th>'.tt('Amount of days * Price per day', 'bookingtable').'</th><th>'.tt('Price', 'seasonalprices').'</th></tr>';
					self::$calculateDay = 0;
					self::$iRow = 0;
					$minPrice = 0;
					$maxPrice = 0;
					foreach($priceRows as $row){
						$seasonStart = DateTime::createFromFormat('d-m-Y', $row['date_start'].'-'.$row['month_start'].'-'.$yearStart);
						$seasonEnd = DateTime::createFromFormat('d-m-Y', $row['date_end'].'-'.$row['month_end'].'-'.$yearEnd);

						$daysOverlap = self::datesOverlap($bookingStart, $bookingEnd, $seasonStart, $seasonEnd);
						if($daysOverlap){
							$minPrice = $minPrice == 0 ? $row['price'] : $minPrice;
							$maxPrice = $maxPrice == 0 ? $row['price'] : $maxPrice;

							$minPrice = $row['price'] < $minPrice ?  $row['price'] : $minPrice;
							$maxPrice = $row['price'] > $maxPrice ?  $row['price'] : $maxPrice;
							self::$calculateDay += $daysOverlap;

							$seasonString = self::formatDate($seasonStart->getTimestamp()) . ' - ' . self::formatDate($seasonEnd->getTimestamp());
							$dayPrice = $daysOverlap.' * '.$row['price'].Currency::getDefaultCurrencyName();
							$dayPriceCalc = $daysOverlap * $row['price'];
							$price += $dayPriceCalc;

							self::addRow($row['name'], $seasonString, $dayPrice, $dayPriceCalc);
						}
					}

					// Если посчитали не за все дни или даже больше чем надо
					if(self::$calculateDay != self::$bookedDays){
						$emptyFlag = $paidService->getFromJson('empty_flag');

						if($emptyFlag && self::$calculateDay < self::$bookedDays){
							$emptyDays = self::$bookedDays - self::$calculateDay;

							if($emptyFlag == PaidBooking::EMPTY_FLAG_PAY_MAX && $maxPrice){
								$dayPriceCalc = $emptyDays * $maxPrice;
								$price += $dayPriceCalc;
								$dayPrice = $emptyDays.' * '.$maxPrice.Currency::getDefaultCurrencyName();
								self::addRow('', '', $dayPrice, $dayPriceCalc);
							}elseif($emptyFlag == PaidBooking::EMPTY_FLAG_PAY_MIN && $minPrice){
								$dayPrice = $emptyDays.' * '.$minPrice.Currency::getDefaultCurrencyName();
								$dayPriceCalc = $emptyDays * $minPrice;
								$price += $dayPriceCalc;
								self::addRow('', '', $dayPrice, $dayPriceCalc);
							}else{
								return 0;
							}
						} else {
							return 0;
						}
					}

					self::addRow(tt('In total', 'booking'), '', '', $price);

					self::$calculateHtml .= '</table>';
					self::$calculateHtml .= '</div>';

				}elseif($apartment->price_type == Apartment::PRICE_PER_DAY){
					$price = self::$bookedDays * $apartment->price;
				}else{
					$price = $apartment->price;
				}

				if(!$price){
					return 0;
				}

				if($booking->num_guest && $paidService->getFromJson('consider_num_guest')){
					$priceTotal = $price * $booking->num_guest;
					self::$calculateHtml .= tt('Taking account of number of guests the fee is').': '.$price.' * '.$booking->num_guest.' = ' . $priceTotal. Currency::getDefaultCurrencyName().'<br>';
					$price = $priceTotal;

					$discount = $paidService->getFromJson('discount_guest');
					if($booking->num_guest > 1 && $discount){
						$discountCalc = $price * ( $discount / 100 );
						self::$calculateHtml .= tt('Discount').': '.$discount.'% = '.$discountCalc.Currency::getDefaultCurrencyName().'<br>';
						$price = $price - $discountCalc;
					}
				}

				self::$calculateHtml .= Yii::t('common', 'The total cost of the booking: {cost}{currency}', array(
					'{cost}' => $price,
					'{currency}' => Currency::getDefaultCurrencyName(),
				));
				self::$calculateHtml .= '<br>';

				$amount = $price * ( $percent / 100 );

				self::$calculateHtml .= Yii::t('common', 'You must pay {percent}% : {cost}{currency}', array(
					'{percent}' => $percent,
					'{cost}' => $amount,
					'{currency}' => Currency::getDefaultCurrencyName(),
				));
			}
		}

		return round($amount);
	}

	private static function addRow($name, $seasonString, $dayPrice, $price)
	{
		$tableClass = self::$iRow%2 ? 'odd' : 'even';
		self::$calculateHtml .= '<tr class="'.$tableClass.'"><td>'.$name.'</td><td>'.$seasonString.'</td><td>'.$dayPrice.'</td><td>'.$price.Currency::getDefaultCurrencyName().'</td></tr>';
		self::$iRow++;
	}

	public static function formatDate($dateTime)
	{
		return Yii::app()->dateFormatter->format(Yii::app()->locale->getDateFormat('long'), $dateTime);
	}

	/** http://stackoverflow.com/questions/14202687/how-can-i-find-overlapping-dateperiods-date-ranges-in-php
	 * @param $start_one
	 * @param $end_one
	 * @param $start_two
	 * @param $end_two
	 * @return int
	 */
	public static function datesOverlap($start_one, $end_one, $start_two, $end_two, $plus = 1) {

		if($start_one <= $end_two && $end_one >= $start_two) { //If the dates overlap
			return min($end_one,$end_two)->diff(max($start_two,$start_one))->days + $plus; //return how many days overlap
		}

		return 0; //Return 0 if there is no overlap
	}

	public static function renderDetails($model, $showTitle = true)
	{
		if($showTitle)
			echo '<h1>'.tt('Booking details', 'booking').'</h1><p>';
		if($model->comment_admin)
			echo $model->comment_admin.'<hr>';
		if($model->details)
			echo $model->details.'<hr>';
		if($model->payment && $model->payment->status == Payments::STATUS_PAYMENTCOMPLETE){
			echo $model->payment->amount . Currency::getNameByCharCode($model->payment->currency_charcode). ' ' . $model->payment->returnStatusHtml();
		}
		echo '</p>';
		Yii::app()->end();
	}
}
?>