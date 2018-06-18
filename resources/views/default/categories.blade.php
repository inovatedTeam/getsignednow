@extends('app')

@section('title'){{ trans('misc.categories').' - ' }}@endsection

@section('content') 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/css/bootstrap-select.min.css">
    <div class="jumbotron md index-header jumbotron_set jumbotron-cover">
        <div class="container wrap-jumbotron position-relative">
            <h2 class="title-site">{{ trans('misc.categories') }}</h2>
            <p class="subtitle-site"><strong>{{trans('misc.browse_by_category')}}</strong></p>
        </div>
    </div>
    <div class="container">
        <div class="form-row">
            <div class="form-group col-md-3">
                <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
              <select id="network_id" name="network_id" class="form-control selectpicker" multiple data-live-search="true">
                <option value="">All Networks</option>
                @foreach ($data['networks'] as $network)
                    <option value="{{$network->id}}">{{$network->network_type}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group col-md-3">
              <select id="category_id" name="category_id" class="form-control selectpicker" multiple data-live-search="true">
                <option value="">All Categories</option>
                @foreach ($data['categories'] as $category)
                    <option value="{{$category->id}}">{{$category->name}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group col-md-3">
              <select id="country_id" name="country_id" class="form-control selectpicker" multiple data-live-search="true">
                <option value="">All Countries</option>
                @foreach ($data['countries'] as $country)
                    <option value="{{$country->id}}">{{$country->country_name}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group col-md-2">
              <input type="text" placeholder="Enter Profile ID" class="form-control" id="profile_id" name="profile_id">
            </div>
            <button class="btn btn-primary searchButton"><span class="glyphicon glyphicon-search"></span> Search</button>
        </div>
    </div>
    <div class="container categoriesContainer margin-bottom-40">
		@foreach ($data['categories'] as $category)
			@include('includes.categories-listing')
		@endforeach
    </div>
    <!-- container wrap-ui -->
@endsection
@section('javascript')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min.js"></script>
<script type="text/javascript">
        $(document).ready(function(){
            $('.searchButton').click(function(e){
                var networkId = $('#network_id').val();
                var categoryId = $('#category_id').val();
                var countryId = $('#country_id').val();
                var profileId = $('#profile_id').val();
                var _token = $('#token').val();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type:'post',
                    url:'',
                    dataType:'json',
                    data:{_token:_token,networkId:networkId, categoryId:categoryId, countryId:countryId, profileId:profileId},
                    success:function(res){
                        $('.categoriesContainer').empty();
                        if(!res.error){
                            $.each(res, function(key, val){
                                profile_id = val.user_id+89066;
                                var featured = '';
                                var avatar = 'default.jpg';
                                
                                if(val.avatar != ''){
                                    avatar = val.avatar;
                                }
                                if(val.featured == 1){
                                    featured = '<span class="box-featured" title="Featured Campaign"><i class="fa fa-trophy"></i></span>';
                                }
                                $('.categoriesContainer').append('<div class="col-xs-12 col-sm-6 col-md-3 col-thumb"><p class="campaignsProfileId">Profile ID: '+profile_id+'</p><div class="thumbnail padding-top-zero"><a class="position-relative btn-block img-grid" href="'+val.url+'">'+featured+'<img title="'+val.title+'" src="public/campaigns/small/'+val.small_image+'" class="image-url img-responsive btn-block radius-image" /></a><div class="caption"><h1 class="title-campaigns font-default"><a title="'+val.title+'" class="item-link" href="'+val.url+'">'+val.title+'</a></h1><p class="desc-campaigns"><img src="public/avatar/'+val.avatar+'" width="20" height="20" class="img-circle avatar-campaign" />'+val.name+' '+val.last_name+'<p class="desc-campaigns text-overflow">'+val.description+'</p><p class="desc-campaigns"><span class="stats-campaigns"><span class="pull-left"><strong>'+val.donations+'</strong> Raised</span> <span class="pull-right"><strong>'+val.percentage+'%</strong></span> </span><span class="progress"><span class="percentage" style="width: '+val.percentage+'%" aria-valuemin="0" aria-valuemax="100" role="progressbar"></span></span></p><h6 class="margin-bottom-zero"><em><strong>Goal '+val.goal+'</strong></em></h6></div></div></div>'); 

                            });
                        }
                        else{
                            $('.categoriesContainer').append('<div class="btn-block text-center margin-top-40"><i class="icon-search ico-no-result"></i></div><h3 class="margin-top-none text-center no-result no-result-mg">'+res.error+'</h3>');     
                        }
                    }
                })
            })
            $('.selectpicker').selectpicker();
        });

    // $(document).ready(function(){
    //     $('.searchCategory').on('keyup', function(e){
    //         var categoryName = $(this).val();
            
    //         $.ajaxSetup({
    //             headers: {
    //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //             }
    //         });
    //         $.ajax({
    //             type:'post',
    //             url:'',
    //             dataType:'json',
    //             data:{categoryName:categoryName},
    //             success:function(res){
    //                 console.log(window.location.host);
    //                 $('.categoriesContainer').empty();
    //                 if(res.length > 0){
                        
    //                     $.each(res, function(key, value){
    //                         if(value.image == ''){
    //                             value.image = 'default.jpg';
    //                         }
    //                         $('.categoriesContainer ').append('<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 row-margin-20"><a href="'+window.location.protocol +'//'+window.location.host+'/category/'+value.slug+'"><img class="img-responsive btn-block custom-rounded" src="public/img-category/'+value.image+'" alt="'+value.name+'"></a><h1 class="title-services"><a href="'+window.location.href+'/'+value.slug+'">'+value.name+' ('+value.campaings_count+')</a></h1></div>');
    //                     })
    //                 }
    //                 else {
    //                     $('.categoriesContainer').append('<div class="col-md-12 margin-top-20 margin-bottom-20"><div class="btn-block text-center"><i class="icon-search ico-no-result"></i></div><h3 class="margin-top-none text-center no-result no-result-mg">No results have been found</h3></div>')
    //                 }
    //             }
    //         })
    //     })
    // })
    
    
</script>
@endsection

