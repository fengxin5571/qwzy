<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/6/17
 * Time: 5:19 PM
 */
namespace App\Admin\Controllers;
use App\Model\CarDiscern;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class CarDiscernController extends AdminController{
    /**
     * 车牌地区列表
     * @param Content $content
     * @return Content
     */
    public function index(Content $content){
        return $content->header('车牌地区识别')
               ->breadcrumb(['text'=>'车牌地区识别'])
               ->description('列表')
               ->body($this->grid());
    }

    /**
     * 新增地区识别
     * @param Content $content
     * @return Content
     */
    public function create(Content $content){
        return $content->header('新增地区识别')
                ->breadcrumb(['text'=>'新增地区识别'])
                ->description('新增')
                ->body($this->form());
    }
    public function edit($id,Content $content){
        return $content->header('编辑地区识别')
               ->breadcrumb(['text'=>'编辑地区识别'])
               ->description('编辑')
               ->body($this->form()->edit($id));
    }
    protected function grid(){
        $grid=new Grid(new CarDiscern);
        $grid->actions(function ($actions) {
            $actions->disableView();
        });
        $grid->column('id','ID')->sortable();
        $grid->column('car_region','地区代号')->display(function ($car_region){
            return "<span class='label label-info'>{$car_region}</span>";
        });
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
        });
        $grid->disableColumnSelector();
        $grid->disableExport();
        $grid->paginate(20);
        return $grid;
    }
    protected function form(){
        $form=new Form(new CarDiscern);
        $form->text('car_region','地区代号')->rules(['required']);
        $form->tools(function (Form\Tools $tools) {
            // 去掉`查看`按钮
            $tools->disableView();
        });
        $form->footer(function ($footer) {
            // 去掉`查看`checkbox
            $footer->disableViewCheck();
            // 去掉`继续编辑`checkbox
            $footer->disableEditingCheck();
            // 去掉`继续创建`checkbox
            $footer->disableCreatingCheck();
        });
        return $form;
    }
}