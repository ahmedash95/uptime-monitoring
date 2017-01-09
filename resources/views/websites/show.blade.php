@extends('laravel_dashboard::layout')
@section('content')
<div class="panel panel-default">
                <div class="panel-heading text-center">
                    Name: {{ $website->name }}
                </div>
                <div class="panel-body">
                    <table class="table">
                        <thead>
                            <tr>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Response Time</th>
                            </tr>
                        </thead>
                        <tbody id="report-rows">
                            @foreach($status as $s)
                                <tr>
                                    <td>{{ $s->created_at->diffForHumans() }}</td>
                                    <td>
	                                    @if($s->status == 'online')
                                            <i class="fa fa-check-circle-o fa-2x" style="color: #0a9e0a;" aria-hidden="true"></i>
                                        @else
                                            <i class="fa fa-minus-circle fa-2x" style="color: red;" aria-hidden="true"></i>
                                        @endif
                                    </td>
                                    <td>{{ $s->response_time }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="panel-footer">{{ $status->links() }}</div>
            </div>
@endsection