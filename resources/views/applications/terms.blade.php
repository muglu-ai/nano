@extends('layouts.application')

@section('title', 'Onboarding Form')
@section('content')
    <main class="mn-inner2">
        <div class="row">
            {{--            <div class="col s12">--}}
            {{--                <div class="page-title">@yield('title')</div>--}}
            {{--            </div>--}}
            <div class="col s12 m12 l12">
                <div class="card">
                    <div class="card-content">

                        <div class="container">
                            <div class="row">
                                <div class="col s12">
                                    <ul class="tabs">
                                        <li class="tab col s3"><a href="#"> Personal Info</a></li>
                                        <li class="tab col s3"><a href="#"> Product Info </a></li>
                                        <li class="tab col s3"><a href="#" class="active waves-effect waves-teal"> Terms
                                                and Conditions </a></li>
                                        <li class="tab col s3"><a href="#"> Review</a></li>
                                    </ul>
                                </div>
                            </div>
                            <h1>Terms and Conditions</h1>
                            <p>Please read the terms and conditions carefully.</p>

                            <!-- Add your terms and conditions text here -->
                            <form action="{{ route('terms.store') }}" method="POST">
                                @csrf

                                <section>
                                    <div class="form-check">
                                        <div class="wizard-content">
                                            <div class="wizardTerms">
                                                Lorem ipsum dolor sit amet, consectetur adipiscing
                                                elit. Morbi vel risus elit. Nunc tempor velit dui, sed
                                                gravida urna posuere in. Cras sollicitudin urna at
                                                sapien vestibulum commodo quis eget tellus. Nam
                                                dapibus fringilla nulla, ac interdum velit. Vestibulum
                                                ante ipsum primis in faucibus orci luctus et ultrices
                                                posuere cubilia Curae; Phasellus at enim lectus.
                                                Phasellus commodo, massa vel congue fermentum, ligula
                                                erat egestas turpis, at tempor tellus nulla at nunc.
                                                Proin in ornare diam. Proin egestas sodales dolor at
                                                rutrum. Suspendisse eu ipsum feugiat, sollicitudin mi
                                                eu, tincidunt nibh. Etiam et orci nulla. Sed
                                                condimentum orci vel maximus egestas.<br/><br/>Donec
                                                malesuada urna sed orci venenatis ultricies nec eu
                                                enim. Vestibulum accumsan iaculis ligula, ac semper
                                                risus feugiat ut. Suspendisse tincidunt iaculis ante
                                                at eleifend. Maecenas ac nulla varius, vehicula tellus
                                                vitae, placerat ipsum. Suspendisse nunc nibh,
                                                efficitur non mollis in, pulvinar volutpat metus.
                                                Nulla sit amet tortor vestibulum, porttitor dui sed,
                                                porta ex. Mauris at justo in sapien semper efficitur
                                                quis eget orci. Donec pellentesque leo sit amet dui
                                                pharetra condimentum. Nullam eleifend tempor augue,
                                                non rutrum nibh tristique non. Nullam eget
                                                pellentesque nisi. Aenean nibh ipsum, suscipit id
                                                imperdiet vitae, sodales et lacus. Cum sociis natoque
                                                penatibus et magnis dis parturient montes, nascetur
                                                ridiculus mus. Sed at ex turpis. Donec tempor molestie
                                                leo eget rutrum. Suspendisse quis nunc a nibh luctus
                                                cursus. Fusce vel varius nibh.<br/><br/>Duis dapibus
                                                consequat iaculis. Maecenas fringilla velit ligula,
                                                non mattis enim vehicula ut. Suspendisse ipsum ante,
                                                pellentesque quis auctor eu, ullamcorper ac ligula.
                                                Morbi laoreet consectetur leo. Nam lacus felis,
                                                feugiat eget felis eget, lobortis dictum justo. Aenean
                                                congue magna at eros rutrum, ut volutpat risus porta.
                                                Vivamus arcu lectus, accumsan sit amet mauris ut,
                                                tristique sollicitudin sapien. Sed pulvinar feugiat
                                                justo, eu mattis sem consequat blandit. Duis blandit
                                                purus sit amet sem ornare accumsan. Donec ullamcorper
                                                ante enim, sed pretium odio ultricies ac. Duis nec
                                                sapien efficitur, faucibus erat ut, bibendum diam.
                                                Cras tempus mattis sapien eu feugiat. Aenean risus
                                                dui, semper eget velit in, ultrices convallis velit.
                                                Morbi a velit dictum, egestas orci eu, venenatis
                                                felis. Sed feugiat eros eget orci semper finibus.<br/><br/>Vivamus
                                                in metus lobortis, bibendum mauris dignissim, dapibus
                                                justo. Nunc tempor lacus dolor, nec venenatis neque
                                                scelerisque sed. Fusce quis est ac erat condimentum
                                                posuere. Pellentesque eleifend mauris dui, eu volutpat
                                                elit commodo id. Donec faucibus, enim nec luctus
                                                elementum, orci justo faucibus dui, ut porttitor
                                                lectus neque id leo. Proin viverra diam lacus, rutrum
                                                feugiat eros tempor eu. Sed et lorem eu lectus
                                                interdum aliquet. In pretium luctus arcu ut
                                                pellentesque. Nam faucibus posuere leo, in vehicula
                                                mauris vestibulum non.<br/><br/>Class aptent taciti
                                                sociosqu ad litora torquent per conubia nostra, per
                                                inceptos himenaeos. Aliquam fringilla efficitur sapien
                                                at volutpat. Vivamus nec enim est. Quisque sit amet ex
                                                non ex lobortis pulvinar vel id sapien. Vestibulum
                                                ante ipsum primis in faucibus orci luctus et ultrices
                                                posuere cubilia Curae; Aenean ut nisl ac ipsum
                                                suscipit consectetur. Vestibulum in sodales turpis,
                                                eget elementum ipsum. Suspendisse ac magna sed turpis
                                                porttitor efficitur quis ac dolor. Vestibulum ante
                                                ipsum primis in faucibus orci luctus et ultrices
                                                posuere cubilia Curae; Phasellus convallis gravida
                                                lacus nec efficitur. Mauris euismod ex accumsan,
                                                convallis ante non, varius dui. Nam nec quam feugiat
                                                justo rhoncus aliquam. Phasellus lectus nisl,
                                                tristique vitae pellentesque ut, faucibus id turpis.
                                            </div>
                                            @php
                                                $isDisabled = (isset($application) && ($application->submission_status == 'submitted' || $application->submission_status == 'approved' )  ) ? 'disabled' : '';
                                                 $checked = (isset($application) && ($application->submission_status == 'submitted' || $application->submission_status == 'approved' )) ? 'checked' : '';
                                                @endphp

                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="terms_accepted"
                                                       name="terms_accepted" value="1" required {{ $checked }} {{$isDisabled}}>
                                                <label class="form-check-label" for="terms_accepted">
                                                    I acknowledge that I have read the above terms and conditions
                                                    carefully.
                                                </label>
                                            </div>
                                            @if (isset($application) && ($application->submission_status == 'submitted' || $application->submission_status == 'approved' ) )
                                                <a href="{{ route('application.preview') }}"
                                                   class="btn">Next</a>
                                            @else
                                                <button type="submit" {{ $isDisabled }}>Submit</button>
                                            @endif
                                        </div>
                                    </div>
                                </section>


                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.querySelector('form').addEventListener('submit', function (event) {
            if (!document.getElementById('terms_accepted').checked) {
                event.preventDefault();
                alert('Please acknowledge that you have read the terms and conditions.');
            }
        });
    </script>
@endsection
