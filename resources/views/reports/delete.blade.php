@extends('layouts.app')

@section('content')
    <div class="container">
        <form method="POST" action="{{ action('ReportsController@destroy', $report->id) }}">
            <input type="hidden" name="_method" value="DELETE">
            {{ csrf_field() }}

            <h3>{{ $report->name }}</h3>
            <p>
                {{ trans('reports.confirm_delete') }}
            </p>
            <p>
                <button type="submit" class="btn btn-danger">{{ trans('delete') }}</button>
            </p>
        </form>
    </div>
@endsection
