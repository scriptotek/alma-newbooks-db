@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>{{ $user->name }}</h2>

        <p>
        	{{ $user->ltid }}
        </p>

    </div>
@endsection
