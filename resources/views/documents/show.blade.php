@extends('layouts.app')

@section('content')

	<div class="container" style="background:white;">
		<h2>{{ $doc->barcode }}</h2>

		<p>
			<a href="{{ $doc->primo_link }}">Vis i Primo</a>
		</p>

		<p>
			MMS ID:
			<a href="{{ action('DocumentsController@index', ['k1' => 'mms_id', 'r1' => 'eq', 'v1' => $doc->mms_id]) }}">{{ $doc->{App\Document::MMS_ID} }}</a>. ISBN: {{ $doc->isbn }}.
			Tittel: {{ $doc->title }}.
		</p>

		<p>
			@if ($doc->edition)
				<strong>Utgave</strong>: {{ $doc->edition}}<br>
			@endif
			@if ($doc->series)
				<strong>Series</strong>: {{ $doc->series }}
			@endif
			{{ $doc->author }}<br>
				{{ $doc->publication_place or '(publication place missing)' }}
				 :
				{{ $doc->publisher or '(publisher missing)' }}
				{{ $doc->publication_date or '(publication date missing)' }}
				<br>
			<strong>Material type</strong>: {{$doc->material_type}}
			<strong>Dewey</strong>: {{$doc->dewey_classification or '(not yet assigned)'}}

		</p>

		@if ($doc->bib_creation_date)
			IZ bib record created:
			{!! $doc->link_to_date('bib_creation_date') !!}.
		@endif

		<h4>All items with this MMS ID</h4>

		<ul>
		@foreach($doc->components as $component)
			<li>
				<a href="{{ action('DocumentsController@show', $component->id) }}">
					@if ($component->barcode)
						{{ $component->barcode }}
					@elseif ($component->portfolio_id)
						{{ $component->portfolio_id }}
					@else
						(item not created yet)
					@endif
				</a>
				@if ($component->process_type)
					Process type: <em>{{ $component->process_type }}</em>.
				@endif
				@if ($component->library_name)
					@ <a href="{{ action('DocumentsController@index', ['k1' => 'library_name', 'r1' => 'eq', 'v1' => $component->library_name]) }}">{{ $component->library_name }}</a>
				@endif
				@if ($component->location_name)
					<a href="{{ action('DocumentsController@index', ['k1' => 'location_name', 'r1' => 'eq', 'v1' => $component->location_name]) }}">{{ $component->location_name }}</a>
				@endif
				{{ $component->permanent_call_number }}
			<!-- Ebooks -->
				@if ($component->collection_name)
					/ Collection: <a href="{{ action('DocumentsController@index', ['k1' => 'collection_name', 'r1' => 'eq', 'v1' => $component->collection_name]) }}">{{ $component->collection_name }}</a>.
				@endif
				@if ($component->temporary_location_name)
					Temporary location:
					<a href="{{ action('DocumentsController@index', ['k1' => 'temporary_location_name', 'r1' => 'eq', 'v1' => $component->temporary_location_name]) }}">{{ $component->temporary_location_name }}</a>
				@endif

				<div>
				@if ($component->acquisition_method == 'PURCHASE')
					Purchase order {{ $component->{App\Document::PO_ID} }} created {{ $component->getDateString('po_creation_date') }}
					@if ($component->po_creator)
						by {{ $component->po_creator }}
					@endif
					and sent
					{!! $component->link_to_date('sent_date') !!}.
				@else
					{{$component->acquisition_method}}
					{{ $component->{App\Document::PO_ID} }}.
				@endif

				</div>


				@if ($component->item_creation_date)
					Item created:
					{!! $component->link_to_date('item_creation_date') !!}
					({{ \Carbon\Carbon::parse($component->item_creation_date)->diffInDays($doc->bib_creation_date) }} days after bib record was created).<br>
				@endif
				@if ($component->receiving_date)
					Item received:
					{!! $component->link_to_date('receiving_date') !!}
					({{ $component->{'receiving_date'}->diffInDays(\Carbon\Carbon::parse($component->item_creation_date)) }} days after item record was created).<br>
				@endif
				@if ($component->activation_date)
					E-book activated:
					{!! $component->link_to_date('activation_date') !!}.<br>
				@endif
				@if ($component->cataloged_at)
					Call number assigned:
					{!! $component->link_to_date('cataloged_at') !!}
					({{ \Carbon\Carbon::parse($component->{'cataloged_at'})->diffInDays($component->receiving_or_activation_date) }} days after item record was received or activated).<br>
				@endif
				@if ($component->ready_at)
					<abbr title="Date when process type first changed from 'In Process' to something else">Item ready</abbr>:
					{!! $component->link_to_date('ready_at') !!}
					({{ \Carbon\Carbon::parse($component->{'ready_at'})->diffInDays($component->receiving_or_activation_date) }} days after item record was received or activated).<br>
				@endif
			</li>
		@endforeach
		</ul>

		<h4>History for this item</h4>
		<ul>
			@foreach ($doc->changes()->orderBy('created_at', 'desc')->get() as $change)
				<li>
					{{ $change->created_at->subDay()->toDateString() }}:
					<span style="color:#888; font-weight: 600;">{{ $change->key }}</span>
					changed from
					<span style="color: rgb(199,0,35);  font-weight: 600;">
						{{ $change->old_value ?: '(no value)' }}
					</span>
					to
					<span style="color: rgb(91,132,150); font-weight: 600;">
						{{ $change->new_value }}
					</span>
				</li>
			@endforeach
		</ul>

		<h4>Details</h4>

		<table class="table">
			@foreach (App\Document::getFields() as $field)
				<tr>
					<th>
						{{ $field }}
					</th>
					<td>
						{{ $doc->{$field} }}
					</td>
				</tr>
			@endforeach
		</table>

	</div>

@endsection
