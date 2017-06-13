
<div class="row">
    <div class="row" v-if="loadingTasks">
        <div class="col-xs-1 col-xs-offset-5">
            <img src="/img/ajax-loader.gif" alt="Loading..." class="loader">
        </div>
    </div>
    <div class="row" v-if="!loadingTasks && isEmptyTimeline()">
        <div class="col-xs-4 col-xs-offset-5">
            No Tasks
        </div>
    </div>
    <div class="col-md-12 col-lg-6 col-lg-push-6" v-for="(index, eventForDay) in activeTimeline">
        <!-- The timeline -->
        <ul class="timeline timeline-inverse">

            <!-- timeline item -->
            <li v-for="(index, event) in eventForDay" v-if="event.closed">
                <i v-if="event.date" class="fa @{{ event.icon }}"></i>
                <div v-if="event.date" class="timeline-item">
                    <span class="tools">
                        <i @click.prevent="openTaskForm(event)" class="fa fa-edit"></i>
                        <i @click.prevent="showRequestForm(event)" class="fa fa-trash-o"></i>
                    </span>
                    <span class="time"><i class="fa fa-calendar"></i> @{{ event.date }}</span>
                    <h3 class="timeline-header">
                        <del v-if="event.closed">@{{ event.name }}</del>
                        <template v-else>@{{ event.name }}</template>
                    </h3>
                </div>
            </li>
            <!-- END timeline item -->

        </ul>
    </div>
    <div class="col-md-12 col-lg-6 col-lg-pull-6" v-for="(index, eventForDay) in activeTimeline">
        <!-- The timeline -->
        <ul class="timeline timeline-inverse">

            <!-- timeline item -->
            <li v-for="(index, event) in eventForDay" v-if="!event.closed">
                <i v-if="event.date" class="fa @{{ event.icon }}"></i>
                <div v-if="event.date" class="timeline-item">
                    <span class="tools">
                        <i @click.prevent="openTaskForm(event)" class="fa fa-edit"></i>
                        <i @click.prevent="showRequestForm(event)" class="fa fa-trash-o"></i>
                    </span>
                    <span class="time"><i class="fa fa-calendar"></i> @{{ event.date }}</span>
                    <h3 class="timeline-header">
                        <del v-if="event.closed">@{{ event.name }}</del>
                        <template v-else>@{{ event.name }}</template>
                    </h3>
                </div>
            </li>
            <!-- END timeline item -->

        </ul>
    </div>
</div>
<div class="row">
  <div class="col-lg-4 col-md-6">
      <a href="#" @click.prevent="openOwnerTaskForm()">
          <div class="info-box">
              <span class="info-box-icon bg-gray text-muted"><i class="fa fa-plus"></i></span>
              <div class="info-box-content text-muted">
                  <h1>Add New</h1>
              </div><!-- /.info-box-content -->
          </div><!-- /.info-box -->
      </a>
  </div>
</div>

