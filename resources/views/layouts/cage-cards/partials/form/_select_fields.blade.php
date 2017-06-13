<select v-on:change="addSelectedFieldToFields" class="form-control">
    <option value="" disabled selected>Choose</option>
    <option v-for="field in fieldsoptions" value="{{ $id }}:@{{ field }}" v-bind:selected="isSelectFieldValue(field, {{ $id }})">
        @{{ getReadableOptionsFieldName(field) }}
    </option>
    <input  class="form-control" v-if="isCustomFieldVisible({{ $id }})" v-on:keyup="setCustomField({{ $id }}, $event.target.value)" placeholder="Enter text..." type="text" v-bind:value="getCustomFieldValue({{ $id }})">
</select>
