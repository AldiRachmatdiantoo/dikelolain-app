{{--
    One project card (Home, Portfolio, slider experiment). The arrow, and the image of a design project,
    open the project modal in layouts/app. Needs $project and $projectFields; optional $delay (seconds),
    $movable (default true), $animate (wow fade-in, default true) and $ratioImage (crop designs like videos, Home).
--}}
@php
    $delay ??= 0;
    $movable ??= true;
    $animate ??= true;
    $ratioImage ??= false;
    $modalData = [
        'title' => $project->title,
        'desc' => $project->description,
        'link' => $project->link,
        'category' => $project->category_label,
        'media-type' => $project->media_type,
        'media-src' => $project->media_url,
    ];
@endphp
<x-vcms.item resource="projects" :item="$project" :fields="$projectFields" label="project" :movable="$movable"
    class="work-item {{ $animate ? 'wow fadeInUp' : '' }} {{ $project->is_active ? '' : 'vcms-hidden' }}" :data-wow-delay="$animate && $delay > 0 ? $delay . 's' : null">
    <div class="work-image">
        @if ($project->isVideo())
            <div class="ratio" style="--bs-aspect-ratio: 76%; border-radius: 30px; overflow: hidden;">
                @if ($project->embed_url)
                    <iframe src="{{ $project->embed_url }}" title="{{ $project->title }}"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
                    </iframe>
                @endif
            </div>
        @elseif ($ratioImage)
            <div class="ratio" style="--bs-aspect-ratio: 76%; border-radius: 30px; overflow: hidden;">
                <figure class="image-anime">
                    <img src="{{ $project->image_url }}" alt="{{ $project->title }}">
                </figure>
            </div>
        @else
            <a href="javascript:void(0)" class="open-portfolio-modal" data-cursor-text="View" @foreach ($modalData as $name => $value) data-{{ $name }}="{{ $value }}" @endforeach>
                <figure class="image-anime">
                    <img src="{{ $project->image_url }}" alt="{{ $project->title }}">
                </figure>
            </a>
        @endif
    </div>
    <div class="work-body">
        <div class="work-content">
            <h3><a href="javascript:void(0)" class="portfolio-title">{{ $project->title }}</a></h3>
            @if (filled($project->description))
                <p>{{ $project->description }}</p>
            @endif
        </div>
        <div class="work-btn">
            <a href="javascript:void(0)" class="open-portfolio-modal" @foreach ($modalData as $name => $value) data-{{ $name }}="{{ $value }}" @endforeach>
                <img src="{{ asset('images/arrrow-light.svg') }}" alt="Arrow">
            </a>
        </div>
    </div>
</x-vcms.item>
