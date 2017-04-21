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

<div class="language-select">
    <?php
    switch ($type) {
        case 'flags':
            foreach ($languages as $lang) {
                if ($lang['name_iso'] != $currentLang) {
                    echo CHtml::link(
                        '<img src="'  . Yii::app()->getBaseUrl() . Lang::FLAG_DIR . $lang['flag_img'] . '" title="' . $lang['name'] . '">',
                        $this->getOwner()->createLangUrl($lang['name_iso'])
                    );
                }
                ;
            }
            break;

        case 'links':
            $lastElement = end($languages);

            foreach ($languages as $lang) {
                $imgFlag = '<img src="'  . Yii::app()->getBaseUrl() . Lang::FLAG_DIR . $lang['flag_img'] . '" title="' . $lang['name'] . '" class="flag_img">';
                if ($lang['name_iso'] != $currentLang) {
                    echo CHtml::link(
                        $imgFlag . $lang['name'],
                        $this->getOwner()->createLangUrl($lang['name_iso']),
                        array('class' => 'language-select-link')
                    );
                } else {
                    echo '<b>' . $imgFlag . $lang['name'] . '</b>';
                }
                if ($lang != $lastElement) echo ' | ';
            }
            break;

        case 'dropdown':
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

            break;
    }

    ?>
</div>