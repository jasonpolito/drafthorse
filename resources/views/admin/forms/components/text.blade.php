<input wire:model.defer="{{ $getStatePath() }}" type="text" value="text">
@push('scripts')
    <script>
        @this.set("{{ $getStatePath() }}", 'f4f434334');
    </script>
@endpush
