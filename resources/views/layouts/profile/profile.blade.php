<template id="profile-template">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> @{{ breedSex }} Profile 
        <div class="btn-group">
          <button class="btn btn-default carrot-btn dropdown-toggle" title="Actions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fa fa-caret-down" aria-hidden="true"></i>
          </button>
          <ul class="dropdown-menu">
              <li v-if="!breeder.archived && !breeder.sold_at"><a href="#" @click.prevent="edit"><i class="fa fa-pencil" ></i> Edit</a></li>
              @if(Auth::user()->isPremium())
                <li v-if="!breeder.archived && !breeder.sold_at && !breeder.died"><a href="#" @click.prevent="cageCard()"><i class="fa fa-list-alt"></i> Cage Card</a></li>
              @endif
              <li v-if="!breeder.archived && !breeder.sold_at"><a href="#" @click.prevent="soldModal(breeder.id)"><i class="fa fa-dollar"></i> Sell</a></li>
              <li v-if="!breeder.butchered && !breeder.died"><a href="#" @click.prevent="butcherBreederModal"><i class="fa fa-cutlery"></i> Butcher</a></li>
              <li v-if="breeder.butchered"><a href="#" @click.prevent="undoButcher"><i class="fa fa-cutlery"></i> Undo Butcher</a></li>
              <li v-if="!breeder.died && !breeder.butchered"><a href="#" @click.prevent="showDied(kit)"><i class="fa fa-heart-o"></i> Died</a></li>
              <li v-if="breeder.died"><a href="#" @click.prevent="undoDied"><i class="fa fa-heart-o"></i> Undo Died</a></li>
              <li v-if="!breeder.archived && !breeder.sold_at"><a href="#" @click.prevent="archiveModal(breeder.id)"><i class="fa fa-archive"></i> Archive</a></li>
              <li><a href="#" @click.prevent="deleteModal((breeder.id))"><i class="fa fa-trash"></i> Delete</a></li>
          </ul>
        </div></h1>
        <ol class="breadcrumb">
            <li><a href="#" v-link="{ path: '/' }"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#" v-link="{ path: '/breeders' }">Breeders</a></li>
            <li class="active">@{{ breeder.name }}</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="row">
            <div class="col-md-5 col-lg-3">
                <!-- Profile Image -->
                <div class="box" v-bind:class="breedSexClass">
                    <div class="box-body box-profile">
                        <div v-if="breeder.pregnant" class="bred-icon" title="Bred"><i class="fa fa-heartbeat"></i></div>
                        <div class="profile-img-container" v-bind:class="{'loading': loading}">
                            <img  class="profile-user-img img-responsive img-circle breeder" src="img/rabbit1.jpg"
                                  v-bind:alt="breeder.name" v-bind:src="breeder.image.path">
                            <a href="" @click.prevent="uploaderHelper" v-if="!loading"><span class="fa fa-pencil fa-5x text-blue" ></span></a>
                            <a href="" @click.prevent="" v-if="loading">
                                <span>
                                    <i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw text-green"></i>
                                </span>
                            </a>
                        </div>
                        <div class="hidden">
                            <input v-el:image type="file" name="file" v-bind:alt="breeder.name" >
                        </div>

                        <h3 class="profile-username text-center"><small v-if="breeder.prefix" class="prefix text-center">@{{ breeder.prefix }}<br /></small> @{{ breeder.name }}</h3>
                        <div class="row box-profile__properties">
                            <div v-if="breeder.tattoo" class="col-xs-4"><p class=" text-center"><strong>ID</strong><br>@{{ breeder.tattoo }}</p></div>
                            <div v-if="breeder.cage" class="col-xs-4"><p class=" text-center"><strong>Cage</strong><br>@{{ breeder.cage }} </p></div>
                            <div v-if="breeder.weight" class="col-xs-4"><p class="text-center"><strong>Weight</strong><br>@{{ breeder.weight_slug }}</p></div>
                            <div class="col-xs-4"><p class="text-center"><strong>Category</strong><br>@{{ breeder.cat_name }}</p></div>
                            <div v-if="breeder.breed" class="col-xs-4"><p class="text-center"><strong>Breed</strong><br>@{{ breeder.breed }}</p></div>
                            <div v-if="breeder.color" class="col-xs-4"><p class="text-center"><strong>Color</strong><br>@{{ breeder.color }}</p></div>
                            <div v-if="breeder.aquired" class="col-xs-4"><p class="text-center"><strong>Aquired</strong><br>@{{ breeder.aquired }}</p></div>
                            <div v-if="breeder.date_of_birth" class="col-xs-4"><p class="text-center"><strong>Born</strong><br>@{{ breeder.date_of_birth }}</p></div>
                            <div v-if="breeder.date_of_birth" class="col-xs-4"><p class="text-center"><strong>Age</strong><br>@{{ showAge(breeder) }}</p></div>
                            <div v-if="breeder.registration_number" class="col-xs-4"><p class="text-center"><strong>Reg #</strong><br>@{{ breeder.registration_number }}</p></div>
                            <div v-if="breeder.champion_number" class="col-xs-4"><p class="text-center"><strong>Champ #</strong><br>@{{ breeder.champion_number }}</p></div>
                            <div v-if="breeder.legs" class="col-xs-4"><p class="text-center"><strong>Legs</strong><br>@{{ breeder.legs }}</p></div>
                        </div>
                        <hr class="margin">
                        <div class="row">
                            <div class="col-xs-6 border-right"><p class="text-muted text-center"><strong>Father</strong><br>{{-- getParent(breeder.father) --}}
                                    <span v-if="breeder.father.name != ''">@{{breeder.father.name}}</span>
                                    <span v-if="breeder.pedigree_father[0].name != null">@{{breeder.pedigree_father[0].name}}</span>
                                    <span v-if="breeder.pedigree_father[0].name == null">Unknown</span>
                                </p>
                            </div>


                            <div class="col-xs-6"><p class="text-muted text-center"><strong>Mother</strong><br>
                                    {{-- getParent(breeder.mother) --}}
                                    <span v-if="breeder.mother.name != ''">@{{breeder.mother.name}}</span>
                                    <span v-if="breeder.pedigree_mother[0].name != null">@{{breeder.pedigree_mother[0].name}}</span>
                                    <span v-if="breeder.pedigree_mother[0].name == null">Unknown</span>
                                </p>
                            </div>

                        </div>
                        <hr class="margin">
                        <div class="row">
                            <div class="col-xs-6 border-right "><p class="text-center"><strong>Live Kits</strong><br>@{{ breeder.live_kits }}</p></div>
                            <div class="col-xs-6"><p class="text-center"><strong>Kits To Date</strong><br>@{{ breeder.kits }}</p></div>
                        </div>

                    </div><!-- /.box-body -->
                </div><!-- /.box -->

                <!-- /.box -->
            </div>
            <div class="col-md-7 col-lg-9">

                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a aria-expanded="true" href="#litters" data-toggle="tab" title="Litters"><i class="fa fa-th"></i> <span class="hidden-xs">Litters</span></a></li>
                        <li class=""><a @click.prevent="loadRabbit(breeder, 'breeders')" aria-expanded="false" href="#timeline" data-toggle="tab" title="Timeline"><i class="fa fa-calendar"></i> <span class="hidden-xs">Timeline</span></a></li>
                        <li class=""><a @click.prevent="loadPedigree" aria-expanded="false" href="#pedigree" data-toggle="tab" title="Pedigree"><i class="fa fa-share-alt"></i> <span class="hidden-xs">Pedigree</span></a></li>
                        <li class=""><a @click.prevent="loadLedger" aria-expanded="false" href="#ledger" data-toggle="tab" title="Ledger"><i class="fa fa-calculator"></i> <span class="hidden-xs">Ledger</span></a></li>
                        <li class=""><a @click.prevent="" aria-expanded="false" href="#notes" data-toggle="tab" title="Notes"><i class="fa fa-files-o"></i> <span class="hidden-xs">Notes</span></a></li>
                    </ul>
                    <div class="tab-content">

                        <div id="litters" class="tab-pane active">
                            <div class="row" v-if="loadingLitters">
                                <div class="col-xs-1 col-xs-offset-5">
                                    <img src="/img/ajax-loader.gif" alt="Loading..." class="loader">
                                </div>
                            </div>
                            <div class="row" v-if="!loadingLitters && !litters.length">
                                <div class="col-xs-4 col-xs-offset-5">
                                    No Litters
                                </div>
                            </div>
                            <div v-for="litter in litters">
                                <div class="row">
                                    <div class="col-md-12">
                                        {{--<div class="box box-success" v-bind:class="{ 'box-default': litter.archived == 1, 'collapsed-box': litter.archived == 1 }">--}}
                                        <div class="box box-success" v-bind:class="{ 'box-default': litter.archived == 1 }">
                                            {{--<a data-widget="collapse" href="#">--}}
                                                {{--<div class="box-header" v-bind:class="{ 'bg-gray-active': litter.archived == 1, 'bg-olive': litter.archived == 0, 'collapsed-box': litter.archived == 1 }">--}}
                                                <div class="box-header" v-bind:class="{ 'bg-gray-active': litter.archived == 1, 'bg-olive': litter.archived == 0 }">
                                                    <h3 class="box-title">Litter @{{ litter.given_id }}</h3>
                                                    <h5 class="widget-user-desc">Born: @{{ litter.born }}</h5>
                                                    <h5 class="widget-user-desc">@{{ getSecondParentSex(litter) }}: @{{ getSecondParentName(litter) }}</h5>
                                                    <span v-if="litter.archived == 1 && !litter.butchered_at">Archived: @{{ makeDate(litter.archived_at) }}</span>
                                                    <span v-if="litter.butchered_at">Butchered: @{{ makeDate(litter.butchered_at) }}</span>
                                                    {{--                                                 <div class="box-statistics" >
                                                                                                         <div></div>
                                                                                                         <div>Total weight: <b>@{{ litter.total_weight }}</b></div>
                                                                                                         <div>Average weight: <b>@{{ litter.total_weight }}</b></div>
                                                                                                     </div>--}}
                                                    {{--<div class="box-tools pull-right btn-group">--}}
                                                        {{--<button v-if="litter.archived != 1" href="#"--}}
                                                                {{--class="btn btn-outline"--}}
                                                                {{--@click.prevent="editLitterModal(litter)"--}}
                                                                {{--title="Edit"><i class="fa fa-pencil"></i>--}}
                                                        {{--</button>--}}
                                                        {{--<button v-if="litter.archived != 1" href="#"--}}
                                                                {{--class="btn btn-outline"--}}
                                                                {{--@click.prevent="weightLitter(litter)"--}}
                                                                {{--title="Weigh"><i class="fa fa-balance-scale"></i>--}}
                                                        {{--</button>--}}
                                                        {{--<button v-if="litter.archived != 1 && !litter.butchered" class="btn btn-outline"--}}
                                                                {{--href="#"--}}
                                                                {{--@click.prevent="butcherModal(litter)" title="Butcher">--}}
                                                            {{--<i class="fa fa-cutlery"></i></button>--}}
                                                        {{--<button v-if="litter.archived != 1" class="btn btn-outline"--}}
                                                                {{--@click.prevent="archiveLitterModal(litter)" title="Archive"><i--}}
                                                                    {{--class="fa fa-archive"></i></button>--}}
                                                        {{--<button class="btn btn-outline"--}}
                                                                {{--@click.prevent="deleteLitterModal(litter)" title="Delete"><i--}}
                                                                    {{--class="fa fa-trash"></i></button>--}}
                                                    {{--</div>--}}

                                                    <div class="btn-group pull-right box-tools dropdown">
                                                        <button  class="btn btn-outline carrot-btn dropdown-toggle" title="Edit"
                                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="fa fa-caret-down" aria-hidden="true"></i>
                                                        </button>
                                                        <ul class="dropdown-menu breed-profile-action">

                                                            <li>
                                                                <a href="#" @click.prevent="editLitterModal(litter)"><i class="fa fa-pencil" ></i> Edit</a>
                                                            </li>

                                                            <li  v-if="litter.archived=='0'">
                                                                <a href="" @click.prevent="weightLitter(litter)"><i class="fa fa-balance-scale"></i> Weight</a>
                                                            </li>
                                                            <li v-if="litter.archived=='0'">
                                                                <a href="" @click.prevent="butcherModal(litter)"><i class="fa fa-cutlery"></i> Butcher</a>
                                                            </li>
															@if(Auth::user()->isPremium())
                                                            <li v-if="litter.archived=='0'">
                                                                <a href="#" @click.prevent="openLitter('cageCard', litter.id)"><i class="fa fa-list-alt"></i> Cage Card</a>
                                                            </li>
															@endif
                                                            <li v-if="litter.archived=='0'">
                                                                <a href="" @click.prevent="archiveLitterModal(litter)"><i class="fa fa-archive"></i> Archive</a>
                                                            </li>
                                                            {{--<li v-if="litter.archived=='1'">
                                                                <a href="" @click.prevent="unarchiveModal(litter)"><i class="fa fa-expand"></i> Unarchive</a>
                                                            </li>--}}
                                                            <li>
                                                                <a href="" @click.prevent="deleteLitterModal(litter)"><i class="fa fa-trash"></i> Delete</a>
                                                            </li>
                                                        </ul>

                                                        
                                                    </div>

                                                    <!-- /.box-tools -->
                                                </div><!-- /.box-header -->
                                            {{--</a>--}}
                                            {{--<div class="box-body" v-bind:style="{ display: litter.archived == 1 ? 'none' : 'block' }">--}}
                                            <div class="box-body" style="display: block;">

                                                <litter-box v-on:edit-kit="editKit" :litter.sync="litter"></litter-box>

                                            </div><!-- /.box-body -->
                                        </div><!-- /.box -->
                                    </div>
                                </div>


                            </div>
                        </div>

                        <div class="tab-pane" id="timeline">
                            @include('layouts.profile._timeline')
                        </div><!-- /.tab-pane -->

                        <div class="tab-pane" id="pedigree">
                            @include('layouts.profile._pedigree')
                        </div><!-- /.tab-pane -->

                        <div class="tab-pane" id="ledger">
                            @include('layouts.profile._ledger')
                        </div><!-- /.tab-profile -->

                        {{--NOTES--}}
                        <div class="tab-pane" id="notes">
                            <form>
                                <div class="row">
                                    <div class="form-group col-sm-12">
                                        <label class="col-sm-2 control-label">Notes</label>
                                        <div class="col-sm-6">
                                            <textarea v-model="newNote" placeholder="Descriptions" rows="3"
                                                  class="form-control"></textarea>
                                        </div>
                                        <div class="col-sm-4">
                                            <button type="button" @click="addNote" class="btn btn-success">Save changes</button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <table class="table table-hover">
                                            <tbody>
                                                <tr v-for="(index, note) in breederNotes">
                                                    <td>

                                                        <div class="row" v-show="noteIndexToEdit.num == index">
                                                            <div class="col-xs-9">
                                                               <textarea v-model="noteIndexToEdit.not" placeholder="Descriptions" rows="3"
                                                                         class="form-control"></textarea>
                                                            </div>
                                                            <div class="col-xs-3">
                                                                <button type="button" @click="editNote" class="btn btn-success">Save</button>
                                                            </div>
                                                        </div>

                                                        <span v-show="noteIndexToEdit.num != index">@{{note}}</span>

                                                    </td>
                                                    <td style="width: 15%;">
                                                        <button class="btn btn-default" v-on:click="setNote(note, index)"><i class="fa fa-pencil"></i></button>
                                                        <button class="btn btn-default" v-on:click="deleteNote(index)"><i class="fa fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                        {{--NOTES END--}}

                    </div><!-- /.tab-content -->
                </div><!-- /.nav-tabs-custom -->

            </div><!-- /.col -->
        </div><!-- /.row -->

        <!-- image cropper modal -->
        <div class="modal" id="profile-image-cropper-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-success bg-info">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">Resize image</h4>
                    </div>
                    <div class="modal-body">
                        <img class="image-cropper-container" style="width: 100%" />
                    </div>
                    <div class="modal-footer bg-success bg-info">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success btn-info" @click.prevent="uploadImage()">Crop/Upload</button>
                    </div>
                </div>
            </div>
        </div><!-- /.image cropper modal -->

        @include('layouts.breeders.partials.breeder')
        @include('layouts.archive-delete')
        @include('layouts.litter.modals.butcher')
        @include('layouts.died', ['unique' => '-breeder'])

        @include('layouts.litter.modals.breeder-butcher')

        <div class="modal modal-danger" id="delete-litter-modal">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12 text-center"><h3><i class="fa fa-fw fa-warning"></i><br>
                                    Do you want to delete this litter?</h3>
                            </div>
                        </div>
                        <div class="row margin">
                            <div class="col-sm-12 text-center">
                                <button class="btn btn-outline" type="button" @click="deleteLitter"><i class="fa fa-check"></i> Yes</button>
                                <button type="button" class="btn btn-outline" data-dismiss="modal"><i
                                            class="fa fa-close"></i> No
                                </button>
                                <button data-dismiss="modal" class="btn btn-outline" @click="archiveLitter" type="button"><i
                                        class="fa fa-archive"></i> Archive
                                </button>
                            </div>
                        </div>

                    </div>

                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>


        <div class="modal modal-default" id="archive-litter-modal">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-body bg-gray">
                        <div class="row">
                            <div class="col-sm-12 text-center"><h3><i class="fa fa-archive"></i><br>
                                    Do you want to archive this litter?</h3>
                            </div>
                        </div>
                        <div class="row margin">
                            <div class="col-sm-12 text-center">
                                <button class="btn btn-default" type="button" @click="archiveLitter"><i class="fa fa-check"></i> Yes</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal"><i
                                            class="fa fa-close"></i> No
                                </button>
                                <button data-dismiss="modal" class="btn btn-default" @click="deleteLitter" type="button"><i
                                        class="fa fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>

                    </div>

                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>

        <div class="modal modal-danger in" id="delete_task" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12 text-center">
                                <h3><i class="fa fa-fw fa-warning"></i><br>Do you want to delete @{{ activeTask.name || 'closed tasks' }}?</h3>
                            </div>
                        </div>
                        <div class="row margin">
                            <div class="col-sm-12 text-center">
                                <button @click.prevent="deleteTask(activeTask)" class="btn btn-outline" type="button"><i class="fa fa-check"></i> Yes</button>
                                <button type="button" class="btn btn-outline" data-dismiss="modal"><i class="fa fa-close"></i> No </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div><!-- /.modal-dialog -->
        </div>

        <div class="modal in" id="new_task" style="display: none;">
            <div class="modal-dialog">
                <task-form :activeTask="activeTask"></task-form>
            </div><!-- /.modal-dialog -->
        </div>

        <div id="litter-weight-modal" class="modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <litter-weight :litter="activeLitter" :litters="litters" :kits.sync="activeKits"></litter-weight>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>


        <div id="litter-form" class="modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <litter-form :litter="activeLitter" :litters="litters" :kits.sync="activeKits"></litter-form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>

        <kit-form :kit.sync="activeKit" :litters.sync="litters" v-on:refresh-kits="refreshKits"></kit-form>

    </section><!-- /.content -->

    @if(Auth::user()->isPremium())
        <!-- Cage card print modal -->
        @include('layouts.cage-cards.print-modal')
    @endif
    
</template>
