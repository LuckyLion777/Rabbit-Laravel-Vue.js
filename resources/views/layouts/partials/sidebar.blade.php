<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">
            <!-- Optionally, you can add icons to the links -->
            <li v-link-active>
                <a href="#" v-link="{ path: '/', activeClass: 'active', exact: true }">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>
            <li class="treeview">
                <a href="#" v-link="{ path: '/breeders' }"><i class="fa fa-venus-mars"></i> <span>Breeders</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a v-link="{ path: '/breeders', query: { newModal: true }, activeClass: 'active', exact: true }" href="#">Add New</a></li>
                    <li v-link-active><a v-link="{ path: '/wizard/breeders', activeClass: 'active' }" href="#">Add Many</a></li>
                    <li v-link-active><a v-link="{ path: '/breeders', activeClass: 'active', exact: true }" href="#">All</a></li>
                    <li v-link-active><a v-link="{ path: '/breeders/does', activeClass: 'active' }" href="#">Does</a></li>
                    <li v-link-active><a v-link="{ path: '/breeders/bucks', activeClass: 'active' }" href="#">Bucks</a></li>
                    <li v-link-active><a v-link="{ path: '/breeders/categories', activeClass: 'active' }">Categories</a></li>
                    <li v-link-active><a v-link="{ path: '/breeders/archive', activeClass: 'active' }" href="#">Archive</a></li>
                    <li v-link-active><a v-link="{ path: '/breeders/butchered', activeClass: 'active' }" href="#">Butchered</a></li>
                    <li v-link-active><a v-link="{ path: '/breeders/sold', activeClass: 'active' }" href="#">Sold</a></li>
                    <li><a href="/admin/export">Export</a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#" v-link="{ path: '/litters', activeClass: 'active' }"><i class="fa fa-th"></i> <span>Litters</span> <i
                            class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a v-link="{ path: '/litters', query: { newModal: true }, activeClass: 'active', exact: true }">Add New</a></li>
                    <li v-link-active><a href="#" v-link="{ path: '/litters', activeClass: 'active', exact: true }">All</a></li>
                    <li v-link-active><a href="#" v-link="{ path: '/litters/butchered', activeClass: 'active' }">Butchered</a></li>
                    <li v-link-active><a href="#" v-link="{ path: '/litters/archive', activeClass: 'active' }">Archive</a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#" v-link="{ path: '/schedule', activeClass: 'active' }">
                    <i class="fa fa-calendar"></i> <span>Schedule</span><i class="fa fa-angle-left pull-right"></i>

                    {{--<small class="label pull-right bg-red">3</small>--}}
                </a>
                <ul class="treeview-menu">
                    <li><a href="#" v-link="{ path: '/schedule', query: { new: true }, activeClass: 'active', exact: true }">Add New</a></li>
                    <li v-link-active><a href="#" v-link="{ path: '/schedule', activeClass: 'active', exact: true }">All</a></li>
                    <li v-link-active><a href="#" v-link="{ path: '/schedule/general', activeClass: 'active', exact: true }">General</a></li>
                    <li v-link-active><a href="#" v-link="{ path: '/schedule/breeder', activeClass: 'active', exact: true }">Breeder</a></li>
                    <li v-link-active><a href="#" v-link="{ path: '/schedule/litter', activeClass: 'active', exact: true }">Litter</a></li>
                    <li v-link-active><a href="#" v-link="{ path: '/schedule/archived', activeClass: 'active', exact: true }">Archived</a></li>
                    <li v-link-active><a href="#" v-link="{ path: '/schedule/plans', activeClass: 'active', exact: true }">Plans</a></li>
                    @if (Auth::check())
                        <li><a href="/ics/{{ Auth::id() }}/{{ CryptHash::hash('user:' . Auth::id() . ':schedule') }}"
                               onclick="App.vent.trigger('export-schedule'); return false">Export</a></li>
                    @endif
                </ul>
            </li>
            @if($currentUser->isPremium())
                <li class="treeview">
                    <a href="#" v-link="{ path: '/ledger', activeClass: 'active' }"><i class="fa fa-calculator"></i> <span>Ledger</span> <i
                                class="fa fa-angle-left pull-right"></i></a>
                    <ul class="treeview-menu">
                        <li><a href="#" v-link="{ path: '/ledger', query: { new: true }, activeClass: 'active', exact: true }">Add New</a></li>
                        <li v-link-active><a href="#" v-link="{ path: '/ledger', activeClass: 'active', exact: true }">All</a></li>
                        <li v-link-active><a href="#" v-link="{ path: '/ledger/income', activeClass: 'active' }">Income</a></li>
                        <li v-link-active><a href="#" v-link="{ path: '/ledger/expenses', activeClass: 'active' }">Expenses</a></li>
                        <li v-link-active><a href="#" v-link="{ path: '/ledger/categories', activeClass: 'active', exact: true }">Categories</a></li>
                        <li v-link-active><a href="#" v-link="{ path: '/ledger/archive', activeClass: 'active' }">Archive</a></li>
                    	<li v-link-active>
                    		<import-ledgers-file></import-ledgers-file>
                    	</li>
                    	<li>
                    		<a href="/admin/ledgers/export">Export</a>
                    	</li>

                    </ul>
                </li>
                <li v-link-active>
                    <a href="#" v-link="{ path: '/reports', activeClass: 'active', exact: true }">
                        <i class="fa fa-line-chart"></i> <span>Reports</span>
                    </a>
                </li>
                <!-- Cage cards -->
                <li class="treeview">
                    <a href="#" v-link="{ path: '/cage-cards/templates', activeClass: 'active' }">
                        <i class="fa fa-list-alt"></i>
                        <span>Cage Cards</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu" style="display: display;">
                        <li><a href="#" v-link="{ path: '/cage-cards/templates', query: { newCageCardTemplateModal: true }, activeClass: 'active', exact: true }">Add new</a></li>
                        <li v-link-active><a href="#" v-link="{ path: '/cage-cards/templates', activeClass: 'active', exact: true }">Templates</a></li>
                        <li><a href="#" v-link="{ path: '/cage-cards/templates', query: { printBatchModal: true }, activeClass: 'active', exact: true }">Print Batch</a></li>
                    </ul>
                </li>

            @endif
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-support"></i> <span>Help</span><i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="https://www.facebook.com/groups/barntrax" target="_blank">Facebook Group</a></li>
                    <li><a href="mailto:hutch@barntrax.com">Email</a></li>
                </ul>
            </li>

            @if($currentUser->can('manageUsers'))
                <li v-link-active>
                    <a href="#" v-link="{ path: '/users', activeClass: 'active' }">
                        <i class="fa fa-user"></i> <span>Users</span>
                    </a>
                </li>
                <li>
                    <a href="#" id="broadcast-modal-trigger">
                        <i class="fa fa-volume-up"></i> <span>Broadcast</span>
                    </a>
                </li>
            @endif
        </ul><!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>
