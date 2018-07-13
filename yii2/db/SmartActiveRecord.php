<?php
namespace yii\db;
use yii\base\SmartException;
use yii\db\ActiveRecord;
class SmartActiveRecord extends ActiveRecord{
	static function addObj($param=array()){
		if(!is_array($param)) throw new SmartException("param is not array");
		$model=new static($param);
		if(!$model->insert()) throw new SmartException(json_encode($model->getErrors()));
		return $model;
	}
	//========================================
	public function updateObj($param=array()){
		//被修改字段的数量
		$changeCount=0;
		if(!is_array($param)) throw new SmartException("param is not array");
		foreach($param as $key=>$val){
			//字段被修改了
			if($this->$key!=$val){
				$this->$key=$val;
				$changeCount++;
			}
		}
		//修改
		if($changeCount>0){
			$result=$this->update();
			if($result!=1) throw new SmartException(json_encode($this->getErrors())."({$result})");
		}
		return true;
	}
	//========================================
	public function getData($keys=array()){
		//没有指定获取哪些字段
		if(empty($keys)) return $this->attributes;
		//获取指定key的值
		$data=array();
		foreach($keys as $key) $data[$key]=isset($this->attributes[$key])?$this->attributes[$key]:NULL;
		//返回
		return $data;
    }
    //========================================
    //并发锁状态
    protected $metuxLocked=false;
    //加并发锁
    public function metuxLock(){
    	if(!$this->metuxLocked){
    		$sql="SELECT `id` FROM ".self::tableName()." WHERE `id`='{$this->id}' FOR UPDATE";
			static::getDb()->createCommand($sql)->query();
			$this->metuxLocked=true;
    	}
    }
}