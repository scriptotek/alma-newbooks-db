@extends('layouts.app')

@section('content')
    <div class="container">

        <h2>{{ trans('reports.header') }}</h2>

        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
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
                <div class="col col-sm-6">
                    <div class="form-group">
                        <label for="name">{{ trans('reports.name') }}</label>
                        <input type="text" name="name" id="name" value="{{ old('name') ?: $report->name }}" class="form-control">
                    </div>
                </div>
                <div class="col col-sm-3">
                    <div class="form-group">
                        <label for="days_start">{{ trans('reports.days_start') }}</label>
                        <input type="number" name="days_start" id="days_start" value="{{ old('days_start') ?: $report->days_start ?: 30 }}" class="form-control">
                    </div>
                </div>
                <div class="col col-sm-3">
                    <div class="form-group">
                        <label for="days_end">{{ trans('reports.days_end') }}</label>
                        <input type="number" name="days_end" id="days_end" value="{{ old('days_end') ?: $report->days_end ?: 2 }}" class="form-control">
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
                <ace-editor id="querystring" mode="mysql" value="{{ old('querystring') ?: $report->querystring }}" fields="{{ implode(',', $fields) }}"></ace-editor>
            </div>
                        <div class="form-group text-right">
                <button type="submit" class="btn btn-primary">{{ trans('save') }}</button>
            </div>

            <live-preview editor="querystring" start="days_start" end="days_end"></live-preview>

        </form>
    </div>

@endsection
