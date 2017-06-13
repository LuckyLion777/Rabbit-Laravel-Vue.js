<template id="breeder-category-template">
    <div>
      <section class="content-header">
          <h1>
              Breeder Categories
              <div class="btn-group">
                  <button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button" aria-expanded="false">
                      <span class="caret"></span>
                      <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul role="menu" class="dropdown-menu" v-on:mouseover.prevent="hoverSubdropdown">
                      <li><a v-link="{ path: '/breeders', activeClass: 'bold', exact: true }" href="#">All</a></li>
                      <li><a v-link="{ path: '/breeders/does', activeClass: 'bold' }" href="#">Does</a></li>
                      <li><a v-link="{ path: '/breeders/bucks', activeClass: 'bold' }" href="#">Bucks</a></li>
                      <li><a v-link="{ path: '/breeders/archive', activeClass: 'bold' }" href="#">Archived</a></li>
                      <li><a v-link="{ path: '/breeders/sold', activeClass: 'bold' }" href="#">Sold</a></li>
                      <li class="divider"></li>
                      {{--<li><a v-link="{ path: '/breeders/categories', activeClass: 'bold' }">Categories</a></li>--}}
                      <li class="custom-dropdown-wrapper hoverClass">
                          {{--<a v-link="{ path: '/breeders/categories', activeClass: 'bold' }" tabindex="-1">Categories</a>--}}
                          <a v-link="{ path: '/breeders/categories', activeClass: 'bold' }" class="subdrop hoverClass">Categories
                              <span class="fa hoverClass"
                                    v-bind:class="{ 'fa-caret-right' : subDropdownOpend, 'fa-caret-down': !subDropdownOpend }"></span></a>
                          <ul class="dropdown-menu category-drop-down hoverClass" v-if="subDropdownOpend">
                              <li v-for="category in categories" class="hoverClass">
                                  <a v-link="{ path: '/breeders'}" class="hoverClass" v-on:click.prevent="clickCategory(category.id)">@{{ category.name }}</a>
                              </li>
                          </ul>
                      </li>
                      <li class="divider"></li>
                      <li><a href="#" @click.prevent="addNew">Add New</a></li>
                  </ul>
              </div>
          </h1>
          <ol class="breadcrumb">
              <li><a href="#" v-link="{ path: '/' }"><i class="fa fa-dashboard"></i> Home</a></li>
              <li><a href="#" v-link="{ path: '/breeders' }"> Breeders</a></li>
              <li class="active">Categories</li>
          </ol>
      </section>

      <!-- Main content -->
      <section class="content">

        <div class="box box-solid box-default">

          <div class="box-body">

              <div class="row" v-if="loading">
                  <div class="col-md-12">
                    <h1 class="loader"><i class="fa fa-spin fa-spinner"></i></h1>
                      <!-- <img src="/img/ajax-loader.gif" alt="Loading..." class="loader"> -->
                  </div>
              </div>
              <div class="row" v-if="!loading && !categories.length">
                <div class="col-md-12">
                    <h3 class="text-orange">No Categories</h3>
                </div>
              </div>

              <table class="table table-striped" v-if="categories.length">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th><span class="sr-only">Actions</span></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="category in categories">
                    <td><a v-link="{ path: '/breeders'}" class="hoverClass" v-on:click.prevent="clickCategory(category.id)">@{{category.name}}</a></td>
                    <td>@{{category.description}}</td>
                    <td>
                        <div class="btn-group pull-right">
                            <button v-if="!category.special" @click="editModel(category)" title="Edit" class="btn btn-default btn-xs"><i class="fa fa-pencil"></i></button>
                            <button v-if="!category.special" :style="{ visibility: category.count ? 'hidden' : 'visible' }"
                                    @click="showDelete(category)" title="Delete" class="btn btn-default btn-xs"><i class="fa fa-trash"></i></button>
                        </div>
                    </td>
                  </tr>
                </tbody>
              </table>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-4 col-md-6">
            <a data-toggle="modal" role="button" href="#" @click.prevent="addModel">
              <div class="info-box">
                  <span class="info-box-icon bg-gray text-muted"><i class="fa fa-plus"></i></span>
                  <div class="info-box-content text-muted">
                    <h1>Add New</h1>
                  </div><!-- /.info-box-content -->
              </div><!-- /.info-box -->
            </a>
          </div>
        </div>

        @include('layouts.breeders.partials.categoryForm')
        @include('layouts.delete')

    </section><!-- /.content -->
  </div>
</template>
