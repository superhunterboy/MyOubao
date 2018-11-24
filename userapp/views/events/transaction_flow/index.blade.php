<!DOCTYPE html>
<html>
<head>
	<title>Water Flow</title>
</head>
<body>

	<div>
			
			<table width="100%" border="1">
			<tr>
				<td>Stage</td>
				<td>Requirement</td>
				<td>Prize</td>
				<td>Status</td>
				<td>Click to Collect</td>
			</tr>
			
			@foreach($oActivityTask as $key => $per_task)

				{{ Form::open(['route'=>['transaction-flow.update'],'method'=>'post']) }}
				
				<tr>
					<td>{{ $per_task->name }}</td>
					<td>{{ $per_task->transaction }}</td>
					<td>{{ $per_task->task_reward['value'] }}</td>

					@if(isset($per_task['condition_satisfied']) && $per_task['condition_satisfied'])
						@if(isset($per_task['signed_in_collected']) && !$per_task['signed_in_collected'])
							<td>Can Collect Prize</td>
						@else
							<td>Already collected , don't be GREEDY</td>
						@endif
					@else
						<td>Not Yet</td>
					@endif

					@if(isset($per_task['condition_satisfied']) && $per_task['condition_satisfied'])
						@if(isset($per_task['signed_in_collected']) && !$per_task['signed_in_collected'])
							<td><input type="submit" value="Click Me!"></td>
						@endif
					@else
						<td><input type="submit" value="Click Me!" disabled="disabled"></td>
					@endif

				</tr>

				{{ Form::hidden('task',$per_task->id) }}
				{{ Form::close() }}

			@endforeach


			</table>

	</div>

</body>
</html>