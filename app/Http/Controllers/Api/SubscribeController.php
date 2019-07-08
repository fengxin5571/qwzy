<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/6/18
 * Time: 9:26 AM
 */
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Model\SubscribeGoods;
use App\Model\SubscribeSupply;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
class SubscribeController extends Controller{
    /**
     * 临时预约供货
     * @param Request $request
     */
    public function temp(Request $request){
        $messages=[
            'driver_name.required'=>'司机姓名不能为空',
            'car_number.required'=>'车牌不能为空',
            'mobile.required'=>'手机号不能为空',
            'mobile.unique'=>'此用户已经预约',
            'mobile.is_mobile'=>'手机格式不正确',
            'goods_name.required'=>'请至少选择一个供货货品',
        ];
        $validator=Validator::make($request->all(),[
            'driver_name'=>'required',
            'car_number' =>'required',
            'goods_name'=>'required',
            'mobile'     =>['required','is_mobile',Rule::unique('subscribe_supply')->where(function($query)use($request){
                $query->where(['driver_name'=>$request->input('driver_name'),'sub_type'=>1,'status'=>0]);
            })
            ],

        ],$messages);
        if($validator->fails()){
            return $this->response->error($validator->errors()->first(),$this->forbidden_code);
        }
        try{
            $sms=new SmsService();
            if(!$code=$sms->sendSms($request->get('mobile'))){
                return $this->response->error('短信发送失败',$this->forbidden_code);
            }
            $data=[
                'driver_name'=>$request->input('driver_name'),
                'car_number' =>$request->input('car_number'),
                'mobile'     =>$request->input('mobile'),
                'goods_name' =>implode(',',SubscribeGoods::whereIn('id',explode(',',$request->input('goods_name')))->pluck('goods_name')->toArray()),
                'sub_time'   =>time(),
                'sub_type'   =>1,
                'expire_time'=>time()+config('expire_time')*60,
                'sub_code'   =>$code,
            ];
            if(!SubscribeSupply::create($data)){
                throw new \Exception('预约失败');
            }
        }catch (\Exception $e){
            return $this->response->error($e->getMessage(),$this->forbidden_code);
        }
        return $this->successResponse('预约成功,请在'.config('expire_time').'分钟内取卡');
    }

    /**
     * 供货商预约供货
     * @param Request $request
     * @return mixed
     */
    public function supplier(Request $request){
        $messages=[
            'driver_name.required'=>'司机姓名不能为空',
            'car_number.required'=>'车牌不能为空',
            'mobile.required'=>'手机号不能为空',
            'mobile.unique'=>'此用户已经预约',
            'mobile.is_mobile'=>'手机格式不正确',
            'bank_address.required'=>'银行卡开户行',
            'bank_code.required'=>'银行卡卡号',
            'goods_name.required'=>'请至少选择一个供货货品',
        ];
        $validator=Validator::make($request->all(),[
            'driver_name'=>'required',
            'car_number' =>'required',
            'bank_address' =>'required',
            'bank_code' =>'required',
            'goods_name'=>'required',
            'mobile'     =>['required','is_mobile',Rule::unique('subscribe_supply')->where(function($query)use($request){
                $query->where(['driver_name'=>$request->input('driver_name'),'sub_type'=>2,'status'=>0]);
            })
            ],

        ],$messages);
        if($validator->fails()){
            return $this->response->error($validator->errors()->first(),$this->forbidden_code);
        }
        return $this->successResponse('预约成功,请在'.config('expire_time').'分钟内取卡');
    }
}