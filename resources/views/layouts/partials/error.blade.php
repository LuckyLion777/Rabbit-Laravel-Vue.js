@if ($error = Session::pull('callout_error'))
<div id="app-error" class="callout callout-danger">
    <p><strong>{{ $error }}</strong></p>
</div>
@endif
@if ($success = Session::pull('callout_success'))
<div id="app-success" class="callout callout-success">
    <p><strong>{{ $success }}</strong></p>
</div>
@endif


