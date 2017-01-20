@extends('layouts.app')

@section('content')
    <div class="container">

        <h2>
        @if (is_null($report->id))
        {{ trans('reports.create') }}
        @else
        {{ trans('reports.edit') }}
        @endif
        </h2>

        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (!count($templates))
            <div class="alert alert-danger">
                <ul>
                    <li>No templates exist! Please <a href="{{ route('templates.create') }}">create one</a> before you create a report.</li>
                </ul>
            </div>
        @endif


        @if (is_null($report->id))
            <form method="POST" action="{{ action('ReportsController@store') }}">
        @else
            <form method="POST" action="{{ action('ReportsController@update', $report->id) }}">
                <input type="hidden" name="_method" value="PUT">
        @endif
            {{ csrf_field() }}

            <div class="row">

                <div class="col col-sm-8">
                    <div class="form-group">
                        <label for="name">{{ trans('reports.name') }}</label>
                        <input type="text" name="name" id="name" value="{{ old('name') ?: $report->name }}" class="form-control">
                    </div>
                </div>

                <div class="col col-sm-1">
                    <div class="form-group">
                        <label for="save">&nbsp;&nbsp;&nbsp;</label>
                        <button type="submit" class="btn btn-primary">{{ trans('save') }}</button>
                    </div>
                </div>

            </div>

            <div class="alert alert-warning">
                <strong>{{ trans('reports.please_note') }}:</strong> {{ trans('reports.do_no_harm') }}
            </div>


            <div>
                <label for="querystring">{{ trans('reports.querystring') }}</label>

                <!-- Using a value rather than slot because of https://github.com/vuejs/Discussion/issues/492 -->
                <div style="height: 200px; position: relative;">
                    <ace-editor id="querystring" mode="mysql" value="{{ old('querystring') ?: $report->querystring }}" fields="{{ implode(',', $fields) }}"></ace-editor>
                </div>
            </div>

            <live-preview editor="querystring" endpoint="lists"></live-preview>

        </form>
    </div>

@endsection
