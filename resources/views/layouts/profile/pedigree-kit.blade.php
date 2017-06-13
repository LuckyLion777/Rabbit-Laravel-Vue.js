<template id="pedigree-kit-template">
    <section class="content">
        <div class="row">
            <section class="col-lg-12">
                <div class="box box-solid box-primary " style="">
                    <div class="box-header">
                        <i class="fa fa-share-alt"></i>
                        <h3 class="box-title">Pedigree</h3>
                        <!-- tools box -->
                        <div class="box-tools pull-right">
                            <button class="btn btn-sm btn-default" onclick="window.history.back();"><i class="fa fa-arrow-left"></i> Go back</button>
                        </div>
                        <!-- /. tools -->
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <a class="btn btn-default" href="#" @click.prevent="showCopy"><i class="fa fa-copy"></i>
                            <strong>Copy Data</strong>
                        </a>
						<a class="btn btn-primary" href="{{url("admin/kits")}}/@{{id}}/pdf"><i class="fa fa-file-pdf-o"></i> <strong>Generate PDF</strong></a>
                        <a class="btn btn-success" target="_blank" href="{{ strtr(route('web.kit.pedigree', ['id' => '%id']), ['%id' => '{' . '{token}' . '}']) }}">
                            <i class="fa fa-external-link"></i> <strong>Public link</strong>
                        </a>
                        <div class="row row-horizon pedigree" style="margin-top:15px">
                            <!-- START GENERATION 1 -->
                            <div class="col-xs-12 col-sm-6 col-md-10 col-lg-4">
                                <div class="whole"></div><div class="whole"></div>
                                @include('layouts.profile.pedigree._main')
                            </div>
                            <!-- END GENERATION 1 -->

                            <!-- START GENERATION 2-->
                            <div class="col-xs-12 col-sm-6 col-md-10 col-lg-4" v-if="generations.g2">
                                <!-- FIRST PARENT -->
                                <div class="whole"></div><div class="half"></div>
                                @include('layouts.profile.pedigree._other', ['g' => 'g2.f1'])

                                <!-- SECOND PARENT -->
                                <div class="whole"></div><div class="whole"></div><div class="whole"></div>
                                @include('layouts.profile.pedigree._other', ['g' => 'g2.m1'])
                            </div>
                            <!-- END GENERATION 2 -->

                            <!-- START GENERATION 3 -->
                            <div id="3" class="col-xs-12 col-sm-6 col-md-10 col-lg-4" v-if="generations.g3">
                                <div class="half"></div>
                                @include('layouts.profile.pedigree._other', ['g' => 'g3.f1'])
                                <div v-if="!generations.g3.f1" style="height: 200px;"></div>

                                <div class="whole"></div>
                                @include('layouts.profile.pedigree._other', ['g' => 'g3.m1'])

                                <div class="whole"></div>
                                @include('layouts.profile.pedigree._other', ['g' => 'g3.f2'])
                                <div v-if="!generations.g3.f2" style="height: 200px;"></div>

                                <div class="whole"></div>
                                @include('layouts.profile.pedigree._other', ['g' => 'g3.m2'])
                            </div>
                            <!-- END GENERATION 3 -->

                            <!-- START GENERATION 4 -->
                            <div class="col-xs-12 col-sm-6 col-md-10 col-lg-4" v-if="generations.g4">
                                @include('layouts.profile.pedigree._other', ['g' => 'g4.f1'])
                                @include('layouts.profile.pedigree._other', ['g' => 'g4.m1'])
                                <div v-if="!generations.g4.f1" style="height: 200px;"></div>

                                @include('layouts.profile.pedigree._other', ['g' => 'g4.f2'])
                                @include('layouts.profile.pedigree._other', ['g' => 'g4.m2'])
                                <div v-if="!generations.g4.f2" style="height: 200px;"></div>

                                @include('layouts.profile.pedigree._other', ['g' => 'g4.f3'])
                                @include('layouts.profile.pedigree._other', ['g' => 'g4.m3'])
                                <div v-if="!generations.g4.f3" style="height: 200px;"></div>

                                @include('layouts.profile.pedigree._other', ['g' => 'g4.f4'])
                                @include('layouts.profile.pedigree._other', ['g' => 'g4.m4'])
                                <div v-if="!generations.g4.f4" style="height: 200px;"></div>
                            </div>
                            <!-- END GENERATION 4 -->
                        </div>
                        <!--The calendar -->

                    </div><!-- /.box-body -->

                    <div class="box-footer">
                        <button class="btn btn-default" onclick="window.history.back();"><i class="fa fa-arrow-left"></i> Go back</button>
                        <a class="btn btn-primary pull-right" href="{{url("admin/kits")}}/@{{id}}/pdf" ><i class="fa fa-file-pdf-o"></i> <strong>Generate PDF</strong></a>
                        <a class="btn btn-success pull-right" target="_blank" href="{{ strtr(route('web.kit.pedigree', ['id' => '%id']), ['%id' => '{' . '{token}' . '}']) }}" style="margin-right:10px">
                            <i class="fa fa-external-link"></i> <strong>Public link</strong>
                        </a>
                    </div>

                </div>
            </section>
        </div>

        @include('layouts.breeders.partials.pedigree', ['type' => 'kit', 'id' => 'id', 'name' => '"#" + generations.g1.custom_id' ])

    </section>
</template>
