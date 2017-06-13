<template id="ledger-table-template">
    <div>
        <div class="row" v-if="showLoading">
            <div class="col-sm-12">
                <h1 class="loader"><i class="fa fa-spin fa-spinner"></i></h1>
            </div>
        </div>
        <div v-if="showSubscribe">
            <p class="lead"><a href="#/settings">Change your plan</a> to obtain access to the ledger.</p>
        </div>
        <div v-if="!showLoading && !showSubscribe">


            <div class="box box-solid box-default">

                <div class="box-body">
                	<div class="row">
                        <div class="col-md-2 col-xs-6 pull-right">
                          <select v-model="order" class="form-control minimal">
                              <option value="">Sort By:</option>
                              <option value="date|desc">Date (desc)</option>
                              <option value="date|asc">Date (asc)</option>
                              <option value="name|asc">Name</option>
                              <option value="category|asc" v-if="!associated_type">Category</option>
                              <option value="amount|asc">Amount (asc)</option>
                              <option value="amount|desc">Amount (desc)</option>
                          </select><br>
                        </div>

                        <div class="col-md-3 col-lg-2" v-if="!associated_type">
                            <div class="form-group">
                              <label>Date range:</label>
                                  <div class="input-group">
                                      <input type="text" class="form-control pull-right" id="reservation" v-daterangepicker="daterange">
                                      <div class="input-group-addon">
                                          <i class="fa fa-calendar"></i>
                                      </div>
                                  </div> <!-- /.input group -->
                            </div>
                        </div>

                        <div class="col-md-2 form-group">
                            <label>Category</label>
                            <select v-model="filters.category" class="form-control">
                                <option selected value="">All</option>
                                <option v-for="category in categories" :value="category.id">@{{ category.name }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-lg-2">
                            <div class="input-group" style="margin-bottom: 20px">
                                <input type="text" class="form-control" placeholder="Search for..." v-model="searchQuery" debounce="500">
                            <span class="input-group-addon">
                                <i class="fa fa-search" aria-hidden="true"></i>
                            </span>
                            </div>
                        </div>
                    </div>

                    <div class="row" v-if="!ledger.length">
                        <div class="col-md-12">
                            <h3 class="text-orange">Ledger is empty</h3>
                        </div>
                    </div>

                    <div class="row row-horizon pedigree" v-if="ledger.length">
                      <table class="table table-striped" v-if="ledger.length">
                          <thead>
                          <tr>
                              <th><span class="sr-only">Debit/credit</span></th>
                              <th>Date</th>
                              <th>Name</th>
                              <th v-if="!associated_type">Category</th>
                              <th>Amount</th>
                              <th><span class="sr-only">Actions</span></th>
                          </tr>
                          </thead>
                          <tbody>
                          <tr v-for="entry in ledger | filterBy itemFilter">
                              <td>
                                <span class="badge" :class="{ 'bg-green' : entry.debit, 'bg-red' : !entry.debit }">
                                  <i class="fa fa-plus" v-if="entry.debit"></i>
                                  <i class="fa fa-minus" v-if="!entry.debit"></i>
                                </span>
                              </td>
                              <td>@{{entry.date}}</td>
                              <td><strong>@{{entry.name}}</strong></td>
                              <td v-if="!associated_type && !entry.associated">@{{entry.category.name}}</td>
                              <td v-if="!associated_type && entry.associated">
                                  <a :href="'#!/' + (entry.associated_type === 'breeder' ? 'profile/' : 'litterprofile/') + entry.associated_id">@{{ entry.category.name }}</a>
                              </td>
                              <td>@{{entry.amount}}</td>
                              <td>
                                  <div class=" pull-right">
                                      <button v-if="!entry.archived_at" @click="editModel(entry)" title="Edit" class="btn btn-default btn-xs"><i class="fa fa-pencil"></i></button>
                                      <button v-if="!entry.archived_at" @click="showArchive(entry)" title="Archive" class="btn btn-default  btn-xs"><i class="fa fa-archive"></i></button>
                                      <button v-if="entry.archived_at" @click="showUnarchive(entry)" title="Unarchive" class="btn btn-default  btn-xs"><i class="fa fa-archive"></i></button>
                                      <button @click="showDelete(entry)" title="Delete" class="btn btn-default btn-xs"><i class="fa fa-trash"></i></button>
                                  </div>
                              </td>
                          </tr>
                          </tbody>
                          <tfoot>
                          <tr>
                              <td colspan="@{{ associated_type ? 3 : 4 }}" align="right"><strong>Total </strong> </td>
                              <td><strong>@{{ total }}</strong></td>
                              <td>&nbsp;</td>
                          </tr>
                          </tfoot>
                      </table>
                   </div>
                </div>
            </div>

            <div class="row" v-if="filter !== 'archive'">
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

            <div v-infinite-scroll="loadMore()" infinite-scroll-disabled="disableLoadMore" infinite-scroll-distance="1000">
                <a @click="loadMore()" class="btn btn-primary btn-lg" v-if="!disableLoadMore">Load More</a>
            </div>

            @include('layouts.ledger.partials.ledgerForm')
            @include('layouts.archive-delete', ['unique' => '-ledger'])
        </div>
    </div>
</template>
