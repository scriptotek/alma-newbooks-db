@extends('layouts.app')

@section('content')

    <div style="background:white;">
        <div class="container">

            <h2><a href="{{ $report->link }}">{{ $report->name }}</a></h2>

            <h3>{{ $header }}</h3>
            <p>
                {!! $prevLink !!}
                |
                {!! $nextLink !!}
            </p>

            <p>
                <a href="?group_by=dewey">Gruppér etter Dewey</a> |
                <a href="?group_by=week">Gruppér etter ukenummer</a> |
                <a href="?">Ingen gruppering</a>
            </p>


            @foreach ($docs as $k => $v)
            @if (!is_null($k))
                <h3>
                @if (isset($groups[$k]))
                <a href="{{ array_get($groups, $k) }}">{{$k}}</a>
                @else
                {{$k}}
                @endif
                </h3>
            @endif
            <ul>
                @foreach ($v as $doc)
                    <li>
                        <a href="{{ action('DocumentsController@show', $doc->mms_id) }}">{{ $doc->title }}</a>
                        <div style="font-size: 85%;">
                            {!! $doc->repr() !!}
                        </div>

                    </li>
                @endforeach
            </ul>
            @endforeach
        </div>

    </div>
@endsection
