<template id="breeders-template" xmlns:v-bind="http://www.w3.org/1999/xhtml">


    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Breeders
            <div class="btn-group">
                <button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button" aria-expanded="false">
                  <span class="caret"></span>
                  <span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul role="menu" class="dropdown-menu" v-on:mouseover.prevent="hoverSubdropdown">
                  <li><a v-link="{ path: '/breeders', activeClass: 'bold', exact: true }" href="#">All</a></li>
                  <li><a v-link="{ path: '/breeders/does', activeClass: 'bold' }" href="#">Does</a></li>
                  <li><a v-link="{ path: '/breeders/opendoes', activeClass: 'bold' }" @click="filters.bred = false" href="#">Open Does</a></li>
                  <li><a v-link="{ path: '/breeders/bucks', activeClass: 'bold' }" href="#">Bucks</a></li>
                  <li><a v-link="{ path: '/breeders/archive', activeClass: 'bold' }" href="#">Archived</a></li>
                  <li><a v-link="{ path: '/breeders/sold', activeClass: 'bold' }" href="#">Sold</a></li>
                  <li><a v-link="{ path: '/breeders/butchered', activeClass: 'bold' }" href="#">Butchered</a></li>
                  <li><a v-link="{ path: '/breeders/died', activeClass: 'bold' }" href="#">Died</a></li>
                  <li class="divider"></li>
                  <li class="custom-dropdown-wrapper hoverClass">
                      {{--<a v-link="{ path: '/breeders/categories', activeClass: 'bold' }" tabindex="-1">Categories</a>--}}
                      <a v-link="{ path: '/breeders/categories', activeClass: 'bold' }" class="subdrop hoverClass">Categories
                          <span class="fa hoverClass"
                                v-bind:class="{ 'fa-caret-right' : subDropdownOpend, 'fa-caret-down': !subDropdownOpend }"></span></a>
                      <ul class="dropdown-menu category-drop-down hoverClass" v-if="subDropdownOpend">
                          <li v-for="category in categories" class="hoverClass">
                              <a href="" class="hoverClass" v-on:click.prevent="filters.category_id = category.id">@{{ category.name }}</a>
                          </li>
                      </ul>
                  </li>
                  <li class="divider"></li>
                  <li><a href="#" @click.prevent="addNew">Add New</a></li>
                  <li><a v-link="{ path: '/wizard/breeders' }">Add Multiple</a></li>
                </ul>
            </div>
            <div class="btn-group">
                <button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button" aria-expanded="false">
                    Sort <span class="caret"></span>

                </button>
                <ul role="menu" class="dropdown-menu">
                    <li><a href="#" @click.prevent="changeOrder('tattoo|asc')" v-bind:class="{ 'bold': order == 'tattoo|asc' }">ID</a></li>
                    <li><a href="#" @click.prevent="changeOrder('name|asc')" v-bind:class="{ 'bold': order == 'name|asc' }">Name</a></li>
                    <li><a href="#" @click.prevent="changeOrder('cage|asc')" v-bind:class="{ 'bold': order == 'cage|asc' }">Cage</a></li>
                    <li><a href="#" @click.prevent="changeOrder('breed|asc')" v-bind:class="{ 'bold': order == 'breed|asc' }">Breed</a></li>
                    <li><a href="#" @click.prevent="changeOrder('date_of_birth|asc')" v-bind:class="{ 'bold': order == 'date_of_birth|asc' }">Age (asc)</a></li>
                    <li><a href="#" @click.prevent="changeOrder('date_of_birth|desc')" v-bind:class="{ 'bold': order == 'date_of_birth|desc' }">Age (desc)</a></li>
                </ul>
            </div>

            <button class="btn btn-default" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                Filters
            </button>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#" v-link="{ path: '/' }"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Breeders</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="collapse" id="collapseExample">
            <div class="row well" style="margin-bottom: 20px" id="filter-form">
                <div class="col-xs-6 col-md-2 form-group">
                    <label for="breeder-name">Name</label>
                    <input type="text" id="f-breeder-name" class="form-control" v-model="filters.name" placeholder="Name" lazy>
                </div>
                <div class="col-xs-6 col-md-2 form-group">
                    <label for="breeder-tattoo">ID</label>
                    <input type="text" id="f-breeder-tattoo" class="form-control" v-model="filters.tattoo" placeholder="ID" lazy>
                </div>
                <div class="col-xs-6 col-md-2 form-group">
                    <label for="breeder-breed">Breed</label>
                    <input type="text" id="f-breeder-breed" class="form-control" v-model="filters.breed" placeholder="Breed" lazy>
                </div>
                <div class="col-xs-6 col-md-2 form-group">
                    <label for="breeder-cage">Cage</label>
                    <input type="text" id="f-breeder-color" class="form-control" v-model="filters.cage" placeholder="Cage" lazy>
                </div>
                <div class="col-xs-6 col-md-2 form-group">
                    <label for="breeder-color">Color</label>
                    <input type="text" id="f-breeder-color" class="form-control" v-model="filters.color" placeholder="Color" lazy>
                </div>
                {{--<div class="col-xs-6 col-md-2 form-group">--}}
                    {{--<label for="breeder-color">Born (from)</label>--}}
                    {{--<div class="input-group date" v-datepicker="filters.date_of_birth.from">--}}
                        {{--<div class="input-group-addon">--}}
                            {{--<i class="fa fa-calendar"></i>--}}
                        {{--</div>--}}
                        {{--<input type="text" id="breeder-born" class="form-control"  placeholder="Born"  >--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div class="col-xs-6 col-md-2 form-group">--}}
                    {{--<label for="breeder-color">Born (to)</label>--}}
                    {{--<div class="input-group date" v-datepicker="filters.date_of_birth.to">--}}
                        {{--<div class="input-group-addon">--}}
                            {{--<i class="fa fa-calendar"></i>--}}
                        {{--</div>--}}
                        {{--<input type="text" id="breeder-born-to" class="form-control"  placeholder="Born" >--}}
                    {{--</div>--}}
                {{--</div>--}}

                <div class="col-xs-6 col-md-2 form-group">
                    <label for="f-breeder-born">Born</label>
                    <div class="input-group">
                        <input type="text" class="form-control pull-right" id="f-breeder-date_of_birth" v-daterangepicker="filterRanges.date_of_birth">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                    </div> <!-- /.input group -->
                </div>


                <div class="col-xs-6 col-md-2 form-group">
                    <label for="f-breeder-born">Acquired</label>
                    <div class="input-group">
                        <input type="text" class="form-control pull-right" id="f-breeder-aquired" v-daterangepicker="filterRanges.aquired">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                    </div> <!-- /.input group -->
                </div>

                <div class="col-xs-6 col-md-2 form-group">
                    <label for="f-breeder-category">Category</label>
                    <select id="f-breeder-category" class="form-control" v-model="filters.category_id">
                        <option selected value="">All</option>
                        <option v-for="category in categories" :value="category.id">@{{ category.name }}</option>
                    </select>
                </div>

                <div class="col-xs-6 col-md-2 form-group" v-if="!openDoes">
                    <label for="f-breeder-category">Bred</label>
                    <select id="f-breeder-category" class="form-control" v-model="filters.bred">
                        <option v-for="item in bredArr" :value="item.value">@{{ item.name }}</option>
                    </select>
                </div>

                {{--<div class="col-xs-6 col-md-2 form-group">--}}
                    {{--<label for="breeder-color">Aquired (from)</label>--}}
                    {{--<div class="input-group date" v-datepicker="filters.aquired.from">--}}
                        {{--<div class="input-group-addon">--}}
                            {{--<i class="fa fa-calendar"></i>--}}
                        {{--</div>--}}
                        {{--<input type="text" id="breeder-aquired" class="form-control"  placeholder="Aquired">--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div class="col-xs-6 col-md-2 form-group">--}}
                    {{--<label for="breeder-color">Aquired (to)</label>--}}
                    {{--<div class="input-group date" v-datepicker="filters.aquired.to">--}}
                        {{--<div class="input-group-addon">--}}
                            {{--<i class="fa fa-calendar"></i>--}}
                        {{--</div>--}}
                        {{--<input type="text" id="breeder-aquired-to" class="form-control"  placeholder="Aquired">--}}
                    {{--</div>--}}
                {{--</div>--}}
                <div class="col-xs-6 col-md-2">
                    <br>
                    <a @click="resetFilter" class="btn btn-default form-control" style="margin-top: 5px">Reset Filter</a>
                </div>

                <div class="col-xs-12">
                    @{{ totalItems }} records found
                </div>
            </div>
        </div>

        <div class="input-group input-group col-md-2" style="margin-bottom: 20px">
            <input type="text" class="form-control" placeholder="Search for..." v-model="searchQuery" debounce="500">
                  <span class="input-group-addon">
                    <i class="fa fa-search" aria-hidden="true"></i>
                  </span>
        </div>

        <!-- Your Page Content Here -->

        <div class="row" v-if="loading">
            <div class="col-md-12">
                <h1 class="loader"><i class="fa fa-spin fa-spinner"></i></h1>
                <!-- <img src="/img/ajax-loader.gif" alt="Loading..." class="loader"> -->
            </div>
        </div>

        <div class="row" v-if="!loading && !breeders.length">
            <div class="col-md-12">
                <h3 class="text-orange">No breeders</h3>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 col-md-6" v-for="transfer in transfers">
                <div class="box box-widget widget-user" :id="'transfer_id_' + transfer.transfer_id">
                    <div class="widget-user-header" v-bind:class="getGenderClass(transfer.sex)">
                        <div class="btn-group pull-right">
                            <button @click.prevent="acceptTransfer(transfer)" class="btn btn-outline" title="Accept">
                                <i class="fa fa-check"></i>
                                Claim
                            </button>
                            <button @click.prevent="declineTransferModal(transfer)" class="btn btn-outline" title="Decline">
                                <i class="fa fa-times"></i>
                                Decline
                            </button>
                        </div>
                        <div class="pull-left">
                            <h3 class="widget-user-username">@{{ transfer.name }}</h3>
                            <h5 class="widget-user-desc">Cage: @{{ transfer.cage }} </h5>
                            <h5 class="widget-user-desc">ID: @{{ transfer.tattoo }}</h5>
                            <h5 class="widget-user-desc">@{{ transfer.breed }}</h5>
                        </div>
                    </div>
                    <div class="widget-user-image breeder">
                        <img v-bind:alt="transfer.name" src="img/rabbit1.jpg" v-bind:src="transfer.image.path" class="img-circle">
                    </div>
                    <div class="box-footer">
                        <div class="row">
                            <div class="col-xs-3 col-xs-offset-9">
                                <div class="description-block">
                                    <h5 class="description-header">
                                        <span v-if="transfer.weight">@{{ transfer.weight_slug }} <!--@{{ transfer.user.weight_slug }}--></span>
                                        <span v-if="!transfer.weight">&mdash;</span>
                                    </h5>
                                    <span class="description-text">weight</span>
                                </div><!-- /.description-block -->
                            </div><!-- /.col -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6" v-for="breed in breeders | filterBy itemFilter">
                <!-- Widget: user widget style 1 -->
                <div class="box box-widget widget-user cursor-pointer" v-bind:id="'id_'+breed.id" v-link="{ path: '/profile/'+breed.id }">
                    <div v-if="breed.pregnant" class="bred-icon" title="Bred"><i class="fa fa-heartbeat"></i></div>
                    <div class="widget-user-header" v-bind:class="getGenderClass(breed.sex)">
                        <div class="btn-group btn-group-sm pull-right">
                            <button @click.prevent="" class="btn btn-outline carrot-btn dropdown-toggle" title="Actions"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-caret-down" aria-hidden="true"></i>
                            </button>
                            <ul class="dropdown-menu breed-profile-action">
                                <li v-if="!breed.archived && !breed.sold_at">
                                    <a href="" @click.prevent="editModal(breed)"><i class="fa fa-pencil"></i> Edit</a>

                                <li v-if="!breeder.archived && !breeder.sold_at && !breeder.died">
                                    <a href="" @click.prevent="openBreeder('cageCard', breed.id)"><i class="fa fa-list-alt"></i> Cage Card</a>
                                </li>

                                </li>
                                <li v-if="!breed.archived && !breed.sold_at">
                                    <a href="" @click.prevent="soldModal(breed)"><i class="fa fa-dollar"></i> Sell</a>
                                </li>


                                <li v-if="!breeder.butchered && !breeder.died">
                                    <a href="" @click.prevent="openBreeder('butcher', breed.id)"><i class="fa fa-cutlery"></i> Butcher</a>
                                </li>
                                <li v-if="breeder.butchered">
                                    <a href="" @click.prevent="openBreeder('undoButcher', breed.id)"><i class="fa fa-cutlery"></i> Undo Butcher</a>
                                </li>

                                <li v-if="!breeder.died && !breeder.butchered">
                                    <a href="" @click.prevent="openBreeder('died', breed.id)"><i class="fa fa-heart-o"></i> Died</a>
                                </li>
                                <li v-if="breeder.died">
                                    <a href="" @click.prevent="openBreeder('undoDied', breed.id)"><i class="fa fa-heart-o"></i> Undo Died</a>
                                </li>


                                <li v-if="!breed.archived && !breed.sold_at">
                                    <a href="" @click.prevent="archiveModal(breed)"><i class="fa fa-archive"></i> Archive</a>
                                </li>
                                <li v-if="breed.archived">
                                    <a href="" @click.prevent="unarchiveModal(breed)"><i class="fa fa-expand"></i> Unarchive</a>
                                </li>
                                <li v-if="breed.sold_at">
                                    <a href="" @click.prevent="unsoldModal(breed)"><i class="fa fa-expand"></i> Unsold</a>
                                </li>
                                <li>
                                    <a href="" @click.prevent="deleteModal(breed)"><i class="fa fa-trash"></i> Delete</a>
                                </li>
                            </ul>
                        </div>
                        {{--<div class="btn-group btn-group-sm pull-right" v-if="subEditFunctionsOpened === breed.id">--}}
                            {{--<a role="button" href="#" v-link="{ path: '/profile/'+breed.id }" class="btn btn-outline"><i--}}
                                        {{--class="fa fa-pencil"></i></a>--}}
                            {{--<button v-if="!breed.archived && !breed.sold_at" @click.prevent="editModal(breed)" class="btn btn-outline" title="Edit"><i class="fa fa-pencil"></i></button>--}}
                            {{--<button v-if="!breed.archived && !breed.sold_at" @click.prevent="soldModal(breed)" class="btn btn-outline" title="Sell"><i class="fa fa-dollar"></i></button>--}}
                            {{--<button v-if="!breed.archived && !breed.sold_at" @click.prevent="archiveModal(breed)" class="btn btn-outline" title="Archive"><i class="fa fa-archive"></i></button>--}}
                            {{--<button v-if="breed.archived" @click.prevent="unarchiveModal(breed)" class="btn btn-outline" title="Unarchive"><i class="fa fa-expand"></i></button>--}}
                            {{--<button v-if="breed.sold_at" @click.prevent="unsoldModal(breed)" class="btn btn-outline" title="Unmark sold"><i class="fa fa-expand"></i></button>--}}
                            {{--<button @click.prevent="deleteModal(breed)" class="btn btn-outline" title="Delete"><i class="fa fa-trash"></i></button>--}}
                        {{--</div>--}}
                        <div class="pull-left">
                            <!-- <small>@{{ breed.prefix }}</small> --->
                            <h3 class="widget-user-username">@{{ breed.name }}</h3>
                            <h5 class="widget-user-desc">Cage: @{{ breed.cage }} </h5>
                            <h5 class="widget-user-desc">ID: @{{ breed.tattoo }}</h5>
                            <h5 class="widget-user-desc">@{{ breed.breed }}</h5>
                        </div>
                    </div>
                    <div class="widget-user-image breeder">
                        <img v-bind:alt="breed.name" src="img/rabbit1.jpg" v-bind:src="breed.image.path" class="img-circle">
                    </div>
                    <div class="box-footer">
                        <div class="row">
                            <div class="col-xs-3 border-right">
                                <div class="description-block">
                                    <h5 class="description-header">
                                        <span v-if="breed.litters_count">@{{ breed.litters_count }}</span>
                                        <span v-if="!breed.litters_count">&mdash;</span>
                                    </h5>
                                    <span class="description-text">LITTERS</span>
                                </div><!-- /.description-block -->
                            </div>
                            <div class="col-xs-2 border-right">
                                <div class="description-block">
                                    <h5 class="description-header">
                                        <span v-if="breed.kits">@{{ breed.kits }}</span>
                                        <span v-if="!breed.kits">&mdash;</span>
                                    </h5>
                                    <span class="description-text">KITS</span>
                                </div><!-- /.description-block -->
                            </div><!-- /.col -->
                            <div class="col-xs-3 border-right">
                                <div class="description-block">
                                    <h5 class="description-header">
                                        <span v-if="breed.date_of_birth">@{{ showAge(breed.date_of_birth) }}</span>
                                        <span v-if="!breed.date_of_birth">&mdash;</span>
                                    </h5>
                                    <span class="description-text">AGE</span>
                                </div><!-- /.description-block -->
                            </div><!-- /.col -->
                            <div class="col-xs-4">
                                <div class="description-block">
                                    <h5 class="description-header">
                                        <span v-if="breed.weight">@{{ breed.weight_slug }} <!--@{{ breed.user.weight_slug }}--></span>
                                        <span v-if="!breed.weight">&mdash;</span>
                                    </h5>
                                    <span class="description-text">weight</span>
                                </div><!-- /.description-block -->
                            </div><!-- /.col -->
                        </div><!-- /.row -->
                    </div>
                </div><!-- /.widget-user --> </div><!-- /.col -->
        </div><!-- /.row -->

        <div class="row">
            <div class="col-lg-4 col-md-6"><a href="#" @click.prevent="addNew">
                    <div class="info-box">
                        <span class="info-box-icon bg-gray text-muted"><i class="fa fa-plus"></i></span>

                        <div class="info-box-content text-muted"><h1>Add New</h1>
                        </div><!-- /.info-box-content -->
                    </div><!-- /.info-box -->
                </a>
            </div>
        </div><!-- /.row -->

        <!--<div class="row">
          <div class="pull-right" v-show="pages > 1">
              <ul class="pagination">
                  <li class="paginate_button previous" v-bind:class="{ disabled: page == 1 }">
                      <a href="#" tabindex="0" @click.prevent="prevPage">Previous</a>
                  </li>

                  <li class="paginate_button" v-for="_page in pages" v-bind:class="{ active: _page+1 == page }">
                      <a href="#" tabindex="0" v-link="{ path: currentRoute, query: {page: _page+1} }">@{{ _page+1 }}</a>
                  </li>

                  <li class="paginate_button next" v-bind:class="{ disabled: page == pages }">
                      <a href="#" tabindex="0" @click.prevent="nextPage">Next</a>
                  </li>
              </ul>
          </div>
        </div><!-- /.row -->
        <div v-infinite-scroll="loadMore()" infinite-scroll-disabled="disableLoadMore" infinite-scroll-distance="1000">
            <a @click="loadMore()" class="btn btn-primary btn-lg" v-if="!disableLoadMore">Load More</a>
        </div>

    </section><!-- /.content -->


    @include('layouts.breeders.partials.breeder')
    @include('layouts.archive-delete')

    {{-- additional modals --}}
    @include('layouts.litter.modals.butcher')
    @include('layouts.died', ['unique' => '-breeder'])

    @include('layouts.litter.modals.breeder-butcher')

    <!-- Cage card print modal -->
    @include('layouts.cage-cards.print-modal')

</template>

