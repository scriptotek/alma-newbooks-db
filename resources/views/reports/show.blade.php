@extends('layouts.app')

@section('content')

    <div class="container">
        @if (Auth::check())

                <a href="{{ action('ReportsController@edit', $report->id) }}">{{ trans('reports.edit') }}</a> |
                <a href="{{ action('ReportsController@delete', $report->id) }}">{{ trans('reports.delete') }}</a> |
                <a href="{{ action('ReportsController@rss', $report->id) }}">{{ trans('reports.rss') }}</a>

                <p class="text-muted">
                    <em>
                        Created by {{ $report->createdBy->name }} at {{ $report->created_at }}.
                        Last updated by {{ $report->updatedBy->name }} at {{ $report->updated_at }}.
                    </em>
                </p>

                <p>
                    Includes items received or activated between <strong>{{ $report->days_start }}</strong>
                    and <strong>{{ $report->days_end }}</strong> days ago, and matching the following query:
                </p>
                <p>
                    <code>{{ $report->querystring }}</code>
                </p>
        @endif
    </div>

    <div style="background:white;">
        <div class="container">

            <h2>{{ $report->name }}</h2>
            <a href="?group_by=dewey">Gruppér etter Dewey</a> |
            <a href="?group_by=week">Gruppér etter ukenummer</a> |
            <a href="?group_by=month">Gruppér etter måned</a> |
            <a href="?">Ingen gruppering</a>

            @foreach ($docs as $k => $v)
            @if (!is_null($k))
                <h3>
                @if (isset($groups[$k]))
                <a href="{{ array_get($groups, $k) }}">{{$k}}</a>
                @else
                {{$k}}
                @endif
                </h3>
            @endif
            <ul>
                @foreach ($v as $doc)
                    <li>
                        <a href="{{ action('DocumentsController@show', $doc->mms_id) }}">{{ $doc->title }}</a>
                        <div style="font-size: 85%;">
                            {!! $doc->repr() !!}
                        </div>

                    </li>
                @endforeach
            </ul>
            @endforeach
        </div>

    </div>
@endsection
