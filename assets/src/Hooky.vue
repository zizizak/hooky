<template id="app">
  <div>
    <table class="hk">
      <thead class="hk-head">
        <td>Post type</td>
        <td>Trigger</td>
        <td>Filter</td>
        <td>Endpoint</td>
        <td>Endpoint Filter</td>
        <td>Auth Method</td>
        <td>Auth Token</td>
        <td>Success Callback</td>
      </thead>
      <Row v-for="(hook, index) in hooks" :hook="hook" v-on:update-hook="updateHook($event, index)" v-on:remove-hook="removeHook($event, index)"></Row>
    </table>
    <div class="hk-buttons">
      <button v-on:click="addHook">Add Hook</button>
      <button v-on:click="save">Save Changes</button>
    </div>
    <div class="hk-output" v-if="messages.length">
      <ul>
        <li v-for="message in messages">{{message}}</li>
      </ul>
    </div>
  </div>
</template>

<script>
import Row from './Row.vue';
import axios from 'axios'
export default {
  data: () => {
    return {
      hooks: window.hooky_hooks,
      messages: []
    }
  },
  methods: {
    // Add an empty hook to a new row
    addHook: function(){
      this.hooks.push({
        type:   'post',
        action:    'CREATE',
        filter:  'default',
        endpoint:   '',
        endpoint_filter: '',
        authmethod: '',
        authtoken:  '',
        success_callback: '',
        id: null
      })
    },
    // Remove a hook (from UI and send AJAX request)
    removeHook: function(event, index){
      let site = window.site_url
      if(event.id){
        axios.delete(`${site}/hooks/${event.id}`)
        .then(res => {
          this.hooks.splice(index, 1)
        })
        .catch(err => {
          console.log(err.response)
        })
      } else {
        this.hooks.splice(index, 1)
      }
    },
    // Update model for a hook on change
    updateHook: function(event, index){
      let target = event.$event.target
      this.hooks[index][event.field] = target.value
    },
    // Save changes
    save: function(){
      let site = window.site_url
      this.hooks.map(hook => {
        let config = {
          method:  'post',
          url:     `${site}/hooks`,
          data:    JSON.stringify(hook),
          headers: {'Content-Type': 'application/json'}
        }

        // Add exisiting hook ID so it can be modified instead of created
        if(hook.id) config.url += `/${hook.id}`

        console.log(config.data);

        axios(config)
        .then(res => {
          this.messages.push(`Hook ${res.data.id} saved`)
          // Remove notice after four seconds
          setTimeout(() => {
            this.messages = []
          }, 4000)
        })
        .catch(err => console.log(err.response))
      })
    }
  },
  components: {Row}
}
</script>

<style>
.hk {
  width: 100%;
}
</style>
