<?php
namespace yii\console;
use Yii;
class SmartDaemonController extends Controller{
	private $beginLog=false;
	//========================================
	//守护进程开始运行
	public function begin(){
		if(!$this->beginLog) $this->beginLog=Yii::$app->smartLog->consoleLog('begin');
	}
	//========================================
	//守护进程存活
	public function alive(){Yii::$app->smartLog->consoleLog('alive');}
	//========================================
	//守护进程休息
	public function sleep(){sleep(Yii::$app->params["consoleSleep"]);}
}