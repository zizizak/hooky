<?php

global $hookah_transforms;
$types = get_post_types();
foreach($types as $type){
  $hookah_transforms[$type][] = new HookahTransform('default', null);
}

class HookahTransform {
  function __construct($alias, $callback){
    $this->alias    = $alias;
    $this->callback = $callback;
  }
}

function hookah_add_transform($alias = '', $types = [], $callback = NULL){
  global $hookah_transforms;
  if(is_array($types)){
    foreach($types as $type){
      $hookah_transforms[$type][] = new HookahTransform($alias, $callback);
    }
  } else {
    $hookah_transforms[$types][] = new HookahTransform($alias, $callback);
  }
}
