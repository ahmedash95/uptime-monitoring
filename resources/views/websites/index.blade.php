@extends('laravel_dashboard::layout')
@section('content')
<div class="panel panel-default">
                <div class="panel-heading">
                    <a href="{{ url('/websites/create') }}" class="btn btn-primary">Add Website</a>
                </div>
                <div class="panel-body">
                    <table class="table">
                        <thead>
                            <tr>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Response Time</th>
                                    <th>Option</th>
                            </tr>
                        </thead>
                        <tbody id="report-rows">
                            @foreach($sites as $site)
                                <tr>
                                    <td>{{ $site['name'] }}</td>
                                    <td class="text-center">
                                        @if($site['status'] == 'online')
                                            <i class="fa fa-check-circle-o fa-2x" style="color: #0a9e0a;" aria-hidden="true"></i>
                                        @else
                                            <i class="fa fa-minus-circle fa-2x" style="color: red;" aria-hidden="true"></i>
                                        @endif
                                    </td>
                                    <td>{{ $site['response'] }}</td>
                                    <td>
                                        <a href="{{ url('/websites/'. $site['id']) }}" class="btn btn-info btn-xs"><i class="fa fa-eye"></i> Show</a>
                                        <form action="{{ url('/websites/'. $site['id']) }}" style="display: inline-block" method="post">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> DELETE</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="panel-footer"></div>
            </div>
@endsection
