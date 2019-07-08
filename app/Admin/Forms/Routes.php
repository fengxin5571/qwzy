<?php

namespace App\Admin\Forms;

use Encore\Admin\Config\ConfigModel;
use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;

class Routes extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = '小程序配置';

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
        ConfigModel::where('name','appid')->update(['value'=>$request->get('appid')]);
        ConfigModel::where('name','appsecret')->update(['value'=>$request->get('appsecret')]);
        admin_success('配置成功');
        return back();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->text('appid','APPID')->rules('required');
        $this->text('appsecret','AppSecret')->rules('required');
    }

    /**
     * The data of the form.
     *
     * @return array $data
     */
    public function data()
    {
        return [
            'appid'       => ConfigModel::where('name','appid')->value('value'),
            'appsecret'      => ConfigModel::where('name','appsecret')->value('value'),
        ];
    }
}
