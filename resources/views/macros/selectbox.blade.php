<select name="{{ $name }}" class="form-control {{ $class or '' }}" data-live-search="{{ $searchable or 'false' }}">
    @foreach ($values as $key => $val)
    <option value="{{ $key }}"{!! ($selected == $key) ? ' selected="selected"' : '' !!}>{{ $val }}</option>
    @endforeach
</select>
