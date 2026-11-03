@extends('admin.layouts.admin')

@section('title', 'Pesan Masuk')
@section('page-title', 'Pesan Masuk')

@section('content')
<div class="container-fluid" style="padding-left: 15px; padding-right: 15px;">

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

<style>
    .msg-yellow { color: #ffcc01 !important; }
    .msg-muted { color: #c9c9c9; }

    .msg-card {
        margin-bottom: 14px;
        padding: 18px;
        background-color: #212529;
        border: 1px solid #333;
        border-left: 4px solid #333;
        border-radius: 16px;
        color: #fff;
    }

    .msg-card.msg-unread { border-left-color: #ffcc01; }

    .msg-head {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 6px 14px;
        margin-bottom: 10px;
    }

    .msg-sender { min-width: 0; }
    .msg-sender h3 { margin: 0 0 2px; font-size: 17px; }
    .msg-sender p { margin: 0; font-size: 13px; word-break: break-word; }
    .msg-sender a { color: #ffcc01; text-decoration: none; }
    .msg-sender a:hover { text-decoration: underline; }
    .msg-sep { color: #8f8f8f; margin: 0 4px; }

    .msg-badge {
        display: inline-block;
        margin-left: 6px;
        padding: 2px 8px;
        border-radius: 999px;
        background-color: #ffcc01;
        color: #000;
        font-size: 11px;
        font-weight: 700;
        vertical-align: middle;
    }

    .msg-time { font-size: 13px; color: #8f8f8f; white-space: nowrap; }

    /* Keeps the visitor's line breaks; the text itself is escaped. */
    .msg-body { margin: 0 0 14px; white-space: pre-line; word-break: break-word; color: #e6e6e6; }

    .msg-actions { display: flex; flex-wrap: wrap; gap: 6px; }
    .msg-actions form { margin: 0; }

    .msg-btn { background-color: #ffcc01; border: 1px solid #ffcc01; color: #000; }
    .msg-btn:hover { background-color: #ffdd33; border-color: #ffdd33; color: #000; }

    .msg-btn-outline { background: transparent; border: 1px solid #ffcc01; color: #ffcc01; }
    .msg-btn-outline:hover { background-color: rgba(255, 204, 1, 0.15); color: #ffcc01; }

    .msg-btn-danger { background: transparent; border: 1px solid #dc3545; color: #ff6b6b; }
    .msg-btn-danger:hover { background-color: rgba(220, 53, 69, 0.15); color: #ff6b6b; }

    .msg-empty { text-align: center; color: #8f8f8f; padding: 40px 18px; }
    .msg-empty i { font-size: 32px; margin-bottom: 10px; }
</style>
@endsection
