<template id="litters-template">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Litters
            <div class="btn-group">       
                <button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button" aria-expanded="false">
                  <span class="caret"></span>
                  <span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul role="menu" class="dropdown-menu">
                    <li><a v-link="{ path: '/litters', activeClass: 'bold', exact: true }" href="#">All</a></li>
                    <li><a v-link="{ path: '/litters/butchered', activeClass: 'bold' }" href="#">Butchered</a></li>
                    <li><a v-link="{ path: '/litters/archive', activeClass: 'bold' }" href="#">Archived</a></li>
                    <li class="divider"></li>
                    <li><a href="#" @click.prevent="newLitter">Add New</a></li>
                </ul>
            </div>
            <div class="btn-group">
                <button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button" aria-expanded="false">
                    Sort <span class="caret"></span>

                </button>
                <ul role="menu" class="dropdown-menu">
                    <li><a href="#" @click.prevent="changeOrder('given_id|asc')" :class="{ 'bold':order == 'given_id|asc' }">ID</a></li>
                    <li><a href="#" @click.prevent="changeOrder('born|desc')" :class="{ 'bold':order == 'born|desc' }">Age (asc)</a></li>
                    <li><a href="#" @click.prevent="changeOrder('born|asc')" :class="{ 'bold':order == 'born|asc' }">Age (desc)</a></li>
                </ul>
            </div>
            <button class="btn btn-default" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                Filters
            </button>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#" v-link="{ path: '/' }"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Litters</li>
        </ol>
    </section>

    <section class="content">


            <div class="collapse" id="collapseExample">
                <div class="row well" style="margin-bottom: 20px" id="filter-form">
                    <div class="col-md-2 form-group">
                        <label for="f-litter-given_id">ID</label>
                        <input type="text" id="f-litter-given_id" class="form-control" v-model="filters.given_id" placeholder="ID" lazy>
                    </div>
                    <div class="col-md-2 form-group">
                        <label for="f-litter-buck">Buck</label>
                        <select v-model="filters.buck" class="form-control">
                            <option selected value="">-</option>
                            <option v-for="buck in bucks" :value="buck.id">@{{ buck.name }}: @{{ buck.tattoo }}</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <label for="f-litter-buck">Doe</label>
                        <select v-model="filters.doe" class="form-control">
                            <option selected value="">-</option>
                            <option v-for="doe in does" :value="doe.id">@{{ doe.name }}: @{{ doe.tattoo }}</option>
                        </select>
                    </div>
                    {{--<div class="col-md-2 form-group">--}}
                        {{--<label for="f-litter-buck">Buck</label>--}}
                        {{--<input type="text" id="f-litter-buck" class="form-control" v-model="filters.buck" placeholder="Name" lazy>--}}
                    {{--</div>--}}
                    {{--<div class="col-md-2 form-group">--}}
                        {{--<label for="f-litter-doe">Doe</label>--}}
                        {{--<input type="text" id="f-litter-doe" class="form-control" v-model="filters.doe" placeholder="Name" lazy>--}}
                    {{--</div>--}}
                    {{--<div class="col-md-2 form-group">--}}
                        {{--<label for="breeder-color">Born (from)</label>--}}
                        {{--<div class="input-group date" v-datepicker="filters.born.from">--}}
                            {{--<div class="input-group-addon">--}}
                                {{--<i class="fa fa-calendar"></i>--}}
                            {{--</div>--}}
                            {{--<input type="text" id="breeder-born" class="form-control"  placeholder="Born"  >--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-md-2 form-group">--}}
                        {{--<label for="breeder-color">Born (to)</label>--}}
                        {{--<div class="input-group date" v-datepicker="filters.born.to">--}}
                            {{--<div class="input-group-addon">--}}
                                {{--<i class="fa fa-calendar"></i>--}}
                            {{--</div>--}}
                            {{--<input type="text" id="breeder-born-to" class="form-control"  placeholder="Born" >--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-md-2 form-group">--}}
                        {{--<label for="breeder-color">Breed (from)</label>--}}
                        {{--<div class="input-group date" v-datepicker="filters.breed.from">--}}
                            {{--<div class="input-group-addon">--}}
                                {{--<i class="fa fa-calendar"></i>--}}
                            {{--</div>--}}
                            {{--<input type="text" id="breeder-aquired" class="form-control"  placeholder="Breed">--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-md-2 form-group">--}}
                        {{--<label for="breeder-color">Breed (to)</label>--}}
                        {{--<div class="input-group date" v-datepicker="filters.breed.to">--}}
                            {{--<div class="input-group-addon">--}}
                                {{--<i class="fa fa-calendar"></i>--}}
                            {{--</div>--}}
                            {{--<input type="text" id="breeder-aquired-to" class="form-control"  placeholder="Breed">--}}
                        {{--</div>--}}
                    {{--</div>--}}

                    

                    <div class="col-md-2 form-group">
                        <label for="f-breeder-born">Bred</label>
                        <div class="input-group">
                            <input type="text" class="form-control pull-right" id="f-litter-breed" v-daterangepicker="filterRanges.bred">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                        </div> <!-- /.input group -->
                    </div>
                    
                    <div class="col-md-2 form-group">
                        <label for="f-breeder-born">Born</label>
                        <div class="input-group">
                            <input type="text" class="form-control pull-right" id="f-litter-born" v-daterangepicker="filterRanges.born">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                        </div> <!-- /.input group -->
                    </div>

                    <div class="col-md-2">
                        <br>
                        <a @click="resetFilter()" class="btn btn-default form-control" style="margin-top: 5px">Reset Filter</a>
                    </div>

                    <div class="col-md-12">
                        @{{ totalItems }} records found
                    </div>
                </div>
            </div>

            <div class="input-group col-md-2" style="margin-bottom: 20px">
                <input type="text" class="form-control" placeholder="Search for..." v-model="searchQuery" debounce="500">
                  <span class="input-group-addon">
                    <i class="fa fa-search" aria-hidden="true"></i>
                  </span>
            </div>

        <div class="row" v-if="loading">
            <div class="col-md-12">
                <h1 class="loader"><i class="fa fa-spin fa-spinner"></i></h1>
                <!-- <img src="/img/ajax-loader.gif" alt="Loading..." class="loader"> -->
            </div>
        </div>

        <div class="row" v-if="!loading && !litters.length">
            <div class="col-md-12">
                <h3 class="text-orange">No litters</h3>
            </div>
        </div>
        <!-- Your Page Content Here -->
        <div class="row">

            <div class="col-lg-4 col-md-6" v-for="litter in litters | filterBy itemFilter">
                <!-- Widget: user widget style 1 -->
                <div v-link="{ path: '/litterprofile/'+litter.id }" class="box box-widget widget-user cursor-pointer display-block" v-bind:id="'id_'+litter.id">
                    <!-- Add the bg color to the header using any of the bg-* classes -->
                    <div class="widget-user-header"
                         v-bind:class="{ 'bg-olive': litter.archived=='0', 'bg-gray-active': litter.archived=='1' }">
                        <div class="btn-group pull-right box-tools ">
                            <button @click.prevent="" class="btn btn-outline carrot-btn dropdown-toggle" title="Edit"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-caret-down" aria-hidden="true"></i>
                            </button>
                            <ul class="dropdown-menu breed-profile-action">

                                <li>
                                    <a href="#" @click.prevent="openLitter('edit', litter.id)"><i class="fa fa-pencil" ></i> Edit</a>
                                </li>

                                <li  v-if="litter.archived=='0'">
                                    <a href="" @click.prevent="weightModal(litter)"><i class="fa fa-balance-scale"></i> Weigh</a>
                                </li>
                                <li v-if="litter.archived=='0'">
                                    <a href="" @click.prevent="butcherModal(litter)"><i class="fa fa-cutlery"></i> Butcher</a>
                                </li>

                                <li>
                                    <a href="#" @click.prevent="openLitter('cageCard', litter.id)"><i class="fa fa-list-alt"></i> Cage Card</a>
                                </li>

                                <li v-if="litter.archived=='0'">
                                    <a href="" @click.prevent="archiveModal(litter)"><i class="fa fa-archive"></i> Archive</a>
                                </li>
                                <li v-if="litter.archived=='1'">
                                    <a href="" @click.prevent="unarchiveModal(litter)"><i class="fa fa-expand"></i> Unarchive</a>
                                </li>
                                <li>
                                    <a href="" @click.prevent="deleteModal(litter)"><i class="fa fa-trash"></i> Delete</a>
                                </li>
                            </ul>

                            {{--<button v-if="litter.archived=='0'" @click.prevent="weightModal(litter)" class="btn btn-outline btn-sm" title="Weigh">--}}
                                {{--<i class="fa fa-balance-scale"></i></button>--}}
                            {{--<button v-if="litter.archived=='0'" @click.prevent="butcherModal(litter)" class="btn btn-outline btn-sm" title="Butcher"><i--}}
                                        {{--class="fa fa-cutlery"></i></button>--}}
                            {{--<button v-if="litter.archived=='0'" @click.prevent="archiveModal(litter)" class="btn btn-outline btn-sm" title="Archive"><i class="fa fa-archive"></i></button>--}}
                            {{--<button v-if="litter.archived=='1'" @click.prevent="unarchiveModal(litter)" class="btn btn-outline btn-sm" title="Unarchive"><i class="fa fa-expand"></i></button>--}}
                            {{--<button @click.prevent="deleteModal(litter)" class="btn btn-outline btn-sm" title="Delete"><i class="fa fa-trash"></i>--}}
                            {{--</button>--}}
                        </div>
                        <div class="pull-left">
                            <h3 class="widget-user-username">@{{ father(litter.parents).name }}<br>@{{ mother(litter.parents).name }}</h3>
                            <h5 class="widget-user-desc">Litter: @{{ litter.given_id }}</h5>
                        </div>

                    </div>
                    <div class="widget-user-image pull-left litter">
                        <img v-show="father(litter.parents).image.path" class="img-circle" v-bind:src="father(litter.parents).image.path" v-bind:alt="father(litter.parents).name">
                        <img v-show="mother(litter.parents).image.path" class="img-circle" v-bind:src="mother(litter.parents).image.path" v-bind:alt="mother(litter.parents).name">
                    </div>
                    <div class="box-footer">
                        <div class="row">
                            <div class="col-xs-3 border-right">
                                <div class="description-block">
                                    <h5 class="description-header nobreak">@{{ calcKits(litter) }}</h5>
                                    <span class="description-text">KITS</span>
                                </div><!-- /.description-block -->
                            </div>
                            <div class="col-xs-3 border-right">
                                <div class="description-block">
                                    <h5 class="description-header nobreak">@{{ litter.total_weight_slug.total }} <!--<span v-if="litter.total_weight">@{{ litter.weight_unit_short }}</span>--> <span v-if="!litter.total_weight && litter.total_weight != 0">&mdash;</span></h5>
                                    <span class="description-text">total</span>
                                </div><!-- /.description-block -->
                            </div><!-- /.col -->
                            <div class="col-xs-3 border-right">
                                <div class="description-block">
                                    <h5 class="description-header nobreak">@{{ litter.total_weight_slug.average }} <!--<span v-if="litter.average_weight">@{{ litter.weight_unit_short }}</span>--> <span v-if="!litter.average_weight && litter.average_weight != 0">&mdash;</span></h5>
                                    <span class="description-text">average</span>
                                </div><!-- /.description-block -->
                            </div><!-- /.col -->
                            <div class="col-xs-3">
                                <div class="description-block">
                                    <h5 class="description-header nobreak">@{{ age(litter) }}</h5>
                                    <span class="description-text">age</span>
                                </div><!-- /.description-block -->
                            </div><!-- /.col -->
                        </div><!-- /.row -->
                    </div>
                </div><!-- /.widget-user -->
            </div><!-- /.col -->
        </div><!--- /.row -->


        <div class="row">
            <div class="col-lg-4 col-md-6">
                <a href="#" @click.prevent="newLitter">
                    <div class="info-box">
                        <span class="info-box-icon bg-gray text-muted"><i class="fa fa-plus"></i></span>

                        <div class="info-box-content text-muted"><h1>Add New</h1>
                        </div><!-- /.info-box-content -->
                    </div><!-- /.info-box -->
                </a>
            </div>
        </div>

        <div v-infinite-scroll="loadMore()" infinite-scroll-disabled="disableLoadMore" infinite-scroll-distance="1000">
            <a @click="loadMore()" class="btn btn-primary btn-lg" v-if="!disableLoadMore">Load More</a>
        </div>
    </section>

    @include('layouts.litter.modals.weight')
    @include('layouts.litter.modals.litter')
    @include('layouts.litter.modals.butcher')
    @include('layouts.archive-delete')

</template>

