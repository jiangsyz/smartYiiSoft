<?php
namespace yii\base;
use Yii;
use yii\base\Exception;
class SmartException extends Exception{
	private static $safeFlag=true;
	public function __construct($msg,$code=false){
		//获取堆栈信息
		$backtrace=debug_backtrace(false,false);
		$backtraceInfo=array();
		foreach($backtrace as $row){
			if(isset($row['function']) && isset($row['class'])){
				$backtraceInfo[]=array('function'=>$row['function'],'class'=>$row['class']);
			}
		}
		array_shift($backtraceInfo);
		//记录日志(self::$safeFlag的作用是防止exceptionLog时又有SmartException抛出导致死循环)
		if(isset(Yii::$app->smartLog) && self::$safeFlag){
			self::$safeFlag=false;
			Yii::$app->smartLog->exceptionLog(json_encode(array('msg'=>$msg,'backtrace'=>$backtraceInfo)));
			self::$safeFlag=true;
		}
		parent::__construct($msg,$code);
	}
}
