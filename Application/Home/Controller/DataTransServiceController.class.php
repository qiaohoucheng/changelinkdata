<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;
use OT\DataDictionary;

/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class DataTransServiceController extends HomeController
{

    //系统首页
    public function index()
    {
        $this->show('hello world');
    }

    //传输单位信息和项目信息
    public function UploadCompProInfo()
    {
        $requset_json  = file_get_contents('php://input','r');
        $requset = json_decode($requset_json);
        if(is_array($requset['data'])){
            foreach($requset['data'] as $k=>$v){
                if($v['dwbh']){
                    $in_data[$k]['dwbh'] = $v['dwbh'];
                }
                if($v['dwmc']){
                    $in_data[$k]['dwmc'] = $v['dwmc'];
                }
                if($v['Fddbrxm']){
                    $in_data[$k]['fddbrxm'] = $v['Fddbrxm'];
                }
                if($v['Fddbrdh']){
                    $in_data[$k]['fddbrdh'] = $v['Fddbrdh'];
                }
                if($v['ZCDZ']){
                    $in_data[$k]['zcdz'] = $v['ZCDZ'];
                }
                ////////////////////////////////////////////
                if($v['Xmbh']){
                    $in_data2[$k]['xmbh'] = $v['Xmbh'];
                }
                if($v['xmmc']){
                    $in_data2[$k]['xmmc'] = $v['xmmc'];
                }
                if($v['FBR']){
                    $in_data2[$k]['fbr'] = $v['FBR'];
                }
                if($v['CBR']){
                    $in_data2[$k]['cbr'] = $v['CBR'];
                }
                if($v['GCDD']){
                    $in_data2[$k]['gcdd'] = $v['GCDD'];
                }
                if($v['GCLNG']){
                    $in_data2[$k]['gclng'] = $v['GCLNG'];
                }
                if($v['GCLAT']){
                    $in_data2[$k]['gclat'] = $v['GCLAT'];
                }
                if($v['LZFZR']){
                    $in_data2[$k]['lzfzr'] = $v['LZFZR'];
                }
                if($v['LZFZRSFZHM']){
                    $in_data2[$k]['lzfzrsfzhm'] = $v['LZFZRSFZHM'];
                }
                if($v['LZFZRDH']){
                    $in_data2[$k]['lzfzrdh'] = $v['LZFZRDH'];
                }
                if($v['CBRDH']){
                    $in_data2[$k]['cbrdh'] = $v['CBRDH'];
                }
                if($v['CBRFDDBR']){
                    $in_data2[$k]['cbrfddbr'] = $v['CBRFDDBR'];
                }
                if($v['CBRDZ']){
                    $in_data2[$k]['cbrdz'] = $v['CBRDZ'];
                }
                if($v['SSXZZGBM']){
                    $in_data2[$k]['ssxzzgbm'] = $v['SSXZZGBM'];
                }
            }
            if($in_data2){
                $result = M('data_projects')->addAll($in_data2);
                if($result!==false){
                    $return['result']= 0;
                    $return['errmessage']= '数据获取成功';
                }else{
                    $return['result']= 2;
                    $return['errmessage']= '系统异常';
                }
            }
            if($in_data){
                $result = M('data_companys')->addAll($in_data);
                if($result!==false){
                    $return['result']= 0;
                    $return['errmessage']= '数据获取成功';
                }else{
                    $return['result']= 2;
                    $return['errmessage']= '系统异常';
                }
            }else{
                $return['result']= 1;
                $return['errmessage']= '数据校验失败';
            }
        }else{
            $return['result']= 1;
            $return['errmessage']= '数据校验失败';
        }
        //$this->show('UploadCompProInfo');
        $this->ajaxReturn($return);
    }
    //传输项目相关人员信息
    public function UploadProPerInfo()
    {
        $requset_json  = file_get_contents('php://input','r');
        $requset = json_decode($requset_json);
        if(is_array($requset['data'])){
            foreach($requset as $k=>$v){
                if($v['ryid']){
                    $in_data[$k]['ryid'] = $v['ryid'];
                }
                if($v['xm']){
                    $in_data[$k]['xm'] = $v['xm'];
                }
                if($v['sfzhm']){
                    $in_data[$k]['sfzhm'] = $v['sfzhm'];
                }
                if($v['xmbh']){
                    $in_data[$k]['xmbh'] = $v['xmbh'];
                }
                if($v['XB']){
                    $in_data[$k]['xb'] = $v['XB'];
                }
                if($v['SSDWZW']){
                    $in_data[$k]['ssdwzw'] = $v['SSDWZW'];
                }
                if($v['GZ']){
                    $in_data[$k]['gz'] = $v['GZ'];
                }
                if($v['GZGZHDFS']){
                    $in_data[$k]['gzgzhdfs'] = $v['GZGZHDFS'];
                }
                if($v['SSBZ']){
                    $in_data[$k]['ssbz'] = $v['SSBZ'];
                }
                if($v['Sfzz']){
                    $in_data[$k]['sfzz'] = $v['Sfzz'];
                }
                if($v['rzsj']){
                    $in_data[$k]['rzsj'] = $v['rzsj'];
                }
                if($v['lzsj']){
                    $in_data[$k]['lzsj'] = $v['lzsj'];
                }
            }
            $result = M('data_personnel')->addAll($in_data);
            if($result!== false){
                $return['result']= 0;
                $return['errmessage']= '数据获取成功';
            }else{
                $return['result']= 2;
                $return['errmessage']= '系统异常';
            }
        }else{
            $return['result']= 1;
            $return['errmessage']= '数据校验失败';
        }
        $this->ajaxReturn($return);
    }
    //传输项目相关人员考勤信息
    public function UploadProAttendInfo()
    {
        $requset_json  = file_get_contents('php://input','r');
        $requset = json_decode($requset_json,true);
        if(is_array($requset['data'])){
            foreach($requset as $k=>$v){
                if($v['kqid']){
                    $in_data[$k]['kqid'] = $v['kqid'];
                }
                if($v['ryid']){
                    $in_data[$k]['ryid'] = $v['ryid'];
                }
                if($v['xmbh']){
                    $in_data[$k]['xmbh'] = $v['xmbh'];
                }
                if($v['sfzh']){
                    $in_data[$k]['sfzh'] = $v['sfzh'];
                }
                if($v['xm']){
                    $in_data[$k]['xm'] = $v['xm'];
                }
                if($v['kqsj']){
                    $in_data[$k]['kqsj'] = $v['kqsj'];
                }
                if($v['sbbs']){
                    $in_data[$k]['sbbs'] = $v['sbbs'];
                }
                if($v['kqsbbm']){
                    $in_data[$k]['kqsbbm'] = $v['kqsbbm'];
                }
            }
            $result = M('data_attendance')->addAll($in_data);
            if($result!== false){
                $return['result']= 0;
                $return['errmessage']= '数据获取成功';
            }else{
                $return['result']= 2;
                $return['errmessage']= '系统异常';
            }
        }else{
            $return['result'] = 1;
            $return['errmessage'] = '数据校验失败';
        }
        $this->ajaxReturn($return);
    }
}