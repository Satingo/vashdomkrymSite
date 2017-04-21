<?php if(isset($page) && $page && isset($page->page)):?>
	<?php if ($page->page->widget && $page->page->widget_position == InfoPages::POSITION_TOP):?>
		<?php Yii::import('application.modules.'.$page->page->widget.'.components.*');
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

		?>
	<?php endif;?>
<?php endif;?>

<?php if(isset($page) && $page && isset($page->page)):?>
	<div class="welcome">
		<div class="title highlight-left-right">
			<span>
			<?php
				if($page->page->title){
					echo '<h1>'.CHtml::encode($page->page->title).'</h1>';
				}
			?>
			</span>
		</div>

		<?php if($page->page->body):?>
			<?php echo $page->page->body;?>
		<?php endif;?>
	</div>
<?php endif;?>

<?php if (isset($entriesIndex) && $entriesIndex) : ?>
	<div class="entries">
		<div class="title highlight-left-right">
			<span>
				<h2><?php echo tt('News', 'entries');?></h2>
			</span>
		</div>

		<?php
		$total = count($entriesIndex);
		$counter = 0;
		?>
		<?php foreach($entriesIndex as $entries) : ?>
			<?php $counter++;?>
			<?php $announce = ($entries->getAnnounce()) ? $entries->getAnnounce() : '&nbsp;';?>

			<div class="new">
				<div class="title">
					<?php //echo CHtml::link(truncateText($entries->getStrByLang('title'), 4), $entries->getUrl());?>
					<?php echo CHtml::link($entries->getStrByLang('title'), $entries->getUrl()); ?>
				</div>

				<?php
					$class = 'no-image-text';
					if($entries->image){
						$src = $entries->image->getThumb(80, 60);
						if($src){
							$class = 'text';
							echo CHtml::image(Yii::app()->getBaseUrl().'/uploads/entries/'.$src, $entries->getStrByLang('title'),
								array('class' => 'float-left')
							);
						}
					}

				?>

				<div class="<?php echo $class; ?>">
					<?php
						if($class == 'text'){
							//echo truncateText($announce, 10);
							echo truncateText($announce, 25);
						} else {
							//echo truncateText($announce, 15);
							echo truncateText($announce, 40);
						}

					?>
				</div>
				<div class="clear"></div>
			</div>

			<?php if($counter != $total):?>
				<div class="dotted_line"></div>
			<?php endif;?>
		<?php endforeach;?>
	</div>
<?php endif;?>
<div class="clear"></div>

<?php if(isset($page) && $page && isset($page->page)):?>
	<?php if ($page->page->widget && $page->page->widget_position == InfoPages::POSITION_BOTTOM):?>
		<?php Yii::import('application.modules.'.$page->page->widget.'.components.*');
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

		?>
	<?php endif;?>
<?php endif;?>