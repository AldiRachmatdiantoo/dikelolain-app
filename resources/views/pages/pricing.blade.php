@extends('layouts.app')

@section('title', 'Dikelolain - Pricing')

@section('content')

    <!-- Page Header -->
    <x-vcms.settings :keys="['pricing.header.title', 'pricing.header.breadcrumb']" label="Header">
        <x-page-header :title="$settings->get('pricing.header.title')">
            <li class="breadcrumb-item active" aria-current="page">{{ $settings->get('pricing.header.breadcrumb') }}</li>
        </x-page-header>
    </x-vcms.settings>

    <!-- Page Pricing Start -->
    <div class="page-pricing">
        <div class="container">
            <div class="row">
                @foreach ($plans as $plan)
                    <div class="col-lg-4 col-md-6">
                        <!-- Pricing Box Start -->
                        <x-vcms.item resource="pricing-plans" :item="$plan" :fields="$planFields" label="paket"
                            class="pricing-box {{ $plan->is_featured ? 'highlighted-box' : '' }} wow fadeInUp {{ $plan->is_active ? '' : 'vcms-hidden' }}" :data-wow-delay="$loop->first ? null : (0.2 * $loop->index) . 's'">
                            <div class="pricing-header">
                                <h3>{{ $plan->name }}</h3>
                                <h2>{{ $plan->price }} @if (filled($plan->period))<sub>{{ $plan->period }}</sub>@endif</h2>
                            </div>

                            <div class="pricing-body">
                                {{-- One title for every plan: editable on the first box only. --}}
                                @if ($loop->first)
                                    <x-vcms.settings :keys="['pricing.list_title']" label="Judul" class="pricing-list-title">
                                        <h3>{{ $settings->get('pricing.list_title') }}</h3>
                                    </x-vcms.settings>
                                @else
                                    <div class="pricing-list-title">
                                        <h3>{{ $settings->get('pricing.list_title') }}</h3>
                                    </div>
                                @endif

                                <div class="pricing-list">
                                    <ul>
                                        @foreach ($plan->features as $feature)
                                            <x-vcms.item resource="pricing-features" :item="$feature" :fields="$featureFields" label="fitur" tag="li"
                                                class="{{ $feature->is_active ? '' : 'vcms-hidden' }}">{{ $feature->text }}</x-vcms.item>
                                        @endforeach
                                    </ul>
                                    <x-vcms.add resource="pricing-features" :fields="$featureFields" label="fitur" :defaults="['pricing_plan_id' => $plan->id]" />
                                </div>
                            </div>

                            @if (filled($plan->button_text))
                                <div class="pricing-btn">
                                    <a href="{{ $plan->button_link ?: '#' }}" class="btn-default btn-highlighted">{{ $plan->button_text }}</a>
                                </div>
                            @endif
                        </x-vcms.item>
                        <!-- Pricing Box End -->
                    </div>
                @endforeach

                <div class="col-lg-12">
                    <x-vcms.add resource="pricing-plans" :fields="$planFields" label="paket" />
                </div>

                <div class="col-lg-12">
                    <!-- Pricing Benefit List Start -->
                    <div class="pricing-benefit-list wow fadeInUp" data-wow-delay="0.6s">
                        <ul>
                            @foreach ($benefits as $benefit)
                                <x-vcms.item resource="page-list-items" :item="$benefit" :fields="$benefitFields" label="keuntungan" tag="li"
                                    class="{{ $benefit->is_active ? '' : 'vcms-hidden' }}">@if (filled($benefit->icon))<img src="{{ $benefit->icon_url }}" alt="">@endif{{ $benefit->text }}</x-vcms.item>
                            @endforeach
                        </ul>
                        <x-vcms.add resource="page-list-items" :fields="$benefitFields" label="keuntungan" :defaults="['list_key' => 'pricing_benefits']" />
                    </div>
                    <!-- Pricing Benefit List End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Page Pricing End -->

    <!-- Creative Tools Section Start -->
    @include('partials.creative-tools', ['class' => 'service-creative-tools'])
    <!-- Creative Tools Section End -->

@endsection

@push('scripts')
@endpush
