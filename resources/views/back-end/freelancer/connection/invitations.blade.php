@extends(file_exists(resource_path('views/extend/back-end/master.blade.php')) ? 'extend.back-end.master' : 'back-end.master')
@section('content')

    <section class="wt-haslayout wt-dbsectionspace" id="profile_settings">
        <div class="wt-dbsectionspace wt-haslayout la-ps-freelancer">
            <div class="freelancer-profile" id="user_profile">
                <div class="preloader-section" v-if="loading" v-cloak>
                    <div class="preloader-holder">
                        <div class="loader"></div>
                    </div>
                </div>
                @if (Session::has('message'))
                    <div class="alert alert-success">
                        {{ Session::get('message') }}
                    </div>
                @elseif (Session::has('error'))
                    <div class="alert alert-danger">
                        {{ Session::get('error') }}
                    </div>
                @endif
                <div class='wt-tabscontenttitle'>
                    <h2>Your Invitations</h2>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-9">
                        <div class="wt-dashboardbox wt-dashboardtabsholder">
                            <div class="wt-location wt-tabsinfo agency-selection-form">
                                <div class='wt-settingscontent'>
                                    <div class='wt-formtheme wt-userform agency-form'>
                                        @if(count($connections) < 1)
                                        <div>
                                            <br>
                                            <div class="alert alert-success" role="alert">
                                                No new invitations!
                                            </div>
                                        </div>
                                        @else
                                            @foreach($connections as $connection)
                                                <br>
                                                <div class="alert alert-warning" role="alert">
                                                    You have been invited to join -- <a target="_blank" href="{{ url('profile/'.$connection->user->slug) }}">{{ $connection->user->first_name }} {{ $connection->user->last_name }}</a> 
                                                </div>
                                                <div class="invitations-buttons">
                                                    <button onclick = "location.href='/accept-request/{{ $connection->id }}'" class="e-button e-button-primary">Accept</button>
                                                    <button onclick="location.href='/reject-request/{{ $connection->id }}'" class="e-button e-button-primary my-3">Decline</a></button>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
