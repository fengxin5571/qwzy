<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/6/14
 * Time: 7:44 AM
 */
namespace App\Admin\Controllers;
use App\Admin\Extensions\SupplierButton;
use App\Admin\Extensions\SupplierButton1;
use App\Exports\SuppliersExports;
use App\Model\Supplier;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Show;
use Encore\Admin\Facades\Admin;
use Illuminate\Support\Facades\Hash;

class SupplierController extends AdminController {


    /**
     * 供货商列表
     * @param Content $content
     * @return Content
     */
    public function list(Content $content){
        return $content->header('供货商管理')
               ->description('列表')
               ->breadcrumb(['text'=>'供货商'])
               ->body($this->grid());
    }
    /**
     *
     * 审核通过
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($id){
        if(Supplier::where('id',$id)->value('status')!=1){
            $data['status']=1;
            $pay_time=Supplier::where('id',$id)->value('pay_time');
            if(config('register_pay')){//如果开启支付
                if(Supplier::where('id',$id)->value('pay_status')==1){//如果是已支付
                    switch (Supplier::where('id',$id)->value('pay_item')){
                        case 0://月
                            $data['expire_time']=strtotime("+1month",$pay_time);
                            break;
                        case 1://三月
                            $data['expire_time']=strtotime("+3month",$pay_time);
                            break;
                        case 2://年
                            $data['expire_time']=strtotime("+1year",$pay_time);
                            break;
                    }

                }else{
                    $data['pay_status']=1;
                    $data['expire_time']=strtotime('+1month');
                }

            }
            if(Supplier::where('id',$id)->update($data)){
                $data = [
                    'status'  => true,
                    'message' => trans('审核通过'),
                ];
                admin_toastr('审核通过', 'success',['timeOut'=>1000]);
            }
        }else{
            $data = [
                'status'  => true,
                'message' => trans('不可重复审核'),
            ];
            admin_toastr('不可重复审核', 'error',['timeOut'=>1000]);
        }

        return response()->json($data);
    }

    /**
     * 审核拒绝
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function refuse($id){
        $data['status']=2;
        if(config('register_pay')){
            $data['pay_status']=0;
            $data['expire_time']=null;
        }
        if(Supplier::where('id',$id)->update($data)){
            $data = [
                'status'  => true,
                'message' => trans('审核不通过'),
            ];
            admin_toastr('审核不通过', 'error',['timeOut'=>1000]);
        }else{
            $data = [
                'status'  => false,
                'message' => trans('审核失败'),
            ];
            admin_toastr('审核通过', 'error',['timeOut'=>1000]);
        }
        return response()->json($data);
    }
    /**
     * 查看供货商
     * @param $id
     * @param Content $content
     * @return Content
     */
    public function show($id,Content $content){
        $supplier=Supplier::findOrFail($id);
        return $content->header('查看')
               ->description('供货商')
               ->breadcrumb(['text'=>'查看供货商'])
               ->body(Admin::show($supplier,function (Show $show)use($supplier){
                   $show->panel()
                       ->style('danger')
                       ->title('供货商基本信息');
                   $show->id('ID');
                   $show->field('shipper_name','货主名称');
                   $show->field('mobile','手机号');
                   $show->field('bank_address','银行卡开户行');
                   $show->field('bank_code','银行卡号');
                   if($supplier->nickname){
                       $show->field('nickname','微信昵称');
                   }
                   if($supplier->headimgurl){
                       $show->field('headimgurl','微信头像')->image();
                   }
                   if(config('register_pay')){
                       $show->field('pay_status','付费状态')->using(['0'=>'未支付','1'=>'已支付']);
                       if($supplier->pay_status){
                           $show->field('expire_time','付费到期时间')->as(function($expire_time){
                               return date('Y-m-d ',$expire_time);
                           });
                           $show->field('pay_amount','付费金额');
                       }


                   }
                   $show->field('status','状态')->as(function ($status){
                       return Supplier::$statusValue[$status];
                   });
               }));
    }

