<?php

/**
 * This file add the HTML for Hooky options page.
 * (Note that the UI is built with Vue.js, so this is sparse.)
 */

add_options_page('Hooky', 'Hooky', 'administrator', 'hooky', function(){
  echo '<h1>Hooky</h1><div id="app" class="wrap"></div>';
});
