<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/6/18
 * Time: 9:26 AM
 */
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Model\AxleNumber;
use App\Model\CarBlacklist;
use App\Model\CarDiscern;
use App\Model\CarLetter;
use App\Model\SubscribeGoods;
use App\Model\SubscribeSupply;
use App\Model\Supplier;
use App\Model\SupplyBlacklist;
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
     * 获取车轴数
     * @return mixed
     */
    public function axle_number(){
        $data=AxleNumber::all(['axle_number']);
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
            'card_id.unique' =>'当前身份证已经预约',
            'goods_name.required'=>'请至少选择一个供货货品',
            'axle_number.required'=>'车轴数不能为空',
            'axle_number.numeric'=>'车轴数必须是数字',
            'load_weight.required'=>'荷载重量不能为空',
            'load_weight.numeric'=>'荷载重量必须是数字',
            'channel.required'=>'运输来源不能为空',
            'unit_name.required'=>'供货单位不能为空',
            'unit_transport.required'=>'运输单位不能为空',
            'paper_number.required'=>'废纸件数不能为空',
            'paper_number.numeric'=>'废纸件数必须为数字',
        ];
        $validator=Validator::make($request->all(),[
            'driver_name'=>'required',
            'car_number' =>['required',function($attribute, $value, $fail) use($request){
                if(CarBlacklist::where('car_number',$request->input('car_number'))->count()){
                    $fail('当前车辆涉嫌违规操作，已在黑名单中，请联系管理员');
                    return;
                }
            }],
            'goods_name'=>'required',
            'mobile'     =>['required','is_mobile',Rule::unique('subscribe_supply')->where(function($query)use($request){
                $query->where(['driver_name'=>$request->input('driver_name'),'sub_type'=>1,'status'=>0]);
            })
            ],
            'card_id'=>['required','is_card',Rule::unique('subscribe_supply')->where(function($query){
                $query->where(['sub_type'=>1,'status'=>0]);
            }),
                function($attribute, $value, $fail)use($request){
                if(SupplyBlacklist::where('card_id',$request->input('card_id'))->count()){
                    $fail('当前用户涉嫌超时过磅，已在黑名单中，请联系管理员');
                    return;
                }
            }],
            'axle_number' =>'required|numeric',
            'load_weight' =>'required|numeric',
            'channel'     =>'required',
            'unit_name'   =>'required',
            'unit_transport'=>'required',
            'paper_number'  =>'required|numeric'
        ],$messages);
        if($validator->fails()){
            return $this->response->error($validator->errors()->first(),$this->forbidden_code);
        }
        try{
            if($request->input('delivery_weight')&&!is_numeric($request->input('delivery_weight',0.00))){
                return $this->response->error('送货重量必须是数字',$this->forbidden_code);
            }
            $code=$this->makeRandCode();
            $data=[
                'driver_name'=>$request->input('driver_name'),
                'car_number' =>strtoupper($request->input('car_number')),
                'mobile'     =>$request->input('mobile'),
                'goods_name' =>implode(',',SubscribeGoods::whereIn('id',explode(',',$request->input('goods_name')))->pluck('goods_name')->toArray()),
                'sub_time'   =>time(),
                'sub_type'   =>1,
                'expire_time'=>time()+config('expire_time')*60*60,
                'sub_code'   =>$code,
                'card_id'    =>$request->input('card_id'),
                'axle_number' =>$request->input('axle_number'),
                'load_weight' =>$request->input('load_weight'),
                'channel'     =>$request->input('channel'),
                'unit_name'   =>$request->input('unit_name'),
                'unit_transport'=>$request->input('unit_transport'),
                'paper_number'  =>$request->input('paper_number'),
                'delivery_weight'=>$request->input('delivery_weight',0.00),
            ];
            if(!$supply=SubscribeSupply::create($data)){
                throw new \Exception('预约失败');
            }
        }catch (\Exception $e){
            return $this->response->error($e->getMessage(),$this->forbidden_code);
        }
        return $this->successResponse('','预约成功,请在'.config('expire_time').'小时内取卡,取卡码：'.$code);
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
            'card_id.required'=>'身份证号不能为空',
            'card_id.is_card'=>'身份证号格式不正确',
            'card_id.unique' =>'当前身份证已经预约',
            'goods_name.required'=>'请至少选择一个供货货品',
            'axle_number.required'=>'车轴数不能为空',
            'axle_number.numeric'=>'车轴数必须是数字',
            'load_weight.required'=>'载重量不能为空',
            'load_weight.numeric'=>'载重量必须是数字',
            'channel.required'=>'运输来源不能为空',
            'unit_name.required'=>'供货单位不能为空',
            'unit_transport.required'=>'运输单位不能为空',
            'paper_number.required'=>'废纸件数不能为空',
            'paper_number.numeric'=>'废纸件数必须为数字',
        ];
        $validator=Validator::make($request->all(),[
            'driver_name'=>'required',
            'car_number' =>['required',function($attribute, $value, $fail) use($request){
                if(CarBlacklist::where('car_number',$request->input('car_number'))->count()){
                    $fail('当前车辆涉嫌违规操作，已在黑名单中，请联系管理员');
                    return;
                }
            }],
            'goods_name'=>'required',
            'mobile'     =>['required','is_mobile',Rule::unique('subscribe_supply')->where(function($query)use($request){
                $query->where(['driver_name'=>$request->input('driver_name'),'sub_type'=>2,'status'=>0]);
            })
            ],
            'card_id'=>['required','is_card',Rule::unique('subscribe_supply')->where(function($query){
                $query->where(['sub_type'=>2,'status'=>0]);
            }),
                function($attribute, $value, $fail)use($request){
                if(SupplyBlacklist::where('card_id',$request->input('card_id'))->count()){
                    $fail('当前用户涉嫌超时过磅，已在黑名单中，请联系管理员');
                    return;
                }
            }],
            'axle_number' =>'required|numeric',
            'load_weight' =>'required|numeric',
            'channel'     =>'required',
            'unit_name'   =>'required',
            'unit_transport'=>'required',
            'paper_number'  =>'required|numeric'

        ],$messages);
        if($validator->fails()){
            return $this->response->error($validator->errors()->first(),$this->forbidden_code);
        }
        try{

            if($request->input('delivery_weight')&&!is_numeric($request->input('delivery_weight',0.00))){
                return $this->response->error('送货重量必须是数字',$this->forbidden_code);
            }
            $code=$this->makeRandCode();
            $supplier=$this->user;
            if(!$supplier){
                throw new \Exception('预约失败');
            }
            $data=[
                'driver_name'=>$request->input('driver_name'),
                'shipper_name'=>$supplier->shipper_name,
                'car_number' =>strtoupper($request->input('car_number')),
                'mobile'     =>$request->input('mobile'),
                'goods_name' =>implode(',',SubscribeGoods::whereIn('id',explode(',',$request->input('goods_name')))->pluck('goods_name')->toArray()),
                'sub_time'   =>time(),
                'sub_type'   =>2,
                'expire_time'=>time()+config('expire_time')*60*60,
                'sub_code'   =>$code,
                'supplier_id'=>$supplier->id,
                'bank_address'=>$supplier->bank_address,
                'bank_code'  =>$supplier->bank_code,
                'card_id'    =>$request->input('card_id'),
                'axle_number' =>$request->input('axle_number'),
                'load_weight' =>$request->input('load_weight'),
                'channel'     =>$request->input('channel'),
                'unit_name'   =>$request->input('unit_name'),
                'unit_transport'=>$request->input('unit_transport'),
                'paper_number'  =>$request->input('paper_number'),
                'delivery_weight'=>$request->input('delivery_weight',0.00),
            ];
            if(!$supply=SubscribeSupply::create($data)){
                throw new \Exception('预约失败');
            }
        }catch (\Exception $e){
            return $this->response->error($e->getMessage(),$this->forbidden_code);
        }
        return $this->successResponse("",'预约成功,请在'.config('expire_time').'小时内取卡,取卡码：'.$code);
    }
    //随机生成短信验证码
    protected function makeRandCode()
    {
        // 生成4位随机数，左侧补0
        return random_int(1000,9999);
    }
}