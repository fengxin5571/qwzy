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
        ConfigModel::where('name','register_pay')->update(['value'=>$request->get('register_pay')]);
        admin_success('配置成功');

        return back();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
       $this->radio('register_pay','注册付费:')->options(['0'=>'关闭','1'=>'打开']);

    }

    /**
     * The data of the form.
     *
     * @return array $data
     */
    public function data()
    {
        return [
            'register_pay'       => ConfigModel::where('name','register_pay')->value('value'),
        ];
    }
}
