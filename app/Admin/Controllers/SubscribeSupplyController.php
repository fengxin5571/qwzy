<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/6/18
 * Time: 11:02 AM
 */
namespace App\Admin\Controllers;
use App\Admin\Extensions\Tools\SubSupplyimport;
use App\Admin\Filters\TimestampBetween;
use App\Exports\SubSupplyExports;
use App\Imports\SubscribeSupplyImport;
use App\Jobs\AutoHandleBlackList;
use App\Model\SubscribeSupply;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Maatwebsite\Excel\Facades\Excel;

class SubscribeSupplyController extends AdminController{
    /**
     * 预约供货记录
     * @param Content $content
     * @return Content
     */
    public function index(Content $content){
        return $content->header('预约记录')
            ->description(trans('admin.list'))
            ->breadcrumb(['text'=>'预约记录'])
            ->body($this->grid());
    }

    /**
     * 查看供货记录
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id,Content $content){
        $info=SubscribeSupply::find($id);
        return $content->header('查看供货记录')
               ->description('查看')
               ->breadcrumb(['text'=>'查看供货记录'])
               ->body(Admin::show($info,function($show)use ($info){
                   if($info->sub_type==2){
                       $show->shipper_name('货主名称：');
                       $show->bank_address('银行卡开户行：');
                       $show->bank_code('银行卡卡号：');
                   }
                  $show->car_number('车牌号：');
                  $show->driver_name('司机姓名：');

                  $show->mobile('司机手机号：');
                  $show->goods_name('供货货品：');
                  $show->sub_type('供货类型：')->as(function($sub_type){
                      return $sub_type==1?'临时供货':'供货商供货';
                   });
                  $show->card_id('司机身份证号：');

                  $show->axle_number('车轴数：');
                  //$show->load_weight('荷载重量：');
                  $show->channel('发货地：');
                  //$show->unit_name('供货单位：');
                  $show->unit_transport('运输单位：');
                  //$show->delivery_weight('送货重量：');
                  $show->paper_number('废纸件数：');
                  $show->people_num('进场人数：');
                  $show->sub_time('预约时间：');
                  $show->take_time('取卡时间：')->as(function($take_time){
                       if($take_time){
                           return date('Y-m-d H:i:s',$take_time);
                       }else{
                           return '未取卡';
                       }

                   });
                   $show->weighed_time('过磅时间：')->as(function($weighed_time){
                       if($weighed_time){
                           return date('Y-m-d H:i:s',$weighed_time);
                       }else{
                           return '未过磅';
                       }

                   });
                  $show->sub_code('取卡码：');
                  $show->expire_time('验证码过期时间：')->as(function($expire_time){
                       return date('Y-m-d H:i:s',$expire_time);
                  });



                  $show->status('供货状态：')->using(['0'=>'未取卡','1'=>'已过期','2'=>'已取卡','3'=>'已过磅','4'=>'已超时']);
                  $show->panel()
                       ->style('info')
                       ->title('供货记录详细信息')
                       ->tools(function ($tools) {
                           $tools->disableEdit();
                       });;
               }));
    }

    /**
     * 预约记录导入
     * @param Content $content
     * @return Content
     */
    public function import(Content $content){
        $content->header('预约记录导入');
        $content->description('导入数据');
        $content->breadcrumb(
            ['text' => '预约记录', 'url' => '/subscribe/list'],
            ['text' => '导入数据']
        );
        $form=new \Encore\Admin\Widgets\Form();
        $form->action(route('subscribe.import.post'));
        $form->file('importFile','Excel预约记录文件：')->required()->rules('mimes:xlsx')
            ->help('请按给定的Excel格式文件上传，下载格式文件点击这里<a href="/storage/Excel/123.xlsx" download="123.xlsx" target="_blank">Excel格式文件</a>');
        $content->body('<div class="box box-info">'.$form->render().'</div>');
        return $content;
    }

