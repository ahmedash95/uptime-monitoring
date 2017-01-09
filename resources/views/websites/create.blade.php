@extends('laravel_dashboard::layout')
@section('content')
<div class="panel panel-default">
	<div class="panel-heading">Servers List</div>
	<div class="panel-body">
		<form method="post" action="{{ url('/websites') }}">
			{{ csrf_field() }}
			<div class="col-md-2">
				<select name="type" id="inputType" class="form-control" required="required">
					<option value="website">Website</option>
					<option value="redis">Redis</option>
				</select>
			</div>
			<div class="col-md-4">
				<input class="form-control" placeholder="name .." type="text" name="name">
			</div>
			<div class="col-md-4">
				<input class="form-control" placeholder="Link .." type="text" name="url">
			</div>
			<div class="col-md-2">
				<button class="btn btn-primary btn-md">Insert</button>
			</div>
		</form>
	</div>
</div>
@endsection