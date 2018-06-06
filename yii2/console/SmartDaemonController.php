<?php
namespace yii\console;
use Yii;
class SmartDaemonController extends Controller{
	private $beginLog=false;
	//========================================
	//命令行开始运行
	public function begin(){
		if(!$this->beginLog) $this->beginLog=Yii::$app->smartLog->consoleLog('begin');
	}
	//========================================
	//命令行存活
	public function alive(){Yii::$app->smartLog->consoleLog('alive');}
	//========================================
	//命令行休息
	public function sleep(){sleep(Yii::$app->params["consoleSleep"]);}
}