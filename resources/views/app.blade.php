@extends('layouts.default')
@section('content')
<body class="sidebar-mini skin-green">
	@include('modals.ie')
<div id="vue-app" class="wrapper">

    @include('layouts.partials.navbar')
    @include('layouts.partials.sidebar')
    @include('layouts.users.users')
    @include('layouts.users.user')
    @include('layouts.dashboard.dashboard')
    @include('layouts.reports.reports')
    @include('layouts.breeders.breedlist')
    @include('layouts.breeders.categories')
    @include('layouts.profile.profile')
    @include('layouts.profile.pedigree')
    @include('layouts.profile.pedigree-kit')
    @include('layouts.litter.littersList')
    @include('layouts.litter.litterprofile')
    @include('layouts.ledger.ledgerTable')
    @include('layouts.ledger.ledger')
    @include('layouts.ledger.categories')

    @include('components.broadcast')
    @include('components.broadcast-alert')
    @include('components.image-upload')
    @include('components.settings-image-upload')
    @include('components.sex-select')
    @include('components.task-form')
    @include('components.breeder-form')
    @include('components.breeder-butcher')
    @include('components.pedigree-form')
    @include('components.copy-pedigree-form')
    @include('components.litter-form')
    @include('components.litter-box')
    @include('components.litter-weight')
    @include('components.litter-butcher')
    @include('components.schedule')
    @include('components.kit-form')
    @include('components.birth-form')
    @include('components.settings')
    @include('components.wizard.wizard')
    @include('components.wizard.wizard__settings')
    @include('components.wizard.wizard__breeders')
    @include('components.wizard.wizard__ledgers')
    @include('components.account-settings')
    @include('components.upcomming-tasks')
    @include('components.schedule-calendar')
    @include('components.notification-tab')
    @include('components.ledger-form')
    @include('components.category-form')
    @include('components.breeder-category-form')
    @include('components.cage-cards.template-form')
    @include('components.cage-cards.print')
    @include('components.cage-cards.templates-list')
    @include('layouts.schedule-export')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Main content -->
        <div id="vue-content">
            @include('modals.trial')
            @include('modals.offline')
            @include('layouts.partials.error')
            <broadcast-alert></broadcast-alert>
            <router-view></router-view>
            <broadcast-form></broadcast-form>
        </div>
    </div>

    @include('layouts.partials.footer')

    <script>
        window.user_id = {{ $user->id }};
    </script>
</div><!-- ./wrapper -->

@endsection


@section('scripts')
    <script>
        {!! $cloudinaryJsConfig !!}
        App.tourShowed = {{ $user->tourShowed }};
        App.cloud_name = "{{ config('cloudder.cloudName') }}";
        App.cloud_api_base_url = "{{ config('cloudder.apiBaseUrl') }}";
        App.cloud_preset = "{{ config('cloudder.cloudPreset') }}";
        App.isSubscribed = {{ $user->isSubscribed() ? 'true' : 'false' }};
        App.currency = '{{ $user->currency }}';
        App.weight_units = '{{ $user->general_weight_units }}';
        App.isPremiumSubscribed = {{ $user->isPremium() ? 'true' : 'false' }};
        $(function () {
            App.init();
        });
    </script>
@endsection
