# Hookah

An extendable WebHook UI for WordPress!

## Installation

Download the zip, install as normal. Easy-peasy. You'll need to be running at least PHP5.3, because this plugin depends egregiously on closures.

## Concepts

Hookah is an interface that allows for relatively painless WebHook setup for your WordPress site. The idea behind the plugin is that you should be able to set up integrations with just a few clicks. Depending on what services you need to integrate with and how custom your site's structure is, it's very possible you won't have to write any code – but if you do, that should be painless too.

<img width="996" alt="screen shot 2017-05-15 at 10 45 16 pm" src="https://cloud.githubusercontent.com/assets/3719099/26087767/bf9252be-39c0-11e7-81f5-97202189561a.png">

For each hook you need to set up, you'll need to specify the following:

* `Post type` - the post type for which the hook should trigger
* `Trigger` - the action that should cause the hook to fire – more on this later
* `Transform` - a filter that can alter the structure of your data – more on this too
* `Endpoint` - where your webhook should POST to
* `Auth method` - optional, how you might authenticate with your endpoint. Currently only basic auth is supported
* `Auth token` - the token for authorization

In order to maximize your mileage with Hookah, there are a few concepts you'll need to understand: `Triggers` and `Transforms`:

### Triggers

> The wonderful thing about triggers, is triggers are wonderful things.

Triggers are actions that can cause an HTTP callback to be fired. The triggers that Hookah ships with are `CREATE`, `UPDATE`, and `DELETE` – you know these. Whenever these actions occur for a post type that you've specified,

### Transforms

> I can transform ya. – Chris Brown

Transforms are filters that can shape alter the structure of the data sent as part of your data. By default, the plugin will use a generic post object. You can extend the transforms you have available by calling `hookah_add_transform()` anywhere in your theme:

```php
/**
 * @param $alias    [string] A unique identifier for your transform
 * @param $types    [string|Array] The post types for which this transform will be available_transforms
 * @param $callback [Function] A closure that will return an array or object of your data structure. Receives $id as an argument.
 */
hookah_add_transform($alias, $types, $callback);
```

For instance, let's say you're integrating with a service that only needs to know the title and author of newly published posts. You could call:

```php
hookah_add_transform('title_and_author', ['post'], function($id){
  return ['post_title' => get_the_title($id), 'post_author' => get_the_author($id)]
});
```

### Endpoints

REST endpoints often involve dynamic path params for content IDs. Accordingly, Hookah endpoints can accommodate this. Including the string `<id>` as part of your endpoint string will be replaced with the ID of your content.

## On the radar

The following are features that will be developed at some point.

* Add API for extending triggers beyond basic CUD operations.
* Add support for other forms of authentication
* Add support for other dynamic endpoint arguments
