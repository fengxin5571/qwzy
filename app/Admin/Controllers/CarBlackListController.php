<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/8/26
 * Time: 3:05 PM
 */
namespace App\Admin\Controllers;
use App\Model\CarBlacklist;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\MessageBag;

class CarBlackListController extends AdminController{
    /**
     * 车牌黑名单
     * @param Content $content
     * @return Content
     */
    public function index(Content $content){
        return $content
            ->header('车牌黑名单')
            ->description(trans('admin.list'))
            ->breadcrumb(['text'=>'车牌黑名单'])
            ->body($this->grid());
    }
    public function create(Content $content){
        return $content
            ->header('新增黑名单')
            ->description('新增')
            ->breadcrumb(['text'=>'车牌黑名单'])
            ->body($this->form());
    }
    protected function grid(){
        $grid=new Grid(new CarBlacklist());
        //$grid->disableCreateButton();
        $grid->column('id','ID')->sortable();
        $grid->column('car_number','车牌');
        $grid->column('add_time','添加时间')->sortable();
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->like('car_number', '车牌')->placeholder('请输入车牌查询');

        });
        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableEdit();

        });
        return $grid;
    }
    protected function form(){
        $form=new Form(new CarBlacklist());
        $form->text('car_number','车牌')->required();
        $form->saving(function(Form $form){
            $form->model()->add_time=time();
            if(CarBlacklist::where('car_number',$form->car_number)->count()){
                $error = new MessageBag([
                    'title'   => '错误',
                    'message' => '此车牌已经存在',
                ]);
                return back()->with(compact('error'));
            }
        });
        return $form;
    }
}