@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>{{ $user->name }}</h2>

        <p>
            UiO-ID: {{ $user->uio_id }}
        </p>

        Alma ID-er:
        <ul>
            @foreach ($user->alma_ids as $alma_id)
                <li>{{$alma_id}}</li>
            @endforeach
        </ul>

    </div>
@endsection
