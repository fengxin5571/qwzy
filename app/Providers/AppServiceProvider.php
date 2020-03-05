<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Encore\Admin\Config\Config;
use Illuminate\Support\Facades\Schema;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        //本地环境生成model
        if ($this->app->environment() == 'local') {
            $this->app->register(\Reliese\Coders\CodersServiceProvider::class);
        }

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        //手机号验证
        Validator::extend('is_mobile',function ($attribute, $value, $parameters, $validator){
            return !!preg_match('/^[1](([3][0-9])|([4][5-9])|([5][0-3,5-9])|([6][5,6])|([7][0-8])|([8][0-9])|([9][1,8,9]))[0-9]{8}$/', $value);
        });
        //身份证号验证
        Validator::extend('is_card',function($attribute, $value, $parameters, $validator){
            return !!preg_match('/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$|^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/',$value);
        });
        //银行卡验证
        Validator::extend('is_bank',function ($attribute, $value, $parameters, $validator){
            return !!preg_match('/^[0-9]{16,19}$/',$value);
        });
        //车牌号验证
        Validator::extend('is_car',function ($attribute, $value, $parameters, $validator){
            if(iconv_strlen($value,"UTF-8")==7){
                return !!preg_match('/[京津冀晋蒙辽吉黑沪苏浙皖闽赣鲁豫鄂湘粤桂琼川贵云渝藏陕甘青宁新使]{1}[A-Z]{1}[0-9a-zA-Z]{5}$/u',$value);
            }
//            elseif (iconv_strlen($value,"UTF-8")==8){
//                //return !!preg_match('/^[\u4e00-\u9fa5]{1}[a-hj-np-zA-HJ-NP-Z]{1}([0-9]{5}[d|f|D|F]|[d|f|D|F][a-hj-np-zA-HJ-NP-Z0-9][0-9]{4})$/',$value);
//            }
        });
        $table = config('admin.extensions.config.table', 'admin_config');
        if (Schema::hasTable($table)) {
            Config::load();
        }
    }
}
