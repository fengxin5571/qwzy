<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/6/13
 * Time: 12:02 PM
 */
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Model\Supplier;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller{
    protected $app;
    public function __construct()
    {
        $config=[
            'app_id' => config('appid'),
            'secret' => config('appsecret'),
        ];
        $this->app=Factory::miniProgram($config);
    }

    /**
     * 供应商注册
     * @param Request $request
     * @param Supplier $supplier
     */
    public function register(Request $request,Supplier $supplier){
        $message=array(
            'shipper_name.required'=>'货主姓名不能为空',
            'mobile.required'=>'手机号不能为空',
            'mobile.is_mobile'=>'请输入正确的手机号码',
            'mobile.unique'=>'此用户已经注册',
            'bank_address.required'=>'请输入银行卡开户行',
            'bank_code.required'=>'请输入银行卡号',
        );
        $validator=Validator::make($request->all(),[
            'shipper_name'=>'required',
            'mobile'=>['required','is_mobile',
                Rule::unique('supplier')->where(function($query) use ($request){
                    $query->where(['shipper_name'=>$request->input('shipper_name')]);
                })
            ],
            'bank_address'=>'required',
            'bank_code'=>'required',
        ],$message);
        if($validator->fails()){
            return $this->response->error($validator->errors()->first(),$this->forbidden_code);
        }
        try{
            $insert_array=$request->all();
            $insert_array['add_time']=time();
            if($supplier->create($insert_array)) {
                return $this->successResponse('','注册成功，我们将在3个工作日内审核通过');
            }else{
                throw new \Exception('注册失败');
            }

        }catch (\Exception $e){
           return $this->response->error('注册失败',$this->forbidden_code);
        }
    }

    /**
     * 供应商普通登录
     * @param Request $request
     */
    public function login(Request $request){
        $message=[
            'shipper_name.required'=>'货主姓名不能为空',
            'mobile.required'=>'手机号不能为空'
        ];
        $validator=Validator::make($request->all(),[
            'shipper_name'=>'required',
            'mobile'=>'required'
        ],$message);
        if($validator->fails()){
            return $this->response->error($validator->errors()->first(),$this->forbidden_code);
        }
        $credentials=$request->only('shipper_name','mobile');
        $supplier=Supplier::where(['shipper_name'=>$credentials['shipper_name'],'mobile'=>$credentials['mobile'],'status'=>1])->first();
        if(!$supplier) return $this->response->error('登录失败，请确认账号是否正确',$this->forbidden_code);
        if(!$token=auth('api')->login($supplier)){
            return $this->response->error('登录失败，请确认账号是否正确',$this->forbidden_code);
        }
        $data['token']=$token;
        return $this->successResponse($data,'登录成功');
    }

    /**
     * 供应商微信快捷的登录
     * @param Request $request
     * @return mixed
     */
    public function routeLogin(Request $request,Supplier $supplier){
        $message=[
            'code.required'=>'code不能为空',
        ];
        $validator=Validator::make($request->all(),[
            'code'=>'required'
        ],$message);
        if($validator->fails()){
            return $this->response->error($validator->errors()->first(),$this->forbidden_code);
        }
        $res=$this->app->auth->session($request->input('code'));
        if(!isset($res['openid'])){
            return $this->response->error('openid获取失败',$this->forbidden_code);
        }
        $data['routine_openid']=$res['openid'];
//        $data['routine_openid']='orc0L0oJ8CTGLxqt6r07R3htqAAs';
        $item=$supplier->routineOauth($data);
        if($item['error']||empty($item['info'])){
            return $this->response->error($item['message'],$this->forbidden_code);
        }
        if(!$token=auth('api')->login($item['info'])){
            return $this->response->error('登录失败，请确认账号是否正确',$this->forbidden_code);
        }
        $result['token']=$token;
        return $this->successResponse($result,'登录成功');
    }

    /**
     * 获取openid
     * @param Request $request
     * @return mixed
     */
    public function getOpenId(Request $request){
        $code=$request->input('code');
        if(empty($code)){
            return $this->response->error('code不能为空',$this->forbidden_code);
        }
        $res=$this->app->auth->session($code);
        if(!isset($res['openid'])){
             return $this->response->error('openid获取失败',$this->forbidden_code);
        }
        $data['routine_openid']=$res['openid'];
        return $this->successResponse($data);
    }
    /**
     * 供应商配置
     * @return mixed
     */
    public function setting(){
        $data['register_pay']=config('register_pay');
        return $this->successResponse($data);
    }

}