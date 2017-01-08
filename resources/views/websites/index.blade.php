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
                                    <th>Url</th>
                                    <th>Status</th>
                                    <th>Response Time</th>
                                    <th>Option</th>
                            </tr>
                        </thead>
                        <tbody id="report-rows">
                            @foreach($sites as $site)
                                <tr>
                                    <td>{{ $site['url'] }}</td>
                                    <td>{{ $site['status'] }}</td>
                                    <td>{{ $site['response'] }}</td>
                                    <td>
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