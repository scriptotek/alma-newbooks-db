@extends('layouts.app')

@section('content')

    <div class="container">

        <h2><a href="{{ $report->link }}">{{ $report->name }}</a></h2>

        <h3>{{ $header }}</h3>
        <p>
            {!! $prevLink !!}
            |
            {!! $nextLink !!}
        </p>

    </div>

    <div style="background:white;">
        <div class="container">
            <rss-generator
                view-url="{{ $viewUrl }}"
                rss-url="{{ $rssUrl }}"
                json-url="{{ $jsonUrl }}"
                :show-limit="false"
                :show-received="false"
                :templates="{{ json_encode($templates) }}"
            ></rss-generator>
        </div>
    </div>

@endsection
