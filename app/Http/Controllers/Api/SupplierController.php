<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/8/1
 * Time: 5:56 PM
 */
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

class SupplierController extends Controller{
    /**
     * 个人中心
     * @return mixed
     */
    public function  my(){
        $data=$this->user;
        if($data){
            $data['is_bind']=$this->user->routine_openid?true:false;
        }
        return $this->successResponse($data);
    }
}