@props(['title', 'value', 'icon'])
<div class="col-xl-3 col-sm-6 col-12">
    <div class="card rounded" style="border: 2px solid; border-image: linear-gradient(90deg, #4f8cff, #34e89e) 1;">
        <div class="card-content">
            <div class="card-body">
                <div class="media d-flex">
                    <div class="align-self-center me-3">
                        <i class="fa fa-{{ $icon }} primary font-large-2 float-left"></i>
                    </div>
                    <div class="media-body text-right">


                            <h3>{{ $value }}</h3>

                        @if($title === "Today's Registrations")
                            <span class="text-muted">Today's Registrations  </span><br>
                            <small class="text-muted"> ({{ now()->format('d M Y') }})</small>
                        @else
                            <span class="text-muted">{{ $title }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
