@extends('laravel_dashboard::layout')
@section('content')
<div class="panel panel-default">
	<div class="panel-heading">Servers List</div>
	<div class="panel-body">
		<form method="post" action="{{ url('/websites') }}">
			{{ csrf_field() }}
			<div class="col-md-9">
				<input class="form-control" placeholder="Link .." type="text" name="url">
			</div>
			<div class="col-md-3">
				<button class="btn btn-primary btn-md">Insert</button>
			</div>
		</form>
	</div>
</div>
@endsection