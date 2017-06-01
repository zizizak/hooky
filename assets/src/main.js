import Vue    from 'vue/dist/vue.js'
import Hooky  from './Hooky.vue'
import Row    from './Row.vue'

// for production
Vue.config.devtools      = false
Vue.config.debug         = false
Vue.config.productionTip = false
Vue.config.silent        = true

new Vue({
  el: '#app',
  render: h => h(Hooky)
})
