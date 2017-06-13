<div class="box box-solid box-success">
    <div class="box-header">
        Subscription
    </div>
    <div class="box-body" v-if="currentPlan.id == 'forever'">
        <br>
        <div class="alert alert-success">
            <i class="fa fa-check"> You are a Hutch Forever member</i>
        </div>
    </div>
    <div class="box-body" v-else>

        <template v-if="subscribingShowLoading">
            <div class="text-center">
                <img src="/img/ajax-loader.gif" alt="Loading..." class="loader">
            </div>
        </template>
        <template v-else>
            <div class="alert alert-success" v-if="success.success[-1]">
                <i class="fa fa-check"></i> @{{ success.success[-1] }}
            </div>

            <h4 v-if="currentPlan && !currentPlan.id && !currentPlan.on_trial" class="alert alert-danger text-center">
                <strong>To continue using Hutch, please select a subscription plan and enter your billing information</strong>
            </h4>
            <p v-if="currentPlan.on_grace" class="alert alert-warning text-center">
                <strong>Your subscription ends on @{{ currentPlan.ends_at | date_format }}</strong>
            </p>
            <h4 v-if="currentPlan.on_trial" class="alert alert-warning text-center">
                <strong>Your trial ends on @{{ currentPlan.trial_ends_at | date_format }}</strong>
            </h4>

            <div class="form-group" :class="{ 'has-error': cardErrors.plan_id }">
                <div class="row">
                    <div class="col-xs-6">
                        <div class="subscription basic radio">
                            <label class="control-label">
                                <input type="radio" v-model="plan_id" value="basic_yr">
                                <strong>Hutch Basic</strong><br>$20/yr<br>Breeder Manager<br>Litter Manager<br>Schedule<br>Pedigrees
                            </label>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="subscription mini radio">
                            <label class="control-label">
                                <input type="radio" v-model="plan_id" value="mini_yr">
                                <strong>Hutch Mini</strong><br>$12/yr<br>Basic Features<br>Limited to 5 Breeders
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <div class="subscription premium radio">
                            <label class="control-label">
                                <input type="radio" v-model="plan_id" value="premium_yr">
                                <strong>Hutch Premium</strong><br>$40/yr<br>Basic Features<br>Ledger<br>Reports<br>Notifications
                            </label>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="subscription forever radio">
                            <label class="control-label">
                                <input type="radio" v-model="plan_id" value="forever">
                                <strong>Hutch Forever</strong><br>$130 one time<br>Premium Features
                            </label>
                        </div>
                    </div>
                </div>
                <small class="error" v-if="cardErrors.plan_id">@{{ cardErrors.plan_id[0] }}</small>
            </div>

            <template v-if="card.attached && currentPlan.id && (!cardErrors || !cardErrors.number)">

                <div class="form-group" v-if="currentPlan.id">
                    <button type="button" class="btn btn-success pull-right" v-if="subscriptionChanged && !currentPlan.on_grace"
                            :style="{ 'opacity': subscribing ? 0.3 : 1 }" @click="subscribeWithPreview">Update</button>
                    <div class="clearfix"></div>
                    <button type="button" class="btn btn-danger" v-if="!subscriptionChanged && !currentPlan.on_grace"
                            :style="{ 'opacity': subscribing ? 0.3 : 1 }" @click="unsubscribe">Unsubscribe</button>
                    <button type="button" class="btn btn-success" v-if="currentPlan.on_grace"
                            :style="{ 'opacity': subscribing ? 0.3 : 1 }" @click="updatePlan">Resume</button>
                </div>

                <div class="form-group card-details" v-if="card.attached">
                    <label class="control-label">Current payment method</label>
                    <p class="form-control-static">
                        Card&emsp;
                        <span class="vendor">@{{ card.brand }}</span>&emsp;
                        <span class="number">... @{{ card.last_four }}</span>&emsp;
                        <button type="button" class="btn btn-default" @click="changeCard">Change</button>
                    </p>
                </div>

            </template>

            <template v-else>

                <img class="img-responsive" src="/img/accepted_c22e0.png">

                <div class="form-group" :class="{ 'has-error': cardErrors.number }">
                    <label for="card-number">CARD NUMBER</label>
                    <div class="input-group">
                        <input type="text" autofocus="" required autocomplete="off" placeholder="Valid Card Number" class="form-control" aria-required="true" v-model="card.number">
                        <span class="input-group-addon"><i class="fa fa-credit-card"></i></span>
                    </div>
                    <small class="error" v-if="cardErrors.number">@{{ cardErrors.number[0] }}</small>
                </div>

                <div class="row">
                    <div class="col-xs-7">
                        <div class="form-group" :class="{ 'has-error': cardErrors.expiration }">
                            <label for="expiry-month"><span class="hidden-xs">EXPIRATION</span><span class="visible-xs-inline">EXP</span> DATE</label>
                            <div class="row">
                                <div class="col-xs-6">
                                    <select title="Expiration month" id="expiry-month" v-model="card.exp_month" class="form-control">
                                        <option value="" disabled>Month</option>
                                        <option value="01">Jan (01)</option>
                                        <option value="02">Feb (02)</option>
                                        <option value="03">Mar (03)</option>
                                        <option value="04">Apr (04)</option>
                                        <option value="05">May (05)</option>
                                        <option value="06">June (06)</option>
                                        <option value="07">July (07)</option>
                                        <option value="08">Aug (08)</option>
                                        <option value="09">Sep (09)</option>
                                        <option value="10">Oct (10)</option>
                                        <option value="11">Nov (11)</option>
                                        <option value="12">Dec (12)</option>
                                    </select>
                                </div>
                                <div class="col-xs-6">
                                    <select title="Expiration year" id="expiry-year" v-model="card.exp_year" class="form-control">
                                        @for($i = (int) date('Y'), $j = $i + 10; $i < $j; $i += 1)
                                            <option value="{{ $i - 2000 }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <small class="error" v-if="cardErrors.expiration">@{{ cardErrors.expiration[0] }}</small>
                        </div>
                    </div>
                    <div class="col-xs-4 col-md-4 pull-right">
                        <div class="form-group" :class="{ 'has-error': cardErrors.cvc }">
                            <label for="card-cvc">CV CODE</label>
                            <input type="text" required="" autocomplete="off" placeholder="CVC" v-model="card.cvc" class="form-control" aria-required="true">
                            <small class="error" v-if="cardErrors.cvc">@{{ cardErrors.cvc[0] }}</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group" :class="{ 'has-error': cardErrors.code }">
                            <label for="code">COUPON CODE</label>
                            <input type="text" id="code" v-model="discountCode" class="form-control">
                            <small class="error" v-if="cardErrors.code">@{{ cardErrors.code[0] }}</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <button type="button" class="subscribe btn btn-success btn-lg btn-block"
                                @click="subscribe" :style="{ 'opacity': subscribing ? 0.3 : 1 }">
                            <i class="fa fa-refresh" v-if="subscribing"></i> @{{ currentPlan.id ? 'Update' : 'Start' }} Subscription
                        </button>
                    </div>
                </div>
            </template>
        </template>
    </div>
