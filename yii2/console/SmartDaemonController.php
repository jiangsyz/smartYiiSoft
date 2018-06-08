<?php
namespace yii\console;
use Yii;
class SmartDaemonController extends Controller{
	private $beginLog=false;
	private $aliveLog=false;
	//========================================
	//守护进程开始运行
	public function begin(){
		if(!$this->beginLog) $this->beginLog=Yii::$app->smartLog->consoleLog('begin');
	}
	//========================================
	//守护进程存活
	public function alive(){
		//第一次报告存活新增日志
		if(!$this->aliveLog) 
			$this->aliveLog=Yii::$app->smartLog->consoleLog('alive');
		//后续报告存活修改日志时间
		else 
			$this->aliveLog->updateObj(array('time'=>time()));
	}
	//========================================
	//守护进程休息
	public function sleep(){sleep(Yii::$app->params["daemonSleep"]);}
}