<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/6/18
 * Time: 9:05 AM
 */
namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Jobs\AutoHandleBlackList;
use App\Model\ArticleCategory;
use App\Model\ArticleTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller {
    public function index(){

    }
    /**
     * 文章分类
     * @return mixed
     */
    public function category(){
        $data=ArticleCategory::get(['id','title']);
        return $this->successResponse($data);
    }

    /**
     * 文章标签
     * @param Request $request
     * @return mixed
     */
    public function tag(Request $request){
       $message=[
           'cid.required'=>'分类id不能为空'
       ];
        $validator=Validator::make($request->all(),[
            'cid'=>'required'
        ],$message);
        if($validator->fails()){
            return $this->response->error($validator->errors()->first(),$this->forbidden_code);
        }
        $data=ArticleTag::where('cid',$request->input('cid'))->get(['id','tag_name']);
        return $this->successResponse($data);
    }
}