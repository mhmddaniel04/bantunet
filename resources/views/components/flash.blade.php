{{-- Shows session('success') / session('error') as a toast --}}
@foreach (['success', 'error'] as $type)
    @if (session($type))
        <div class="toast toast--{{ $type }}" role="status" data-toast>
            <span>{{ session($type) }}</span>
            <button type="button" class="toast__close" data-toast-close aria-label="Close">&times;</button>
        </div>
    @endif
@endforeach