@extends('layouts.app')

@section('content')

	<div class="container" style="background:white;">
		<h2>{{ $doc->title }}</h2>

		<h3>Summary</h3>

		<p>
			MMS ID:
			<a href="https://bibsys-almaprimo.hosted.exlibrisgroup.com/primo_library/libweb/action/dlSearch.do?institution=UBO&amp;vid=UBO&search_scope=default_scope&amp;query=any,contains,{{ $doc->{App\Document::MMS_ID} }}">{{ $doc->{App\Document::MMS_ID} }}</a>
			<!--<a href="https://services.bibsys.no/services/html/marcPresentation.html?{App\Document::MMS_ID}={{ $doc->{App\Document::MMS_ID} }}">{{ $doc->{App\Document::MMS_ID} }}</a>
			(todo: konvertere til nz-id..)-->
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

		<ul>
		@foreach($doc->components as $component)
			<li>

				{{ $component->barcode }} / {{ $component->item_id }}
				<div>
				@if ($component->library_name)
					Permanent location: <a href="{{ action('DocumentsController@index', ['k1' => 'library_name', 'r1' => 'eq', 'v1' => $component->library_name]) }}">{{ $component->library_name }}</a>
				@endif
				@if ($component->location_name)
					<a href="{{ action('DocumentsController@index', ['k1' => 'location_name', 'r1' => 'eq', 'v1' => $component->location_name]) }}">{{ $component->location_name }}</a>
				@endif
				{{ $component->permanent_call_number }}
			<!-- Ebooks -->
				{{ $component->collection_name }}
				</div>

				@if ($component->temporary_location_name)
					<div>
						Temporary location:
						<a href="{{ action('DocumentsController@index', ['k1' => 'temporary_location_name', 'r1' => 'eq', 'v1' => $component->temporary_location_name]) }}">{{ $component->temporary_location_name }}</a>
					</div>
				@endif

				<div>
				Acquisition:
				@if ($component->acquisition_method == 'PURCHASE')
					Order {{ $component->{App\Document::PO_ID} }} created {{ $component->getDateString('po_creation_date') }}
					@if ($component->po_creator)
						by {{ $component->po_creator }}
					@endif
					and sent
					{!! $component->link_to_date('sent_date') !!}.
				@else
					{{ $component->{App\Document::PO_ID} }}:
					{{$component->acquisition_method}}.
				@endif

				@if ($component->receiving_date)
					Item received:
					{!! $component->link_to_date(App\Document::RECEIVING_OR_ACTIVATION_DATE) !!}
				@endif

				@if ($component->activation_date)
					E-book activated:
					{!! $component->link_to_date(App\Document::RECEIVING_OR_ACTIVATION_DATE) !!}
				@endif
				</div>
				@if ($component->process_type)
				<div>
					Process type: {{ $component->process_type }}
				</div>
				@endif
			</li>
		@endforeach
		</ul>

		<h3>History</h3>
		<ul>
			@foreach ($doc->changes as $change)
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

		<h3>Details</h3>

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
