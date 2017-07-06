<?php

// This function outputs success or error messages after posts have been saved.

if(array_key_exists(HOOKY_SESSION_ERR, $_SESSION)):
  foreach($_SESSION[HOOKY_SESSION_ERR] as $err):
?>
    <div class="error">
      <p><?php echo $err; ?></p>
    </div>
<?php
  endforeach;
  unset($_SESSION[HOOKY_SESSION_ERR] );
endif;

if(array_key_exists(HOOKY_SESSION_SUCCESS, $_SESSION)):
  foreach($_SESSION[HOOKY_SESSION_SUCCESS] as $message):
?>
    <div class="notice notice-success">
      <p><?php echo $message; ?></p>
    </div>
<?php
  endforeach;
  unset($_SESSION[HOOKY_SESSION_SUCCESS] );
endif;
