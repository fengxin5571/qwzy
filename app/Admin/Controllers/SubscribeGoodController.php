<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/6/17
 * Time: 6:29 PM
 */
namespace App\Admin\Controllers;
use App\Model\SubscribeGoods;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class SubscribeGoodController extends AdminController{
    /**
     * 预约货品管理
     * @param Content $content
     * @return Content
     */
    public function index(Content $content){
        return $content->header('预约货品')
            ->description(trans('admin.list'))
            ->breadcrumb(['text'=>'预约货品'])
            ->body($this->grid());
    }

    /**
     * 新增预约货品
     * @param Content $content
     * @return Content
     */
    public function create(Content $content){
        return $content->header('新增预约货品')
            ->description(trans('新增'))
            ->breadcrumb(['text'=>'新增预约货品'])
            ->body($this->form());
    }

    /**
     * 编辑预约货品
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id,Content $content){
        return $content->header('编辑预约货品')
            ->description(trans('编辑'))
            ->breadcrumb(['text'=>'编辑预约货品'])
            ->body($this->form()->edit($id));
    }
    protected function grid(){
        $grid=new Grid(new SubscribeGoods);
        $grid->column('id',"ID")->sortable();
        $grid->column('goods_name','货品名称')->editable();
        $grid->column('add_time','添加时间')->sortable();
        $grid->column('is_temp','临时预约')->radio([
            0 => '关闭',
            1 => '打开',
        ]);
        $grid->column('is_sup','供货商预约')->radio([
            0 => '关闭',
            1 => '打开',
        ]);
        $grid->actions(function ($actions) {
            $actions->disableView();
        });
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->like('goods_name','货品名称');
            $filter->equal('is_temp','临时预约')->radio([
                ''   => '所有',
                0    => '关闭',
                1    => '打开',
            ]);
            $filter->equal('is_sup','供货商预约')->radio([
                ''   => '所有',
                0    => '关闭',
                1    => '打开',
            ]);
        });
        $grid->disableColumnSelector();
        $grid->disableExport();
        $grid->paginate(20);
        return $grid;
    }
    protected function form(){
        $form=new Form(new SubscribeGoods);
        $form->text('goods_name','货品名称')->rules(['required']);
        $form->radio('is_temp','临时预约')->options(['0'=>'关闭','1'=>'开启']);
        $form->radio('is_sup','供货商预约')->options(['0'=>'关闭','1'=>'开启']);
        $form->saving(function (Form $form){
            $form->model()->add_time=time();
        });
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