    /**
     * 导入提交
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\think\response\Redirect|void
     */
    public function importPost(Request $request){
        try{
            $file=$request->file('importFile');
            if(!$file->isValid()){
                throw new \Exception('上传错误,请重新上传！');
            }
            if($file->getClientOriginalExtension()!='xlsx'){
                throw new \Exception('请上传Execl文件');
            }

            Excel::import(new SubscribeSupplyImport(),$file);
            admin_toastr('导入成功', 'success',['timeOut'=>1000]);
            return redirect('/admin/subscribe/list');
        }catch (\Exception $e){
            $message=[
                'title'=>'错误',
                'message'=>$e->getMessage(),
            ];
            $error=new MessageBag($message);
            return back()->with(compact('error'));
        }

    }
    protected function grid(){
        $grid=new Grid(new SubscribeSupply);
        $grid->model()->orderBy('sub_time','desc');
        $grid->column('car_number','车牌号')->label('primary');
        $grid->column('driver_name','司机姓名');
        $grid->column('shipper_name','货主名称')->display(function($shipper_name){
            return $shipper_name=$shipper_name?:'临时供货';
        });
        $grid->column('mobile','手机号')->display(function($mobile){
            return '<span style="color: #999;"><i class="fa fa-phone"></i> '.$mobile.'</span>';
        });
        $grid->column('goods_name','货物名称')->display(function($goods_name){
            return explode(',',$goods_name);
        })->label();
        $grid->column('paper_number','废纸件数')->sortable();
        $grid->column('sub_type','预约类型')->using(['1'=>'临时','2'=>'供货商']);
        $grid->column('sub_code','取卡码')->width(89);
        $grid->column('sub_time','预约时间')->sortable()->width(150);
        $grid->column('take_time','取卡时间')->display(function ($take_time){
            if($take_time){
                return date('Y-m-d H:i:s',$take_time);
            }else{
                return '未取卡';
            }

        })->sortable();
        $grid->column('weighed_time','过磅时间')->display(function ($weighed_time){
            if($weighed_time){
                return date('Y-m-d H:i:s',$weighed_time);
            }else{
                return '未过磅';
            }

        })->sortable();
        $grid->column('expire_time','验证码过期时间')->display(function ($expire_time){
            return date('Y-m-d H:i:s',$expire_time);
        })->width(150)->hide();
        $grid->column('status','供货状态')->using([
            '0'=>'<span class="label label-info">未取卡</span>',
            '1'=>'<span class="label label-danger">已过期</span>',
            '2'=>'<span class="label label-success">已取卡</span>',
            '3'=>'<span class="label label-success">已过磅</span>',
            '4'=>'<span class="label label-danger">已超时</span>'
        ])->help('未取卡：预约成功没有取卡；已过期：超过取卡过期时间；已取卡：已成功取卡；已超时：取卡后超过过磅时间');
        $grid->actions(function ($actions) {
            $actions->disableEdit();

        });
        $grid->disableColumnSelector(false);
        $grid->tools(function ($tools) {
            $tools->append(new SubSupplyimport());
        });
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->column(1/2, function ($filter) {
                $filter->like('driver_name','司机姓名');
                $filter->like('shipper_name','货主名称');
                $filter->equal('mobile','手机号')->mobile();
                $filter->use(new TimestampBetween('weighed_time','过磅时间'))->datetime();
                $filter->equal('sub_type','供货类型')->radio([
                    ''   => '所有',
                    1    => '临时供货',
                    2    => '供货商供货',
                ]);
            });
            $filter->column(1/2, function ($filter) {
                $filter->like('car_number','车牌号');
                $filter->like('goods_name','货品名称');
                $filter->use(new TimestampBetween('sub_time','预约时间'))->datetime();
                $filter->use(new TimestampBetween('take_time','取卡时间'))->datetime();

                $filter->equal('status','供货状态')->radio([
                    ''  =>'所有',
                    0   => '未取卡',
                    1   => '已过期',
                    2   => '已取卡',
                    3   => '已过磅',
                    4   =>'已超时'
                ]);
            });

        });
        $grid->exporter(new SubSupplyExports());
        $grid->disableCreateButton();
        $grid->disableExport(false);
        $grid->paginate(20);
        return $grid;
    }
    protected function form(){
        $form=new Form(new SubscribeSupply);
        $form->multipleImage('sub_images','供货验收图片')->removable()->sortable()
            ->help('支持多图上传，点击 <i class="glyphicon glyphicon-zoom-in"></i> 可查看大图');
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
