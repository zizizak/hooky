<template id="app">
  <div>
    <table class="hk">
      <thead class="hk-head">
        <td>Post type</td>
        <td>Trigger</td>
        <td>Transform</td>
        <td>Endpoint</td>
        <td>Auth Method</td>
        <td>Auth Token</td>
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
      hooks: window.hookah_hooks,
      messages: []
    }
  },
  methods: {
    addHook: function(){
      this.hooks.push({
        type:   'post',
        action:    'CREATE',
        transform:  'default',
        endpoint:   '',
        authmethod: '',
        authtoken:  '',
        id: null
      })
    },
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
    updateHook: function(event, index){
      let target = event.$event.target
      this.hooks[index][event.field] = target.value
    },
    save: function(){
      let site = window.site_url
      this.hooks.map(hook => {
        let config = {
          method:  'post',
          url:     `${site}/hooks`,
          data:    JSON.stringify(hook),
          headers: {'Content-Type': 'application/json'}
        }

        if(hook.id) config.url += `/${hook.id}`

        axios(config)
        .then(res => {
          this.messages.push(`Hook ${res.data.id} saved`)
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
