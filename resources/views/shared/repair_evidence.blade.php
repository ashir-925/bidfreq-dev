<section class="sec" id="submitted-evidence">
    <div class="sec__head"><div class="sec__title">Submitted Evidence</div></div>
    <div class="sec__body">
        <dl>
            @if ($repair->property_name)<dt>Property</dt><dd>{{ $repair->property_name }}</dd>@endif
            <dt>Address</dt><dd>{{ $repair->street_address }}, {{ $repair->city }}, {{ $repair->state }} {{ $repair->zip }}</dd>
            <dt>Home size</dt><dd>{{ $repair->home_sqft }} sq ft · {{ $repair->stories }} {{ Str::plural('story', $repair->stories) }}</dd>
            <dt>Priority</dt><dd>{{ ucfirst($repair->priority) }}</dd>
            
        </dl>
        <h2>Repair description</h2>
        @foreach ($repair->descriptions as $item)
            <p><strong>{{ $item['trade'] }}</strong> · {{ $item['description'] }}</p>
        @endforeach
        @if ($repair->dimensions)
            <h2>Customer-provided damaged-area measurements</h2>
            <ul>@foreach ($repair->dimensions as $area)
                <li>{{ $area['label'] }}
                    @foreach (['length' => 'Length', 'width' => 'Width', 'height' => 'Height', 'area' => 'Area'] as $key => $label)
                        @if (isset($area[$key])) · {{ $label }} {{ $area[$key] }} {{ $key === 'area' ? 'sq ft' : 'ft' }} @endif
                    @endforeach
                </li>
            @endforeach</ul>
        @endif
        <h2>Attachments</h2>
        @php($imageRoute = str_replace('.download', '.image', $downloadRoute))
        @php($photos = $repair->files->whereIn('mime', \App\Models\RepairRequest::PHOTO_MIMES))
        @if ($photos->isNotEmpty())
            <div class="photo-grid">
                @foreach ($photos as $photo)
                    <a class="photo-tile" href="{{ route($imageRoute, [$repair, $photo]) }}" target="_blank" rel="noopener" aria-label="Open photo {{ $photo->name }}">
                        <img class="photo-tile__img" src="{{ route($imageRoute, [$repair, $photo]) }}" alt="{{ $photo->name }}" loading="lazy">
                        <span class="photo-tile__label">#{{ $photo->id }} · {{ $photo->name }}</span>
                    </a>
                @endforeach
            </div>
        @endif
        <ul>@foreach ($repair->files as $file)
            <li><a href="{{ route($downloadRoute, [$repair, $file]) }}">#{{ $file->id }} · {{ $file->name }}</a>
                @if ($file->trade) · {{ $file->trade }} @endif</li>
        @endforeach</ul>
    </div>
</section>
