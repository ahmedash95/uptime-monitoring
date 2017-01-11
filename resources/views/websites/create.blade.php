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
					<option value="mysql">MySql</option>
				</select>
			</div>
			<div class="col-md-4">
				<input class="form-control" placeholder="name .." type="text" name="name">
			</div>
			<div class="col-md-4">
				<input class="form-control" placeholder="Link .." type="text" name="url">
			</div>
			<div class="col-md-2">
				
			</div>
			<div class="clearfix"></div>
			<div><p></p></div>
			<div class="col-md-4 col-md-offset-2">
				<input class="form-control" placeholder="username .." type="text" name="username">
			</div>
			<div class="col-md-4">
				<input class="form-control" placeholder="password .." type="password" name="password">
			</div>
			<div class="clearfix"></div>
			<div><p></p></div>
			<div class="col-md-4 col-md-offset-2">
				<input class="form-control" placeholder="database name .." type="text" name="db_name">
			</div>
			<div class="col-md-4">
				<input class="form-control" placeholder="table name .." type="text" name="table_name">
			</div>
			<div class="col-md-2">
				<button class="btn btn-primary btn-md">Insert</button>
			</div>
		</form>
	</div>
</div>
@endsection