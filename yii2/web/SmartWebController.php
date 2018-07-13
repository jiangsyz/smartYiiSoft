<?php
namespace yii\web;
use Yii;
class SmartWebController extends Controller{
	//http请求基本信息
	private $httpInfo=[
		'httpType'=>NULL,
		'isAjax'=>NULL,
		'isJsonp'=>NULL,
		'domain'=>NULL,//域名
		'uri'=>NULL,//controller+action
		'requestTime'=>NULL,//请求时间
		'requestData'=>NULL,//请求数据
		'phpInput'=>NULL,//请求的原始数据的只读流
		'session'=>NULL,//session
		'cookie'=>NULL,//cookie
		'responseTime'=>NULL,//处理完成时间
		'responseData'=>NULL,//响应数据
	];
	//====================================================
	//http日志
	private $httpLog=false;
	//====================================================
	//初始化http请求基本信息
	private function initHttpInfo(){
		$request=Yii::$app->request;
		if($request->isPost) $this->httpInfo['httpType']='post';
		if($request->isGet) $this->httpInfo['httpType']='get';
		$this->httpInfo['isAjax']=$request->isAjax?true:false;
		$this->httpInfo['jsonpCallBack']=$request->get('jsonpcallback',NULL);
		$this->httpInfo['domain']=$request->hostInfo.$request->scriptUrl;
		$this->httpInfo['uri']="{$this->id}/{$this->action->id}";
		$this->httpInfo['requestTime']=time();
		$this->httpInfo['requestData']=$_REQUEST;
		$this->httpInfo['phpInput']=file_get_contents('php://input');
		$this->httpInfo['session']=isset($_SESSION)?$_SESSION:NULL;
		$this->httpInfo['cookie']=isset($_COOKIE)?$_COOKIE:NULL;
		//记录日志
		$this->httpLog=Yii::$app->smartLog->httpLog(json_encode($this->httpInfo));
	}
	//====================================================
	//获取http请求基本信息
	public function getHttpInfo(){return $this->httpInfo;}
	//====================================================
	public function beforeAction($action){
		//初始化http请求基本信息
		$this->initHttpInfo();
		//校验签名
		if(isset(Yii::$app->smartSignature)) Yii::$app->smartSignature->checkWebSignature($this);
		return parent::beforeAction($action);
	}
	//====================================================
	/*
	响应
	type=1:输出json,多用于api
	type=2:跳转(可选择是否带提示信息)
	type=3:直接输出
	*/
	public function response($type,$data){
		$this->httpInfo['responseTime']=time();
		$this->httpInfo['responseData']=array('type'=>$type,'data'=>$data);
		//记录日志
		if($this->httpLog) $this->httpLog->updateObj(array('data'=>json_encode($this->httpInfo)));
		//输出json,多用于api
		if($type==1 && is_array($data)){
			$data['httpId']=$this->runningId;
			//确认输出内容
			$response=json_encode($data);
			if($this->httpInfo['jsonpCallBack']) $response=$this->httpInfo['jsonpCallBack'].'('.$response.')';
			die($response);
		}
		//跳转
		if($type==2 && isset($data['uri'])){
			//带提示信息,js弹框后跳转
			if(isset($data['msg'])){
				header('Content-Type:text/html;charset=utf-8');
				$response="<script language='javascript' type='text/javascript'>";
				$response.="alert('{$data['msg']}');window.location.href='{$data['uri']}';";
				$response.="</script>";
				die($response);
			}
			//不带提示信息,直接跳转
			header("Location:{$data['uri']}");exit;
		}
		//直接输出
		if($type==3){
			echo $data;exit;
		}
	}
	//====================================================
	//获取get(urldecode)
	public function requestGet($key,$defaultVal=false){
		$val=Yii::$app->request->get($key,$defaultVal);
		if(is_string($val)){
			if(urlencode(urldecode($val))==$val) $val=urldecode($val);
			trim($val);
		}
		return $val;
	}
	//====================================================
	//获取post(urldecode)
	public function requestPost($key,$defaultVal=false){
		$val=Yii::$app->request->post($key,$defaultVal);
		if(is_string($val)){
			if(urlencode(urldecode($val))==$val) $val=urldecode($val);
			trim($val);
		}
		return $val;
	}
}