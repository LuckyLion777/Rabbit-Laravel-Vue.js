<a @click="edit(generations.{{ $g }}.id)" role="button" href="javascript: void(0);" v-if="generations.{{ $g }}">
    <div class="info-box {{ generations.<?= $g ?>.css.color }} {{ generations.<?= $g ?>.sex }}">
        <span class="info-box-icon">
            <img style="max-width: 90%; margin:5px auto; border: 3px solid" v-if="!generations.{{ $g }}.image"
                 src="{{asset('')}}media/pedigree/default.jpg" class="img-responsive img-circle">
            <img style="max-width: 90%; margin:5px auto; border: 3px solid" v-if="generations.{{ $g  }}.image.path"
                 v-bind:src="generations.{{ $g }}.image.path" class="img-responsive img-circle">
        </span>
        <div class="info-box-content">
            <span class="info-box-number"><small style="font-size: 10px;">{{ generations.<?= $g ?>.prefix }}</small></span>
            <span class="info-box-number">{{ generations.<?= $g ?>.name }}: {{ generations.<?= $g ?>.custom_id }}
                <i class="{{ generations.<?= $g ?>.css.icon }} pull-right"></i>
            </span>
            <span class="info-box-text">
                <span class="pull-left" style="max-width:50%">
                    <span v-show="generations.<?= $g ?>.day_of_birth">
                        DoB: {{ generations.<?= $g ?>.day_of_birth }}<br/>
                    </span>
                    <span v-show="generations.<?= $g ?>.aquired">
                        Acq: {{ generations.<?= $g ?>.aquired }}<br/>
                    </span>
                    <span v-show="generations.<?= $g ?>.registration_number">
                    Reg#: {{ generations.<?= $g ?>.registration_number }}<br/>
                	</span>
                    <span v-show="generations.<?= $g ?>.champion_number">
                      GC#: {{ generations.<?= $g ?>.champion_number }}<br>
                	</span>
                </span>
                <span class="pull-right text-right" style="clear: right; max-width:50%;">
                	<span v-show="generations.<?= $g ?>.color">
                       {{ generations.<?= $g ?>.color }}<br/>
                    </span>
                    <span v-show="generations.<?= $g ?>.breed">
                       {{ generations.<?= $g ?>.breed }}<br/>
                    </span>
                    <span v-show="generations.<?= $g ?>.weight_slug">
                       {{ generations.<?= $g ?>.weight_slug }}<br/>
                    </span>
                    <span v-show="generations.<?= $g ?>.legs">
                        Legs: {{ generations.<?= $g ?>.legs }}<br/>
                    </span>
                </span>
            </span>
            
            
            <span class="info-box-text">
                <span class="notes">{{ generations.<?= $g ?>.notes }}</span>
            </span>
        </div>
    </div>
</a>
