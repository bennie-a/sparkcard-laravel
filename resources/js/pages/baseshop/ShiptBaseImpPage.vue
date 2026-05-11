<script setup>
import {VDateInput} from 'vuetify/labs/VDateInput';


</script>
<template>
      <v-date-input
        prepend-icon=""
        prepend-inner-icon="$calendar"
        variant="outlined"
        input-format="yyyy/mm/dd"
      ></v-date-input>
<div class="ui horizontal segment">
  <div class="ui two column stackable left aligned grid">
    <div class="ui vertical divider">Or</div>
    <div class="row">
      <div class="column">
        <h2 class="ui header">Find Country</h2>
        <div class="field">
          <div class="ui search">
            <div class="ui icon input">
              <input class="prompt" type="text" placeholder="Search countries...">
              <i class="search icon"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="column">
        <h2 class="ui header">Create New Country</h2>
        <v-icon icon="mdi-home" />
        <div class="ui primary button">
          Create
        </div>
      </div>
    </div>
  </div>
</div>
</template>
