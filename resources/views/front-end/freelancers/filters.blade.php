<aside id="wt-sidebar" class="wt-sidebar wt-usersidebar">
    {!! Form::open(['url' => url('search-results'), 'method' => 'get', 'class' => 'wt-formtheme wt-formsearch wt-formbackground']) !!}
        <input type="hidden" value="{{$type}}" name="type">
        <div class="wt-widget wt-effectiveholder wt-startsearch" style=" margin: 0px; ">
            <div class="wt-widgettitle">
                <h2>{{ trans('lang.start_search') }}</h2>
            </div>
            <div class="wt-widgetcontent">
                <span  onclick="myListFunction()"><i id="icon-list" class="fas fa-list"></i></span>
                <span onclick="myGridFunction()"><i id="icon-grid" class="fas fa-th"></i></span>
                
            </div>
        </div>
       
        <div class="wt-widget wt-effectiveholder">
            <div class="wt-widgettitle">
                <h2>{{ trans('lang.cats') }}</h2>
            </div>
            <div class="wt-widgetcontent">
                <fieldset>
                    <div class="form-group">
                        <input type="text" class="form-control filter-records" placeholder="{{ trans('lang.ph_search_cat') }}">
                        <a href="javascrip:void(0);" class="wt-searchgbtn"><i class="lnr lnr-magnifier"></i></a>
                    </div>
                </fieldset>
                <fieldset>
                    @if (!empty($categories))
                        <div class="wt-checkboxholder wt-verticalscrollbar">
                            @foreach($categories as $category)
                                @php $checked = ( !empty($_GET['category']) && in_array($category->id, $_GET['category'] )) ? 'checked' : ''; @endphp
                                <span class="wt-checkbox">
                                    
                                    <input id="cat-{{{ $category->slug }}}" type="checkbox" name="category[]" value="{{{ $category->slug }}}" {{$checked}} >
                                  <label for="cat-{{{ $category->slug }}}"> <a href="{{ url('hire/'.$category->slug) }}"  >{{{ $category->title }}}</a></label>
                                </span>
                            @endforeach
                        </div>
                    @endif
                </fieldset>
            </div>
        </div>
        
        
        <div class="wt-widget wt-effectiveholder">
            <div class="wt-widgettitle">
                <h2>{{ trans('lang.skills') }}</h2>
            </div>
            <div class="wt-widgetcontent">
                <fieldset>
                    @if (!empty($skills))
                        <div class="wt-checkboxholder wt-verticalscrollbar">
                            @foreach ($skills as $key => $skill)
                                @php $checked = ( !empty($_GET['skills']) && in_array($skill->slug, $_GET['skills'])) ? 'checked' : '' @endphp
                                <span class="wt-checkbox">
                                    <input id="skill-{{{ $skill->slug }}}" type="checkbox" name="skill[]" value="{{{ $skill->slug }}}" {{$checked}} data-category="{{{ $skill->category_slug }}}">

                                    <label for="skill-{{{ $key }}}"><a href="{{ url('hire/'.$skill->slug) }}"  >{{{ $skill->title }}}</a></label>
                                </span>
                            @endforeach
                        </div>
                    @endif
                </fieldset>
            </div>
        </div>
        <div class="wt-widget wt-effectiveholder">
            <div class="wt-widgettitle">
                <h2>{{ trans('lang.location') }}</h2>
            </div>
            <div class="wt-widgetcontent">
                <fieldset>
                    <div class="form-group">
                        <input type="text" class="form-control filter-records" placeholder="{{ trans('lang.search_loc') }}">
                        <a href="javascrip:void(0);" class="wt-searchgbtn"><i class="lnr lnr-magnifier"></i></a>
                    </div>
                </fieldset>
                <fieldset>
                    @if (!empty($locations))
                        <div class="wt-checkboxholder wt-verticalscrollbar">
                            @foreach ($locations as $location)
                                @php $checked = ( !empty($_GET['locations']) && in_array($location->slug, $_GET['locations'])) ? 'checked' : '' @endphp
                                <span class="wt-checkbox">
                                    <input id="location-{{{ $location->slug }}}" type="checkbox" name="locations[]" value="{{{$location->slug}}}" {{$checked}} >
                                    <label for="location-{{{ $location->slug }}}">
                                        <a href="{{ url('hire/'.$location->slug) }}" class="anchor-content-center" > 
                                            <div><img src="{{{asset(App\Helper::getLocationFlag($location->flag))}}}" alt="{{ trans('lang.img') }}" style="width:25px;height:18px"> </div>
                                            <div class="location-name"> {{{ $location->title }}}</div>
                                        </a>
                                    </label>
                                </span>
                            @endforeach
                        </div>
                    @endif
                </fieldset>
            </div>
        </div>
        <div class="wt-widget wt-effectiveholder">
            <div class="wt-widgettitle">
                <h2>{{{ trans('lang.hourly_rate') }}}</h2>
            </div>
            <div class="wt-widgetcontent">
                <div class="wt-formtheme wt-formsearch">
                    <fieldset>
                        <div class="form-group">
                            <input type="text" class="form-control filter-records" placeholder="{{ trans('lang.ph_search_rate') }}">
                            <a href="javascrip:void(0);" class="wt-searchgbtn"><i class="lnr lnr-magnifier"></i></a>
                        </div>
                    </fieldset>
                    <fieldset>
                        <div class="wt-checkboxholder wt-verticalscrollbar">
                            @foreach (Helper::getHourlyRate() as $key => $hourly_rate)
                                @php $checked = ( !empty($_GET['hourly_rate']) && in_array($key, $_GET['hourly_rate'])) ? 'checked' : '' @endphp
                                <span class="wt-checkbox">
                                    <input id="rate-{{ $key }}" type="checkbox" name="hourly_rate[]" value="{{ $key }}" {{ $checked }}>
                                    <label for="rate-{{ $key }}"><a href="{{ url('hire/'.$key) }}"  >{{ $hourly_rate }}</a></label>
                                </span>
                            @endforeach
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
        
        <div class="wt-widget wt-effectiveholder">
            <div class="wt-widgettitle">
                <h2>{{ trans('lang.freelancer_type') }}</h2>
            </div>
            <div class="wt-widgetcontent">
                <div class="wt-formtheme wt-formsearch">
                    <div class="wt-checkboxholder wt-verticalscrollbar">
                        @php $i = 1; @endphp
                        @foreach (Helper::getFreelancerLevelList() as $freelancer_level => $freelancer)
                        @php $checked = ( !empty($_GET['freelaner_type']) && in_array($freelancer, $_GET['freelaner_type'])) ? 'checked' : '' @endphp
                            <span class="wt-checkbox">
                                <input id="freelancer-{{  $freelancer_level.$i  }}" type="checkbox" name="freelaner_type[]" value="{{ $freelancer }}" {{ $checked }}>
                                <label for="freelancer-{{ $freelancer_level.$i++ }}"><a href="{{ url('hire/'.$freelancer) }}"  >{{ $freelancer }}</a></label>
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="wt-widget wt-effectiveholder">
            <div class="wt-widgettitle">
                <h2>{{ trans('lang.english_level') }}</h2>
            </div>
            <div class="wt-widgetcontent">
                <div class="wt-formtheme wt-formsearch">
                    <fieldset>
                        <div class="wt-checkboxholder wt-verticalscrollbar">
							@php $i = 1; @endphp
                            @foreach (Helper::getEnglishLevelList() as $key => $english_level)
                                @php $checked = ( !empty($_GET['english_level']) && in_array($key, $_GET['english_level'])) ? 'checked' : '' @endphp
                                <span class="wt-checkbox">
                                    <input id="englishlevel-{{ $key.$i }}" type="checkbox" name="english_level[]" value="{{ $key }}" {{ $checked }}>
                                    <label for="englishlevel-{{ $key.$i++ }}"><a href="{{ url('hire/'.$key) }}"  >{{ $english_level }}</a></label>
                                </span>
                            @endforeach
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
        {{--
        <div class="wt-widget wt-effectiveholder">
            <div class="wt-widgettitle">
                <h2>{{ trans('lang.langs') }}</h2>
            </div>
            <div class="wt-widgetcontent">
                <fieldset>
                    <div class="form-group">
                        <input type="text" class="form-control filter-records" placeholder="{{ trans('lang.ph_search_langs') }}">
                        <a href="javascrip:void(0);" class="wt-searchgbtn"><i class="lnr lnr-magnifier"></i></a>
                    </div>
                </fieldset>
                <fieldset>
                    @if (!empty($languages))
                        <div class="wt-checkboxholder wt-verticalscrollbar">
                            @foreach ($languages as $language)
                                @php $checked = ( !empty($_GET['languages']) && in_array($language->slug, $_GET['languages'])) ? 'checked' : '' @endphp
                                <span class="wt-checkbox">
                                    <input id="language-{{{ $language->slug }}}" type="checkbox" name="languages[]" value="{{{$language->slug}}}" {{$checked}} >
                                    <label for="language-{{{ $language->slug }}}">{{{ $language->title }}}</label>
                                </span>
                            @endforeach
                        </div>
                    @endif
                </fieldset>
            </div>
        </div>
        --}}
        <div class="wt-widget wt-effectiveholder">
            <div class="wt-widgetcontent">
                <div class="wt-applyfilters">
                    <span>{{ trans('lang.apply_filter') }}<br> {{ trans('lang.changes_by_you') }}</span>
                    {!! Form::submit(trans('lang.btn_apply_filters'), ['class' => 'wt-btn']) !!}
                </div>
            </div>
        </div>
    {!! form::close(); !!}
</aside>

<script type="application/javascript">
function myListFunction() {
    document.getElementById("grid-layout").style.display = "none";
    document.getElementById("list-layout").style.display = "block";
    document.getElementById("icon-grid").style.color = "#ffffff8a";
    document.getElementById("icon-list").style.color = "#ffffff";
}

function myGridFunction() {
    document.getElementById("list-layout").style.display = "none";
    document.getElementById("grid-layout").style.display = "block";
    document.getElementById("icon-grid").style.color = "#ffffff";
    document.getElementById("icon-list").style.color = "#ffffff8a";
}
</script>