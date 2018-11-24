{{ Form::open(['route' => ('bets.upload-bet-number'), 'method' => 'post', 'files' => true, 'class' => 'form-horizontal']) }}
<input name="betNumber" type="file" class="form-control" style="padding:5px;">
<button type="submit" class="btn btn-success">{{ __('Submit') }}</button>
{{ Form::close() }}