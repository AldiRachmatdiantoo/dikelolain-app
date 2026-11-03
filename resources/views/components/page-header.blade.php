@props(['title'])

<div class="page-header bg-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="page-header-box">
                    <h1 class="text-anime-style-2" data-cursor="-opaque">{!! $title !!}</h1>
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">home</a></li>
                            {{ $slot }}
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
