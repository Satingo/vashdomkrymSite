<?php if (isset($entriesIndex) && $entriesIndex) : ?>
	<div class="clear"></div>
	<div class="last-entries-index">
		<p class="title"><?php echo tt('News', 'entries');?></p>
		<?php foreach($entriesIndex as $entries) : ?>
			<div class="last-entries-item">
				<div class="last-entries-date">
					<p class="ns-label">
						<?php echo $entries->dateCreatedLong;?>
					</p>
				</div>
				<div class="last-entries-title">
					<?php echo CHtml::link(truncateText($entries->getStrByLang('title'), 8), $entries->getUrl());?>
				</div>
			</div>
		<?php endforeach;?>
	</div>
	<div class="clear"></div>
<?php endif;?>

<?php
if($page){
	if (isset($page->page)) {

		if ($page->page->widget && $page->page->widget_position == InfoPages::POSITION_TOP){
			echo '<div>';
			Yii::import('application.modules.'.$page->page->widget.'.components.*');
			$widgetData = array();

			switch($page->page->widget){
				case 'contactform':
					$widgetData = array('page' => 'index');
					break;

				case 'apartments':
					$criteria = $page->page->getCriteriaForAdList();
					$criteria = HGeo::setForIndexCriteria($criteria);

					$widgetData = array('criteria' => $criteria);
					break;
				
				case 'entries':
					$widgetData = array('criteria' => $page->page->getCriteriaForEntriesList());
					break;
			}
			$this->widget(ucfirst($page->page->widget).'Widget', $widgetData);
			echo '</div><div class="clear">&nbsp;</div>';
		}

		if($page->page->body){
			echo $page->page->body;
		}

		if ($page->page->widget && $page->page->widget_position == InfoPages::POSITION_BOTTOM){
			echo '<div class="clear"></div><div>';
			Yii::import('application.modules.'.$page->page->widget.'.components.*');
			$widgetData = array();

			switch($page->page->widget){
				case 'contactform':
					$widgetData = array('page' => 'index');
					break;

				case 'apartments':
					$criteria = $page->page->getCriteriaForAdList();
					$criteria = HGeo::setForIndexCriteria($criteria);

					$widgetData = array('criteria' => $criteria);
					break;
				
				case 'entries':
					$widgetData = array('criteria' => $page->page->getCriteriaForEntriesList());
					break;
			}
			$this->widget(ucfirst($page->page->widget).'Widget', $widgetData);
			echo '</div>';
		}
	}
}