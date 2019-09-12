<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/8/1
 * Time: 5:56 PM
 */
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Model\Notice;
use App\Model\SubscribeSupply;
use App\Model\SupSupply;
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
            return $this->successResponse($supinfo,'绑定成功');
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
    /**
     * 我的通知
     * @param Request $request
     * @return mixed
     */
    public function notice(Request $request){
        $type=$request->input('type');
        if(!$type){
            return $this->response->error('通知类型id为空',$this->forbidden_code);
        }
        $data['list']=Notice::where(['type'=>$type,'supplier_id'=>$this->user->id])->forPage($request->input('page',1),$request->input('limit',15))
            ->orderBy('add_time','desc')->get(['id','title','content','add_time']);
        $data['count']=$data['list']->count();
        return $this->successResponse($data);
    }

    /**
     * 供货商预约进度
     * @param Request $request
     * @return mixed
     */
    public function progress(Request $request){
        $data['count']=SubscribeSupply::where('supplier_id',$this->user->id)->count();
        $data['list'] =SubscribeSupply::where('supplier_id',$this->user->id)->orderBy('sub_time','desc')->forPage($request->input('page',1),$request->input('limit',15))
            ->get(['id','shipper_name','goods_name','sub_code','sub_time','status']);
        return $this->successResponse($data);
    }
    /**
     * 预约进度详情
     * @param Request $request
     * @return mixed
     */
    public function progressDetails(Request $request){
        $id=$request->input('id');
        $subinfo=SubscribeSupply::find($id);
        if(!$id||!$subinfo){
            return $this->response->error('id为空或信息不存在',$this->forbidden_code);
        }
        return $this->successResponse($subinfo);
    }

    /**
     * 我的供货记录
     * @param Request $request
     * @param SupSupply $supply
     * @return mixed
     */
    public function mySupply(Request $request,SupSupply $supply){
        $data=$supply->getList($request,$this->user);
        return $this->successResponse($data);
    }
    /**
     * 我的供货记录详情
     * @param Request $request
     * @return mixed
     */
    public function supplyDetails(Request $request){
        $id=$request->input('id');
        $supply_info=SupSupply::find($id)->toArray();
        if(!$id||!$supply_info){
            return $this->response->error('id为空或记录不存在',$this->forbidden_code);
        }
        foreach ($supply_info['sub_imgs'] as $k=>$img){
           $supply_info['sub_imgs'][$k]=config('filesystems.disks.admin.url').'/'.$img;
        }
        return $this->successResponse($supply_info);
    }
}