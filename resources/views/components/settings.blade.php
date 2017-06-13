<template id="settings-template" xmlns="http://www.w3.org/1999/html" xmlns:v-on="http://www.w3.org/1999/xhtml">

    @if (isset($currentUser))
    <input type="hidden" name="id" value="{{ $currentUser->id }}" v-model="user_id">
    @endif

    <section class="content-header">
        <h1>Settings</h1>
        <ol class="breadcrumb">
            <li><a href="#" v-link="{ path: '/' }"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Settings</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="row">

            <div class="col-xs-12 col-md-6 col-lg-4" v-if="isSubscribed">
                <div class="box box-solid box-primary">
                    <div class="box-header">
                        General Settings
                    </div>
                    <div class="box-body">
                        <div class="alert alert-success" v-if="success_general.success && success_general.success[0]">
                            <i class="fa fa-check"></i> Settings Updated
                        </div>

                        <div class="form-group">
                            <label for="weight-units">Weight Units</label>
                            <select id="weight-units" name="user_general_weight_units" class="form-control"
                                    v-model="user.general_weight_units">
                                <option>Ounces</option>
                                <option>Pounds</option>
                                <option>Pound/Ounces</option>
                                <option>Grams</option>
                                <option>Kilograms</option>
                            </select>

                        </div>

                        <div class="form-group">
                            <label for="currency">Currency</label>
                            <select id="currency" name="user_currency" class="form-control" v-model="user.currency">
                                <option value="AUD">Australian Dollar</option>
                                <option value="BRL">Brazilian Real </option>
                                <option value="CAD">Canadian Dollar</option>
                                <option value="CZK">Czech Koruna</option>
                                <option value="DKK">Danish Krone</option>
                                <option value="EUR">Euro</option>
                                <option value="HKD">Hong Kong Dollar</option>
                                <option value="HUF">Hungarian Forint </option>
                                <option value="ILS">Israeli New Sheqel</option>
                                <option value="JPY">Japanese Yen</option>
                                <option value="MYR">Malaysian Ringgit</option>
                                <option value="MXN">Mexican Peso</option>
                                <option value="NOK">Norwegian Krone</option>
                                <option value="NZD">New Zealand Dollar</option>
                                <option value="PHP">Philippine Peso</option>
                                <option value="PLN">Polish Zloty</option>
                                <option value="GBP">Pound Sterling</option>
                                <option value="SGD">Singapore Dollar</option>
                                <option value="ZAR">South African Rand</option>
                                <option value="SEK">Swedish Krona</option>
                                <option value="CHF">Swiss Franc</option>
                                <option value="TWD">Taiwan New Dollar</option>
                                <option value="THB">Thai Baht</option>
                                <option value="TRY">Turkish Lira</option>
                                <option value="UGX">Uganda Shilling</option>
                                <option value="USD">U.S. Dollar</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="date-format">Date Format</label>
                            <select id="date-format" name="user_date_format" class="form-control"
                                    v-model="user.date_format">
                                <option value="US">US mm/dd/yyyy</option>
                                <option value="INT">International dd/mm/yyyy</option>
                            </select>
                        </div>
                    </div>
                    <div class="box-footer"><button type="button" class="btn btn-submit btn-primary pull-right" @click.prevent="updateSettings('general')">Save changes</button></div>
                </div>

                <div class="box box-solid box-primary">
                    <div class="box-header">
                        Notification Settings
                    </div>
                    <div class="box-body">
                        <p class="text-muted" v-if="!supports_notifications">
                            You browser does not support notifications!<br>
                            You should install latest Chrome, Firefox or Opera if you want to receive instant notifications.
                        </p>
                        <template v-else>
                            <div class="alert alert-success" v-if="success_notifications.success && success_notifications.success[0]">
                                <i class="fa fa-check"></i> Settings Updated
                            </div>

                            <div class="form-group">
                                <label>Receive notification in this browser</label>
                                <template v-if="notifications_disabled">
                                    <p class="form-control-static text-danger">
                                        You have denied us the notifications.
                                        Please change your settings if you wish to receive notifications.
                                    </p>
                                </template>
                                <template v-else>
                                    <template v-if="notifications_subscribed">
                                        <p class="form-control-static text-success">Notifications are enabled</p>
                                        <button type="button" class="btn btn-danger" @click="disableNotifications">Disable</button>
                                        <button type="button" class="pull-right btn btn-primary" @click="sendTestNotification">
                                            Send test notification
                                        </button>
                                    </template>
                                    <template v-else>
                                        <p class="form-control-static text-danger">Notifications are not enabled</p>
                                        <button type="button" class="btn btn-success" @click="subscribeToNotifications">Enable</button>
                                    </template>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-md-6 col-lg-4" v-if="isSubscribed">
                <div class="box box-solid box-primary">
                    <div class="box-header">
                        Schedule Settings
                    </div>
                    <div class="box-body">
                        <div class="alert alert-success" v-if="success_schedule.success && success_schedule.success[0]">
                            <i class="fa fa-check"></i> Settings Updated
                        </div>

                        <div class="form-group" v-show="isPremium">
                            <label for="">Weekly Digest</label>
                            <div class="row">
                                <div class="col-xs-12">
                                    <input type="checkbox" v-model="digest_enabled" v-el:digest_enabled>
                                    <span v-show="!digest_enabled">Check to enable weekly digest</span>
                                    <span v-show="digest_enabled">Select day of the week</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group" v-show="isPremium">
                            <select v-show="digest_enabled" id="digest_select" class="form-control" v-model="user.digest_day">
                                <option value="-1">Choose</option>
                                <option value="1">Monday</option>
                                <option value="2">Tuesday</option>
                                <option value="3">Wednesday</option>
                                <option value="4">Thursday</option>
                                <option value="5">Friday</option>
                                <option value="6">Saturday</option>
                                <option value="0">Sunday</option>

                            </select>
                            <small class="error" v-if="errors.password_confirmation_pass">@{{ errors.password_confirmation_pass[0] }}</small>
                            <div class="text-center" v-show="digest_enabled"><span class="small"><i>Add <a href="mailto:hutch@barntrax.com">hutch@barntrax.com</a> to your contacts and/or whitelist</i></span></div>
                        </div>
                        <hr v-show="isPremium">
                        <label for="">Breed Chain</label>
                        <div class="row">

                            <div class="col-sm-12">

                                <div class="tab-pane active" id="timeline">

                                    <!-- The timeline -->
                                    <ul class="timeline timeline-inverse">

                                        <li v-for="chain in chains" v-bind:class="chain.id">
                                            <i v-bind:class="chain.icon" class="fa"></i>
                                            <div class="timeline-item">
                                                <button v-if="canRemove(chain)" type="button" class="btn btn-default btn-sm pull-right" style="margin-right: -33px; margin-top: 9px;"  @click.prevent="removeChain(chain.id)"><i class="fa fa-trash"></i></button>
                                                <input type="hidden" v-model="user.breedchains.icon[chain.id]" value="@{{ chain.icon }}">
                                                <span class="time"><input size="2" type="text" placeholder="0" value="@{{ chain.days }}" v-model="user.breedchains.days[chain.id]"> Days</span>
                                                <h3 class="timeline-header"><input placeholder="Breed" type="text" value="@{{ chain.name }}" v-model="user.breedchains.name[chain.id]"></h3>
                                            </div>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                            <div class="col-sm-10"><button class="btn btn-submit btn-defaukt" data-toggle="modal" href="#new_chain"><i class="fa fa-plus"></i> Add New</button></div>
                        </div>

                    </div>
                    <div class="box-footer"><button type="button" class="btn btn-submit btn-primary pull-right" @click.prevent="updateSettings('schedule')">Save changes</button></div>
                </div>
            </div>

            <div class="col-xs-12 col-md-6 col-lg-4" v-if="isSubscribed">
                <div class="box box-solid box-info">
                    <div class="box-header">
                        Pedigree Settings
                    </div>
                    <div class="box-body">

                        <div class="alert alert-success" v-if="success_pedigree.success && success_pedigree.success[0]">
                            <i class="fa fa-check"></i> Settings Updated
                        </div>

                        <div class="form-group">
                            <label for="">Number of Generations</label>
                            <select class="form-control" name="units" v-model="user.pedigree_number_generations">
                              <option>2</option>
                              <option>3</option>
                              <option>4</option>
							  <!-- <option>5</option>
							  <option>6</option>
							  <option>7</option> -->
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="">Rabbitry Information</label>
                            <textarea placeholder="Rabbitry Name" rows="3" class="form-control" v-model="user.pedigree_rabbitry_information" ></textarea>
                        </div>


                        <div class="form-group">
                            <settings-image-upload :image.sync="user.pedigree_logo"></settings-image-upload>
                        </div>
                    </div>

                    <div class="box-footer"><button type="button" class="btn btn-submit btn-info pull-right" @click.prevent="updateSettings('pedigree')">Save changes</button></div>
                </div>
            </div>

        </div>
    </section>

 @include('components/_chain-modal-settings')



</template>