    /**
     * 编辑供货商
     * @param $id
     * @param Content $content
     * @return Content
     */
    public function edit($id,Content $content){
        $supplier=Supplier::find($id);
        if($supplier->status==0){
            admin_toastr('当前供货商还未审核', 'error',['timeOut'=>1000]);
            return redirect()->back();
        }
        return $content
            ->header('编辑供货商')
            ->description('编辑')
            ->body($this->form()->edit($id));
    }
    protected function grid(){
        $grid=new Grid(new Supplier);
        $grid->id('ID')->width(50)->sortable();
        $grid->column('shipper_name','货主名称');
        $grid->column('mobile','手机号')->width(80);
        $grid->column('bank_address','开户行');
        $grid->column('bank_code','卡号');
        $grid->column('nickname','微信昵称')->display(function ($nickname){
            if($nickname){
                return $nickname;
            }else{
                return '暂未绑定微信';
            }
        });
        $grid->column('headimgurl','微信头像')->display(function ($headimgurl) {
            if($headimgurl){
                return $headimgurl;
            }else{
                return config('app.url').'/vendor/laravel-admin/timg.jpeg';
            }
        })->image('',50,50);
        $grid->column('add_time','注册时间');
        if(config('register_pay')){//是否开启注册支付
            $grid->column('expire_time','付费到期时间')->display(function ($expire_time){
                if(!$expire_time){
                    return '还未审核或账号不可用';
                }elseif($expire_time<time()){
                    return '已过期';
                }else{
                    return date('Y-m-d ',$expire_time);
                }
            });
            $grid->column('pay_status','支付状态')->using(['0'=>'未支付','1'=>'已支付']);
            $grid->column('pay_amount','支付金额');
        }
        $grid->column('status','状态')->using(['0'=>"<span class='label label-danger'>未审核</span>",'1'=>"<span class='label label-success'>正常</span>",
            '2'=>"<span class='label label-danger'>不可用</span>"]);
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->scope('status0', '未审核')->where('status', 0);
            $filter->scope('status1', '正常')->where('status', 1);
            $filter->scope('status2', '不可用')->where('status', 2);
            // 在这里添加字段过滤器
            $filter->like('shipper_name', '货主名称');
            $filter->like('mobile','手机号');
            $filter->equal('status','状态')->radio([
                ''   => ' 所有',
                0    => ' 未审核',
                1    => ' 正常',
                2    => ' 不可用',
            ]);
        });
        $grid->disableCreateButton();

        $grid->disableColumnSelector();
        $grid->actions(function ($actions) {
            if($actions->row->status==0){
                // append一个操作
                $actions->prepend(new SupplierButton1($actions->getKey()));
                // prepend一个操作
                $actions->prepend(new SupplierButton($actions->getKey()));
            }

        });
        $grid->disableExport(false);
        $grid->exporter(new SuppliersExports());
        $grid->paginate('15');
        return $grid;
    }
    public function form(){
        $form=new Form(new Supplier);
        $form->display('id', 'ID');
        $form->text('shipper_name', '货主名称')->rules('required');
        $form->mobile('mobile', '手机号')->rules('required|is_mobile');
        $form->text('bank_address', '银行卡开户行')->rules('required');
        $form->text('bank_code', '银行卡号')->rules('required');
        if(config('register_pay')){
            //$form->datetime('expire_time','付费到期时间')->setMinDate(date('Y/m/d',time()+24*3600))->format('YYYY-MM-DD');

        }
        $form->display('show_pass','显示密码')->with(function ($show_pass) {
            return  $show_pass;
        });
        $form->password('password', trans('admin.password'))->rules('required|min:6');
        $form->radio('status','状态')->options(['0' => '未审核', '1'=> '正常','2'=>'不可用']);
        $form->saving(function (Form $form){
            if(config('register_pay')){//是否开启注册支付
                if($form->status==0){//未审核
                    $form->model()->pay_status=0;
                    $form->model()->expire_time=null;
//                    $form->model()->pay_time=null;
                }elseif ($form->status==1){//正常
                    $form->model()->pay_status=1;
                    if($form->model()->expire_time){
                        $form->model()->expire_time=$form->model()->expire_time;
                    }else{
                        $form->model()->expire_time=strtotime('+1month');
                    }
                    //$form->expire_time=$form->expire_time?strtotime("$form->expire_time"):strtotime('+1month');
                }else{//不可用
                    $form->model()->pay_status=0;
                    $form->model()->expire_time=null;
//                    $form->model()->pay_time=null;
                }
            }
            if ($form->password && $form->model()->password != $form->password) {
                $form->model()->show_pass=$form->password;
                $form->password = bcrypt($form->password);

            }
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