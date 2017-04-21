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

class DbMessageSource extends CMessageSource {
	const CACHE_KEY_PREFIX = 'Yii.DbMessageSource.';
	private $_messages=array();

	/**
	 * @var integer the time in seconds that the messages can remain valid in cache.
	 * Defaults to 0, meaning the caching is disabled.
	 */
	public $cachingDuration = 60;
	/**
	 * @var string the ID of the cache application component that is used to cache the messages.
	 * Defaults to 'cache' which refers to the primary cache application component.
	 * Set this property to false if you want to disable caching the messages.
	 */
	public $cacheID = 'cache';

	/**
	 * Loads the message translation for the specified language and category.
	 * @param string $category the message category
	 * @param string $language the target language
	 * @return array the loaded messages
	 */
	protected function loadMessages($category, $language) {
		if ($this->cachingDuration > 0 && $this->cacheID !== false && ($cache = Yii::app()->getComponent($this->cacheID)) !== null) {
			$key = self::CACHE_KEY_PREFIX . '.messages.' . $category . '.' . $language;
			if (($data = $cache->get($key)) !== false) {
				return unserialize($data);
			}
		}

		$messages = $this->loadMessagesFromDb($category, $language);

		if (isset($cache)) {
			$cache->set($key, serialize($messages), $this->cachingDuration);
		}

		return $messages;
	}

	/**
	 * Translates the specified message.
	 * If the message is not found, an {@link onMissingTranslation}
	 * event will be raised.
	 * @param string $category the category that the message belongs to
	 * @param string $message the message to be translated
	 * @param string $language the target language
	 * @return string the translated message
	 */
	protected function translateMessage($category,$message,$language)
	{
		$key=$language.'.'.$category;
		if(!isset($this->_messages[$key]))
			$this->_messages[$key]=$this->loadMessages($category,$language);
		if(isset($this->_messages[$key][$message]['translation']) && $this->_messages[$key][$message]['translation']!=='')
			return $this->_messages[$key][$message]['translation'];
		elseif($this->hasEventHandler('onMissingTranslation') && (!isset($this->_messages[$key][$message]['status']) || $this->_messages[$key][$message]['status'] == 0))
		{
			$event=new CMissingTranslationEvent($this,$category,$message,$language);
			$this->onMissingTranslation($event);
			return $event->message;
		}
		else
			return $message;
	}

	/**
	 * Loads the messages from database.
	 * You may override this method to customize the message storage in the database.
	 * @param string $category the message category
	 * @param string $language the target language
	 * @return array the messages loaded from database
	 * @since 1.1.5
	 */
	protected function loadMessagesFromDb($category, $language) {
		$sql = "SELECT message, translation_" . $language . " AS translation, status
                        FROM {{translate_message}}
                        WHERE category=:category";
		$command = Yii::app()->db->createCommand($sql);
		$command->bindValue(':category', $category);
		$messages = array();
		foreach ($command->queryAll() as $row) {
			$messages[$row['message']]['translation'] = $row['translation'];
			$messages[$row['message']]['status'] = $row['status'];
		}

		return $messages;
	}

}
