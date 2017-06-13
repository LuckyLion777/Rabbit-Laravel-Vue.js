<!-- Schedule Export -->

@if(Auth::check())
    @php($link = route('schedule.export', [ 'user' => Auth::id(), 'hash' => CryptHash::hash("user:" . Auth::id() . ":schedule") ], true))
    <div class="modal modal-primary" id="export-schedule-modal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-body">
                	<button aria-label="Close" data-dismiss="modal" class="close pull-right" type="button"><span aria-hidden="true">×</span></button>
                    <div class="row margin">
                        <div class="col-sm-12 text-center"><h3>Schedule Sync</h3>
                            <p>Copy and paste the URL to automatically sync/update with your calendar program when new tasks are added to your Hutch Schedule</p>
                        </div>
                    </div>
                    <div class="row margin">
                        <div class="col-sm-12 text-center">
                            <input type="text" id="export-schedule-url" class="form-control" value="{{ $link }}">
                        </div>
                    </div>
                    <div class="row margin">
                        <div class="col-sm-12 text-center">
                            <button class="btn btn-outline" type="button" data-clipboard-target="#export-schedule-url">
                                <i class="fa fa-copy"></i> Copy
                            </button>
                        </div>
                    </div>
                    <div class="row margin">
                        <div class="col-sm-12 text-center"><hr><h3>Schedule Download</h3>
                            <p>Click the button to download your current Hutch schedule. This file will not automatically update.</p>
                        </div>
                    </div>
                    <div class="row margin">
                        <div class="col-sm-12 text-center">
                            <a class="btn btn-outline" id="export-schedule-link" href="{{ $link }}"
                               onclick="App.vent.trigger('export-schedule-download')">
                                    <i class="fa fa-download"></i> Download
                            </a>
                        </div>
                    </div>

                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
@endif
