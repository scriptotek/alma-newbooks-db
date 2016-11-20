@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>{{ trans('users.header') }}</h2>

        <ul>
            @foreach($users as $user)
                <li>
                    <a href="{{ action('UsersController@show', $user->id) }}">{{ $user->name }}</a>
                ({{ implode(', ', $user->alma_ids) }})
                </li>
            @endforeach
        </ul>

    </div>
@endsection
