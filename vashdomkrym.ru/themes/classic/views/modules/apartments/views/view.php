<?php
$this->pageTitle .= ' - '.$model->getStrByLang('title');
if (isset($model->city) && isset($model->city->name)) {
	$this->pageTitle .=  ', '.tc('City'). ' ' . $model->city->name;
}

if ($model->getStrByLang('description'))
	$this->pageDescription = truncateText($model->getStrByLang('description'), 20);

$searchUrl = Yii::app()->user->getState('searchUrl');
if($searchUrl){
	$this->breadcrumbs=array(
		Yii::t('common', 'Apartment search') => $searchUrl,
		truncateText($model->getStrByLang('title'), 10),
	);
} else {
	$this->breadcrumbs=array(
		Yii::t('common', 'Apartment search') => array('/quicksearch/main/mainsearch'),
		truncateText($model->getStrByLang('title'), 10),
	);
}

?>

<div class='div-pdf-fix'>
	<?php
		echo '<div class="floatleft printicon">';


		if($searchUrl){
			echo CHtml::link('<img src="'.Yii::app()->theme->baseUrl.'/images/design/back2search.png"
				alt="'.tc('Go back to search results').'" title="'.tc('Go back to search results').'"  />',
				$searchUrl);
		} elseif (stripos(Yii::app()->request->urlReferrer, Yii::app()->getBaseUrl(true)) !== false)
			echo CHtml::link('<img src="'.Yii::app()->theme->baseUrl.'/images/design/back2search.png"
			 	alt="'.tc('Go back to search results').'" title="'.tc('Go back to search results').'"  />',
				'#', array('onclick'=>'window.history.back(); return false;'));


	echo CHtml::link('<img src="'.Yii::app()->theme->baseUrl.'/images/design/print.png"
				alt="'.tc('Print version').'" title="'.tc('Print version').'"  />',
			$model->getUrl().'?printable=1', array('target' => '_blank'));


		$editUrl = $model->getEditUrl();

		if($editUrl){
			echo CHtml::link('<img src="'.Yii::app()->theme->baseUrl.'/images/design/edit.png"
				alt="'.tt('Update apartment').'" title="'.tt('Update apartment').'"  />',
				$editUrl);
		}
		echo '</div>';
	?>

	<div class="floatleft-title">
		<div>
			<div class="div-title">
				<h1 class="h1-ap-title"><?php echo CHtml::encode($model->getStrByLang('title')); ?></h1>
			</div>
			<?php if($model->rating): ?>
			<div class="ratingview-title">
				<?php
				$this->widget('CStarRating',
					array(
						'name'=>'ratingview'.$model->id,
						'id'=>'ratingview'.$model->id,
						'value'=>intval($model->rating),
						'readOnly'=>true,
					));
				?>
			</div>
			<?php endif; ?>
		</div>
		<div class="clear"></div>
		<div class="stat-views">
			<?php if (isset($statistics) && is_array($statistics)) : ?>
			<?php echo tt('Views') ?>: <?php echo tt('views_all') . ' ' . $statistics['all'] ?>, <?php echo tt('views_today') . ' ' . $statistics['today'].'.&nbsp;';?>
			<?php echo '&nbsp;'.tc('Date created') . ': ' . $model->getDateTimeInFormat('date_created'); ?>
			<?php endif; ?>
		</div>
	</div>
</div>
<div class="clear"></div>
<?php
	// show ad
	$this->renderPartial('_view', array(
		'data'=>$model,
	));
?>

<div>
	<?php
	$criteria = new CDbCriteria();
	$criteria->compare('t.parent_id', $model->id);

	$this->widget('application.modules.apartments.components.ApartmentsWidget', array(
		'criteria' => $criteria,
		'widgetTitle' => tt('offers', 'apartments'),
		'showSwitcher' => 0,
		'usePagination' => 0,
		'showSorter' => 0,
		'showIfNone' => 0,
	));
	?>
</div>

