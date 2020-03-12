<?php

namespace App\Admin\Forms;

use Encore\Admin\Config\ConfigModel;
use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;

class Setting extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = '基本配置';

    /**
     * Handle the form request.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request)
    {
        //dump($request->all());
        $hot_search=$request->get('hot_search')?:'';
       // ConfigModel::where('name','register_pay')->update(['value'=>$request->get('register_pay')]);
        ConfigModel::where('name','hot_search')->update(['value'=>$request->get('hot_search')]);
        ConfigModel::where('name','contact_phone')->update(['value'=>$request->get('contact_phone')]);
        admin_success('配置成功');

        return back();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
       //$this->radio('register_pay','注册付费:')->options(['0'=>'关闭','1'=>'打开']);
       $this->text('hot_search','企业通知热门搜索')->help('多个词请用,号分割');
       $this->mobile('contact_phone','联系电话');
    }

    /**
     * The data of the form.
     *
     * @return array $data
     */
    public function data()
    {
        return [
            //'register_pay'       => ConfigModel::where('name','register_pay')->value('value'),
            'hot_search'         => ConfigModel::where('name','hot_search')->value('value'),
            'contact_phone'      => ConfigModel::where('name','contact_phone')->value('value'),
        ];
    }
}
