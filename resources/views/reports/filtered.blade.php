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
            <vortex-rss-generator :show-limit="false" :show-received="false" urlbase="{{ URL::current() }}" :templates="{{ json_encode($templates) }}"></vortex-rss-generator>
        </div>
    </div>

@endsection
