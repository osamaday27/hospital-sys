<div class="mt-3">
    <div class="d-flex align-items-center mb-2">
        <div class="flex-shrink-0 me-1">
            <i class="bx bx-layer fs-24 align-middle me-1" style="color:#0c5a97;"></i>
        </div>
        <div class="flex-grow-1">
            <h5 class="fs-16 mb-0 fw-semibold">{{ $title }}</h5>
        </div>
    </div>

    <div class="row mt-3">
        @foreach ($cards as $card)
            <div class="col-md-4">
                <a href="{{ url($card['link']) }}">
                    <div class="card card-body text-center">
                        <div class="avatar-md mx-auto mb-3" style="background-color: #afc7ff57; border-radius: 10%;">
                            <div class="avatar-title p-2 text-success rounded" style="background-color: #afc7ff57; border-radius: 10%;">
                                <lord-icon 
                                    src="{{ URL::asset('assets/images/icon/' . $card['icon']) }}" 
                                    trigger="hover" 
                                    delay="2000"
                                    colors="primary:#0c5a97,secondary:#08a88a,tertiary:#104891,quaternary:#104891"
                                    style="width:200px; height:200px">
                                </lord-icon>
                            </div>
                        </div>
                        <h4 class="card-title font">{{ $card['text'] }}</h4>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>
