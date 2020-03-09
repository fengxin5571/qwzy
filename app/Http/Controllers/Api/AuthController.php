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
use App\Model\TruckQueue;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Manager;
use Tymon\JWTAuth\Token;

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
            'bank_code.is_bank'=>'您输入的银行卡号不正确'
        );
        $validator=Validator::make($request->all(),[
            'shipper_name'=>'required',
            'mobile'=>['required','is_mobile',
                Rule::unique('supplier')->where(function($query) use ($request){
                   // $query->where(['shipper_name'=>$request->input('shipper_name')]);
                })
            ],
            'bank_address'=>'required',
            'bank_code'=>'required|is_bank',
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
     * 验证是否登录
     * @param Request $request
     * @return mixed
     */
    public function chekc_login(Request $request){
        $credentials=$request->only('shipper_name','mobile');
        if(!isset($credentials['shipper_name']))$credentials['shipper_name']='';
        if(!isset($credentials['mobile']))$credentials['mobile']='';
        $supplier=Supplier::where(['shipper_name'=>$credentials['shipper_name'],'mobile'=>$credentials['mobile'],'status'=>1])->first();
        $data['is_login']=false;
        if($supplier&&$supplier->old_token){
            $data['is_login']=true;
        }
        return $this->successResponse($data,'ok');
    }
    /**
     * 供应商普通登录
     * @param Request $request
     */
    public function login(Request $request){
        $message=[
            'shipper_name.required'=>'货主姓名不能为空',
            'mobile.required'=>'手机号不能为空',
            'password.required'=>'密码不能为空',
        ];
        $validator=Validator::make($request->all(),[
            'shipper_name'=>'required',
            'mobile'=>'required',
            //'password'=>'required|min:6|max:14'
        ],$message);
        if($validator->fails()){
            return $this->response->error($validator->errors()->first(),$this->unauth_code);
        }
        $credentials=$request->only('shipper_name','mobile','password');
        $credentials['password']='123456';
        $supplier=Supplier::where(['shipper_name'=>$credentials['shipper_name'],'mobile'=>$credentials['mobile'],'status'=>1])->first();
        if(!$supplier) return $this->response->error('登录失败，请确认账号是否正确',$this->unauth_code);
        if(!Hash::check($credentials['password'],$supplier->password)) return $this->response->error('密码错误',$this->unauth_code);
        if($supplier->old_token){
            $old_tokenn=new Token($supplier->old_token);
            \JWTAuth::invalidate(false,$old_tokenn);
        }
        if(!$token=auth('api')->login($supplier)){
            return $this->response->error('登录失败，请确认账号是否正确',$this->unauth_code);
        }
        $data['token']=$token;
        $supplier->old_token=$token;
        $supplier->save();
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
            return $this->response->error($validator->errors()->first(),$this->unauth_code);
        }
        $res=$this->app->auth->session($request->input('code'));
        if(!isset($res['openid'])){
            return $this->response->error('openid获取失败',$this->unauth_code);
        }
        $data['routine_openid']=$res['openid'];
//        $data['routine_openid']='orc0L0oJ8CTGLxqt6r07R3htqAAs';
        $item=$supplier->routineOauth($data);
        if($item['error']||empty($item['info'])){
            return $this->response->error($item['message'],$this->unauth_code);
        }
        if(!$token=auth('api')->login($item['info'])){
            return $this->response->error('登录失败，请确认账号是否正确',$this->unauth_code);
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

    /**
     * 获取小程序码
     * @return mixed
     */
    public function getCode(){
        try{
            if(Storage::disk('public')->exists('code/qwzycode.png')){
                $data['codeImg']=Storage::disk('public')->url('code/qwzycode.png');
            }else{
                $response=$this->app->app_code->get('pages/index/index');
                $filename=$response->saveAs(storage_path('app/public').'/code','qwzycode.png');
                $data['codeImg']=Storage::disk('public')->url('code/'.$filename);
            }

        }catch (\Exception $e){
            return $this->response->error($e->getMessage(),$this->forbidden_code);
        }
        return $this->successResponse($data);
    }

    /**
     * 获取排队小程序码
     * @return mixed
     */
    public function getQueueCode(){
        try{
            if(Storage::disk('public')->exists('code/queuecode.png')){
                $data['codeImg']=Storage::disk('public')->url('code/queuecode.png');
            }else{
                $response=$this->app->app_code->get('pages/carsQueue/index');
                $filename=$response->saveAs(storage_path('app/public').'/code','queuecode.png');
                $data['codeImg']=Storage::disk('public')->url('code/'.$filename);
            }

        }catch (\Exception $e){
            return $this->response->error($e->getMessage(),$this->forbidden_code);
        }
        return $this->successResponse($data);
    }

    /**
     * 获取排队信息
     * @return mixed
     */
    public function getQueueMessage(){
        $queue=TruckQueue::all();
        $wait=$weight=$weighing=0;
        $queue->each(function ($item,$key) use(&$wait,&$weight,&$weighing) {
            if($item->status==1){
                $wait=$wait+1;
            }elseif ($item->status==2){
                $weight=$weight+1;
            }elseif($item->status==3){
                $weighing=$weighing+1;
            }

        });
        $data['message']="当前有{$wait}辆车排队，{$weight}辆车等待上磅，{$weighing}辆车正在过磅。";
        return $this->successResponse($data);
    }
}