</div>

<div class="modal modal-success" id="confirm-subscription-modal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 text-center"><h3>Submit payment?</h3>
                    </div>
                </div>
                <template v-if="confirm.lines">
                    <div class="row" v-for="line in confirm.lines">
                        <div class="col-xs-6 text-right">
                            <label class="control-label">@{{ line.title }}</label>
                        </div>
                        <div class="col-xs-2 text-right">
                            <span class="form-control-static">$@{{ Math.abs(line.amount / 100)  }}</span>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <div class="row" v-if="confirm.amount">
                        <div class="col-xs-6 text-right">
                            <label class="control-label">@{{ confirm.plan }}</label>
                        </div>
                        <div class="col-xs-2 text-right">
                            <span class="form-control-static">$@{{ confirm.amount }}</span>
                        </div>
                    </div>
                    <div class="row" v-if="confirm.setup_price">
                        <div class="col-xs-6 text-right">
                            <label class="control-label">@{{ confirm.plan }} (One-time payment)</label>
                        </div>
                        <div class="col-xs-2 text-right">
                            <span class="form-control-static">$@{{ confirm.setup_price }}</span>
                        </div>
                    </div>
                    <div class="row" v-if="confirm.coupon">
                        <div class="col-xs-6 text-right">
                            <label class="control-label">Coupon: @{{ confirm.coupon }}</label>
                        </div>
                        <div class="col-xs-2 text-right">
                            <span class="form-control-static">$-@{{ confirm.discount }}</span>
                        </div>
                    </div>
                </template>
                <div class="row">
                    <div class="col-xs-6 text-right">
                        <h4><strong>Total</strong></h4>
                    </div>
                    <div class="col-xs-2 text-right">
                        <h4><strong>$@{{ confirmSum }}</strong></h4>
                    </div>
                </div>
                <div class="row margin">
                    <div class="col-sm-12 text-center">
                        <button class="btn btn-outline" type="button" @click="doSubscribe"><i class="fa fa-check"></i> Yes</button>
                        <button type="button" class="btn btn-outline" data-dismiss="modal"><i
                                    class="fa fa-close"></i> No
                        </button>
                    </div>
                </div>

            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

