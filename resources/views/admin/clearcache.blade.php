<button type="button" class="btn btn-lg btn-block btn-{{$envs['style'][$index]}}" ><a href="{{$envs['url'][$index]}}">{{$envs['text'][$index]}}</a></button>
@if(session('tip'))
    {{--{{admin_toastr('清除成功', 'info')}}--}}
@endif