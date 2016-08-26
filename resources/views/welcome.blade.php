@extends('layouts.app')

@section('content')
    <div class="container">
        <ul>
            <li><a href="/docs">{{ trans('documents.header') }}</a></li>
            <li><a href="/reports">{{ trans('reports.header') }}</a></li>
            <li><a href="/users">{{ trans('users.header') }}</a></li>
        </ul>
    </div>
@endsection
