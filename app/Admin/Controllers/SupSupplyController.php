<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/8/29
 * Time: 2:59 PM
 */
namespace App\Admin\Controllers;
use App\Admin\Filters\TimestampBetween;
use App\Model\SupSupply;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\MessageBag;

class SupSupplyController extends AdminController{
    /**
     * 供货商供货记录
     * @param Content $content
     * @return Content
     */
    public function index(Content $content){
        return $content
            ->header('供货商供货记录')
            ->description(trans('admin.list'))
            ->breadcrumb(['text'=>'供货商供货记录'])
            ->body($this->grid());
    }
    /**
     * 查看供货记录
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id,Content $content){
        $info=SupSupply::find($id);
        return $content->header('查看供货记录')
            ->description('查看')
            ->breadcrumb(['text'=>'查看供货记录'])
            ->body(Admin::show($info,function($show)use ($info){
                $show->panel()
                     ->tools(function ($tools) {
                            $tools->disableEdit();
                     });
                $show->order_sn('磅单号：');
                $show->supplier('供货商姓名：')->as(function ($supplier){
                    return $supplier->shipper_name;
                });
                $show->car_number('车牌号：');
                $show->driver_name('司机姓名：');
                $show->mobile('手机号：');
                $show->goods_level('级别：');
                $show->goods_name('货品名称：');
                $show->direction('运输方向：');
                $show->weight('货物总重：');
                $show->price('单价：');
                $show->Total('总额：');
                $show->pct_other('扣杂：');
                $show->pct_water('扣水：');
                $show->add_time('供货时间：');
                if($info->sub_imgs){
                    $show->field('sub_imgs')->carousel();
                }
            }));
    }
    public function edit($id,Content $content){
        return $content
            ->header('上传供货图片')
            ->description(trans('上传'))
            ->breadcrumb(['text'=>'上传供货图片'])
            ->body($this->form()->edit($id));
    }
    protected function grid(){
        $grid=new Grid(new SupSupply());
        $grid->column('id','ID')->sortable();
        $grid->column('order_sn','磅单号')->copyable();
        $grid->column('supplier.shipper_name','供货商姓名');
        $grid->column('car_number','车牌号');
        $grid->column('driver_name','司机姓名');
        $grid->column('mobile','手机号');
        $grid->column('goods_level','级别');
        $grid->column('goods_name','货品名称');
        $grid->column('Total','总额');
        $grid->column('add_time','供货时间')->sortable();
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            $actions->disableView(false);
        });
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->like('shipper_name','货主名称');
            $filter->like('driver_name','司机姓名');
            $filter->equal('mobile','手机号')->mobile();
            $filter->like('car_number','车牌号');
            $filter->like('goods_name','货品名称');
            $filter->use(new TimestampBetween('add_time','供货时间'))->date();
        });
        return $grid;
    }
    protected function form(){
        $form=new Form(new SupSupply());
        $form->multipleImage('sub_imgs','供货图片')->uniqueName()->removable()
             ->rules('mimes:jpeg,bmp,png')->help('图片大小不能超过800k');
        $form->saving(function(Form $form){
            if(request()->file('sub_imgs')){
                foreach (request()->file('sub_imgs') as $file){
                    if($file->getSize()>="819200"){
                        $message=[
                            'title'=>'错误',
                            'message'=>'供货图片大小不能超过800K'
                        ];
                        $error=new MessageBag($message);
                        return back()->with(compact('error'));
                    }
                }
            }
        });
        return $form;
    }
}