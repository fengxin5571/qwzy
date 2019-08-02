<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/6/18
 * Time: 9:26 AM
 */
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Model\CarDiscern;
use App\Model\CarLetter;
use App\Model\SubscribeGoods;
use App\Model\SubscribeSupply;
use App\Model\Supplier;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
class SubscribeController extends Controller{
    /**
     * 车牌地区识别
     * @param Request $request
     */
    public function carDiscern(Request $request){
        $data=CarDiscern::where('status',1)->orderBy('sort')->get(['car_region']);
        return $this->successResponse($data);
    }

    /**
     * 车牌字母
     * @return mixed
     */
    public function carLetter(){
        $data=CarLetter::all(['car_letter']);
        return $this->successResponse($data);
    }
    //获取供货货品
    public function goods(Request $request){
        $type=$request->input('type',1);
        $data=SubscribeGoods::getTypeGoods($type,['id','goods_name']);
        return $this->successResponse($data);
    }
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
            'card_id.required'=>'身份证号不能为空',
            'card_id.is_card'=>'身份证号格式不正确',
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
            'card_id'=>'required|is_card',
        ],$messages);
        if($validator->fails()){
            return $this->response->error($validator->errors()->first(),$this->forbidden_code);
        }
        try{
            $code=$this->makeRandCode();
            $data=[
                'driver_name'=>$request->input('driver_name'),
                'car_number' =>$request->input('car_number'),
                'mobile'     =>$request->input('mobile'),
                'goods_name' =>implode(',',SubscribeGoods::whereIn('id',explode(',',$request->input('goods_name')))->pluck('goods_name')->toArray()),
                'sub_time'   =>time(),
                'sub_type'   =>1,
                'expire_time'=>time()+config('expire_time')*60*60,
                'sub_code'   =>$code,
                'card_id'    =>$request->input('card_id'),
            ];
            if(!$supply=SubscribeSupply::create($data)){
                throw new \Exception('预约失败');
            }
        }catch (\Exception $e){
            return $this->response->error($e->getMessage(),$this->forbidden_code);
        }
        return $this->successResponse('','预约成功,请在'.config('expire_time').'小时内取卡');
    }

    /**
     * 供货商预约供货
     * @param Request $request
     * @return mixed
     */
    public function supplier(Request $request){
        $messages=[
//            'driver_name.required'=>'司机姓名不能为空',
            'car_number.required'=>'车牌不能为空',
//            'mobile.required'=>'手机号不能为空',
//            'mobile.unique'=>'此用户已经预约',
//            'mobile.is_mobile'=>'手机格式不正确',
//            'bank_address.required'=>'银行卡开户行',
//            'bank_code.required'=>'银行卡卡号',
            'goods_name.required'=>'请至少选择一个供货货品',
            'sup_id.required'=>'供货商id不能为空',
        ];
        $validator=Validator::make($request->all(),[
//            'driver_name'=>'required',
            'car_number' =>'required',
//            'bank_address' =>'required',
//            'bank_code' =>'required',
            'goods_name'=>'required',
//            'mobile'     =>['required','is_mobile',Rule::unique('subscribe_supply')->where(function($query)use($request){
//                $query->where(['driver_name'=>$request->input('driver_name'),'sub_type'=>2,'status'=>0]);
//            })
//            ],
            'sup_id'=>'required'

        ],$messages);
        if($validator->fails()){
            return $this->response->error($validator->errors()->first(),$this->forbidden_code);
        }
        try{
            $code=$this->makeRandCode();
            $supplier=Supplier::where(['status'=>1,'id'=>$request->input('sup_id')])->first();
            if(!$supplier){
                throw new \Exception('预约失败');
            }
            $data=[
                'driver_name'=>$supplier->driver_name,
                'shipper_name'=>$supplier->shipper_name,
                'car_number' =>$request->input('car_number'),
                'mobile'     =>$supplier->mobile,
                'goods_name' =>implode(',',SubscribeGoods::whereIn('id',explode(',',$request->input('goods_name')))->pluck('goods_name')->toArray()),
                'sub_time'   =>time(),
                'sub_type'   =>2,
                'expire_time'=>time()+config('expire_time')*60*60,
                'sub_code'   =>$code,
                'supplier_id'=>$supplier->id,
                'bank_address'=>$supplier->bank_address,
                'bank_code'  =>$supplier->bank_code,
                'card_id'    =>$supplier->card_id,
            ];
            if(!$supply=SubscribeSupply::create($data)){
                throw new \Exception('预约失败');
            }
        }catch (\Exception $e){
            return $this->response->error($e->getMessage(),$this->forbidden_code);
        }
        return $this->successResponse("",'预约成功,请在'.config('expire_time').'小时内取卡');
    }
    //随机生成短信验证码
    protected function makeRandCode()
    {
        // 生成4位随机数，左侧补0
        return random_int(1000,9999);
    }
}