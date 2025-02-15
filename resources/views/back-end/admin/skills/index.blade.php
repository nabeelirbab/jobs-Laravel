@extends(file_exists(resource_path('views/extend/back-end/master.blade.php')) ? 'extend.back-end.master' : 'back-end.master')
@section('content')
    <div class="skills-listing" id="skill-list">
        @if (Session::has('message'))
            <div class="flash_msg">
                <flash_messages :message_class="'success'" :time ='5' :message="'{{{ Session::get('message') }}}'" v-cloak></flash_messages>
            </div>
        @elseif (Session::has('error'))
            <div class="flash_msg">
                <flash_messages :message_class="'danger'" :time ='5' :message="'{{{ Session::get('error') }}}'" v-cloak></flash_messages>
            </div>
        @endif
        <section class="wt-haslayout wt-dbsectionspace la-admin-skills">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-4 float-left">
                    <div class="wt-dashboardbox">
                        <div class="wt-dashboardboxtitle">
                            <h2>{{{ trans('lang.add_skill') }}}</h2>
                        </div>
                        <div class="wt-dashboardboxcontent">
                            {!! Form::open([
                                'url' => url('admin/store-skill'), 'class' =>'wt-formtheme wt-formprojectinfo wt-formcategory',
                                'enctype' => 'multipart/form-data',
                                'id' => 'skills'
                                ])
                            !!}
                            <fieldset>
                                <div class="form-group">
                                    {!! Form::text( 'skill_title', null, ['class' =>'form-control'.($errors->has('skill_title') ? ' is-invalid' : ''),
                                    'placeholder' => trans('lang.ph_skill_title')] ) !!}
                                    <span class="form-group-description">{{{ trans('lang.desc') }}}</span>
                                    @if ($errors->has('skill_title'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('skill_title') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    {!! Form::text( 'skill_heading', null, ['class' =>'form-control'.($errors->has('skill_heading') ? ' is-invalid' : ''), 'placeholder' => 'Heading Text'] ) !!}
                                    <span class="form-group-description">{{{ 'Heading Text for skills' }}}</span>
                                    @if ($errors->has('skill_heading'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('skill_heading') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                   <!-- Category Dropdown -->
                                   <div class="form-group">
                                    {!! Form::label('category_id', 'Category') !!}
                                    {!! Form::select('categories[]', $categories, null, [
                                        'class' => 'form-control select2-multiple',
                                        'multiple' => 'multiple'
                                    ]) !!}
                                    @if ($errors->has('categories'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('categories') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                
                                
                                <div class="form-group">
                                    <label for="imageInput">Skill Logo</label>
                                    <input name="skill_logo" type="file" id="imageInput" accept="image/*" >
                                </div>
                                <div class="form-group">
                                    <label for="is_featured">Featured Skill</label>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <label class="col-md-6">
                                                <input type="radio" id="enable" name="is_featured" value="1"> Enabled
                                            </label>
                                            <label class="col-md-6">
                                                <input type="radio" id="disable" name="is_featured" value="0"> Disabled
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::textarea( 'skill_desc', null, ['class' => 'wt-tinymceblogeditor', 'id' => 'wt-tinymceeditor', 'placeholder' => trans('lang.ph_desc')] ) !!}
                                    <span class="form-group-description">{{{ trans('lang.cat_desc') }}}</span>
                                </div>
                                <div class="form-group wt-btnarea">
                                    {!! Form::submit(trans('lang.add_skill'), ['class' => 'wt-btn']) !!}
                                </div>
                            </fieldset>
                            {!! Form::close(); !!}
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-8 float-right">
                    <div class="wt-dashboardbox">
                        <div class="wt-dashboardboxtitle wt-titlewithsearch">
                            <h2>{{{ trans('lang.skills') }}}</h2>
                            {!! Form::open(['url' => url('admin/skills/search'), 'method' => 'get', 'class' => 'wt-formtheme wt-formsearch',  'style' => 'max-width: 500px; display:flex']) !!}
                            <fieldset>
                                <div class="form-group">
                                    {!! Form::select('category_id', $categories, null, ['class' => 'form-control', 'placeholder' => 'Select Category']) !!}
                                </div>
                            </fieldset>
                            <fieldset>
                                <div class="form-group">
                                    <input type="text" name="keyword" value="{{{ !empty($_GET['keyword']) ? $_GET['keyword'] : '' }}}"
                                        class="form-control" placeholder="{{{ trans('lang.ph_search_skills') }}}">
                                    <button type="submit" class="wt-searchgbtn"><i class="lnr lnr-magnifier"></i></button>
                                </div>
                            </fieldset>
                            {!! Form::close() !!}
                            <a href="javascript:void(0);" v-if="this.is_show" @click="deleteChecked('{{ trans('lang.ph_delete_confirm_title') }}', '{{ trans('lang.ph_skill_delete_message') }}')" class="wt-skilldel">
                                <i class="lnr lnr-trash"></i>
                                <span>{{ trans('lang.del_select_rec') }}</span>
                            </a>
                        </div>
                        @if ($skills->count() > 0)
                            <div class="wt-dashboardboxcontent wt-categoriescontentholder">
                                <table class="wt-tablecategories" id="checked-val">
                                    <thead>
                                        <tr>
                                            <th>
                                                <span class="wt-checkbox">
                                                    <input name="skills[]" id="wt-skills" @click="selectAll" type="checkbox" name="head">
                                                    <label for="wt-skills"></label>
                                                </span>
                                            </th>
                                            
                                            <th>Logo</th>
                                            <th>{{{ trans('lang.name') }}}</th>
                                            <th>{{{ trans('lang.slug') }}}</th>
                                            <th>Category</th>
                                            <th>{{{ trans('lang.action') }}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $counter = 0; @endphp
                                        @foreach ($skills as $skill)
                                            <tr class="del-{{{ $skill->id }}}" v-bind:id="skillID">
                                                <td>
                                                    <span class="wt-checkbox">
                                                        <input name="skills[]" @click="selectRecord" value="{{{ $skill->id }}}" id="wt-check-{{{ $counter }}}" type="checkbox" name="head">
                                                        <label for="wt-check-{{{ $counter }}}"></label>
                                                    </span>
                                                </td>
                                                
                                                <td style="width:12%">@if($skill->logo)<img src="{{ asset('/uploads/logos/'.$skill->logo)}}" style="max-width: 60%">@endif</td>
                                                <td>{{{ $skill->title }}}</td>
                                                <td>{{{ $skill->slug }}}</td>
                                                <td> 
                                                    @if($skill->categories->count() > 0)
                                                    @foreach($skill->categories as $category)
                                                    {{ $category->title ?? 'No Category' }},
                                                    @endforeach
                                                    @else
                                                    No Category
                                                    @endif

                                                </td> 
                                                <td>
                                                    <div class="wt-actionbtn">
                                                        <a href="{{{ url('admin/skills/edit-skills') }}}/{{{ $skill->id }}}" class="wt-addinfo wt-skillsaddinfo">
                                                            <i class="lnr lnr-pencil"></i>
                                                        </a>
                                                        <delete :title="'{{trans("lang.ph_delete_confirm_title")}}'" :id="'{{$skill->id}}'" :message="'{{trans("lang.ph_skill_delete_message")}}'" :url="'{{url('admin/skills/delete-skills')}}'"></delete>
                                                    </div>
                                                </td>
                                            </tr>
                                            @php $counter++; @endphp
                                        @endforeach
                                    </tbody>
                                </table>
                                @if ( method_exists($skills,'links') )
                                    {{ $skills->links('pagination.custom') }}
                                @endif
                            </div>
                        @else
                            @if (file_exists(resource_path('views/extend/errors/no-record.blade.php'))) 
                                @include('extend.errors.no-record')
                            @else 
                                @include('errors.no-record')
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@push('stripe')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-multiple').select2();
    });
</script>
@endpush
