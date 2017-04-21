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
?>

<?php
	switch ($type) {
		case 'flags':
			echo '<div class="language-select">';
				foreach ($languages as $lang) {
					if ($lang['name_iso'] != $currentLang) {
						echo CHtml::link(
							'<img src="'  . Yii::app()->getBaseUrl() . Lang::FLAG_DIR . $lang['flag_img'] . '" alt="'.$lang['name'].'" title="' . $lang['name'] . '" />',
							$this->getOwner()->createLangUrl($lang['name_iso'])
						);
					}
					;
				}
			echo '</div>';
			break;

		case 'links':
			$lastElement = end($languages);

			echo '<div class="language-select">';
				foreach ($languages as $lang) {
					$imgFlag = '<img src="'  . Yii::app()->getBaseUrl() . Lang::FLAG_DIR . $lang['flag_img'] . '" title="' . $lang['name'] . '" alt="' . $lang['name'] . '" class="flag_img" />';
					if ($lang['name_iso'] != $currentLang) {
						echo CHtml::link(
							$imgFlag . $lang['name'],
							$this->getOwner()->createLangUrl($lang['name_iso']),
							array('class' => 'language-select-link')
						);
					} else {
						echo '<strong>' . $imgFlag . $lang['name'] . '</strong>';
					}
					if ($lang != $lastElement) echo ' | ';
				}
			echo '</div>';
			break;
		case 'li':
			$lastElement = end($languages);
            $countLang = count($languages);

			foreach ($languages as $lang) {
				echo '<li>';
					$imgFlag = '<img src="'  . Yii::app()->getBaseUrl() . Lang::FLAG_DIR . $lang['flag_img'] . '" alt="' . $lang['name'] . '" title="' . $lang['name'] . '" class="flag_img" />';
					$class = ($lang['name_iso'] == $currentLang) ? 'active' : '';
                    $label = ( Yii::app()->user->isGuest || $countLang > 3 ) ? $imgFlag . $lang['name'] : $imgFlag;

					echo CHtml::link(
                        $label,
						$this->getOwner()->createLangUrl($lang['name_iso']),
						array('class' => $class)
					);
				echo '</li>';
			}
			break;

		case 'dropdown':
			echo '<div class="language-select">';
				echo CHtml::form();
				$dropDownLangs = array();
				foreach ($languages as $lang) {
					echo CHtml::hiddenField(
						$lang['name_iso'],
						$this->getOwner()->createLangUrl($lang['name_iso'])
						, array('id' => 'langurl_' . $lang['name_iso'])
					);
					$dropDownLangs[$lang['name_iso']] = $lang['name'];
				}
				echo CHtml::dropDownList('lang', $currentLang, $dropDownLangs,
					array(
						'onclick' => ' this.form.action=$("#langurl_"+this.value).val(); this.form.submit(); return false; ',
					)
				);
				echo CHtml::endForm();
			echo '</div>';
			break;
	}
?>