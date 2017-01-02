@extends('layouts.app')

@section('content')
    <div class="container">

        <h2>{{ trans('reports.edit') }}</h2>

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

                <div class="col col-sm-4">
                    <div class="form-group">
                        <label for="name">{{ trans('reports.name') }}</label>
                        <input type="text" name="name" id="name" value="{{ old('name') ?: $report->name }}" class="form-control">
                    </div>
                </div>

                <div class="col col-sm-2">
                    <div class="form-group">
                        <label for="max_items">{{ trans('reports.max_items') }}</label>
                        <input type="numeric" name="max_items" id="max_items" value="{{ old('max_items') ?: $report->max_items }}" class="form-control">
                    </div>
                </div>

                <div class="col col-sm-3">
                    <div class="form-group">
                        <label for="template_id">{{ trans('reports.template_id') }}</label>
                        @include('macros.selectbox', [
                            'name' => 'template_id',
                            'values' => $templates,
                            'selected' => old('template_id') ?: $report->template_id,
                            'class' => 'field selectpicker',
                        ])
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
                <strong>Merk:</strong> Skjemaet lar deg skrive en hvilken som helst spørring mot databasen.
                Det er ikke farlig om du skriver noe feil, da får du bare en feilmelding.
                Du vil ikke utilsiktet ødelegge noe, men hvis du faktisk <em>ønsker</em> å slette noe (grunnet dårlig
                dag på jobben eller noe sånt) så kommer du til å få til det, så du trenger ikke å teste det
                bare for å teste ;)
            </div>


            <div class="form-group">
                <label for="querystring">{{ trans('reports.querystring') }}</label>

                <!-- Using a value rather than slot because of https://github.com/vuejs/Discussion/issues/492 -->
                <div style="height: 200px; position: relative;">
                    <ace-editor id="querystring" mode="mysql" value="{{ old('querystring') ?: $report->querystring }}" fields="{{ implode(',', $fields) }}"></ace-editor>
                </div>
            </div>

            <live-preview editor="querystring" endpoint="reports"></live-preview>

        </form>
    </div>

@endsection
