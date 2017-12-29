<?php
namespace Api\Controller;
use Think\Controller;

class DataViewController extends ApiController
{

    //系统首页
    public function index()
    {
        $this->show('hello world');
    }
	public function chord()
	{
		header("Content-type: text/html; charset=utf-8");
		header("Access-Control-Allow-Origin: *");
		header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With');  
		$webkit = array(
			'type'=>'graph'
		);
		$sql = "SELECT MAX(num) as value,bumen_name,theme_name,bumen_id,bumen_url FROM big_data_list GROUP BY bumen_name ORDER BY bumen_id";
		$Model = M();
		$list = $Model->query($sql);
		$sql = " SELECT * FROM big_data_theme order by num desc ";
		$webkit['rank'] = $Model->query($sql);
		if($list){
			$theme_arr = array_values(array_unique(array_column($list,'theme_name')));
			foreach($theme_arr as $k=>$v){
				$webkit['categories'][$k]['name'] = $v;	
				$webkit['categories'][$k]['keyword'] = '';			
			}
			foreach($list as $k=>$v){
				$webkit['nodes'][$k]['name'] = $v['bumen_name'];
				$webkit['nodes'][$k]['value'] = $v['value'];
				$webkit['nodes'][$k]['url'] = $v['bumen_url'];
				foreach($webkit['categories'] as $wk=>$wv){
					if($v['theme_name'] == $wv['name']){
						$webkit['nodes'][$k]['category'] = $wk;
					}
				}
				$webkit['nodes'][$k]['symbolSize'] = '30';
				
			}
			foreach($webkit['nodes'] as $k=>$v){
				foreach($webkit['nodes'] as $nk=>$nv){
					if($v['name']!=$nv['name'] && $v['category'] ==$nv['category']){
						$webkit['link'][] = array('source'=>$k,'target'=>$nk);
					}
				}
			}
		}
		$this->ajaxReturn($webkit);
	}
    public function force_bank(){
		header("Content-type: text/html; charset=utf-8");
		header("Access-Control-Allow-Origin: *");
		header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With');
		$keyword = I('get.keyword');
		$keyword ='李光元';
		if($keyword){
			$map['name'] = array('like', '%'.(string)$keyword.'%');
			$name_list= M('data_poorfamilyinfo')->where($map)->limit('10')->select();
			$default = current($name_list);
			if($default){
				$webkit = $this->nodes_init($default['name']);
				if($webkit){
					$webkit['nodes'][] = array('name'=>'年龄','value'=>'3','category'=>'1','symbolSize'=>'20','child'=>$this->get_age($default['identity_num']));
					$webkit['nodes'][] = array('name'=>'性别','value'=>'3','category'=>'1','symbolSize'=>'20','child'=>$this->get_sex($default['identity_num']));
					$webkit['nodes'][] = array('name'=>'民族','value'=>'3','category'=>'1','symbolSize'=>'20','child'=>$default['ethnic']);
					$webkit['nodes'][] = array('name'=>'身份证号','value'=>'3','category'=>'1','symbolSize'=>'20','child'=>$default['identity_num']);
					$webkit['nodes'][] = array('name'=>'手机号','value'=>'3','category'=>'1','symbolSize'=>'20','child'=>$default['phone_number']);
					$webkit['nodes'][] = array('name'=>$default['skills'],'value'=>'3','category'=>'1','symbolSize'=>'20');
				}
				if($default['family_id']){
					$f_map['id'] = array('neq',$default['id']);
					$f_map['family_id'] = array('eq',$default['family_id']);
					$family = M('data_poorfamilyinfo')->where($f_map)->select();
					foreach($family as $k=>$v){
						$webkit['nodes'][] = array('name'=>$this->get_relationship($v['relationship']),'value'=>'3','category'=>'2','symbolSize'=>'20','child'=>$v['name']);
					}
				}
				if($default['district']){
					$webkit['nodes'][] = array('name'=>$default['district'].$default['town'].$default['village'],'value'=>'3','category'=>'3','symbolSize'=>'20');
				}
				$webkit['nodes'][] = array('name'=>'贵州宏发贸易有限公司','value'=>'3','category'=>'4','symbolSize'=>'20');
			}
			$webkit['nodes'][] = array('name'=>'学历','value'=>'3','category'=>'5','symbolSize'=>'20','child'=>$default['education']);
			$webkit['nodes'][] = array('name'=>'医疗机构','value'=>'3','category'=>'6','symbolSize'=>'20');
			$webkit['nodes'][] = array('name'=>'健康状况','value'=>'3','category'=>'6','symbolSize'=>'20','child'=>$default['health']);
			$webkit['nodes'][] = array('name'=>'关注信息','value'=>'3','category'=>'7','symbolSize'=>'20');
			$webkit['nodes'][] = array('name'=>'随地摆摊','value'=>'3','category'=>'8','symbolSize'=>'20');
			foreach($webkit['nodes'] as $k=>$v){
				if($v['child']){
					$webkit['nodes'][] = array('name'=>$v['child'],'value'=>'3','category'=>$v['category'],'symbolSize'=>'10');
				}
			}
			foreach($webkit['nodes'] as $k=>$v){
				if($v['symbolSize']==40){
					$webkit['links'][] = array('source'=>0,'target'=>$k);
				}
				foreach($webkit['nodes'] as $wk=>$wv){
					if($wv['symbolSize'] == 20 && $v['category'] == $wv['category'] && $v['symbolSize'] == 40){
						$webkit['links'][] = array('source'=>$k,'target'=>$wk);
					}
					if($v['child'] == $wk['name'] && strlen($v['child'])>0){
						$webkit['links'][] = array('source'=>$k,'target'=>$wk);
					}
				}
     		}
		}
		$this->ajaxReturn($webkit);
	}
    public function force(){
        header("Content-type: text/html; charset=utf-8");
        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With');
        $keyword = I('get.keyword');
        if($keyword){
            if(is_mobile($keyword)){
                $map['phone_number'] = array('eq',$keyword);
            }elseif(strlen($keyword)>12){
                $map['identity_num'] = array('eq',$keyword);
            }else{
                $map['name'] = array('like', '%'.(string)$keyword.'%');
            }

            $name_list = M('data_poorfamilyinfo')->where($map)->limit('10')->select();
            $default = current($name_list);
            if($default){
                $webkit = $this->nodes_init($default['name']);
                foreach ($name_list as $k=>$v){
                    $webkit['person'][$k]['id'] = $v['id'];
                    $webkit['person'][$k]['addresss'] = $v['district'].$v['town'].$v['village'];
                    $webkit['person'][$k]['name'] = $v['name'];
                    $webkit['person'][$k]['identity_num'] = $v['identity_num'];
                    $webkit['person'][$k]['ethnic'] = $v['ethnic'];
                    $webkit['person'][$k]['education'] = $v['education'];
                    $webkit['person'][$k]['age'] = $this->get_age($v['identity_num']);
                    $webkit['person'][$k]['sex'] = $this->get_sex($v['identity_num']);
                    $webkit['person'][$k]['phone_number'] = $v['phone_number'];
                }
                if($webkit){
                    $webkit['nodes'][] = array('id'=>$this->add_id(),'name'=>'年龄','value'=>'3','category'=>1,'symbolSize'=>'20','child'=>$this->get_age($default['identity_num']));
                    $webkit['nodes'][] = array('id'=>$this->add_id(),'name'=>'性别','value'=>'3','category'=>1,'symbolSize'=>'20','child'=>$this->get_sex($default['identity_num']));
                    $webkit['nodes'][] = array('id'=>$this->add_id(),'name'=>'民族','value'=>'3','category'=>1,'symbolSize'=>'20','child'=>$default['ethnic']);
                    $webkit['nodes'][] = array('id'=>$this->add_id(),'name'=>'身份证号','value'=>'3','category'=>1,'symbolSize'=>'20','child'=>$default['identity_num']);
                    $webkit['nodes'][] = array('id'=>$this->add_id(),'name'=>'手机号','value'=>'3','category'=>1,'symbolSize'=>'20','child'=>$default['phone_number']);
                    $webkit['nodes'][] = array('id'=>$this->add_id(),'name'=>$default['skills'],'value'=>'3','category'=>1,'symbolSize'=>'20');
                }
                if($default['family_id']){
                    $f_map['id'] = array('neq',$default['id']);
                    $f_map['family_id'] = array('eq',$default['family_id']);
                    $family = M('data_poorfamilyinfo')->where($f_map)->select();
                    foreach($family as $k=>$v){
                        $webkit['nodes'][] = array('id'=>$this->add_id(),'name'=>$this->get_relationship($v['relationship']),'value'=>'3','category'=>2,'symbolSize'=>'20','child'=>$v['name']);
                    }
                }
                if($default['district']){
                    $webkit['nodes'][] = array('id'=>$this->add_id(),'name'=>$default['district'].$default['town'].$default['village'],'value'=>'3','category'=>3,'symbolSize'=>'20');
                }
                $webkit['nodes'][] = array('id'=>$this->add_id(),'name'=>'贵州宏发贸易有限公司','value'=>'3','category'=>4,'symbolSize'=>'20');
            }
            $webkit['nodes'][] = array('id'=>$this->add_id(),'name'=>'学历','value'=>'3','category'=>5,'symbolSize'=>'20','child'=>$default['education']);
            //获取医疗
            $hospital =  M('data_medicalrecord')->where("identity_num = '{$default['identity_num']}'")->find();
            if($hospital){
                $webkit['nodes'][] = array('id'=>$this->add_id(),'name'=>'医疗机构','value'=>'3','category'=>6,'symbolSize'=>'20','child'=>$hospital['medical_facility']);
            }else{
                $webkit['nodes'][] = array('id'=>$this->add_id(),'name'=>'医疗机构','value'=>'3','category'=>6,'symbolSize'=>'20');
            }

            $webkit['nodes'][] = array('id'=>$this->add_id(),'name'=>'健康状况','value'=>'3','category'=>6,'symbolSize'=>'20','child'=>$default['health']);
            $webkit['nodes'][] = array('id'=>$this->add_id(),'name'=>'关注信息','value'=>'3','category'=>7,'symbolSize'=>'20');
            $webkit['nodes'][] = array('id'=>$this->add_id(),'name'=>'随地摆摊','value'=>'3','category'=>8,'symbolSize'=>'20');
            foreach($webkit['nodes'] as $k=>$v){
                if($v['child']){
                    $webkit['nodes'][] = array('id'=>$this->add_id(),'name'=>$v['child'],'value'=>'3','category'=>$v['category'],'symbolSize'=>'10');
                }
            }
            foreach($webkit['nodes'] as $k=>$v){
                if($v['symbolSize']==40){
                    $webkit['links'][] = array('source'=>0,'target'=>$k);
                }
                foreach($webkit['nodes'] as $wk=>$wv){
                    if($wv['symbolSize'] == 20 && $v['category'] == $wv['category'] && $v['symbolSize'] == 40){
                        $webkit['links'][] = array('source'=>$k,'target'=>$wk);
                    }
                    if($v['child'] == $wv['name'] && strlen($v['child'])>0){
                        $webkit['links'][] = array('source'=>$k,'target'=>$wk);
                    }
                }
            }
        }
        $this->ajaxReturn($webkit);
    }
	//根据身份证计算性别
	private function get_sex($idcard) {
		if(empty($idcard)) return null;
		$sexint = (int) substr($idcard, 16, 1);
		return $sexint % 2 === 0 ? '女' : '男';
	}
	//根据身份证计算年龄
	private function get_age($id)
	{
		if(empty($id)) return '';
		$date=strtotime(substr($id,6,8));
		$today=strtotime('today');
		$diff=floor(($today-$date)/86400/365);
		$age=strtotime(substr($id,6,8).' +'.$diff.'years')>$today?($diff+1):$diff;
		return $age;
	}
	private function nodes_init($name)
	{
		$webkit = array(
			'type'=>'force'
		);
		$webkit['categories'] = array(
			array(
				'name'=>'人物',
				'keyword'=>'',
			),
			array(
				'name'=>'基础信息',
				'keyword'=>'',
			),
			array(
				'name'=>'亲属关系',
				'keyword'=>'',
			),
			array(
				'name'=>'居住信息',
				'keyword'=>'',
			),
			array(
				'name'=>'单位信息',
				'keyword'=>'',
			),
			array(
				'name'=>'教育信息',
				'keyword'=>'',
			),
			array(
				'name'=>'医疗信息',
				'keyword'=>'',
			),
			array(
				'name'=>'舆情信息',
				'keyword'=>'',
			),
			array(
				'name'=>'事件信息',
				'keyword'=>'',
			),
		);
		$webkit['nodes'][] = array(
            'id'=>$this->add_id(),
			'name'=>$name,
			'value'=>'0',
			'category'=>0,
			'symbolSize'=>'70',
		) ;
		$webkit['nodes'][] = array(
            'id'=>$this->add_id(),
			'name'=>'基础信息',
			'value'=>'1',
			'category'=>1,
			'symbolSize'=>'40',
		);
		$webkit['nodes'][] = array(
            'id'=>$this->add_id(),
			'name'=>'亲属关系',
			'value'=>'2',
			'category'=>2,
			'symbolSize'=>'40',
		);
		$webkit['nodes'][] = array(
            'id'=>$this->add_id(),
			'name'=>'居住信息',
			'value'=>'3',
			'category'=>3,
			'symbolSize'=>'40',
		);
		$webkit['nodes'][] = array(
            'id'=>$this->add_id(),
			'name'=>'单位信息',
			'value'=>'4',
			'category'=>4,
			'symbolSize'=>'40',
		);
		$webkit['nodes'][] = array(
            'id'=>$this->add_id(),
			'name'=>'教育信息',
			'value'=>'5',
			'category'=>5,
			'symbolSize'=>'40',
		);
		$webkit['nodes'][] = array(
            'id'=>$this->add_id(),
			'name'=>'医疗信息',
			'value'=>'6',
			'category'=>6,
			'symbolSize'=>'40',
		);
		$webkit['nodes'][] = array(
            'id'=>$this->add_id(),
			'name'=>'舆情信息',
			'value'=>'7',
			'category'=>7,
			'symbolSize'=>'40',
		);
		$webkit['nodes'][] = array(
            'id'=>$this->add_id(),
			'name'=>'事件信息',
			'value'=>'8',
			'category'=>8,
			'symbolSize'=>'40',
		);
		return $webkit;
	}
	//head 1 是户主 2.是非户主
	private function get_relationship($relationship,$is_head = 1){
		if($is_head == 1){
			 $return = str_replace('之','',$relationship);
		}else{
		}
		array('之外孙女','之外孙子','之外祖母','之外祖父','之女','之女婿','之婆婆','之子','之孙女',
			'之孙子','之岳母','之岳父','之母','之父','之祖母','之祖父','其他','户主','配偶');
		return $return;
	}
	public function add_id(){
		 $id = S('view_id');
		 $id +=1;
		 S('view_id',$id);
		return $id;
	}
	public function robot()
    {
        header("Content-type: text/html; charset=utf-8");
        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With');
        $url = 'http://www.tuling123.com/openapi/api';
        $keyword = I('post.keyword');
        if($keyword){
            $data = array(
                'key'=>'665400e292654c248be693e7c9bcdca7',
                'userid'=>'siri',
                'info'=>$keyword,
            );
            $data =json_encode($data);
            $return = post_data($url,$data);
        }
        $this->ajaxReturn($return);
    }
	public function tableview()
	{
		
        header("Content-type: text/html; charset=utf-8");
        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With');
		$category = I('get.category');
		$identity_num = I('get.identity_num');
		//$category = 6;
		//$identity_num = '522121199309226246';
		switch($category)
		{
			case 0:
			$table = 'data_poorfamilyinfo';
			break;
			case 1:
			$table = 'data_poorfamilyinfo';
			break;
			case 2:
			$table = 'data_poorfamilyinfo';
			break;
			case 3:
			$table = 'data_poorfamilyinfo';
			break;
			case 4:
			$table = 'data_poorfamilyinfo';
			break;
			case 5:
			$table = 'data_poorfamilyinfo';
			break;
			case 6:
			$table = 'data_medicalrecord';
			//$table[] = 'data_medicalrecord_clinic';
			break;
			case 7:
			$table = 'data_poorfamilyinfo';
			break;
			case 8:
			$table = 'data_poorfamilyinfo';
			break;
			default:
			$table = 'data_medicalrecord';
			break;
		}
		//if(is_array($table)){
			 
		//}else{
			$res = M($table)->where("identity_num = '{$identity_num}' ")->select();
			if($res){
				$arr1 = M($table)->where("identity_num != '{$identity_num}' ")->limit('100')->select();
				$new = array_merge($res,$arr1);
			}
		//}
		$this->ajaxReturn($new);
	}
}