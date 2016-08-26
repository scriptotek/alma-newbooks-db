@extends('layouts.app')

@section('content')

    <div class="container">
    <form class="form-horizontal panel panel-default"  method="GET" action="{{ action('DocumentsController@index') }}">

        <div class="panel-body">

            <div>
                @foreach (range(1, 25) as $n)
                    @if ($request->has('k' . $n) || $n == 1)
                    <div class="form-group" id="inp{{ $n  }}">
                        <div class="col-sm-3">
                            @include('macros.selectbox', [
                                'name' => 'k'.$n,
                                'values' => $fields,
                                'selected' => $request->get('k' . $n),
                                'class' => 'field selectpicker',
                                'searchable' => true,
                            ])
                        </div>
                        <div class="col-sm-2">
                            @include('macros.selectbox', [
                                'name' => 'r'.$n,
                                'values' => $relations,
                                'selected' => $request->get('r' . $n),
                                'class' => 'relation selectpicker',
                            ])
                        </div>
                        <div class="col-sm-5">
                            <input type="text" class="form-control value" name="v{{ $n }}" value="{{ $request->get('v' . $n) }}">
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>

            <button type="button" id="addstmt" class="btn btn-primary"> <span class="glyphicon glyphicon-plus"></span> </button>

        </div>
        <div class="panel-footer">

            <label for="show">Show fields:</label>
            <select id="show" name="show[]" class="selectpicker" multiple data-selected-text-format="count">
                @foreach( $fields as $field)
                    <option value="{{ $field }}"{!! in_array($field, $show) ? ' selected="selected"' : '' !!}>{{ $field }}</option>
                @endforeach
            </select>

            <label for="sort">Sort by:</label>
            <select id="sort" name="sort" class="selectpicker">
                @foreach( $fields as $field)
                    <option value="{{ $field }}"{!! ($field == $sort) ? ' selected="selected"' : '' !!}>{{ $field }}</option>
                @endforeach
            </select>
            <select id="sortDir" name="sortDir" class="selectpicker">
                <option value="asc"{!! ('asc' == $sortDir) ? ' selected="selected"' : '' !!}>stigende</option>
                <option value="desc"{!! ('desc' == $sortDir) ? ' selected="selected"' : '' !!}>synkende</option>
            </select>


            <button type="submit" class="btn btn-primary">Go!</button>
            </div>
        </form>
    </div>

    <p>
        {{ $docs->total() }} documents found.
    </p>

    <table class="table table-condensed table-hover" style="background:white; font-size:80%;">
        <tr>
            @foreach($show as $field)
            <th class="text-nowrap">
                @if ($field == $sort)
                    <span class="glyphicon glyphicon-sort-by-alphabet"></span>
                @endif
                {{ $field }}
            </th>
            @endforeach
        </tr>
        @foreach ($docs as $doc)
        <tr style="clear:both;" class="clickable-row" data-href="{{ action('DocumentsController@show', $doc->mms_id) }}">
            @foreach($show as $field)
            <td>
                {{ $doc->{$field} }}
            </td>
            @endforeach
        </tr>
        @endforeach
    </table>

    <div class="text-center">
        {{ $docs->appends($request->all())->links() }}
    </div>

@endsection

@section('scripts')

    <script type="text/javascript">

        // Clickable rows
        $(document).ready(function() {
            $('.clickable-row').click(function() {
                window.location = $(this).data("href");
            });
        });

        // Quick filter
        $(function() {
            var idx = $('#inp1').parent().children().length;
            $('#addstmt').on('click', function () {
                idx++;
                console.log('::', idx);
                var tmp = $('#inp1').clone();
                tmp.find('button').remove();
                tmp.find('.dropdown-menu').remove();
                tmp.attr('id', 'inp' + idx)
                tmp.find('.field').attr('name', 'k' + idx)[0].selectedIndex = 0;
                tmp.find('.relation').attr('name', 'r' + idx)[0].selectedIndex = 0;
                tmp.find('.value').attr('name', 'v' + idx).val('');
                $('#inp1').parent().append(tmp);

                $('.selectpicker').selectpicker();
            });
        });

        // Enrich : TODO: Move to PHP
        /*
        $(function() {
            $('.biblio').each(function() {
                var isbn = $(this).data('isbn');
                var img = $(this).find('img');
                if (isbn) {
                    var tmp = new Image();
                    var bs_url = 'http://innhold.bibsys.no/bilde/forside/?size=mini&id=' + isbn + '.jpg';
                    // console.log(bs_url);
                    tmp.onload= function() {
                        if (this.naturalHeight + this.naturalWidth === 0) {
                            this.onerror();
                        } else {
                            console.log('Image loaded:', bs_url);
                            img.attr('src', bs_url);
                        }
                    };
                    tmp.onerror = function() {
                        console.log('Image failed: ', bs_url);
                        $.getJSON('https://www.googleapis.com/books/v1/volumes?q=isbn:' + isbn, function(gbooks) {
                            console.log(gbooks);
                            if (gbooks.items && gbooks.items.length > 0) {
                                var volume = gbooks.items[0].volumeInfo;
                                if (volume.imageLinks && volume.imageLinks.smallThumbnail) {
                                    var cover = gbooks.items[0].volumeInfo.imageLinks.smallThumbnail;
                                    console.log('Got cover: ', cover);
                                    img.attr('src', cover);
                                }
                            }
                        });
                    };
                    tmp.src = bs_url;
                }
            });
        });
         */
    </script>

@endsection
