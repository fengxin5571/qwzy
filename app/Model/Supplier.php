<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 13 Jun 2019 03:48:06 +0000.
 */

namespace App\Model;

use Illuminate\Notifications\Notifiable;
use Reliese\Database\Eloquent\Model as Eloquent;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;
/**
 * Class Supplier
 * 
 * @property int $id
 * @property string $mobile
 * @property string $shipper_name
 * @property string $driver_name
 * @property string $bank_address
 * @property string $bank_code
 * @property int $add_time
 * @property int $status
 * @property int $pay_status
 * @property string $routine_openid
 * @property string $nickname
 * @property string $headimgurl
 *
 * @package App\Models
 */
class Supplier extends Authenticatable implements JWTSubject
{
    use Notifiable;
	protected $table = 'supplier';
	public $timestamps = false;
    public static $statusValue=[
        '0'=>'未审核',
        '1'=>'正常',
        '2'=>'不可用'
    ];
	protected $casts = [
		'add_time' => 'date:Y-m-d H:i:s',
		'status' => 'int',
		'pay_status' => 'int',
        'expire_time'=>'int',
	];
    //protected $hidden = ['show_pass'];
	protected $fillable = [
		'mobile',
		'shipper_name',
		'bank_address',
		'bank_code',
		'add_time',
		'status',
		'pay_status',
		'routine_openid',
		'nickname',
		'headimgurl',
        'expire_time',
        'pay_time',
        'pay_item',
        'show_pass',
        'pay_amount',
        'old_token',
        'password',
        'is_notice',
        'is_supply'
	];
    //protected $hidden=['password'];
	public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * 检查是否已绑定openId
     * @param $routine
     */
    public function routineOauth($routine){
        $data=[
            'error'=>0,
            'message'=>'success',
            'info'=>[],
        ];
        $routineInfo['routine_openid'] = $routine['routine_openid'];//openid
        //根据小程序openid判断
        if(!$supplier=$this->where(['routine_openid'=>$routineInfo['routine_openid'],'status'=>1])->first()){
           $data['error']=1;
           $data['message']='该微信没有绑定相关账号或停用,请到个人中心绑定微信';
        }else{
            $data['info']=$supplier;
        }
        return $data;
    }
}
