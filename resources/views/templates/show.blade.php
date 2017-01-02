@extends('layouts.app')

@section('content')

    <div class="container">
        @if (Auth::check())

                <a href="{{ action('TemplatesController@edit', $template->id) }}">{{ trans('templates.edit') }}</a>

                <p class="text-muted">
                    <em>
                        This template has {{ $template->versions->count() }} revisions.
                        Latest revision by {{ $template->currentVersion->createdBy->name }} at {{ $template->currentVersion->created_at }}.
                    </em>
                </p>
        @endif
    </div>

    <div style="background:white;">
        <div class="container">

            <h2>{{ $template->name }}</h2>

            <ace-editor style="height:600px; position: relative;" readonly="true" id="body" mode="twig" value="{{ $template->currentVersion->body }}"></ace-editor>

            <h3>{{ trans('templates.usage') }}</h3>

            <ul>
                @foreach ($template->reports as $report)
                    <li>
                        <a href="{{ action('ReportsController@show', $report->id) }}">{{ $report->name }}</a>
                    </li>
                @endforeach
            </ul>

        </div>
    </div>

@endsection
