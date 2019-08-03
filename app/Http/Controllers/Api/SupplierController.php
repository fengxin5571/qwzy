<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/8/1
 * Time: 5:56 PM
 */
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class SupplierController extends Controller{
    /**
     * 个人中心
     * @return mixed
     */
    public function  my(){
        $data=$this->user;
        $data['is_bind']=$this->user->routine_openid?true:false;
        return $this->successResponse($data);
    }
    /**
     * 退出登录
     * @return mixed
     */
    public function loginout(){
        auth('api')->logout();
        return $this->successResponse('','退出登录成功');
    }
    /**
     * 微信快捷绑定
     * @param Request $request
     * @return mixed
     */
    public function bind(Request $request){
        $message=[
            'nickName.required'=>'昵称不能为空',
            'avatarUrl.required'=>'头像不能为空',
            'routine_openid.required'=>'openid不能为空'
        ];
        $validator=Validator::make($request->all(),[
            'nickName'=>'required',
            'avatarUrl'=>'required',
            'routine_openid'=>'required'
        ],$message);
        if($validator->fails()){
            return $this->response->error($validator->errors()->first(),$this->forbidden_code);
        }
        $supinfo=$this->user;
        if(!$supinfo||$supinfo->status!=1){
            return $this->response->error('无此账号或账号已停用',$this->forbidden_code);
        }
        if($supinfo->update(
            ['routine_openid'=>$request->input('routine_openid'),'nickname'=>$request->input('nickName'),'headimgurl'=>$request->input('avatarUrl')])){
            return $this->successResponse('','绑定成功');
        }else{
            return $this->response->error('绑定失败',$this->forbidden_code);
        }

    }

    /**
     * 解除微信绑定
     * @return mixed
     */
    public function unbind(){
        $supplier=$this->user;
        if($supplier->update(['routine_openid'=>'','nickname'=>'','headimgurl'=>''])){
            return $this->successResponse('','解绑成功');
        }
        return $this->response->error('解绑失败',$this->forbidden_code);
    }
}