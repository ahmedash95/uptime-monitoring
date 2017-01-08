@extends('laravel_dashboard::layout')
@section('content')
<div class="panel panel-default">
                <div class="panel-heading">
                    <p class="text-center">Home</p>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-body">
                    <table class="table">
                        <thead>
                            <tr>
                                @foreach($titles as $title)
                                    <th>{{ $title }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody id="report-rows">
                            @foreach($rows as $row)
                                <tr>
                                    <td>{{ $row['url'] }}</td>
                                    <td>{{ $row['reachable'] }}</td>
                                    <td>{{ $row['onlineSince'] }}</td>
                                    <td>{{ $row['certificateFound'] }}</td>
                                    <td>{{ $row['certificateExpirationDate'] }}</td>
                                    <td>{{ $row['certificateIssuer'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="panel-footer"></div>
            </div>
@endsection