<?php if (param('useSchemaOrgMarkup')) {
	$jsonLDMain = array();
	$jsonLDMain['@context'] = 'http://schema.org';
	$jsonLDMain['@type'] = 'Offer';
	$jsonLDMain['name'] = CHtml::encode($model->getTitle());
	$jsonLDMain['description'] = strip_tags($model->getStrByLang("description"));
	if (!$model->is_price_poa) {
		$jsonLDMain['price'] = $model->getPriceFrom();
		$jsonLDMain['priceCurrency'] = (issetModule('currency')) ? Currency::getCurrentCurrencyModel()->char_code : param('siteCurrency', '$');
	}
	
	if (isset($model->images) && !empty($model->images)) {
		$res = Images::getMainThumb(640, 400, $model->images);
		$img = CHtml::image($res['thumbUrl'], $res['comment']);
		
		$jsonLDMain['image'] = array(
			'@type' => 'ImageObject',
			'url' => $img,
			'height' => 400,
			'width' => 640
		);
	}
	echo '<script type="application/ld+json">'.  CJavaScript::jsonEncode($jsonLDMain).'</script>';
	
	if ($model->lat && $model->lng) {
		$jsonLDCoordinates = array();
		$jsonLDCoordinates['@context'] = 'http://schema.org';
		$jsonLDCoordinates['@type'] = 'GeoCoordinates';
		$jsonLDCoordinates['latitude'] = $model->lat;
		$jsonLDCoordinates['longitude'] = $model->lng;
		
		echo '<script type="application/ld+json">'.  CJavaScript::jsonEncode($jsonLDCoordinates).'</script>';
	}

	
	if($model->rating) {
		$jsonLDRating = array();
		$jsonLDRating['@context'] = 'http://schema.org';
		$jsonLDRating['@type'] = 'AggregateRating';
		$jsonLDRating['ratingValue'] = (int) $model->rating;
		$jsonLDRating['bestRating'] = 10;
		$jsonLDRating['worstRating'] = 1;
		$commentCount = Comment::countForModel('Apartment', $model->id);
		$jsonLDRating['reviewCount'] = ($commentCount && $commentCount > 1) ? $commentCount : 1;
		$jsonLDRating['itemReviewed'] = array(
			'@type' => 'Offer',
			'name' => CHtml::encode($model->getTitle()),
			'url' => $model->getUrl()
		);
		
		echo '<script type="application/ld+json">'.  CJavaScript::jsonEncode($jsonLDRating).'</script>';
	}
	
	if ($model->canShowInView('address')) {
		$jsonLDAddress = array();
		$isShowAddress = true;
		
		# Search engine crawlers is "GUEST"
		if (Yii::app()->user->isGuest && issetModule('tariffPlans') && issetModule('paidservices')) {
			$defaultTariffInfo = TariffPlans::getFullTariffInfoById(TariffPlans::DEFAULT_TARIFF_PLAN_ID);
			if (!$defaultTariffInfo['showAddress'])
				$isShowAddress = false;
		}
		
		if ($isShowAddress) {
			if (issetModule('location') && isset($model->locCountry) && isset($model->locRegion) && isset($model->locCity)) {
				$jsonLDAddress['@context'] = 'http://schema.org';
				$jsonLDAddress['@type'] = 'PostalAddress';
				$jsonLDAddress['addressCountry'] = $model->locCountry->getStrByLang('name');
				$jsonLDAddress['addressRegion'] = $model->locRegion->getStrByLang('name');
				$jsonLDAddress['addressLocality'] = $model->locCity->getStrByLang('name');
				$jsonLDAddress['streetAddress'] = CHtml::encode($model->getStrByLang('address'));
			}
			elseif (isset($model->city)) {
				$jsonLDAddress['@context'] = 'http://schema.org';
				$jsonLDAddress['@type'] = 'PostalAddress';
				$jsonLDAddress['addressLocality'] = CHtml::encode($model->city->name);
				$jsonLDAddress['streetAddress'] = CHtml::encode($model->getStrByLang('address'));
			}
			
			echo '<script type="application/ld+json">'.  CJavaScript::jsonEncode($jsonLDAddress).'</script>';
		}
	}
}