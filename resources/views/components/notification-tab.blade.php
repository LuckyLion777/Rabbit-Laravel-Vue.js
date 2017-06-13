<template id="notification-tab-template">
    <li class="dropdown tasks-menu">
        <a @click="opened" href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id="step7">
            <i class="fa fa-flag-o"></i>
            <span class="label label-danger">@{{ (onlyUnseen(activeTasks).length + onlyUnseen(transfers).length) || '' }}</span>
        </a>
        <ul class="dropdown-menu">
            <li class="header">You have @{{ activeTasks.length }} tasks</li>
            <li>
                <!-- inner menu: contains the actual data -->
                <ul class="menu">
                    <li v-for="($index, task) in activeTasks">
                        <i v-if="!task.read" v-on:click.prevent.stop="toggle(task)" class="notification-read fa fa-circle" title="Mark as read"></i>
                        <i v-else v-on:click.prevent.stop="toggle(task)" class="notification-read fa fa-circle-o" title="Mark as unread"></i>
                        <a @click.prevent="openManagingModal(task)" href="#">
                            <h3>
                                <i class="fa @{{ task.icon }} circle-background"></i>
                                <strong v-if="!task.read">@{{ getFullTaskName(task) }}</strong>
                                <template v-else>@{{ getFullTaskName(task) }}</template>
                            </h3>
                        </a>
                    </li>

                    <li><!-- Task item -->
                    </li><!-- end task item -->
                </ul>
            </li>
            <template v-if="transfers.length">
                <li class="header">You have @{{ transfers.length }} transfers</li>
                <li>
                    <ul class="menu">
                        <li v-for="transfer in transfers">
                            <i v-if="!transfer.read" v-on:click.prevent.stop="toggle(transfer)" class="notification-read fa fa-circle" title="Mark as read"></i>
                            <i v-else v-on:click.prevent.stop="toggle(transfer)" class="notification-read fa fa-circle-o" title="Mark as unread"></i>
                            <a @click.prevent="acceptTransfer(transfer)" href="#">
                                <h3>
                                    <i class="fa fa-info circle-background"></i>
                                    <strong v-if="!transfer.read">Claim @{{ transfer.breeder.name }}: @{{ transfer.breeder.tattoo }}</strong>
                                    <template v-else>Claim @{{ transfer.breeder.name }}: @{{ transfer.breeder.tattoo }}</template>
                                </h3>
                            </a>
                        </li>
                    </ul>
                </li>
            </template>
            <li class="footer">
                <a href="/#!/schedule">View all tasks</a>
            </li>
        </ul>
    </li>

</template>
