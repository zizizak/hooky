# Hooky

An extendable WebHook UI for WordPress!

## Installation

Download the zip, install as a plugin the normal way. You'll need to be running at least PHP5.3, because this plugin depends egregiously on closures.

## Building

To hack on the JS, run `npm install`. `gulp watchify` will rebuild the JS and watch for changes.

## Concepts

Hooky is an interface that allows for relatively painless WebHook setup for your WordPress site. The idea behind the plugin is that you should be able to set up integrations with just a few clicks. Depending on what services you need to integrate with and how custom your site's structure is, it's very possible you won't have to write any code – but if you do, that should be painless too.

<img width="1518" alt="screen shot 2017-06-01 at 11 23 05 am" src="https://cloud.githubusercontent.com/assets/3719099/26687017/b94e0f52-46bc-11e7-8d38-ee22a341462a.png">

For each hook you need to set up, you'll need to specify the following:

* `Post type` - the post type for which the hook should trigger
* `Action` - the action that should cause the hook to fire – more on this later
* `Filter` - a callback that can alter the structure of your data – more on this too
* `Endpoint` - where your webhook should POST to
* `Endpoint Filter` – a callback that can alter your endpoint
* `Auth method` - optional, how you might authenticate with your endpoint. Currently only basic auth is supported
* `Auth token` - the token for authorization
* `Success Callback` - a callback that will execute after POST is complete

In order to maximize your mileage with Hooky, there are a few concepts you'll need to understand:

### Actions

> [Time for some action](https://www.youtube.com/watch?v=SZAzRjmz5gQ). – N.E.R.D.

Actions specify when an HTTP callback should be fired. The triggers that Hooky ships with are `CREATE`, `UPDATE`, and `DELETE`. Whenever these actions occur for a post type that you've specified, an HTTP callback will be sent to the endpoint you've specified. You can rely on the default structure for data to be sent in, or you can specify your own filters.

### Filters

By default, the data sent in callbacks will be created using the `WP_Rest_Posts_Controller` class' `prepare_item_for_response()` method. You can specify your own filters for transforming your data by calling `hooky_add_filter()` anywhere in your theme's function file. Filters will alter the structure of the data sent as part of your hook.

```php
/**
 * @param $alias    [string] A unique identifier for your transform
 * @param $types    [string|Array] The post types for which this transform will be available
 * @param $callback [Function] A closure that will return an array or object of your data structure. Receives $id as an argument.
 */
hooky_add_filter($alias, $types, $callback);
```

For instance, let's say you're integrating with a service that only needs to know the title and author of newly published posts. You could call:

```php
hooky_add_filter('title_and_author', ['post'], function($id){
  return ['post_title' => get_the_title($id), 'post_author' => get_the_author($id)]
});
```

One other thing you might try would be using `get_post_meta` in your filter! Have fun.

### Endpoint Filters

REST endpoints often involve dynamic path params when updating existing content. You can filter the endpoint for a hook by calling `hooky_add_endpoint_filter()` in your theme's function file. Endpoint filters will let you use your data to dynamically generate a hooky's endpoint.

```php
/**
 * Can be used to register custom filters for endpoints that data can be send to.
 * @param string       $alias    Name of filter.
 * @param array|string $types    Post type or types for this filter. Can be string or array of strings.
 * @param function     $callback Closure that will return filter value. Receives endpoint and post object as arguments.
 * @return void
 */
hooky_add_endpoint_filter($alias, $types, $callback);
```

For instance, let's say your external service requires updated to be made at a REST endpoint using an ID as a path param. You can accomplish this by calling:

```php
// Endpoint would be something like http://myservice.dev/api/posts/<id>
hooky_add_endpoint_filter('id_endpoint', ['post'], function($endpoint, $post){
  return str_replace('<id>', $post->ID, $endpoint);
});
```

Generally, you'll be using `str_replace` or `preg_replace` in these filters.

### Success Callbacks

After POSTing to your service, you might want to execute code on your WordPress site based on the response send by the service. You can execute code by calling `hooky_add_success_callback()`.

```php
/**
 * Can be used to register custom callbacks for when data has been successfully POSTed.
 * @param string       $alias    Name of filter.
 * @param array|string $types    Post type or types for this filter. Can be string or array of strings.
 * @param function     $callback Closure that will handle succes. Receives ID and response.
 * @return void
 */
hooky_add_success_callback($alias, $types, $callback);
```

For instance, let's say that your external service uses different IDs for content than your WordPress site. You might want to store the external ID as a meta value on your post for use later. You could do this by calling something like:

```php
hooky_add_success_callback('save_external_id', 'post', function($id, $response){
  update_post_meta($id, $response['body']['id'], true);
  return;
});
```

Hooky uses the `wp_remote_post()` function to execute HTTP callbacks – read [that documentation](https://codex.wordpress.org/Function_Reference/wp_remote_post) for what you can expect from the response.

## On the radar

The following are features that will be developed at some point.

* Add API for extending actions beyond basic CREATE/UPDATE/DELETE operations.
* Add support for other forms of authentication than Basic.

## Thanks

The UI is build on [vue.js](https://github.com/vuejs/vue).
