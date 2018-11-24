@if (Session::get('is_client'))
@include('w.client.header')
@else
@include('w.header-v4')
@endif