@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>{{ trans('reports.header') }}</h2>

        <p>
            <a href="{{ action('ReportsController@create') }}">{{ trans('reports.create') }}</a>
        </p>

        <ul>
            @foreach($reports as $report)
                <li>
                    <a href="{{ action('ReportsController@show', $report->id) }}">{{ $report->name }}</a>
                </li>
            @endforeach
        </ul>

    </div>
@endsection
