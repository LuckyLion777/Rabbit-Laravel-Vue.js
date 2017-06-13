<template id="litter-profile-template">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Litter Profile 
        <div class="btn-group">
          <button class="btn btn-default carrot-btn dropdown-toggle" title="Actions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fa fa-caret-down" aria-hidden="true"></i>
          </button>
          <ul class="dropdown-menu">
              <li><a href="#" @click.prevent="editModal"><i class="fa fa-pencil" ></i> Edit</a></li>
              <li v-if="litter.archived=='0'"><a href="#" @click.prevent="weightModal"><i class="fa fa-balance-scale"></i> Weigh</a></li>
              <li v-if="litter.archived=='0'"><a href="#" @click.prevent="butcherModal"><i class="fa fa-cutlery"></i> Butcher</a></li>
              @if(Auth::user()->isPremium())
                <li><a href="#" @click.prevent="cageCard()"><i class="fa fa-list-alt"></i> Cage Card</a></li>
              @endif
              <li v-if="litter.archived=='0'"><a href="#" @click.prevent="archiveModal"><i class="fa fa-archive"></i> Archive</a></li>
              <li><a href="#" @click.prevent="deleteModal"><i class="fa fa-trash"></i> Delete</a></li>
          </ul>
        </div></h1>
        <ol class="breadcrumb">
            <li><a href="#" v-link="{ path: '/' }"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#" v-link="{ path: '/litters' }">Litters</a></li>
            <li class="active">Litter profile</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="row" v-if="!litterLoad">
            <div class="col-md-12 text-center">
                <i class="fa fa-spinner fa-spin fa-3x"></i>
            </div>
        </div>

        <div class="row" v-if="litterLoad">
            <div class="col-md-5 col-lg-3">

                <div class="box box-success">
                    <div class="box-body box-profile">
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="profile-img-container" v-bind:class="{'loading': loadingImageFather}">
                                    <img v-show="father().image.path" v-bind:alt="father().name" v-bind:src="father().image.path"
                                         class="profile-user-img img-responsive img-circle buck-avatar-border litter">
                                    <a href="" @click.prevent="uploaderHelperFather" v-if="!loadingImageFather"><span class="fa fa-pencil fa-5x text-blue" ></span></a>
                                    <a href="" @click.prevent="" v-if="loadingImageFather">
                                        <span>
                                            <i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw text-green"></i>
                                        </span>
                                    </a>
                                </div>

                                <div class="hidden">
                                    <input v-el:imagefather type="file" name="file" v-bind:alt="father().name" >
                                </div>
                                <a v-link="{ path : '/profile/' + father().id }"><h3 class="profile-username text-center">@{{ father().name  }}</h3></a>
                            </div>

                            <div class="col-xs-6">
                                <div class="profile-img-container" v-bind:class="{'loading': loadingImageMother}">
                                    <img v-show="mother().image.path" class="profile-user-img img-responsive img-circle doe-avatar-border litter"
                                         v-bind:src="mother().image.path" v-bind:alt="mother().name">
                                    <a href="" @click.prevent="uploaderHelperMother" v-if="!loadingImageMother"><span class="fa fa-pencil fa-5x text-red" ></span></a>
                                    <a href="" @click.prevent="" v-if="loadingImageMother">
                                        <span>
                                            <i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw text-green"></i>
                                        </span>
                                    </a>
                                </div>
                                <div class="hidden">
                                    <input v-el:imagemother type="file" name="file" v-bind:alt="mother().name" >
                                </div>
                                <a v-link="{ path : '/profile/' + mother().id }"><h3 class="profile-username text-center">@{{ mother().name }}</h3></a>
                            </div>
                        </div>
                        <hr class="margin">
                        <div v-if="litter.bred || litter.born">
                            <div class="row">
                                <div  class="col-xs-6 border-right"><p v-if="litter.bred" class=" text-center"><strong>Bred</strong><br>@{{ litter.bred }}
                                    </p></div>
                                <div  class="col-xs-6"><p v-if="litter.born" class="text-center"><strong>Born</strong><br>@{{ litter.born }}</p></div>
                            <hr class="margin">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6 border-right"><p class="text-center"><strong>Litter ID</strong><br>@{{ litter.given_id }}</p>
                            </div>

                            <div class="col-xs-6"><p class="text-center"><strong>Live Kits</strong><br>@{{ aliveKits }}</p></div>
                        </div>
                        {{--<div v-show="litter.notes">--}}
                            {{--<hr class="margin">--}}
                            {{--<strong><i class="fa fa-file-text-o margin-r-5"></i> Notes</strong>--}}
                            {{--<p>@{{ litter.notes }}</p>--}}
                        {{--</div>--}}
                    </div><!-- /.box-body -->
                </div><!-- /.box -->

            </div>

            <div class="col-md-7 col-lg-9">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#litters" aria-expanded="true" title="Kits"><i class="fa fa-th"></i> <span class="hidden-xs">Kits</span></a></li>
                        <li class=""><a data-toggle="tab" @click.prevent="loadRabbit(litter, 'litters')" href="#timeline" aria-expanded="false" title="Timeline"><i class="fa fa-calendar"></i> <span class="hidden-xs">Timeline</span></a></li>
                        <li class=""><a data-toggle="tab" @click.prevent="loadLedger()" href="#ledger" aria-expanded="false" title="Ledger"><i class="fa fa-calculator"></i> <span class="hidden-xs">Ledger</span></a></li>
                        <li class=""><a @click.prevent="" aria-expanded="false" href="#notes" data-toggle="tab" title="Notes"><i class="fa fa-files-o"></i> <span class="hidden-xs">Notes</span></a></li>
                    </ul>
                    <div class="tab-content">

                        <div class="tab-pane active" id="litters">
                            <div class="row">

                                <litter-box @edit-kit="editKit" @alive-kits="updateAlive" :litter.sync="litter" v-on:refresh-kits="refreshKits"></litter-box>

                            </div>
                        </div>

                        <div id="timeline" class="tab-pane">
                            <div class="tab-pane" id="timeline">
                                @include('layouts.profile._timeline')
                            </div><!-- /.tab-pane -->
                        </div><!-- /.tab-pane -->

                        <div id="ledger" class="tab-pane">
                            @include('layouts.profile._ledger-litter')
                        </div><!-- /.tab-pane -->


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

            </div>
        </div>

    </section>


    <kit-form :kit.sync="activeKit" :litter.sync="litter" v-on:refresh-kits="refreshKits"></kit-form>

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

    @include('layouts.litter.modals.butcher', ['litter' => 'litter', 'kits' => 'kits', 'refresh' => 'refreshKits', 'nosync' => true])

    <div id="litter-weight-modal" class="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <litter-weight v-on:refresh-kits="refreshKits" :litter.sync="activeLitter" :litters="[activeLitter]" :kits.sync="activeKits"></litter-weight>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    @include('layouts.litter.modals.litter')
    @include('layouts.archive-delete')
    <!-- image cropper modal -->
    <div class="modal image-cropper-modal" id="image-cropper-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success bg-info">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title">Resize image</h4>
                </div>
                <div class="modal-body">
                    <img class="image-cropper-container" />
                </div>
                <div class="modal-footer bg-success bg-info">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success btn-info" @click.prevent="uploadImage()">Crop/Upload</button>
                </div>
            </div>
        </div>
    </div><!-- /.image cropper modal -->

    @if(Auth::user()->isPremium())
        <!-- Cage card print modal -->
        @include('layouts.cage-cards.print-modal')
    @endif
</template>
