@if (Auth::user()->isTrial() && !Auth::user()->hasRole(['admin', 'free']))
<div class="callout callout-warning" id="callout-trial">
    <h4>Your trial will expire soon, upgrade now!
        <button type="button" v-link="{ path: '/account' }" class="btn btn-warning btn-outline margin">Subscribe <i class="fa fa-arrow-circle-right"></i></button>
    </h4>
</div>
@endif
