<template id="wizard__settings-template" xmlns="http://www.w3.org/1999/html" xmlns:v-on="http://www.w3.org/1999/xhtml">

    <div id="wizard__settings">

        <div class="col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
            <div class="box box-solid bg-green-gradient">

                <!-- /.box-header -->
                <div class="box-body"><h2 class="text-center"><img src="img/logo-tiny.png"><br>Welcome to Hutch!</h2>

                    <h4 class="text-center">Let's start by setting up your account</h4>
                    <p class="text-center">Enter these basic settings to customize Hutch to your rabbitry<br></p>
                    <a href="#" class="btn btn-success btn-sm pull-right" v-link="{ path: '/wizard/breeders'}">
                        Skip <i class="fa fa-chevron-right"></i>
                    </a>
                </div>
                <!-- /.box-body -->
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
                <div class="box box-solid box-primary">
                    <div class="box-header text-center"><h3 class="box-title"><i class="fa fa-calendar-check-o"></i> Customize Your Breed Chain</h3></div>
                    <div class="box-body">
                        <div class="row"><div class="col-md-4"><p>When you schedule a breed, Hutch adds a chain of events to your schedule.  </p>
                                <p>Space these events by editing the amount of days from the breed date.</p>
                                <p>Events before "Kindle/birth" will be added to the schedule when the breeding is created. Events after the birth date will be added to the schedule when the Birth is reported.</p>
                                <p>Change the name of events or add new tasks to this chain to customize it to your needs. </p>

                                <p>To add a task to the chain, click the "Add New Event" button at the bottom, and delete an event by clicking the trash can to the right of the event.</p></div>

                            <div class="col-sm-8">

                                <div class="tab-pane active" id="timeline">

                                    <!-- The timeline -->
                                    <ul class="timeline timeline-inverse">

                                        <li v-for="chain in chains" v-bind:class="chain.id">
                                            <i v-bind:class="chain.icon" class="fa"></i>
                                            <div class="timeline-item">
                                                <button v-if="['fa-venus-mars bg-blue', 'fa-birthday-cake bg-green', 'fa-balance-scale bg-yellow first-weight'].indexOf(chain.icon) === -1" type="button" class="btn btn-default btn-sm pull-right" style="margin-right: -33px; margin-top: 9px;"  @click.prevent="removeChain(chain.id)"><i class="fa fa-trash"></i></button>
                                                <input type="hidden" v-model="user.breedchains.icon[chain.id]" value="@{{ chain.icon }}">
                                                <span class="time"><input size="2" type="text" placeholder="0" value="@{{ chain.days }}" v-model="user.breedchains.days[chain.id]"> Days</span>
                                                <h3 class="timeline-header"><input placeholder="Breed" type="text" value="@{{ chain.name }}" v-model="user.breedchains.name[chain.id]"></h3>
                                            </div>
                                        </li>

                                    </ul>
                                </div><button class="btn btn-submit btn-default btn-lg" data-toggle="modal" href="#new_chain"><i class="fa fa-plus"></i> Add New Event</button>
                            </div>

                        </div>

                    </div>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
                <div class="box box-solid box-primary">
                    <div class="box-header text-center"><h3 class="box-title"><i class="fa fa-balance-scale"></i> Select Weight Units </h3></div>
                    <div class="box-body">
                        <div class="col-md-3"></div>

                        <div class="col-md-6"><p>Weight units determine how rabbit weights are displayed throughout the system. Select the units you prefer.</p>
                            <div class="radio">
                                <label>
                                    <input name="weight_units" value="Ounces" type="radio" v-model="user.general_weight_units">
                                    Ounces as <strong>1.2 oz</strong>
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input name="weight_units" value="Pounds" checked="" type="radio" v-model="user.general_weight_units">Pounds as <strong>3.6 lbs</strong>
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input name="weight_units" value="Pound/Ounces" type="radio" v-model="user.general_weight_units">Pounds/Ounces as <strong>10 lbs 2 oz</strong>
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input name="weight_units" value="Grams" type="radio" v-model="user.general_weight_units">Grams as <strong>25.5 g</strong>
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input name="weight_units" value="Kilograms" type="radio" v-model="user.general_weight_units">Kilograms as <strong>2.8 kg</strong>
                                </label>
                            </div></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
                <div class="box box-solid box-primary">
                    <div class="box-header text-center"><h3 class="box-title"><i class="fa fa-share-alt"></i> Pedigree
                            Settings</h3></div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6"><p>Select the number of generations you want to display on your
                                    pedigrees. This will determine the layout of your web and pdf pedigrees generated by
                                    Hutch.</p>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="" class="">Number of Generations</label>
                                    <select class="form-control input-lg" name="units" v-model="user.pedigree_number_generations">
                                        <option>2</option>
                                        <option>3</option>
                                        <option>4</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6"><p>Rabbitry Information is displayed at the top of your pedigrees.
                                    This should include the name, address, and contact information (phone, web adrees,
                                    email) of your Rabbitry.</p>


                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Rabbitry Information</label>
                                    <textarea placeholder="Rabbitry name, address, and contact information" rows="3"
                                              class="form-control" v-model="user.pedigree_rabbitry_information">
                                    </textarea>
                                </div>
                            </div>

                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6"><p>Upload a logo to be included on your pedigrees above your rabbitry
                                    information. The logo should be gif, jpg, or png, and should be no more than 300
                                    pixels wide and 100 pixels tall (300px X 100px).</p>


                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <settings-image-upload :image.sync="user.pedigree_logo"></settings-image-upload>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row text-center">
            <button type="button" class="btn btn-success btn-lg" @click.prevent="updateSettings()">
                <i v-if="loading" class="fa fa-spinner fa-pulse fa-fw"></i>
                <span v-if="!loading">
                    Save Settings <i class="fa fa-arrow-circle-right"></i>
                </span>

            </button>
            <br><br>
            <button type="button" class="btn btn-default btn-sm" v-link="{ path: '/wizard/breeders'}" href="#"> Skip <i class="fa fa-chevron-right"></i> </button>
            <br><br><br><br>
        </div>

        @include('components/_chain-modal-settings')

    </div>
</template>
