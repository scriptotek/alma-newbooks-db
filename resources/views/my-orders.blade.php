@extends('layouts.app')

@section('content')

    <div class="container">
    <h2>Not yet received</h2>
    @foreach ($sent as $doc)
        <ul>
            <li>
                <div>
                    <a href="{{ action('DocumentsController@show', $doc->id) }}">{{ $doc }}</a>
                </div>
                <div>
                    @if ($doc->sent_date)
                        Order sent: {{ $doc->sent_date }}
                        {{ $doc->expected_receiving_date ? 'Expected receiving date: '. $doc->expected_receiving_date : '' }}
                    @else
                        Order not sent yet
                    @endif
                </div>
            </li>
        </ul>
    @endforeach

    {{ $sent->links() }}

    <h2>Received</h2>

    @foreach ($received as $doc)
        <ul class="list">
            <li>
                <div>
                    <a href="{{ action('DocumentsController@show', $doc->id) }}">{{ $doc }}</a>
                </div>
                <div>
                    Order sent: {{ $doc->sent_date }}
                    Received or activated: {{ $doc->{App\Document::RECEIVING_OR_ACTIVATION_DATE} }}
                </div>
            </li>
        </ul>
    @endforeach

    {{ $received->links() }}

</div>

@endsection
