<template id="ledger-template">
  <div>
          <section class="content-header">
              <h1>
                  Ledger
                  <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button" aria-expanded="false">
                      <span class="caret"></span>
                      <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul role="menu" class="dropdown-menu">
                      <li><a v-link="{ path: '/ledger', activeClass: 'bold', exact: true }" href="#">All</a></li>
                      <li><a v-link="{ path: '/ledger/income', activeClass: 'bold' }" href="#">Income</a></li>
                      <li><a v-link="{ path: '/ledger/expenses', activeClass: 'bold' }" href="#">Expenses</a></li>
                      <li><a v-link="{ path: '/ledger/archive', activeClass: 'bold' }" href="#">Archive</a></li>
                      <li class="divider"></li>
                      <li><a href="#" v-link="{ path: '/ledger/categories', activeClass: 'bold' }">Categories</a></li>
                      <li class="divider"></li>
                      <li><a href="#" @click.prevent="$refs.table.addModel()">Add New</a></li>
                    </ul>
                  </div>
              </h1>
              <ol class="breadcrumb">
                  <li><a href="#" v-link="{ path: '/' }"><i class="fa fa-dashboard"></i> Home</a></li>
                  <li class="active">Ledger</li>
              </ol>
          </section>

          <!-- Main content -->
          <section class="content">
            <!-- <div class="row" v-show="loading">
              <div class="col-sm-12">
                <img src="/img/ajax-loader.gif" alt="Loading..." class="loader">
              </div>
            </div> -->
            <!-- <div v-show="!loading"> -->
            <div class="row">
              <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-green" v-link="{ path: filter == 'income' ? '/ledger' : '/ledger/income' }">
                  <div class="inner">
                    <h3>@{{{ currencySign() }}}@{{ statistics.debits }}</h3>
                    <p>Income</p>
                  </div>
                  <div class="icon">
                    <i class="fa fa-dollar"></i>
                  </div>

                </div>
              </div><div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-red" v-link="{ path: filter == 'expenses' ? '/ledger' : '/ledger/expenses' }">
                  <div class="inner">
                    <h3>@{{{ currencySign() }}}@{{ statistics.credits }}</h3>
                    <p>Expenses</p>
                  </div>
                  <div class="icon">
                    <i class="fa fa-shopping-cart"></i>
                  </div>

                </div>
              </div><!-- ./col -->
              <div class="col-lg-3 col-xs-6" v-if="filter !== 'archive'">
                <!-- small box -->
                <div class="small-box bg-aqua">
                  <div class="inner">
                    <h3>@{{{ currencySign() }}}@{{ statistics.debitPerBreeder }}</h3>
                    <p>Income per Breeder</p>
                  </div>
                  <div class="icon">
                    <i class="fa fa-venus-mars"></i>
                  </div>

                </div>
              </div><!-- ./col -->
              <!-- ./col -->
              <div class="col-lg-3 col-xs-6" v-if="filter !== 'archive'">
                <!-- small box -->
                <div class="small-box bg-blue">
                  <div class="inner">
                    <h3>@{{{ currencySign() }}}@{{ statistics.creditPerBreeder }}</h3>
                    <p>Expenses per Breeder</p>
                  </div>
                  <div class="icon">
                    <i class="fa fa-venus-mars"></i>
                  </div>

                </div>
              </div><!-- ./col -->
            </div>

            <ledger-table :canLoading="!loading" :filter="filter" :page="page"
                          :order.sync="order" v-ref:table></ledger-table>
          <!-- </div> -->

        </section><!-- /.content -->
  </div>
</template>
