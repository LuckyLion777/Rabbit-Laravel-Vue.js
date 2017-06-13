<template id="upcomming-tasks-template">
    <!-- Your Page Content Here -->

    <div class="col-sm-12" v-if="type_of_task != 'plans' && $route.path.startsWith('/schedule')">
        <div class="input-group input-group col-md-2" style="margin-bottom: 20px">
            <input type="text" class="form-control" placeholder="Search for..." v-model="searchQuery" debounce="500">
                  <span class="input-group-addon">
                    <i class="fa fa-search" aria-hidden="true"></i>
                  </span>
        </div>
    </div>
    <div class="col-sm-12" v-if="loading && $route.path.startsWith('/schedule')">
        <h1 class="loader"><i class="fa fa-spin fa-spinner"></i></h1>
        <!-- <img src="/img/ajax-loader.gif" alt="Loading..." class="loader"> -->
    </div>
    <section class="col-sm-6 col-md-5 col-lg-4">
        <div class="box box-primary">
            <div class="box-header">
                <i class="ion ion-clipboard"></i>
                <h3 v-if="type_of_task != 'plans'" class="box-title">Tasks</h3>
                <h3 v-if="type_of_task == 'plans'" class="box-title">Plans</h3>
                <button v-if="type_of_task != 'archived' && type_of_task != 'plans'" @click.prevent="showRequestForm(null)" type="button" class="btn btn-xs pull-right">Clear</button>
            </div><!-- /.box-header -->

            <div class="box-body">
                <ul class="todo-list" id="todo-list-fastclick">
                    <li id="id_task_@{{task.id}}"  v-for="(index, task) in activeTasks" v-if="type_of_task != 'plans'">
                        <!-- checkbox -->
                        <input v-if="type_of_task != 'archived'" class="needsclick" @click="finishTask(task)" type="checkbox" v-model="task.closed == 1" name="">
                        <!-- todo text -->
                        <i class="fa @{{ task.icon }} circle-background"></i>
                        <span v-bind:class="['text', (task.closed == 1) ? 'task-is-close' : '']" > @{{ getFullTaskName(task) }} </span>
                        <!-- Emphasis label -->
                        <small v-if="getColorForDate(task)" v-bind:class="getColorForDate(task)"><i class="fa fa-clock-o"></i> @{{ getTimeLeft(task) }}</small>
                        <!-- General tools such as edit or delete-->
                        <div class="tools">
                        	<span class="time">@{{ shortDate(task.date) }}</span>
                            <!-- <i  v-if="!task.breed_id" @click.prevent="openTaskForm(task)" class="fa fa-edit"></i> -->
                            <i @click.prevent="openTaskForm(task)" class="fa fa-edit"></i>
                            <i @click.prevent="showRequestForm(task, index)" class="fa fa-trash-o"></i>
                            <i v-if="type_of_task == 'archived'" @click.prevent="showUnarchiveForm(task, index)" class="fa fa-expand"></i>
                        </div>
                    </li>

                    <li id="id_plan_@{{plan.id}}"  v-for="(index, plan) in plans" v-if="type_of_task == 'plans'">
                        <i class="fa fa-venus-mars circle-background" v-bind:class="['bg-black', plan.missed == 0 ? 'bg-blue' : '']"></i>
                        <span class="text"> @{{ plan.name }} </span>
                        <div class="tools">
                            <span class="time">@{{ shortDate(plan.date) }}</span>
                            <!-- <i  v-if="!task.breed_id" @click.prevent="openTaskForm(task)" class="fa fa-edit"></i> -->
                            <i @click.prevent="openPlanForm(plan)" class="fa fa-edit"></i>
                            <i @click.prevent="showPlanDeletingForm(plan, index)" class="fa fa-trash-o"></i>
                        </div>
                    </li>
                </ul>
            </div><!-- /.box-body -->
            <div class="box-footer clearfix no-border">
                <div v-if="pagination_page.length" class="box-tools pull-left">
                    <ul class="pagination pagination-sm inline">
                        <li><a @click.prevent="loadTasks(null, 0)" href="#">«</a></li>
                        <li v-for="(index, tag) in pagination_page"><a @click.prevent="loadTasks(null, tag)" href="#">@{{ tag }}</a></li>
                        <li><a @click.prevent="loadTasks(null, last_page)"href="#">»</a></li>
                    </ul>
                </div>
                <button v-if="type_of_task != 'plans'" @click.prevent="openTaskForm()" class="btn btn-default pull-right" id="step5">
                    <i class="fa fa-plus"></i> Add New
                </button>
                <button v-if="type_of_task == 'plans'" @click.prevent="openPlanForm()" class="btn btn-default pull-right">
                    <i class="fa fa-plus"></i> Add New
                </button>
            </div>
        </div>
    </section>

    <div class="modal in" id="new_task" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header @{{ info_panel_class }}">
                    <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title">
                        <span>@{{ name_of_modal }}</span>
                        <span class="loader1" v-if="loading"><i class="fa fa-spin fa-spinner"></i></span>
                    </h4>
                </div>
                <div class="modal-body">
                    <validator name="activeTaskValidator">
                        <form class="form-horizontal row-paddings-compensation">

                            <div class="row">
                                <div class="form-group col-xs-7 col-sm-6">
                                    <label class="col-sm-4 control-label">Type</label>
                                    <div class="col-sm-8">
                                        <select id="taskType" class="form-control" v-model="activeTask.type">
                                            <option value="general">General</option>
                                            <option value="litter">Litter</option>
                                            <option value="breeder">Breeder</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-xs-7 col-sm-6" v-if="activeTask.type != 'general'" v-bind:class="{ 'has-error': ($activeTaskValidator.breederlitterform.touched && $activeTaskValidator.breederlitterform.invalid) }">
                                    <label class="col-sm-4 control-label">@{{ capitalize(activeTask.type) }}</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <select id="breederlitterform" class="form-control" v-model="activeTask.relation.id"
                                                    v-validate:breederlitterform="{required: (activeTask.type != 'general')}">
                                                <option value="">Choose @{{ activeTask.type }}...</option>
                                                <option v-if="activeTask.type == 'litter'" value="@{{bl.id}}" v-for="(index, bl) in breeder_litter">@{{ bl.given_id }}
                                                    : @{{ bl.name }}</option>
                                                <option v-if="activeTask.type != 'litter'" value="@{{bl.id}}" v-for="(index, bl) in breeder_litter  | filterBy filterTasksBreeders">@{{ bl.name }}
                                                    : @{{ bl.tattoo }}</option>
                                            </select>
                                        <span v-if="activeTask.type == 'breeder'" class="input-group-addon" title="With Archived">
                                            <input type="checkbox" v-model="withArchivedBreeders">
                                        </span>
                                        </div>
                                        <small class="error" v-if="$activeTaskValidator.breederlitterform.touched && $activeTaskValidator.breederlitterform.invalid">Field is mandatory.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-sm-6 col-xs-7" v-bind:class="{ 'has-error': ($activeTaskValidator.name.touched && $activeTaskValidator.name.invalid) }">
                                    <label class="col-sm-4 control-label">Name</label>

                                    <div class="col-sm-8">
                                        <input id="name" type="text" class="form-control" placeholder="Enter ..." v-validate:name="['required']" v-model="activeTask.name" >
                                        <small class="error" v-if="$activeTaskValidator.name.touched && $activeTaskValidator.name.required">Field is mandatory.</small>
                                    </div>
                                </div>

                                <div class="form-group col-xs-7 col-sm-6" v-bind:class="{ 'has-error': ($activeTaskValidator.date.touched && $activeTaskValidator.date.invalid) }">
                                    <label class="col-sm-4 control-label">Date</label>
                                    <div class="col-sm-8">
                                        <div id="datepick" class="input-group date" v-datepicker="activeTask.date" container="#new_task">
                                            <input id="date" type="text" class="form-control" v-validate:date="['required']" data-disable-past="false">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                                        </div>
                                        <small class="error" v-if="$activeTaskValidator.date.touched && $activeTaskValidator.date.required">Field is mandatory.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-xs-7 col-sm-6">
                                    <label class="col-sm-4 control-label">Recurring</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" v-model="activeTask.recurring">
                                            <option value="1">Once</option>
                                            <option value="2">Every Week</option>
                                            <option value="3">Every 2 Weeks</option>
                                            <option value="4">Every Month</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-xs-7 col-sm-6"  v-bind:class="{ 'has-error': errors.icon }">
                                    <label class="col-sm-4 control-label">Icon</label>
                                    <div class="col-sm-8">
                                        <div class="select-icon-of-task">
                                            <label><input type="radio" name="selecticon" v-model="activeTask.icon" value="fa-cutlery bg-red" /><i class="fa fa-cutlery icon-circle" v-bind:class="{'bg-red': iconBackground['bg-red']}"></i></label>
                                            <label><input type="radio" name="selecticon" v-model="activeTask.icon" value="fa-venus-mars bg-blue" /><i class="fa fa-venus-mars icon-circle" v-bind:class="{'bg-blue': iconBackground['bg-blue']}"></i></label>
                                            <label><input type="radio" name="selecticon" v-model="activeTask.icon" value="fa-check bg-maroon" /><i class="fa fa-check icon-circle" v-bind:class="{'bg-maroon': iconBackground['bg-maroon']}"></i></label>
                                            <label><input type="radio" name="selecticon" v-model="activeTask.icon" value="fa-birthday-cake bg-green" /><i class="fa fa-birthday-cake icon-circle" v-bind:class="{'bg-green': iconBackground['bg-green']}"></i></label>
                                            <label><input type="radio" name="selecticon" v-model="activeTask.icon" value="fa-balance-scale bg-yellow" /><i class="fa fa-balance-scale icon-circle" v-bind:class="{'bg-yellow': iconBackground['bg-yellow']}"></i></label>
                                            <label><input type="radio" name="selecticon" v-model="activeTask.icon" value="fa-calendar bg-gray" /><i class="fa fa-calendar icon-circle" v-bind:class="{'bg-black': iconBackground['fa-calendar']}"></i></label>
                                            <label><input type="radio" name="selecticon" v-model="activeTask.icon" value="fa-heart bg-gray" /><i class="fa fa-heart icon-circle" v-bind:class="{'bg-black': iconBackground['fa-heart']}"></i></label>
                                            <label><input type="radio" name="selecticon" v-model="activeTask.icon" value="fa-asterisk bg-gray" /><i class="fa fa-asterisk icon-circle" v-bind:class="{'bg-black': iconBackground['fa-asterisk']}"></i></label>
                                            <label><input type="radio" name="selecticon" v-model="activeTask.icon" value="fa-bookmark bg-gray" /><i class="fa fa-bookmark icon-circle" v-bind:class="{'bg-black': iconBackground['fa-bookmark']}"></i></label>
                                            <label><input type="radio" name="selecticon" v-model="activeTask.icon" value="fa-eye bg-gray" /><i class="fa fa-eye icon-circle" v-bind:class="{'bg-black': iconBackground['fa-eye']}"></i></label>
                                            <label><input type="radio" name="selecticon" v-model="activeTask.icon" value="fa-flag bg-gray" /><i class="fa fa-flag icon-circle" v-bind:class="{'bg-black': iconBackground['fa-flag']}"></i></label>
                                            <label><input type="radio" name="selecticon" v-model="activeTask.icon" value="fa-medkit bg-gray" /><i class="fa fa-medkit icon-circle" v-bind:class="{'bg-black': iconBackground['fa-medkit']}"></i></label>
                                            <label><input type="radio" name="selecticon" v-model="activeTask.icon" value="fa-paw bg-gray" /><i class="fa fa-paw icon-circle" v-bind:class="{'bg-black': iconBackground['fa-paw']}"></i></label>
                                            <label><input type="radio" name="selecticon" v-model="activeTask.icon" value="fa-trophy bg-gray" /><i class="fa fa-trophy icon-circle" v-bind:class="{'bg-black': iconBackground['fa-trophy']}"></i></label>
                                            <label><input type="radio" name="selecticon" v-model="activeTask.icon" value="fa-inbox bg-purple" /><i class="fa fa-inbox icon-circle" v-bind:class="{'bg-purple': iconBackground['bg-purple']}"></i></label>
                                            <label><input type="radio" name="selecticon" v-model="activeTask.icon" value="fa-list-alt bg-orange" /><i class="fa fa-list-alt icon-circle" v-bind:class="{'bg-orange': iconBackground['bg-orange']}"></i></label>
                                        </div>
                                        <small class="error" v-if="errors.icon" >@{{ errors.icon }}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row" v-if="modeOfTask == 'edit'">
                                <div class="form-group col-xs-7 col-sm-6">
                                    <label for="taskIsDone" class="col-sm-4 control-label">
                                        <input type="checkbox" id="taskIsDone" v-model="activeTask.closed"  v-bind:true-value="'1'"  v-bind:false-value="'0'"> Done. </label>
                                </div>
                            </div>
                        </form>
                    </validator>
                </div>
                <div class="modal-footer @{{ info_panel_class }}">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button @click.prevent="createTask(modeOfTask)" type="button" class="btn @{{ button_class }}">Save changes</button>
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

    <div class="modal modal-danger in" id="delete_plan" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <h3><i class="fa fa-fw fa-warning"></i><br>Do you want to delete @{{ activePlan.name }}?</h3>
                        </div>
                    </div>
                    <div class="row margin">
                        <div class="col-sm-12 text-center">
                            <button @click.prevent="deletePlan(activePlan)" class="btn btn-outline" type="button"><i class="fa fa-check"></i> Yes</button>
                            <button type="button" class="btn btn-outline" data-dismiss="modal"><i class="fa fa-close"></i> No </button>
                        </div>
                    </div>
                </div>

            </div>
        </div><!-- /.modal-dialog -->
    </div>

    <div class="modal in" id="archive_task" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body bg-gray">
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <h3><i class="fa fa-fw fa-warning"></i><br>Do you want to archive tasks?</h3>
                        </div>
                    </div>
                    <div class="row margin">
                        <div class="col-sm-12 text-center">
                            <button @click.prevent="deleteTask(activeTask)" class="btn btn-default" type="button"><i class="fa fa-check"></i> Yes</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> No </button>
                        </div>
                    </div>
                </div>

            </div>
        </div><!-- /.modal-dialog -->
    </div>

    <div class="modal in" id="unarchive_task" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body bg-gray">
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <h3><i class="fa fa-fw fa-warning"></i><br>Do you want to unarchive this task?</h3>
                        </div>
                    </div>
                    <div class="row margin">
                        <div class="col-sm-12 text-center">
                            <button @click.prevent="unarchiveTask(activeTask)" class="btn btn-default" type="button"><i class="fa fa-check"></i> Yes</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> No </button>
                        </div>
                    </div>
                </div>

            </div>
        </div><!-- /.modal-dialog -->
    </div>
    <!-- modal -->
    <div class="modal" id="breed-edit">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-aqua">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                    <h4 class="modal-title">Schedule Breed</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal row-paddings-compensation">
                        <div class="row">
                            <div class="form-group col-sm-6 col-xs-7" :class="{ 'has-error': errors.buck }">
                                <label class="col-sm-4 control-label">Buck</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <select class="form-control" v-model="activePlan.buck">
                                            <option value="-1">Choose...</option>
                                            <option v-for="(index, buck) in planBreeders.bucks | filterBy filterFatherBreeders" value="@{{ buck.id }}">@{{ buck.name }}: @{{ buck.tattoo }}</option>
                                        </select>
                                        <span class="input-group-addon" title="With Archived">
                                            <input type="checkbox" v-model="withArchivedFather">
                                        </span>
                                    </div>
                                    <small class="error" v-if="errors.buck">@{{ errors.buck[0] }}</small>
                                </div>
                            </div>
                            <div class="form-group col-sm-6 col-xs-7" :class="{ 'has-error': errors.doe }">
                                <label class="col-sm-4 control-label">Doe</label>
                                <div class="col-sm-8">

                                    <div class="input-group">
                                        <select class="form-control" v-model="activePlan.doe">
                                            <option value="-1">Choose doe...</option>
                                            <option v-for="(index, doe) in planBreeders.does | filterBy filterMotherBreeders"  value="@{{ doe.id }}">@{{ doe.name }}: @{{ doe.tattoo }}</option>
                                        </select>
                                        <span class="input-group-addon" title="With Archived">
                                            <input type="checkbox" v-model="withArchivedMother">
                                        </span>
                                    </div>
                                    <small class="error" v-if="errors.doe">@{{ errors.doe[0] }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xs-7 col-sm-6" :class="{ 'has-error': errors.date }">
                                <label class="col-sm-4 control-label">Date</label>
                                <div class="col-sm-8">
                                    <div class="input-group date" v-datepicker="activePlan.date" container="#breed-edit">
                                        <input type="text" class="form-control"><span class="input-group-addon"><i
                                                    class="glyphicon glyphicon-th"></i></span>
                                    </div>
                                    <small class="error" v-if="errors.date">@{{ errors.date[0] }}</small>
                                </div>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-2"></div>
                            <div class="col-md-8">
                                <div class="tab-pane active" id="timeline">
                                    <!-- The timeline -->
                                    <ul class="timeline timeline-inverse">

                                        <!-- timeline item -->
                                        <li v-for="(index, event) in dummyEvents">
                                            <i v-if="event.date" class="fa @{{ event.icon }}"></i>
                                            <div v-if="event.date" class="timeline-item">
                                                <span class="time"><i class="fa fa-calendar"></i> @{{ event.date }}</span>
                                                <h3 class="timeline-header">@{{ event.name }}</h3>
                                            </div>
                                        </li>
                                        <!-- END timeline item -->

                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- /.box-header -->
                        <!-- form start -->

                        <!-- /.box-body -->
                        <!-- /.box-footer -->
                    </form>
                </div>
                <div class="modal-footer bg-info">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close
                    </button>
                    <button v-show="activePlan.id && activePlan.missed != 1" type="button" @click="showMissed(activePlan)" class="btn btn-danger"><i class="fa fa-ban"></i> Missed</button>
                    <button @click="updatePlan(activePlan)" type="button" class="btn btn-info">Save changes</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    @include('layouts.missing', ['unique' => '-plan'])
</template>
