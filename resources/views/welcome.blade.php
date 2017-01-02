@extends('layouts.app')

@section('content')
    <div class="container">
        <p>
            Database contents:
            {{ App\Document::count() }} {{ lcfirst(trans('documents.header')) }},
            {{ App\Report::count() }} <a href="/reports">{{ lcfirst(trans('reports.header')) }}</a>,
            {{ App\Template::count() }} {{ lcfirst(trans('templates.header')) }}
            and
            {{ App\User::count() }} users.
        </p>
    </div>
@endsection
