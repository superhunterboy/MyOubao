{{ Form::open(array('url' => 'admin/issues/batchDelete')) }}

Lottery:
<select  name="lottery_id">
    @foreach ($aLotteries as $id => $title)
    <option value="{{ $id }}" {{ Input::old('language', isset($data) ? $data->language : '') == $id ? 'selected' : ''}}>{{ $title }}</option>
    @endforeach
</select><br>
Begin Issue:<input type="text" name="begin_issue" /><br>
End Issue:<input type="text" name="end_issue" /><br>
Begin Time:<input type="text" name="begin_time" /><br>
End Time:<input type="text" name="end_time" /><br>
<input type="submit" value="Batch Delete" />
{{ Form::close() }}

