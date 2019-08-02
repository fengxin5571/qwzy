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
    public $title = '预约设置';

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
        ConfigModel::where('name','take_time')->update(['value'=>$request->get('take_time')]);
//        ConfigModel::where('name','access_key_secret')->update(['value'=>$request->get('access_key_secret')]);
        admin_success('配置成功');

        return back();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->datetime('expire_time','验证码过期时间(小时):')->format('H');
        $this->datetime('take_time','过磅超时时间(小时):')->format('H');
//        $this->text('access_key_id','阿里云AccessKey ID');
//        $this->text('access_key_secret','阿里云AccessKey Secret');
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
            'take_time'        => ConfigModel::where('name','take_time')->value('value'),
//            'access_key_id'    => ConfigModel::where('name','access_key_id')->value('value'),
//            'access_key_secret'=> ConfigModel::where('name','access_key_secret')->value('value'),
        ];
    }
}
