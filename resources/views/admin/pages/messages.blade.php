@extends('admin.layouts.admin')

@section('title', 'Pesan Masuk')
@section('page-title', 'Pesan Masuk')

@section('content')
<div class="container-fluid admin-fluid">

    <div class="mb-4">
        <h2 class="mb-1 fw-bold msg-yellow">Pesan Masuk</h2>
        <p class="mb-0 msg-muted">Pesan dari form di halaman Contact. Setelah mengirim, pengunjung juga diarahkan ke WhatsApp dengan isi yang sama.</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success py-2">{{ session('status') }}</div>
    @endif

    @forelse ($messages as $message)
        <div class="msg-card {{ $message->read_at ? '' : 'msg-unread' }}">
            <div class="msg-head">
                <div class="msg-sender">
                    <h3>
                        {{ $message->name }}
                        @unless ($message->read_at)
                            <span class="msg-badge">Baru</span>
                        @endunless
                    </h3>
                    <p>
                        <a href="mailto:{{ $message->email }}">{{ $message->email }}</a>
                        <span class="msg-sep">·</span>
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $message->phone) }}">{{ $message->phone }}</a>
                    </p>
                </div>
                <time class="msg-time" datetime="{{ $message->created_at->toIso8601String() }}">{{ $message->created_at->format('d M Y, H:i') }}</time>
            </div>

            @if (filled($message->message))
                <p class="msg-body">{{ $message->message }}</p>
            @else
                <p class="msg-body msg-muted fst-italic">(tanpa isi pesan)</p>
            @endif

            <div class="msg-actions">
                @if ($message->whatsapp_url)
                    <a href="{{ $message->whatsapp_url }}" target="_blank" rel="noopener" class="btn btn-sm fw-bold msg-btn">
                        <i class="fa-brands fa-whatsapp me-1"></i> Balas via WhatsApp
                    </a>
                @endif
                <form action="{{ route('admin.messages.update', $message->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="read" value="{{ $message->read_at ? 0 : 1 }}">
                    <button type="submit" class="btn btn-sm msg-btn-outline">
                        <i class="fa-solid {{ $message->read_at ? 'fa-envelope' : 'fa-envelope-open' }} me-1"></i>
                        {{ $message->read_at ? 'Tandai belum dibaca' : 'Tandai sudah dibaca' }}
                    </button>
                </form>
                <form action="{{ route('admin.messages.destroy', $message->id) }}" method="POST" onsubmit="return confirm(@js('Hapus pesan dari ' . $message->name . '?'));">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm msg-btn-danger" title="Hapus pesan">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="msg-card msg-empty">
            <i class="fa-regular fa-envelope"></i>
            <p class="mb-0">Belum ada pesan.</p>
        </div>
    @endforelse

    <div class="mt-3">
        {{ $messages->links('pagination::bootstrap-5') }}
    </div>
</div>

@endsection
