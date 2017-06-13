<template id="cage-cards-templates-list" xmlns:v-bind="http://www.w3.org/1999/xhtml">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Cage Card Templates

            <div class="btn-group">
                <button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button" aria-expanded="false">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul role="menu" class="dropdown-menu">
                    <li><a @click.prevent="printModal()" href="#">Print Batch</a></li>
                    <li class="divider"></li>
                    <li><a v-link="{ path: '/cage-cards/templates', activeClass: 'bold', exact: true }" href="#">Templates</a></li>
                    <li class="divider"></li>
                    <li><a @click.prevent="addNewCageCardTemplateModal()" href="#">Add New</a></li>
                </ul>
            </div>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i>Home</a></li>
            <li class="active">Here</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Your Page Content Here -->
        <div class="box box-solid box-default">

            <div class="box-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Size</th>
                        <th>Orientation</th>
                        <th><span class="sr-only">Actions</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr id="cage-card-template-row-@{{ template.id }}" v-for="template in paginatedtemplates">
                        <td>@{{ template.name }}</td>
                        <td>@{{ template.type | capitalize }}</td>
                        <td>@{{ template.size }}</td>
                        <td>@{{ template.orientation }}</td>
                        <td>
                            <div class="btn-group pull-right">
                                <button @click.prevent="editCageCardTemplateModal(template)" title="Edit" class="btn btn-default btn-sm"><i class="fa fa-pencil"></i></button>
                                <button @click.prevent="copy(template)" title="Copy" class="btn btn-default btn-sm"><i class="fa fa-copy"></i></button>
                                <button id="print-modal-btn-@{{ template.id }}" @click.prevent="printModal(template)" title="Print" class="btn btn-default btn-sm"><i class="fa fa-print"></i></button>
                                <button @click.prevent="deleteModal(template)" title="Delete" class="btn btn-default btn-sm" style=""><i class="fa fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row" v-if="!disableLoadMore">
            <p class="col-lg-4 col-md-6">
                <a @click="loadMore()" class="btn btn-primary btn-lg">Load More</a>
            </p>
        </div>

        <div class="row">
            <div class="col-lg-4 col-md-6">
                <a href="#" @click.prevent="addNewCageCardTemplateModal">
                    <div class="info-box">
                        <span class="info-box-icon bg-gray text-muted"><i class="fa fa-plus"></i></span>
                        <div class="info-box-content text-muted">
                            <h1>Add New</h1>
                        </div><!-- /.info-box-content -->
                    </div><!-- /.info-box -->
                </a>
            </div>
        </div>
    </section>

    <!-- Delete cage card template modal -->
    @include('layouts.cage-cards.delete-modal')

    <!-- Copy cage card template modal -->
    @include('layouts.cage-cards.copy-modal')

    <!-- Cage card template form modal -->
    @include('layouts.cage-cards.template-form-modal')

    <!-- Cage card print modal -->
    @include('layouts.cage-cards.print-modal')

</template>

