<template id="account-settings-template">

    <section class="content-header">
        <h1>Account</h1>
        <ol class="breadcrumb">
            <li><a href="#" v-link="{ path: '/' }"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Account</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="row">
            <div class="col-xs-12 col-md-6 col-lg-4">
                @include('components.parts.subscription')
            </div>

            <div class="col-xs-12 col-md-6 col-lg-4">
                <div class="box box-solid box-success">
                    <div class="box-header">
                        Account Settings
                    </div>
                    <div class="box-body">

                        <input type="hidden" name="id" value="{{ $currentUser->id }}" v-model="user_id">

                        <div class="alert alert-success" v-if="success.success[0]">
                            <i class="fa fa-check"></i> @{{ success.success[0] }}
                        </div>

                        <div class="form-group" :class="{ 'has-error': errors.email }">
                            <label for="user-name">Name</label>
                            <input id="user-name" class="form-control" placeholder="Name" autocomplete="off" type="text"
                                   name="name" v-model="user.name">
                            <small class="error" v-if="errors.name">@{{ errors.password[0] }}</small>
                        </div>
                        <div class="form-group" v-bind:class="{ 'has-error': errors.email }">
                            <label for="user-email">Email</label>
                            <input id="user-email" class="form-control" placeholder="E-mail" autocomplete="off" type="email"
                                   name="email" value="@{{user.email}}" disabled>
                            <small class="error" v-if="errors.email">@{{ errors.email[0] }}</small>
                        </div>
                        <div class="form-group" v-bind:class="{ 'has-error': errors.password }">
                            <label for="user-old-password">Old password</label>
                            <input id="user-old-password" class="form-control" placeholder="password" autocomplete="off" type="password"
                                   name="password" v-model="user.password">
                            <small class="error" v-if="errors.password">@{{ errors.password[0] }}</small>
                        </div>

                        <hr>
                        <div class="form-group" v-bind:class="{ 'has-error': errors.new_password }">
                            <label for="user-new-password">New password (only if you want to change it)</label>
                            <input id="user-new-password" class="form-control" placeholder="password" autocomplete="off" type="password"
                                   name="new_password" v-model="user.new_password">
                            <small class="error" v-if="errors.new_password">@{{ errors.new_password[0] }}</small>

                        </div>
                        <div class="form-group" v-bind:class="{ 'has-error': errors.password_confirmation_pass }">
                            <label for="user-new-passwork-confirm">Confirm password</label>
                            <input id="user-new-password-confirm" class="form-control" placeholder="password" autocomplete="off" type="password"
                                   name="new_password_confirmation" v-model="user.new_password_confirmation">
                            <small class="error" v-if="errors.password_confirmation_pass">@{{ errors.password_confirmation_pass[0] }}</small>
                        </div>

                    </div>
                    <div class="box-footer"><button class="btn btn-submit btn-success pull-right" @click.prevent="updateSettings()">Save changes</button></div>

                </div>

            </div>

            <div class="col-xs-12 col-md-6 col-lg-4">
                <div class="box box-solid box-success">
                    <div class="box-header">
                        Receipts
                    </div>
                    <div class="box-body">
                        <table class="table table-condensed">
                            <tr v-for="invoice in invoices">
                                <td>@{{ invoice.date }}</td>
                                <td>@{{ invoice.total }}</td>
                                <td><a href="/subscription/invoice/@{{ invoice.id }}">Download</a></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="box box-solid box-success">
                    <div class="box-header">
                        Referrals
                    </div>
                    <div class="box-body">

                        <div class="form-group">
                            <label>Your referral link</label>
                            <p class="form-control-static">
                                @if (isset($currentUser))
                                    <a href="{{ route('web.invite', ['inviter' => $currentUser->getSlug() ]) }}">
                                        {{ route('web.invite', ['inviter' => $currentUser->getSlug() ]) }}
                                    </a>
                                @endif
                            </p>
                        </div>

                        <div class="form-group" :class="{ 'has-error': referrerErrors.email }">
                            <label>You were referred by</label>
                            <p class="form-control-static" v-if="referred_by">
                                &lt;@{{ referred_by }}&gt;
                            </p>
                            <div class="input-group" v-if="!referred_by">
                                <input type="email" class="form-control" v-model="referrer"
                                       placeholder="Your referrer email (optional)">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-primary" @click="setReferrer">Save</button>
                                </span>
                            </div>
                            <small class="error" v-if="referrerErrors.email">@{{ referrerErrors.email[0] }}</small>
                        </div>

                        <div class="form-group" :class="{ 'has-error': referralErrors.email }">
                            <label>Your referrals</label>
                            <ul v-if="refs">
                                <li v-for="ref in refs">
                                    @{{ ref.name }} &lt;@{{ ref.email }}&gt;
                                </li>
                            </ul>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Email" v-model="referral"
                                       v-on:input="clearReferralSent">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-success" @click=addReferral>Add</button>
                                </span>
                            </div>
                            <small class="error" v-if="referralErrors.email">@{{ referralErrors.email[0] }}</small>
                            <small class="success" v-if="referralSent">Confirmation request has been sent to the user.</small>
                        </div>

                        <div class="form-group" v-if="referralsCredit">
                            <label>Total credit earned: $@{{ referralsCredit }}</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-md-6 col-lg-4">
                <div class="box box-solid box-success">
                    <div class="box-header">
                        Social Accounts
                    </div>
                    <div class="box-body">
                        <p v-if="!socials.length">
                            You do not currently have any social accounts connected
                        </p>
                        <p v-if="socialError" class="alert alert-danger">
                            @{{ socialError }}
                        </p>
                        <table class="table table-condensed">
                            <tr v-for="social in socials">
                                <td width="1"><img width="32" height="32" :src="social.avatar"></td>
                                <td>@{{ social.username }}</td>
                                <td>@{{ social.provider }}</td>
                                <td width="1">
                                    <a class="text-nowrap" href="#" title="Disconnect"
                                       v-on:click.prevent="disconnectSocial(social.id)">
                                        <strong>&times; Disconnect</strong>
                                    </a>
                                </td>
                            </tr>
                        </table>
                        <hr />
                        <div class="row">
                        	<div class="col-xs-6">
                        		<a href="{{ route('auth.add_provider', ['provider' => 'facebook']) }}"
                                       class="btn btn-block btn-social btn-facebook">
                                        <i class="fa fa-facebook"></i> Add Facebook
                                    </a>
                        	</div>
                        	<div class="col-xs-6">
                            	<a href="{{ route('auth.add_provider', ['provider' => 'google']) }}"
                                       class="btn btn-block btn-social btn-google">
                                        <i class="fa fa-google-plus"></i> Add Google+
                                    </a>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>


    <!-- referred by confirmation email -->
    <div class="modal modal-warning" id="referred-by-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="text-center">Did <strong>@{{ referrer }}</strong> refer you to Hutch?</h3>
                            <p class="lead text-center">
                                This user will receive credit for this referral.
                                <br>This action cannot be undone or changed later.
                            </p>
                        </div>
                        <div class="row">
                            <div class="col-xs-6 text-right">
                                <label class="control-label" for="referred-by-email">Referrer Email</label>
                            </div>
                            <div class="col-xs-6">
                                <span class="form-control-static">@{{ referrer }}</span>
                            </div>
                        </div>
                        <div class="row margin">
                            <div class="col-sm-12 text-center">
                                <button class="btn btn-outline" type="button" v-on:click="saveReferrer"><i class="fa fa-check"></i> Confirm</button>
                                <button type="button" class="btn btn-outline" v-on:click="cancelReferrer"><i class="fa fa-close"></i> Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>
