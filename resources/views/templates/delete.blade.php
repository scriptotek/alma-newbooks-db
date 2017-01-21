@extends('layouts.app')

@section('content')
    <div class="container">
        <form method="POST" action="{{ action('TemplatesController@destroy', $template->id) }}">
            <input type="hidden" name="_method" value="DELETE">
            {{ csrf_field() }}

            <h3>{{ $template->name }}</h3>
            <p>
                {{ trans('templates.confirm_delete') }}
            </p>
            <p>
                <button type="submit" class="btn btn-danger">{{ trans('common.delete') }}</button>
            </p>
        </form>
    </div>
@endsection
