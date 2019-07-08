<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/6/18
 * Time: 9:05 AM
 */
namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;

class ArticleController extends Controller {
    public function __construct()
    {
        $this->middleware(function ($request, $next){
            $supllier=auth('api')->user();
            if(empty($supllier)){
                return $this->response->error('token过期或不正确',401);
            }
            return $next($request);
        });
    }
    public function index(){

    }
    public function category(){

    }
}