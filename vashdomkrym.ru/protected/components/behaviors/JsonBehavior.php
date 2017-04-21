<?php
/**
 * Created by PhpStorm.
 * User: Andrey Pasynkov
 * Date: 19.11.13
 * Time: 11:56
 */

class JsonBehavior extends CActiveRecordBehavior{
    /** @var  array decoded data */
    private $cacheJson;

    public $jsonField = 'json_data';

    public function setInJson($key, $value, $save = false){
        $this->loadCache();
        $this->cacheJson[$key] = $value;
        if($save){
            return $this->saveJson();
        } else {
            $this->getOwner()->{$this->jsonField} = CJSON::encode($this->cacheJson);
        }
        return true;
    }

    public function getFromJson($key, $default = NULL){
        $this->loadCache();
        return isset($this->cacheJson[$key]) ? $this->cacheJson[$key] : $default;
    }

    public function deleteInJson($key, $save = true){
        $this->loadCache();
        if(isset($this->cacheJson[$key])){
            unset($this->cacheJson[$key]);
            if($save){
                $this->saveJson();
            }
        }
    }

    private function loadCache(){
        if(!$this->cacheJson){
            $this->cacheJson = $this->getOwner()->{$this->jsonField} ? CJSON::decode($this->getOwner()->{$this->jsonField}) : array();
        }
    }

    private function saveJson(){
        $this->getOwner()->{$this->jsonField} = CJSON::encode($this->cacheJson);
        if($this->getOwner()->save(true, array($this->jsonField))){
            return true;
        }
        //logs($this->getOwner()->errors);
        return false;
    }
}