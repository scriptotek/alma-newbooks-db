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
                    @if ($template->trashed())
                        <s style="color: #888;">
                            {{ $template->name }}
                        </s>
                    @else
                        <a href="{{ action('TemplatesController@edit', $template->id) }}">{{ $template->name }}</a>
                        [<a href="{{ action('TemplatesController@delete', $template->id) }}">delete</a>]
                    @endif
                </li>
            @endforeach
        </ul>

    </div>
@endsection
