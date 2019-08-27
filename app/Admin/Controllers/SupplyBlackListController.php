<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/8/2
 * Time: 10:27 AM
 */
namespace App\Admin\Controllers;
use App\Model\SupplyBlacklist;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\MessageBag;

class SupplyBlackListController extends  AdminController{
    /**
     * 预约黑名单
     * @param Content $content
     * @return Content
     */
    public function index(Content $content){
        return $content
            ->header('预约黑名单')
            ->description(trans('admin.list'))
            ->breadcrumb(['text'=>'预约黑名单'])
            ->body($this->grid());
    }
    /**
     * 新增黑名单
     * @param Content $content
     * @return Content
     */
    public function create(Content $content){
        return $content
            ->header('新增黑名单')
            ->description('新增')
            ->breadcrumb(['text'=>'预约黑名单'])
            ->body($this->form());

    }
    protected function grid(){
        $grid=new Grid(new SupplyBlacklist());
        $grid->column('id','ID')->sortable();
        $grid->column('driver_name','司机姓名');
        $grid->column('mobile','司机手机号');
        $grid->column('card_id','身份证号');
        $grid->column('add_time','添加时间')->sortable();
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
                      // 在这里添加字段过滤器
            $filter->like('driver_name', '司机姓名')->placeholder('请输入司机姓名查询');
            $filter->like('mobile','手机号')->placeholder('请输入司机手机号查询');

        });
        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableEdit();

        });
        return $grid;
    }
    protected function form(){
        $form=new Form(new SupplyBlacklist());
        $form->text('driver_name','司机姓名')->required();
        $form->mobile('mobile','手机号')->options(['mask' => '999 9999 9999'])->required();
        $form->text('card_id','身份证号')->required();
        $form->saving(function(Form $form){
            $form->model()->add_time=time();
            if(SupplyBlacklist::where('card_id',$form->card_id)->count()){
                $error = new MessageBag([
                    'title'   => '错误',
                    'message' => '此司机已经存在',
                ]);
                return back()->with(compact('error'));
            }
        });
        return $form;
    }
}