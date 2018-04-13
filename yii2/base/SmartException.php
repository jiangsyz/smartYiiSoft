<?php
namespace yii\base;
use Yii;
use yii\base\Exception;
class SmartException extends Exception{
	private static $safeFlag=true;
	public function __construct($msg,$code=-1){
		//获取堆栈信息
		$backtrace=debug_backtrace(false,false);
		$backtraceInfo=array();
		foreach($backtrace as $row){
			if(!isset($row['function'])) continue;
			if(!isset($row['class'])) continue;
			if(!isset($row['line'])) continue;
			if(!isset($row['args'])) continue;
			$backtrace=array();
			$backtrace['function']=$row['function'];
			$backtrace['class']=$row['class'];
			$backtrace['line']=$row['line'];
			//$backtrace['args']=$row['args'];
			$backtraceInfo[]=$backtrace;
		}
		//记录日志(self::$safeFlag的作用是防止exceptionLog时又有SmartException抛出导致死循环)
		if(isset(Yii::$app->smartLog) && self::$safeFlag){
			self::$safeFlag=false;
			$log=json_encode(array('msg'=>$msg,'code'=>$code,'backtrace'=>$backtraceInfo));
			Yii::$app->smartLog->exceptionLog($log);
			self::$safeFlag=true;
		}
		parent::__construct($msg,$code);
	}
}
