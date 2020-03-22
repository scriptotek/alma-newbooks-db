<select name="{{ $name }}" class="{{ $class ?? '' }}" data-live-search="{{ $searchable ?? 'false' }}">
    @foreach ($values as $key => $val)
    <option value="{{ $key }}"{!! ($selected == $key) ? ' selected="selected"' : '' !!}>{{ $val }}</option>
    @endforeach
</select>
