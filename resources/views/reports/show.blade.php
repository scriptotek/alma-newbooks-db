@extends('layouts.app')

@section('content')

    <div class="container">
        @if (Auth::check())

                <a href="{{ action('ReportsController@edit', $report->id) }}">{{ trans('reports.edit') }}</a> |
                <a href="{{ action('ReportsController@delete', $report->id) }}">{{ trans('reports.delete') }}</a> |
                <a href="{{ action('ReportsController@rss', $report->id) }}">{{ trans('reports.rss') }}</a> |
                <a href="{{ action('TemplatesController@show', $report->template->id) }}">{{ trans('templates.show') }}</a>

                <p class="text-muted">
                    <em>
                        Created by {{ $report->createdBy->name }} at {{ $report->created_at }}.
                        Last updated by {{ $report->updatedBy->name }} at {{ $report->updated_at }}.
                    </em>
                </p>

                <p>
                    Includes items matching the following query:
                </p>
                <p>
                    <code>{{ $report->querystring }}</code>
                </p>

                <h3>RSS-snutt til Vortex</h3>
                <pre><code>${include:feed url=[{{ action('ReportsController@rss', $report->id) }}] item-description=[true] item-picture=[true] published-date=[none] max-messages=[30] allow-markup=[true] all-messages-link=[true] if-empty-message=[Ingen nye bøker]}</code></pre>
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
            <ul class="list">
                @foreach ($v as $doc)
                    <li>
                        <a style="font-size: 120%;" href="{{ action('DocumentsController@show', $doc->mms_id) }}">{{ $doc->title }}</a> (<a href="{{ $doc->getPrimoLink() }}">Primo</a>)
                        <!--<div style="font-family: monospace; color: #484;">
                            {{ $doc->{App\Document::RECEIVING_OR_ACTIVATION_DATE} }}
                        </div>-->
                        <div>
                            {!! $report->template->render($doc) !!}
                        </div>

                    </li>
                @endforeach
            </ul>
            @endforeach
        </div>

    </div>
@endsection
