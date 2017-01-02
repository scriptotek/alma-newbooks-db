@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>{{ trans('templates.header') }}</h2>

        <p>
            <a href="{{ action('TemplatesController@create') }}">{{ trans('templates.create') }}</a>
        </p>

        <ul class="list">
            @foreach($templates as $template)
                <li>
                    <a href="{{ action('TemplatesController@show', $template->id) }}">{{ $template->name }}</a>
                </li>
            @endforeach
        </ul>

    </div>
@endsection
