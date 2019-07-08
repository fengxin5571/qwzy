<?php

namespace App\Admin\Forms;

use Encore\Admin\Config\ConfigModel;
use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;

class Sms extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = '短信设置';

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
        ConfigModel::where('name','expire_time')->update(['value'=>$request->get('expire_time')]);
        ConfigModel::where('name','access_key_id')->update(['value'=>$request->get('access_key_id')]);
        ConfigModel::where('name','access_key_secret')->update(['value'=>$request->get('access_key_secret')]);
        admin_success('配置成功');

        return back();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->datetime('expire_time','短信过期时间(分钟):')->format('mm');
        $this->text('access_key_id','阿里云AccessKey ID');
        $this->text('access_key_secret','阿里云AccessKey Secret');
    }

    /**
     * The data of the form.
     *
     * @return array $data
     */
    public function data()
    {
        return [
            'expire_time'      => ConfigModel::where('name','expire_time')->value('value'),
            'access_key_id'    => ConfigModel::where('name','access_key_id')->value('value'),
            'access_key_secret'=> ConfigModel::where('name','access_key_secret')->value('value'),
        ];
    }
}
