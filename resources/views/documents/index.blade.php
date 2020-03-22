@extends('layouts.app')

@section('content')

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form class="form-horizontal card card-default"  method="GET" action="{{ action('DocumentsController@index') }}">

        <div class="card-body">

            <div>
                @foreach ($statements as $stmt)
                    <div class="d-flex" id="inp{{ $stmt['idx']  }}">
                        <div class="p-1">
                            @include('macros.selectbox', [
                                'name' => 'k' . $stmt['idx'],
                                'values' => $fields,
                                'selected' => $stmt['key'],
                                'class' => 'field selectpicker',
                                'searchable' => true,
                            ])
                        </div>
                        <div class="p-1">
                            @include('macros.selectbox', [
                                'name' => 'r' . $stmt['idx'],
                                'values' => $relations,
                                'selected' => $stmt['rel'],
                                'class' => 'relation selectpicker',
                            ])
                        </div>
                        <div class="p-1 flex-grow-1">
                            <input type="text" class="form-control value" name="v{{ $stmt['idx'] }}" value="{{ $stmt['val'] }}">
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="button" id="addstmt" class="btn btn-primary"> <span class="glyphicon glyphicon-plus"></span> </button>
            <a href="{{ action('DocumentsController@resetForm') }}" class="btn btn-warning">Reset</a>

        </div>
        <div class="card-footer">

            <label for="show" class="font-weight-bold">Show fields:</label>
            <select id="show" name="show[]" class="selectpicker" multiple data-selected-text-format="count">
                @foreach( $fields as $field)
                    <option value="{{ $field }}"{!! in_array($field, $show) ? ' selected="selected"' : '' !!}>{{ $field }}</option>
                @endforeach
            </select>

            <label for="sort" class="font-weight-bold">Sort by:</label>
            <select id="sort" name="sort" class="selectpicker">
                @foreach( $fields as $field)
                    <option value="{{ $field }}"{!! ($field == $sort) ? ' selected="selected"' : '' !!}>{{ $field }}</option>
                @endforeach
            </select>
            <select id="sortDir" name="sortDir" class="selectpicker">
                <option value="asc"{!! ('asc' == $sortDir) ? ' selected="selected"' : '' !!}>stigende</option>
                <option value="desc"{!! ('desc' == $sortDir) ? ' selected="selected"' : '' !!}>synkende</option>
            </select>


            <button type="submit" class="btn btn-primary">Search</button>
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
        {{ $docs->links() }}
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

        // Check the state of the relation dropdown menus. For menus set to "is null" or "not null",
        // hide and disable (so no value is submitted) the corresponding input fields.
        function checkRelations(evt) {
            $('select.relation').each(function(idx, relation) {
                let targetIndex = relation.name.substr(1),
                    $target = $(relation),
                    selectedRelation = $target.val(),
                    $inputField = $(`input[name="v${targetIndex}"]`);
                if (selectedRelation == 'nu' || selectedRelation == 'nn') {
                    $inputField.attr("disabled", true).hide();
                } else {
                    $inputField.attr("disabled", false).show();
                }

            });
        }

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

                tmp.find('.selectpicker').selectpicker();

                tmp.find('select.relation').on('change', checkRelations);

            });

            $('select.relation').on('change', checkRelations);
            checkRelations();
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
