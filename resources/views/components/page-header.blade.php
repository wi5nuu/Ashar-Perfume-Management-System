@props([
    'title',
    'subtitle' => null,
    'icon' => null,
    'breadcrumbs' => [],
])

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    @if($icon)
                        <i class="{{ $icon }} mr-2"></i>
                    @endif
                    {{ $title }}
                </h1>
                @if($subtitle)
                    <p class="text-muted mt-1">{{ $subtitle }}</p>
                @endif
            </div>
            <div class="col-sm-6">
                @if(count($breadcrumbs) > 0)
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> Dashboard</a></li>
                        @foreach($breadcrumbs as $breadcrumb)
                            @if($loop->last)
                                <li class="breadcrumb-item active">{{ $breadcrumb['label'] }}</li>
                            @else
                                <li class="breadcrumb-item"><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['label'] }}</a></li>
                            @endif
                        @endforeach
                    </ol>
                @endif
            </div>
        </div>
    </div>
</div>
