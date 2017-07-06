<?php

class HookySend {

  /**
   * Prepares a POST request.
   * @param  array|object  $data     Data to be sent in POST body.
   * @param  string        $endpoint URL for POST request to be sent.
   * @param  array|boolean $auth     False is no auth is needed for post request. Array with keys `authmethod` and `authtoken` is auth is to be used.
   * @return array|WP Error          Return value of wp_remote_post()
   *
   * TODO: Add possible bodies other than JSON – XML, perhaps.
   */
  function __construct($data, $endpoint, $auth = false){
    $this->body     = json_encode($data);
    $this->endpoint = $endpoint;
    $this->headers  = ['Content-Type' => 'application/json; charset=utf-8'];

    if($auth){
      $this->headers['auth'] = $auth['authmethod'] . ' ' . $auth['authtoken'];
    }

    $this->endpoint_filter = null;
  }

  /**
   * Sends the POST request.
   * @param  function $success Closure to be called on POST success. Receives return value of wp_remote_post() as argument.
   * @param  function $err     Closure to be called on POST error. Receives WP_Error object as argument.
   * @return void
   */
  public function send($success = null, $err = null){
    $submit = wp_remote_post($this->endpoint, [
      'headers' => $this->headers,
      'body'    => $this->body,
      'timeout' => 60
    ]);

    if(
      is_array($submit) &&
      isset($submit['response']) &&
      $submit['response']['code'] === 200 || $submit['response']['code'] === 201){
      if($success) $success($submit);
    } elseif($err) {
      $err($submit);
    }
  }

}
