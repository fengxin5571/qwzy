<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/9/9
 * Time: 2:29 PM
 */
namespace  App\Admin\Extensions;
use Encore\Admin\Admin;
class QueueButton {
    protected $id;
    public function __construct($id)
    {
        $this->id = $id;
    }
    protected function script(){
        return <<<EOT

$('.{$this->id}-orderable').on('click', function() {

    var key = $(this).data('id');
    var direction = $(this).data('direction');
    var table = $(this).data('table');

    $.post('/admin/queue/sortable/' + key, {_method:'post', _token:LA.token, sortable:direction}, function(data){
        if (data.status) {
            $.pjax.reload('#pjax-container');
            toastr.success(data.message);
        }
    });

});
EOT;
    }
    protected function render(){
        Admin::script($this->script());
        return <<<EOT
<div class="btn-group">
    <!-- <button type="button" class="btn btn-xs btn-success {$this->id}-orderable" data-id="{$this->id}"  data-direction="top">
        <i class="fa fa-caret-up fa-fw"></i>置顶
    </button> -->
    <button type="button" class="btn btn-xs btn-success {$this->id}-orderable" data-id="{$this->id}"  data-direction="up">
        <i class="fa fa-caret-up fa-fw"></i>上移
    </button>
    <button type="button" class="btn btn-xs btn-danger {$this->id}-orderable" data-id="{$this->id}"  data-direction="down">
        <i class="fa fa-caret-down fa-fw"></i>下移
    </button>
    <!--<button type="button" class="btn btn-xs btn-danger {$this->id}-orderable" data-id="{$this->id}" d data-direction="end">
        <i class="fa fa-caret-down fa-fw"></i>置尾
    </button> -->
</div>

EOT;
    }
    public function __toString()
    {
        return $this->render();
    }
}