<template id="users-template" xmlns:v-bind="http://www.w3.org/1999/xhtml">
    <section class="content">
        <div class="row">
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-blue">
                    <div class="inner">
                        <h3>@{{ usersTotal }}</h3>
                        <p>Users (@{{ usersForever + usersPremium + usersBasic + usersMini }} paid)</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-users"></i>
                    </div>

                </div>
            </div>
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-gray">
                    <div class="inner">
                        <h4>@{{ usersPremium }} Premium</h4>
                        <h4>@{{ usersForever }} Forever</h4>
                
                    </div>
                    <div class="icon">
                        <i class="fa fa-user-plus"></i>
                    </div>
                </div>
            </div><!-- ./col -->
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-gray">
                    <div class="inner">
                        <h4>@{{ usersBasic }} Basic</h4>
                        <h4>@{{ usersMini }} Mini</h4>
                    </div>
                    <div class="icon">
                        <i class="fa fa-user"></i>
                    </div>
                </div>
            </div><!-- ./col -->
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3>@{{ usersForever * 101 + usersPremium * 31 + usersBasic * 15 + usersMini * 10 }}</h3>
                        <p>Net Revenue</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-dollar"></i>
                    </div>
                </div>
            </div><!-- ./col -->

            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3>@{{ breedersTotal + kitsTotal }}</h3>
                        <p>Total Rabbits</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-line-chart"></i>
                    </div>

                </div>
            </div><!-- ./col -->


            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3>@{{ breedersTotal }}</h3>
                        <p>Breeders</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-venus-mars"></i>
                    </div>

                </div>
            </div><!-- ./col -->
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3>@{{ littersTotal }}</h3>
                        <p>Litters</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-th"></i>
                    </div>

                </div>
            </div><!-- ./col -->
            <!-- ./col -->
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3>@{{ kitsTotal }}</h3>
                        <p>Kits</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-birthday-cake"></i>
                    </div>

                </div>
            </div><!-- ./col -->



        </div>
        <div class="box box-solid box-default">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-user"></i> Users</h3>
            </div>
            <div class="box-body">

                <button class="btn btn-default" style="margin-bottom: 20px" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                    Filters
                </button>

                <div class="col-md-12">
                    <div class="collapse" id="collapseExample">
                        <div class="row well" style="margin-bottom: 20px" id="filter-form">
                            <div class="col-md-2 form-group">
                                <label>ID</label>
                                <input type="text"  class="form-control" v-model="filters.id" placeholder="ID" lazy>
                            </div>
                            <div class="col-md-2 form-group">
                                <label>Email</label>
                                <input type="text"  class="form-control" v-model="filters.email" placeholder="Email" lazy>
                            </div>
                            <div class="col-md-2 form-group">
                                <label>Name</label>
                                <input type="text"  class="form-control" v-model="filters.name" placeholder="Name" lazy>
                            </div>
                            <div class="col-md-2 form-group">
                                <label>Subcription</label>
                                <select v-model="filters.subscription_status" class="form-control">
                                    <option selected value="">All</option>
                                    <option value="on trial">Trial</option>
                                    <option value="expired">Expired</option>
                                    <option value="mini_yr">Mini</option>
                                    <option value="basic_yr">Basic</option>
                                    <option value="premium_yr">Premium</option>
                                    <option value="forever">Forever</option>
                                </select>
                            </div>
                            <div class="col-md-2 form-group">
                                <label>Stripe</label>
                                <input type="text"  class="form-control" v-model="filters.stripe_id" placeholder="Id" lazy>
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="breeder-color">Trial Ends (from)</label>
                                <div class="input-group date" v-datepicker="filters.trial_ends_at.from">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" id="breeder-born" class="form-control"  placeholder="From"  >
                                </div>
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="breeder-color">Trial Ends (to)</label>
                                <div class="input-group date" v-datepicker="filters.trial_ends_at.to">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" id="breeder-born-to" class="form-control"  placeholder="To" >
                                </div>
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
                </div>


                <div class="input-group input-group col-md-2" style="margin-bottom: 20px">
                    <input type="text" class="form-control" placeholder="Search for..." v-model="searchQuery" debounce="500">
                  <span class="input-group-addon">
                    <i class="fa fa-search" aria-hidden="true"></i>
                  </span>
                </div>

                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Subscription</th>
                        <th colspan="3"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="user in users | filterBy itemFilter">
                        <td>@{{ user.id }}</td>
                        <td>@{{ user.name }}</td>
                        <td>@{{ user.email }}</td>
                        <th>@{{ user.subscription_status }}</th>
                        <td>
                            <a v-link="{ name: 'userEdit', params: { userId: user.id }}">
                                <i class="fa fa-pencil fa-2x"></i>
                            </a>
                        </td>
                        <td><a @click.prevent="confirmDelete(user)" href="#"><i class="fa fa-times fa-2x"></i></a></td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-infinite-scroll="loadMore()" infinite-scroll-disabled="disableLoadMore" infinite-scroll-distance="1000">
            <a @click="loadMore()" class="btn btn-primary btn-lg" v-if="!disableLoadMore">Load More</a>
        </div>

        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12"><a data-toggle="modal" role="button" v-link="{ path: '/users/create' }" href="#">
                    <div class="info-box">
                        <span class="info-box-icon bg-gray text-muted"><i class="fa fa-plus"></i></span>
                        <div class="info-box-content text-muted"><h1>Add New</h1>
                        </div><!-- /.info-box-content -->
                    </div><!-- /.info-box -->
                </a>
            </div>
        </div>
    </section>


    <div class="modal modal-default" id="delete">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body bg-gray">
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <h3><i class="fa fa-archive"></i><br>
                                Are you sure you want to delete this user?</h3>
                        </div>
                    </div>
                    <div class="row margin">
                        <div class="col-sm-12 text-center">
                            <button class="btn btn-default" type="button" data-dismiss="modal" @click="deleteUser()">
                                <i class="fa fa-check"></i> Yes</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">
                                <i class="fa fa-close"></i> No
                            </button>
                        </div>
                    </div>

                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

</template>
