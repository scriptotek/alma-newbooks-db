@extends('layouts.app')

@section('content')

    <div class="container">

        <h2>{{ $report->name }}</h2>

        @if (Auth::check())

            <a href="{{ action('ReportsController@edit', $report->id) }}">{{ trans('reports.edit') }}</a> |
            <a href="{{ action('ReportsController@delete', $report->id) }}">{{ trans('reports.delete') }}</a>

            <p class="text-muted">
                <em>
                    {{ trans('reports.created_by', [
                        'created_by' =>  $report->createdBy->name,
                        'created_at' =>  $report->created_at,
                    ]) }}
                    {{ trans('reports.updated_by', [
                        'updated_by' =>  $report->updatedBy->name,
                        'updated_at' =>  $report->updated_at,
                    ]) }}
                </em>
            </p>

            <p>
                This list includes documents that matches the following query:
            </p>
            <p>
                <code><pre class="pre-scrollable" style="color: #822; font-size: 80%; padding-left: 2em;">{{ $report->getExpandedQuery() }}</pre></code>
            </p>

        @endif

    </div>

    <div style="background:white; padding: 1em 0;">
        <div class="container">

            <div class="panel panel-default">
                <div class="panel-body">
                    <rss-generator
                        view-url="{{ $viewUrl }}"
                        rss-url="{{ $rssUrl }}"
                        json-url="{{ $jsonUrl }}"
                        :templates="{{ json_encode($templates) }}"
                    ></rss-generator>
                </div>
            </div>

        </div>
    </div>

@endsection
