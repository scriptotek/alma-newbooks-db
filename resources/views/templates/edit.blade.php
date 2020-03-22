@extends('layouts.app')

@section('content')
    <div class="container">

        <h2>{{ trans('templates.header') }}</h2>

        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        @if (is_null($id))
            <form method="POST" action="{{ action('TemplatesController@store') }}">
        @else
            <form method="POST" action="{{ action('TemplatesController@update', $id) }}">
                <input type="hidden" name="_method" value="PUT">
        @endif
            {{ csrf_field() }}

            <div class="row">

                <div class="col col-sm-6">
                    <div class="form-group">
                        <input type="text" name="name" placeholder="{{ trans('templates.name') }}" id="name" value="{{ old('name') ?: $name }}" class="form-control">
                    </div>
                </div>

                <div class="col col-sm-2">
                    @if (is_null($id))
                        <a href="{{ action('TemplatesController@index') }}" class="btn btn-light">{{ trans('cancel') }}</a>
                    @else
                        <a href="{{ action('TemplatesController@show', $id) }}" class="btn btn-light">{{ trans('cancel') }}</a>
                    @endif
                    <button type="submit" class="btn btn-primary">{{ trans('save') }}</button>
                </div>

            </div>

            <div class="form-group">
                <label for="body">{{ trans('templates.body') }}</label>
                <p>
                    You can use <a href="http://twig.sensiolabs.org/documentation" target="_blank">Twig</a> syntax in the form below. Two custom filter are available for formatting dates: <code>humandiff</code> and <code>dateformat</code>,
                    where the latter takes the same arguments as <a href="http://carbon.nesbot.com/docs/" target="_blank"><code>Carbon::formatLocalized</code></a>.
                </p>

                <!-- Using a value rather than slot because of https://github.com/vuejs/Discussion/issues/492 -->
                <ace-editor style="height:400px; position: relative;" id="body" mode="twig" value="{{ old('body') ?: $body }}"></ace-editor>
                <live-preview editor="body" endpoint="templates"></live-preview>

            </div>

        </form>
    </div>

@endsection
