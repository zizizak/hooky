<template>
  <tr class="hk-row">
    <td>
      <select @change="$emit('update-hook', {$event, field: 'type'})">
        <option v-for="(value, key) in transforms" :selected="key === hook.type">{{key}}</option>
      </select>
    </td>
    <td>
      <select @change="$emit('update-hook', {$event, field: 'action'})">
        <option :selected="hook.action === 'CREATE'">CREATE</option>
        <option :selected="hook.action === 'UPDATE'">UPDATE</option>
        <option :selected="hook.action === 'DELETE'">DELETE</option>
      </select>
    </td>
    <td>
      <select @change="$emit('update-hook', {$event, field: 'transform'})">
        <option v-for="(transform, index) in transforms[hook.type]" :selected="transform.alias === hook.transform">{{transform.alias}}</option>
      </select>
    </td>
    <td>
      <input @change="$emit('update-hook', {$event, field: 'endpoint'})" type="url" :value="hook.endpoint">
    </td>
    <td>
      <select @change="$emit('update-hook', {$event, field: 'authmethod'})">
        <option :selected="hook.authmethod === 'Basic'">Basic</option>
        <option :selected="hook.authmethod === 'None'">None</option>
      </select>
    </td>
    <td>
      <input @change="$emit('update-hook', {$event, field: 'authtoken'})" type="text" :value="hook.authtoken" :disabled="hook.authmethod === 'None'">
    </td>
    <td>
      <button @click="$emit('remove-hook', {id: hook.id})">&times;</button>
    </td>
  </tr>
</template>

<script>
export default {
  props: ['hook'],
  data: function(){
    return {
      transforms: window.hookah_transforms
    }
  }
}
</script>

<style>
.hk-row input,
.hk-row select {
  width: 100%;
  margin: 0;
}
</style>
