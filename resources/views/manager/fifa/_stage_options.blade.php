@php $stages = ['Group Stage', 'Round of 32', 'Round of 16', 'Quarter-final', 'Semi-final', 'Third Place', 'Final']; @endphp
<select name="stage" class="wp-input" @isset($selectId) id="{{ $selectId }}" @endisset required>
    @foreach($stages as $s)
        <option value="{{ $s }}">{{ $s }}</option>
    @endforeach
</select>
