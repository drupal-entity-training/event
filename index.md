---
layout: default
title: {{ site.name }}
---

This guide documents the process of creating a custom entity type in Drupal 8
using the example of an  _Event_ entity type.

You can reach this guide at [https://git.io/d8entity][guide-short-url].

The starting point is a stock Drupal 8.6 core _Standard_ installation with the
contributed [Entity API][contrib-entity-api] available at `modules/entity` and an
empty module directory at `modules/event`.

Having [Drush 9][drush] available is required to follow along. When Drush
commands are to be run, run them from within the Drupal installation. When PHP
code is to be executed, this can be done by running `drush core:cli` or by
creating a test script and then running `drush php:script <name-of-script>`.

**Table of contents**
1. [Using entities for data storage](#using-entities-for-data-storage)
      1. [Create a module](#create-a-module)
      2. [Create a minimal entity class](#create-a-minimal-entity-class)
      3. [Install the entity type](#install-the-entity-type)
      4. [Add field definitions](#add-field-definitions)
      5. [Install the fields](#install-the-fields)
      6. [Add field methods](#add-field-methods)
2. [Viewing entities on a page](#viewing-entities-on-a-page)
      1. [Install the contributed _Entity API_ module.](#install-the-contributed-_entity-api_-module.)
      2. [Add a route](#add-a-route)
      3. [Configure fields for display](#configure-fields-for-display)
3. [Manipulating entities through forms](#manipulating-entities-through-forms)
      1. [Add the routes](#add-the-routes)
      2. [Configure fields for display](#configure-fields-for-display)
      3. [Add a specialized form](#add-a-specialized-form)
4. [Listing entities](#listing-entities)
      1. [Add a route](#add-a-route)
      2. [Add a specialized list builder](#add-a-specialized-list-builder)
      3. [Add an administrative view](#add-an-administrative-view)
5. [Adding administrative links](#adding-administrative-links)
      1. [Add a menu link for the event listing](#add-a-menu-link-for-the-event-listing)
      2. [Add a local task for the event listing](#add-a-local-task-for-the-event-listing)
      3. [Add an action link for the add form](#add-an-action-link-for-the-add-form)
      4. [Add local tasks for the edit and delete forms](#add-local-tasks-for-the-edit-and-delete-forms)
6. [Adding permission-based access-control](#adding-permission-based-access-control)
      1. [Add permissions](#add-permissions)
      2. [Add an access control handler](#add-an-access-control-handler)
7. [Adding additional fields](#adding-additional-fields)
      1. [Add the field definitions](#add-the-field-definitions)
      2. [Install the fields](#install-the-fields)
      3. [Add additional field methods](#add-additional-field-methods)
8. [Storing dynamic data in configuration](#storing-dynamic-data-in-configuration)
      1. [Create an entity class](#create-an-entity-class)
      2. [Provide a configuration schema](#provide-a-configuration-schema)
      3. [Install the entity type](#install-the-entity-type)
9. [Providing a user interface for configuration entities](#providing-a-user-interface-for-configuration-entities)
      1. [Add a list of event types](#add-a-list-of-event-types)
      2. [Add forms for event types](#add-forms-for-event-types)
10. [Categorizing different entities of the same entity type](#categorizing-different-entities-of-the-same-entity-type)
      1. [Add the bundle field](#add-the-bundle-field)
      2. [Install the bundle field](#install-the-bundle-field)
11. [Configuring bundles in the user interface](#configuring-bundles-in-the-user-interface)
      1. [Enable Field UI for events](#enable-field-ui-for-events)
      2. [Add dynamic fields to events](#add-dynamic-fields-to-events)
      3. [Configure view modes](#configure-view-modes)
      4. [Configure the form](#configure-the-form)
12. [Translating content](#translating-content)
      1. [Install the Content Translation module](#install-the-content-translation-module)
      2. [Make events translatable](#make-events-translatable)
13. [Translating configuration](#translating-configuration)
      1. [Install the Configuration Translation module](#install-the-configuration-translation-module)

### 1. Using entities for data storage

#### 1.1. Create a module

* Within the `/modules/event` directory create an `event.info.yml` file with the
  following:

  ```yaml
  name: Event
  type: module
  core: 8.x
  ```

* Run `drush pm:enable event` or visit `/admin/modules` and install the _Event_
  module

#### 1.2. Create a minimal entity class

_Classes_ allow categorizing objects as being of a certain type. Event
entities, that will be created below, will be _instances_ of the entity
class. In terms of code organization, classes can be used to group related
functionality.

* <details><summary>Create a <code>src</code> directory</summary>

    In Drupal 8 the `src` directory contains all object-oriented code (classes,
    interfaces, traits). Procedural code (functions) is placed in the `.module`
    file (or other files) outside of the `src` directory.

  </details>

* <details>
    <summary>Create a <code>src/Entity</code> directory</summary>

    As modules often contain many classes, they can be placed into arbitrary
    subdirectories for organizational purposes. Certain directory names have a
    special meaning in Drupal and are required for certain things. In
    particular, Drupal looks in `Entity` for entity types.
  </details>

* Create a `src/Entity/Event.php` file with the following:

  ```php
  <?php

  namespace Drupal\event\Entity;

  use Drupal\Core\Entity\ContentEntityBase;

  /**
   * @ContentEntityType(
   *   id = "event",
   *   label = @Translation("Event"),
   *   base_table = "event",
   *   entity_keys = {
   *     "id" = "id",
   *     "uuid" = "uuid",
   *   },
   * )
   */
  class Event extends ContentEntityBase {

  }
  ```

  <details><summary>Click here for more information on the above</summary>

  * Namespace:

    ```php
    namespace Drupal\event\Entity;
    ```

      Namespaces allow code from different frameworks (Drupal, Symfony, …) to be
      used simultaneously without risking naming conflicts. Namespaces can have
      multiple parts. All classes in Drupal core and modules have `Drupal` as the
      top-level namespace. The second part of module classes must be the module
      name. Further sub-namespaces correspond to the directory structure within
      the `src` directory of the module.

  * Import:

    ```php
    use Drupal\Core\Entity\ContentEntityBase;
    ```

    In the same way we declare a namespace for the `Event` class the
    `ContentEntityBase` class used below also belongs to a namespace. Thus, in
    order to use it below, we need to import the class using the full namespace.

  * Annotation:

    _Annotations_ are a way to provide metadata about code. Because the
    annotation is placed right next to the code itself, this makes classes truly
    self-contained as both functionality and metadata are in the same file.

    Even though the annotation is part of a comment block, it is required for
    the entity type to function.

    * ID:

      ```php
       *   id = "event",
      ```

      This is the ID of the entity type that is needed whenever interacting with
      a specific entity type in code.

    * Label:

      ```php
       *   label = @Translation("Event"),
      ```

      This is the label of this entity type when presented to a user.

      To make the values we provide in the annotation translatable we need to
      wrap them in `@Translation` which is themself an annotations.

    * Storage information:

      ```php
       *   base_table = "event",
       *   entity_keys = {
       *     "id" = "id",
       *     "uuid" = "uuid",
       *   },
      ```

      We need to specify the name of the database table we want the event data
      to be stored. (This is called _base_ table, as there can be multiple
      tables that store entity information, as will be seen below.)

      Entities are required to have an ID which they can be loaded by. We need
      to specify what the ID field will be called for our entity. This will also
      determine the name of the database column that will hold the entity IDs.
      Similarly entity types can (and are encouraged to) provide a UUID field.
      Again, we can specify the name of the UUID field.

      Note that top-level keys of the annotation are not quoted, but keys in
      mappings (such as the `entity_keys` declaration) _are_ quoted and trailing
      commas are allowed in mappings.

    See [Drupal API: Annotations][api-annotations] for more information.

  * Class declaration:

    ```php
    class Event extends ContentEntityBase {

    }
    ```

    The file name must correspond to class name (including capitalization).

    * Inheritance:

      ```php
      extends
      ```

      Base classes can be used to implement functionality that is generic and
      useful for many classes. Classes can inherit all functionality from such a
      base class by using the `extends` keyword. they them. Then they only need
      to provide functionality specific to them, which avoids code duplication.

    * Content entities:

      ```php
      ContentEntityBase
      ```

      Content entities are entities that are created by site users. They are
      typically stored in the database, often with a auto-incrementing integer ID.

    See [Drupal API: Object-oriented programming conventions][api-oop] for more
    information.
  </details>

#### 1.3. Install the entity type

Drupal can create the database schema for our entity type automatically but this
needs to be done explicitly. The preferred way of doing this is with Drush.

* Run `drush entity-updates`

  Note that the `{event}` table has been created in the database with `id`
  and `uuid` columns.

* Create and save an event

  Run the following PHP code:

  ```php
  use Drupal\event\Entity\Event;

  $event = Event::create();
  $event->save();
  ```

  Note that there is a new row in the `{event}` table with an ID and a UUID.

  <details><summary>Click here for more information on the above</summary>

    The `Event` class inherits the `create()` and `save()` methods from
    `ContentEntityBase` so they can be called without being present in the
    `Event` class itself.

    `create()` is a _static_ method so it is called by using the class name and
    the `::` syntax. `save()` is not a static method so it is used with an
    instance of the class and the `->` syntax.
  </details>

* Load an event fetch its ID and UUID

  Run the following PHP code:

  ```php
  use Drupal\event\Entity\Event;

  $event = Event::load(1);
  print 'ID: ' . $event->id() . PHP_EOL;
  print 'UUID: ' . $event->uuid() . PHP_EOL;
  ```

  Note that the returned values match the values in the database.

* Delete the event

  Run the following PHP code:

  ```php
  use Drupal\event\Entity\Event;

  $event = Event::load(1);
  $event->delete();
  ```

  Note that the row in the `{event}` table is gone.

#### 1.4. Add field definitions

Fields are the pieces of data that make up an entity. The ID and UUID that
were saved as part of the event above are examples of field values. To be
able to store actual event data in our entities, we need to declare
additional fields.

Just like with the ID and UUID fields above, Drupal can automatically provide
_Author_ and _Published_ fields for us, which we will take advantage of so that
we can track who created events and distinguish published and unpublished
events.

* Add the following to `event.info.yml`:

  ```yaml
  dependencies:
    - drupal:user
  ```

* Add the following use statements to `src/Entity/Event.php`:

  ```php
  use Drupal\Core\Entity\EntityPublishedInterface;
  use Drupal\Core\Entity\EntityPublishedTrait;
  use Drupal\Core\Entity\EntityTypeInterface;
  use Drupal\Core\Field\BaseFieldDefinition;
  use Drupal\user\EntityOwnerInterface;
  use Drupal\user\EntityOwnerTrait;
  ```

* Add the following to the `entity_keys` part of the annotation of the `Event`
  class:

  ```php
   *     "label" = "title",
   *     "owner" = "author",
   *     "published" = "published",
  ```

  Declaring a `label` key makes the (inherited) `label()` method on the `Event`
  class work and also allows autocompletion of events by their title.

* Add the following to the end of the class declaration of the `Event` class:

  ```php
  implements EntityOwnerInterface, EntityPublishedInterface
  ```

* Add the following inside of the class declaration of the `Event` class:

  ```php
  use EntityOwnerTrait, EntityPublishedTrait;

  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    // Get the field definitions for 'id' and 'uuid' from the parent.
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setRequired(TRUE);

    $fields['date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Date'))
      ->setRequired(TRUE);

    $fields['description'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Description'));

    // Get the field definitions for 'author' and 'published' from the trait.
    $fields += static::ownerBaseFieldDefinitions($entity_type);
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    return $fields;
  }
  ```

  <details><summary>Click here for more information on the above</summary>

  <!-- TODO: Explain traits -->

  * Type hint:

    ```php
    EntityTypeInterface $entity_type
    ```

    _Interfaces_ are contracts that specify the methods a class must have in
    order to fulfill it.

    The interface name in front of the `$entity_type` parameter is a _type
    hint_. It dictates what type of object must be passed. Type hinting an
    interface allows any class that _implements_ the interface to be passed.

  * Inheritance:

    ```php?start_inline
    $fields = parent::baseFieldDefinitions($entity_type);
    ```

    The class that is extended (`ContentEntityBase` in this case) is called the
     _parent_ class. The `baseFieldDefinitions()` method in `ContentEntityBase`
     provides field definitions for the `id` and `uuid` fields. Inheritance
     allows us to re-use those field definitions while still adding additional
     ones.

  * Field definition:

    ```php
    BaseFieldDefinition::create('string');
    ```

    _Field definitions_ are objects that hold metadata about a field. They
    are created by passing the field type ID into the static `create` method.
    There is no list of IDs of available field types, but
    [Drupal API: List of classes annotated with FieldType][api-field-types]
    lists all field type classes in core. The ID of a given field type can be
    found in its class documentation or by inspecting the `@FieldType`
    annotation.

  * Chaining:

    ```php
    ->setLabel(t('Title'))
    ->setRequired(TRUE)
    ```

    Many setter methods return the object they were called on to allow
    _chaining_ multiple setter methods after another. The setting up of the
    `title` field definition above is functionally equivalent to the
    following code block which avoids chaining:

    ```php
    $fields['title'] = BaseFieldDefinition::create('string');
    $fields['title']->setLabel(t('Title'));
    $fields['title']->setRequired(TRUE);
    ```

  <!-- TODO: Explain += -->

  </details>

  <details><summary>Click here to see the entire <code>Event.php</code> file at this point</summary>

    ```php
    <?php
    
    namespace Drupal\event\Entity;
    
    use Drupal\Core\Entity\ContentEntityBase;
    use Drupal\Core\Entity\EntityPublishedInterface;
    use Drupal\Core\Entity\EntityPublishedTrait;
    use Drupal\Core\Entity\EntityTypeInterface;
    use Drupal\Core\Field\BaseFieldDefinition;
    use Drupal\user\EntityOwnerInterface;
    use Drupal\user\EntityOwnerTrait;
    
    /**
    * @ContentEntityType(
    *   id = "event",
    *   label = @Translation("Event"),
    *   base_table = "event",
    *   entity_keys = {
    *     "id" = "id",
    *     "uuid" = "uuid",
    *     "label" = "title",
    *     "owner" = "author",
    *     "published" = "published",
    *   },
    * )
    */
    class Event extends ContentEntityBase implements EntityOwnerInterface, EntityPublishedInterface {
    
    use EntityOwnerTrait, EntityPublishedTrait;
    
    public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
      // Get the field definitions for 'id' and 'uuid' from the parent.
      $fields = parent::baseFieldDefinitions($entity_type);
    
      $fields['title'] = BaseFieldDefinition::create('string')
        ->setLabel(t('Title'))
        ->setRequired(TRUE);
    
      $fields['date'] = BaseFieldDefinition::create('datetime')
        ->setLabel(t('Date'))
        ->setRequired(TRUE);
    
      $fields['description'] = BaseFieldDefinition::create('text_long')
        ->setLabel(t('Description'));
    
      // Get the field definitions for 'author' and 'published' from the traits.
      $fields += static::ownerBaseFieldDefinitions($entity_type);
      $fields += static::publishedBaseFieldDefinitions($entity_type);
    
      return $fields;
    }
    
    }
    ```

  </details>

#### 1.5. Install the fields

Drupal notices changes to the entity type that affect the database schema and can
update it automatically.

* Run `drush entity-updates`

  Note that `title`, `date`, `description__value`, `description__format` and
  `published` columns have been created in the `{event}` table.

  Although most field types consist of a single `value` _property_, text
  fields, for example, have an additional `format` property. Therefore
  two database columns are required for text fields.

* Create and save an event

  Run the following PHP code:

  ```php
  use Drupal\event\Entity\Event;

  $event = Event::create([
    'title' => 'Drupal User Group',
    'date' => (new \DateTime())->format(DATETIME_DATETIME_STORAGE_FORMAT),
    'description' => [
      'value' => '<p>The monthly meeting of Drupalists is happening today!</p>',
      'format' => 'restricted_html',
    ],
  ]);
  $event->save();
  ```

  Note that there is a new row in the `{event}` table with the proper field
  values.

* Load an event and fetch its field values.

  Run the following PHP code:

  ```php
  use Drupal\event\Entity\Event;

  $event = Event::load(1);
  
  print 'Title: ' . $event->get('title')->value . "\n\n";
  
  print 'Date value: ' . $event->get('date')->value . "\n";
  print 'Date object: ' . var_export($event->get('date')->date, TRUE) . "\n\n";
  
  print 'Description value: ' . $event->get('description')->value . "\n";
  print 'Description format: ' . $event->get('description')->format . "\n";
  print 'Processed description: ' . var_export($event->get('description')->processed, TRUE) . "\n\n";
  
  print 'Author: ' . $event->get('author')->entity->getDisplayName() . "\n\n";
  
  print 'Published: ' . $event->get('published')->value . "\n";
  ```

  Note that the returned values match the values in the database.

  In addition to the stored properties field types can also declare
  _computed_ properties, such as the `date` property of a datetime field or
  the `processed` property of text fields.

* Update an event's field values and save them.

  Run the following PHP code:

  ```php
  use Drupal\event\Entity\Event;

  $event = Event::load(2);

  $event
    ->set('title', 'DrupalCon')
    ->set('date', (new \DateTime('yesterday'))->format(DATETIME_DATETIME_STORAGE_FORMAT))
    ->set('description', [
      'value' => '<p>DrupalCon is a great place to meet international Drupal superstars.</p>',
      'format' => 'basic_html',
    ])
    ->set('author', 1)
    ->set('published', FALSE)
    ->save();
  ```

  Note that the values in the database have been updated accordingly.

#### 1.6. Add field methods

Instead of relying on the generic `get()` and `set()` methods it is recommended
to add field-specific methods that wrap them. This makes interacting with
events in code more convenient. Futhermore, it is recommended to add an
interface

* Add the following use statements to `src/Entity/Event.php`:

  ```php
  use Drupal\Core\Datetime\DrupalDateTime;
  ```

* Add the following methods to the `Event` class:

  ```php
  /**
   * @return string
   */
  public function getTitle() {
    return $this->get('title')->value;
  }

  /**
   * @param string $title
   *
   * @return $this
   */
  public function setTitle($title) {
    return $this->set('title', $title);
  }

  /**
   * @return \Drupal\Core\Datetime\DrupalDateTime
   */
  public function getDate() {
    return $this->get('date')->date;
  }

  /**
   * @param \Drupal\Core\Datetime\DrupalDateTime $date
   *
   * @return $this
   */
  public function setDate(DrupalDateTime $date) {
    return $this->set('date', $date->format(DATETIME_DATETIME_STORAGE_FORMAT));
  }

  /**
   * @return \Drupal\filter\Render\FilteredMarkup
   */
  public function getDescription() {
    return $this->get('description')->processed;
  }

  /**
   * @param string $description
   * @param string $format
   *
   * @return $this
   */
  public function setDescription($description, $format) {
    return $this->set('description', [
      'value' => $description,
      'format' => $format,
    ]);
  }
  ```

  Field methods not only provide autocompletion, but also allow designing richer
  APIs than the bare field types provide. The `setDate()` method, for example,
  hides the internal storage format of datetime values from anyone working with
  events. Similarly the `setDescription()` method requires setting the
  description and the text format simultaneously for security. The `publish()`
  and `unpublish()` methods make the code more readable than with a generic
  `setPublished()` method.

  Note that entity types in core provide an entity-type-specific interface (such
  as `EventInterface` in this case) to which they add such field methods. This
  is omitted here for brevity.

  <details><summary>Click here to see the entire <code>Event.php</code> file at this point</summary>
    
    ```php
    <?php
    
    namespace Drupal\event\Entity;
    
    use Drupal\Core\Datetime\DrupalDateTime;
    use Drupal\Core\Entity\ContentEntityBase;
    use Drupal\Core\Entity\EntityPublishedInterface;
    use Drupal\Core\Entity\EntityPublishedTrait;
    use Drupal\Core\Entity\EntityTypeInterface;
    use Drupal\Core\Field\BaseFieldDefinition;
    use Drupal\user\EntityOwnerInterface;
    use Drupal\user\EntityOwnerTrait;
    
    /**
    * @ContentEntityType(
    *   id = "event",
    *   label = @Translation("Event"),
    *   base_table = "event",
    *   entity_keys = {
    *     "id" = "id",
    *     "uuid" = "uuid",
    *     "label" = "title",
    *     "owner" = "author",
    *     "published" = "published",
    *   },
    * )
    */
    class Event extends ContentEntityBase implements EntityOwnerInterface, EntityPublishedInterface {
    
    use EntityOwnerTrait, EntityPublishedTrait;
    
    public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
      // Get the field definitions for 'id' and 'uuid' from the parent.
      $fields = parent::baseFieldDefinitions($entity_type);
    
      $fields['title'] = BaseFieldDefinition::create('string')
        ->setLabel(t('Title'))
        ->setRequired(TRUE);
    
      $fields['date'] = BaseFieldDefinition::create('datetime')
        ->setLabel(t('Date'))
        ->setRequired(TRUE);
    
      $fields['description'] = BaseFieldDefinition::create('text_long')
        ->setLabel(t('Description'));
    
      // Get the field definitions for 'author' and 'published' from the traits.
      $fields += static::ownerBaseFieldDefinitions($entity_type);
      $fields += static::publishedBaseFieldDefinitions($entity_type);
    
      return $fields;
    }
    
    /**
     * @return string
     */
    public function getTitle() {
      return $this->get('title')->value;
    }
    
    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title) {
      return $this->set('title', $title);
    }
    
    /**
     * @return \Drupal\Core\Datetime\DrupalDateTime
     */
    public function getDate() {
      return $this->get('date')->date;
    }
    
    /**
     * @param \Drupal\Core\Datetime\DrupalDateTime $date
     *
     * @return $this
     */
    public function setDate(DrupalDateTime $date) {
      return $this->set('date', $date->format(DATETIME_DATETIME_STORAGE_FORMAT));
    }
    
    /**
     * @return \Drupal\filter\Render\FilteredMarkup
     */
    public function getDescription() {
      return $this->get('description')->processed;
    }
    
    /**
     * @param string $description
     * @param string $format
     *
     * @return $this
     */
    public function setDescription($description, $format) {
      return $this->set('description', [
        'value' => $description,
        'format' => $format,
      ]);
    }
    
    }
    ```

  </details>

* Try out the new getter methods

  Run the following PHP code:

  ```php
  use Drupal\event\Entity\Event;

  $event = Event::load(1);

  $event->getTitle();
  $event->getDate();
  $event->getDescription();
  $event->isPublished();
  ```

  Note that the returned values match the values in the database.

* Try out the new setter methods

  Run the following PHP code:

  ```php
  use Drupal\Core\Datetime\DrupalDateTime;
  use Drupal\event\Entity\Event;

  $event
    ->setTitle('Drupal Developer Days')
    ->setDate(new DrupalDateTime('tomorrow'))
    ->setDescription(
      '<p>The Drupal Developer Days are a great place to nerd out about all things Drupal!</p>',
      'basic_html'
    )
    ->setOwnerId(0)
    ->setPublished(FALSE)
    ->save();
  ```

  Note that the values in the database have been updated accordingly.

### 2. Viewing entities on a page

Viewing an entity on a page requires a route on which the entity's field values
are output on a given path. This can be automated by amending the entity
annotation.

#### 2.1. Install the contributed _Entity API_ module.

The contributed [Entity API](contrib-entity-api) provides various enhancements
to the core Entity API. One such enhancement is the ability to more easily
provide permissions entity types which we will now use.

* Run `drush pm:enable entity` or visit `/admin/modules` and install the
 _Entity API_ module

* Add the following to the `dependencies` section of `event.info.yml`:

  ```yaml
    - entity:entity
  ```

#### 2.2. Add a route

* Add the following to the annotation in `src/Entity/Event.php`:

  ```php
   *   handlers = {
   *     "access" = "Drupal\entity\EntityAccessControlHandler",
   *     "permission_provider" = "Drupal\entity\EntityPermissionProvider",
   *     "route_provider" = {
   *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
   *     },
   *   },
   *   links = {
   *     "canonical" = "/event/{event}"
   *   },
   *   admin_permission = "administer event",
  ```

  <details><summary>Click here to see the entire <code>Event.php</code> file at this point</summary>
    
    ```php
    <?php
    
    namespace Drupal\event\Entity;
    
    use Drupal\Core\Datetime\DrupalDateTime;
    use Drupal\Core\Entity\ContentEntityBase;
    use Drupal\Core\Entity\EntityPublishedInterface;
    use Drupal\Core\Entity\EntityPublishedTrait;
    use Drupal\Core\Entity\EntityTypeInterface;
    use Drupal\Core\Field\BaseFieldDefinition;
    use Drupal\user\EntityOwnerInterface;
    use Drupal\user\EntityOwnerTrait;
    
    /**
    * @ContentEntityType(
    *   id = "event",
    *   label = @Translation("Event"),
    *   base_table = "event",
    *   entity_keys = {
    *     "id" = "id",
    *     "uuid" = "uuid",
    *     "label" = "title",
    *     "owner" = "author",
    *     "published" = "published",
    *   },
    *   handlers = {
    *     "access" = "Drupal\entity\EntityAccessControlHandler",
    *     "permission_provider" = "Drupal\entity\EntityPermissionProvider",
    *     "route_provider" = {
    *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
    *     },
    *   },
    *   links = {
    *     "canonical" = "/event/{event}"
    *   },
    *   admin_permission = "administer event"
    * )
    */
    class Event extends ContentEntityBase implements EntityOwnerInterface, EntityPublishedInterface {
    
    use EntityOwnerTrait, EntityPublishedTrait;
    
    public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
      // Get the field definitions for 'id' and 'uuid' from the parent.
      $fields = parent::baseFieldDefinitions($entity_type);
    
      $fields['title'] = BaseFieldDefinition::create('string')
        ->setLabel(t('Title'))
        ->setRequired(TRUE);
    
      $fields['date'] = BaseFieldDefinition::create('datetime')
        ->setLabel(t('Date'))
        ->setRequired(TRUE);
    
      $fields['description'] = BaseFieldDefinition::create('text_long')
        ->setLabel(t('Description'));
    
      // Get the field definitions for 'owner' and 'published' from the traits.
      $fields += static::ownerBaseFieldDefinitions($entity_type);
      $fields += static::publishedBaseFieldDefinitions($entity_type);
    
      return $fields;
    }
    
    /**
     * @return string
     */
    public function getTitle() {
      return $this->get('title')->value;
    }
    
    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title) {
      return $this->set('title', $title);
    }
    
    /**
     * @return \Drupal\Core\Datetime\DrupalDateTime
     */
    public function getDate() {
      return $this->get('date')->date;
    }
    
    /**
     * @param \Drupal\Core\Datetime\DrupalDateTime $date
     *
     * @return $this
     */
    public function setDate(DrupalDateTime $date) {
      return $this->set('date', $date->format(DATETIME_DATETIME_STORAGE_FORMAT));
    }
    
    /**
     * @return \Drupal\filter\Render\FilteredMarkup
     */
    public function getDescription() {
      return $this->get('description')->processed;
    }
    
    /**
     * @param string $description
     * @param string $format
     *
     * @return $this
     */
    public function setDescription($description, $format) {
      return $this->set('description', [
        'value' => $description,
        'format' => $format,
      ]);
    }
    
    }
    ```

  </details>

  <details><summary>Click here for more information on the above</summary>

    * Entity handlers:

      ```php
       *   handlers = {
      ...
       *   },
      ```

      Entity _handlers_ are objects that take over certain tasks related to
      entities. Each entity type can declare which handler it wants to use for
      which task. In many cases - as can be seen above - Drupal core provides
      generic handlers that can be used as is. In other cases or when more
      advanced functionality is required, custom handlers can be used instead.

    <!-- TODO: Explain access handlers, permission providers -->

    * Route providers:

      ```php
       *     "route_provider" = {
       *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
       *     },
      ```

      Instead of declaring routes belonging to entities in a `*.routing.yml`
      file like other routes, they can be provided by a handler, as well. This
      has the benefit of being able to re-use the same route provider for 
      multiple entity types, as is proven by the usage of the generic route
      provider provided by core.

    * Links:

      ```php
       *   links = {
       *     "canonical" = "/event/{event}",
       *   },
      ```

      Entity links denote at which paths on the website we can see an entity (or
      multiple entities) of the given type. They are used by the default route
      provider to set the path of the generated route. The usage of `canonical`
      (instead of `view`, for example) stems from the specification of link
      relations in the web by the IANA.

      See [Wikipedia: Link relation][wikipedia-link-relation] and
      [IANA: Link relations][iana-link-relations] for more information.

    <!-- TODO: Explain admin permission -->

  </details>

* Rebuild caches

  Run `drush cache-rebuild`

* Verify the route has been generated

  Visit `/event/2`

  Note that an empty page is shown. However, no field values are shown.

#### 2.3. Configure fields for display

Which fields to display when rendering the entity, as well as how to display
them, can be configured as part of the field definitions. Fields are not
displayed unless explicitly configured to.

* Add the following to the `$fields['date']` section of the
  `baseFieldDefinitions()` method of the `Event` class before the semicolon:

  ```php
  ->setDisplayOptions('view', [
    'label' => 'inline',
    'settings' => [
      'format_type' => 'html_date',
    ],
    'weight' => 0,
  ])
  ```

  <details><summary>Click here for more information on the above</summary>

    * Display mode:

      ```php
      ->setDisplayOptions('view'
      ```

      Display options can be set for two different display _modes_: `view` and
      `form`. Form display options will be set below.

    * Label display:

      ```php
      'label' => 'inline',
      ```

      The field label can be configured to be displayed above the field value (the
      default), inline in front of the field value or hidden altogether. The
      respective values of the `label` setting are `above`, `inline` and `hidden`.

    * Formatter settings:

      ```php
      'settings' => [
        'format_type' => 'html_date',
      ],
      ```

      Each field is displayed using a _formatter_. The field type declares a
      default formatter which is used unless a different formatter is chosen by
      specifying a `type` key in the display options. Some formatters have
      settings which can be configured through the `settings` key in the display
      options. There is no list of IDs of available field types, but
      [Drupal API: List of classes annotated with FieldFormatter][api-field-formatters]
      lists all field formatter classes (for all field types) in core. The ID of a
      given field formatter can be found in its class documentation or by
      inspecting the `@FieldFormatter` annotation which also lists the field types
      that the formatter can be used for. Given a formatter class the available
      settings can be found by inspecting the keys returned by the class'
      `defaultSettings()` method.

    * Weight:

      ```php
      'weight' => 0,
      ```

      Weights allow the order of fields in the rendered output to be different
      than their declaration order in the `baseFieldDefinitions()` method. Fields
      with heigher weights "sink" to the bottom and are displayed after fields
      with lower weights.

    Altogether, setting the view display options is comparable to using the
    _Manage display_ table provided by _Field UI_ module, which also allows
    configuring the label display, formatter, formatter settings and weight for
    each field.

  </details>

* Add the following to the `$fields['description']` section of the
  `baseFieldDefinitions()` method of `src/Entity/Event.php` before the
  semicolon:

  ```php
  ->setDisplayOptions('view', [
    'label' => 'hidden',
    'weight' => 10,
  ])
  ```


  <details><summary>Click here to see the entire <code>Event.php</code> file at this point</summary>
    
    ```php
    <?php
    
    namespace Drupal\event\Entity;
    
    use Drupal\Core\Datetime\DrupalDateTime;
    use Drupal\Core\Entity\ContentEntityBase;
    use Drupal\Core\Entity\EntityPublishedInterface;
    use Drupal\Core\Entity\EntityPublishedTrait;
    use Drupal\Core\Entity\EntityTypeInterface;
    use Drupal\Core\Field\BaseFieldDefinition;
    use Drupal\user\EntityOwnerInterface;
    use Drupal\user\EntityOwnerTrait;
    
    /**
    * @ContentEntityType(
    *   id = "event",
    *   label = @Translation("Event"),
    *   base_table = "event",
    *   entity_keys = {
    *     "id" = "id",
    *     "uuid" = "uuid",
    *     "label" = "title",
    *     "owner" = "author",
    *     "published" = "published",
    *   },
    *   handlers = {
    *     "access" = "Drupal\entity\EntityAccessControlHandler",
    *     "permission_provider" = "Drupal\entity\EntityPermissionProvider",
    *     "route_provider" = {
    *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
    *     },
    *   },
    *   links = {
    *     "canonical" = "/event/{event}"
    *   },
    *   admin_permission = "administer event"
    * )
    */
    class Event extends ContentEntityBase implements EntityOwnerInterface, EntityPublishedInterface {
    
    use EntityOwnerTrait, EntityPublishedTrait;
    
    public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
      // Get the field definitions for 'id' and 'uuid' from the parent.
      $fields = parent::baseFieldDefinitions($entity_type);
    
      $fields['title'] = BaseFieldDefinition::create('string')
        ->setLabel(t('Title'))
        ->setRequired(TRUE);
    
      $fields['date'] = BaseFieldDefinition::create('datetime')
        ->setLabel(t('Date'))
        ->setRequired(TRUE)
        ->setDisplayOptions('view', [
          'label' => 'inline',
          'settings' => [
            'format_type' => 'html_date',
          ],
          'weight' => 0,
        ]);
    
      $fields['description'] = BaseFieldDefinition::create('text_long')
        ->setLabel(t('Description'))
        ->setDisplayOptions('view', [
          'label' => 'hidden',
          'weight' => 10,
        ]);
    
      // Get the field definitions for 'owner' and 'published' from the traits.
      $fields += static::ownerBaseFieldDefinitions($entity_type);
      $fields += static::publishedBaseFieldDefinitions($entity_type);
    
      return $fields;
    }
    
    /**
     * @return string
     */
    public function getTitle() {
      return $this->get('title')->value;
    }
    
    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title) {
      return $this->set('title', $title);
    }
    
    /**
     * @return \Drupal\Core\Datetime\DrupalDateTime
     */
    public function getDate() {
      return $this->get('date')->date;
    }
    
    /**
     * @param \Drupal\Core\Datetime\DrupalDateTime $date
     *
     * @return $this
     */
    public function setDate(DrupalDateTime $date) {
      return $this->set('date', $date->format(DATETIME_DATETIME_STORAGE_FORMAT));
    }
    
    /**
     * @return \Drupal\filter\Render\FilteredMarkup
     */
    public function getDescription() {
      return $this->get('description')->processed;
    }
    
    /**
     * @param string $description
     * @param string $format
     *
     * @return $this
     */
    public function setDescription($description, $format) {
      return $this->set('description', [
        'value' => $description,
        'format' => $format,
      ]);
    }
    
    }
    ```

  </details>

* Rebuild caches

  Run `drush cache-rebuild`

* Verify that the fields are shown

  As the event title is automatically used as a page title we do not explicitly
  enable the title field for display.

  Note that the output of the entity can be further customized by adding a theme
  function. This is omitted for brevity.

### 3. Manipulating entities through forms

#### 3.1. Add the routes

* Add the following to the `handlers` section of the annotation in
  `src/Entity/Event.php`:

  ```php
   *     "form" = {
   *       "add" = "Drupal\Core\Entity\ContentEntityForm",
   *       "edit" = "Drupal\Core\Entity\ContentEntityForm",
   *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
   *     },
  ```

  <!-- TODO: Explain this -->

* Add the following to the `links` section of the annotation in 
  `src/Entity/Event.php`:

  ```php
   *     "add-form" = "/admin/content/events/add",
   *     "edit-form" = "/admin/content/events/manage/{event}",
   *     "delete-form" = "/admin/content/events/manage/{event}/delete",
  ```

* Rebuild caches

  Run `drush cache-rebuild`

* Visit `/admin/content/events/add`

  Note that a route exists and a _Save_ button is shown, but no
  actual form fields are shown.

* Visit `/admin/content/events/manage/2`

  Note that a route exists and _Save_ and _Delete_ buttons are shown, but no
  actual form fields are shown.

#### 3.2. Configure fields for display


* Add the following to the `$fields['title']` section of the
  `baseFieldDefinitions()` method of `src/Entity/Event.php` before the
  semicolon:

  ```php
  ->setDisplayOptions('form', ['weight' => 0])
  ```

* Add the following to the `$fields['date']` section of the
  `baseFieldDefinitions()` method of `src/Entity/Event.php` before the
  semicolon:

  ```php
  ->setDisplayOptions('form', ['weight' => 10])
  ```

* Add the following to the `$fields['description']` section of the
  `baseFieldDefinitions()` method of `src/Entity/Event.php` before the
  semicolon:

  ```php
  ->setDisplayOptions('form', ['weight' => 20])
  ```

* Add the following to the `$fields['published']` section of the
  `baseFieldDefinitions()` method of `src/Entity/Event.php` before the
  semicolon:

  ```php
  ->setDisplayOptions('form', [
    'settings' => [
      'display_label' => TRUE,
    ],
    'weight' => 30,
  ])
  ```

* Rebuild caches

  Run `drush cache-rebuild`

* Add an event in the user interface

  Visit `/admin/content/events/add`

  Note that the form fields are displayed.

  Enter a title, date and description and press _Save_.

  Verify that the event was saved by checking that a new row was created in the
  `{event}` table.

  Note that no message is displayed and no redirect is performed.

#### 3.3. Add a specialized form

* Add a `src/Form` directory

* Add a `src/Form/EventForm.php` file with the
  following:

  ```php
  <?php

  namespace Drupal\event\Form;

  use Drupal\Core\Entity\ContentEntityForm;
  use Drupal\Core\Form\FormStateInterface;

  class EventForm extends ContentEntityForm {

    public function save(array $form, FormStateInterface $form_state) {
      parent::save($form, $form_state);

      $entity = $this->getEntity();
      $entity_type = $entity->getEntityType();

      $arguments = [
        '@entity_type' => $entity_type->getLowercaseLabel(),
        '%entity' => $entity->label(),
        'link' => $entity->toLink($this->t('View'), 'canonical')->toString(),
      ];

      $this->logger($entity->getEntityTypeId())->notice('The @entity_type %entity has been saved.', $arguments);
      drupal_set_message($this->t('The @entity_type %entity has been saved.', $arguments));

      $form_state->setRedirectUrl($entity->toUrl('canonical'));
    }

  }
  ```

* Replace the value of the `add` and `edit` annotation keys in the form handlers
  section of the annotation in `src/Entity/Event.php` with
  `"Drupal\event\Form\EventForm"`.

* Rebuild caches

  Run `drush cache-rebuild`

* Edit an event in the user interface

  Visit `/admin/content/events/manage/3`

  Note that a route exists and form fields are displayed including proper
  default values.

  Modify the title, date and description and published status and press _Save_.

  Note that again no message is displayed and no redirect is performed.

  Verify that the values in the respective row in the `{event}` table have been
  updated. Also note that the default values of the form fields are correct on
  the reloaded page.

* Delete an event in the user interface

  Visit `/admin/content/events/manage/3/delete`

  Note that a route exists and a confirmation form is shown.

  Press _Delete_.

  Note that a message is shown and you are redirected to the front page.

  Verify that the respective row in the `{event}` table has been deleted.

### 4. Listing entities

#### 4.1. Add a route

* Add the following to the `handlers` section of the annotation in
  `src/Entity/Event.php`:

  ```php
   *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
  ```

* Add the following to the `links` section of the annotation in
  `src/Entity/Event.php`:

  ```php
   *     "collection" = "/admin/content/events",
  ```

* Rebuild caches

  Run `drush cache-rebuild`

* Visit `/admin/content/events`

  Note that a route is provided and a list of entities is provided with
  _Edit_ and _Delete_ operation links for each entity.

  By not showing at least the title of each event the list is not actually
  usable so we need to provide a specialized list builder.

#### 4.2. Add a specialized list builder

* Create a `src/Controller` directory

* Add a `src/Controller/EventListBuilder.php` file with the following:

  ```php
  <?php

  namespace Drupal\event\Controller;

  use Drupal\Core\Entity\EntityInterface;
  use Drupal\Core\Entity\EntityListBuilder;

  class EventListBuilder extends EntityListBuilder {

    public function buildHeader() {
      $header = [];
      $header['title'] = $this->t('Title');
      $header['date'] = $this->t('Date');
      $header['published'] = $this->t('Published');
      return $header + parent::buildHeader();
    }

    public function buildRow(EntityInterface $event) {
      /** @var \Drupal\event\Entity\EventInterface $event */
      $row = [];
      $row['title'] = $event->toLink();
      $row['date'] = $event->getDate()->format('m/d/y h:i:s a');
      $row['published'] = $event->isPublished() ? $this->t('Yes') : $this->t('No');
      return $row + parent::buildRow($event);
    }

  }
  ```

  Parts of this code block are explained below:

  * Separate methods:

    ```php
    public function buildHeader() {
    ```

    List builders build the table header and the table rows in separate methods.

  * Translation:

    ```php
    $this->t('Title')
    ```

    The base `EntityListBuilder` class, like many other base classes in Drupal,
    provides a `t()` function that can be used to translate strings.

  * Array merging:

    ```php
    $header + parent::buildHeader()
    ```

    Arrays with string keys can be merged in PHP by "adding" them. Because the
    base class provides the operations column we put our own part of the header
    first and add the part from the parent last.

  * Inline type hint:

    ```php
    /** @var \Drupal\event\Entity\EventInterface $event */
    ```

    Because `EntityListBuilderInterface`, the interface for list builders,
    dictates that we type hint the `$event` variable with `EntityInterface`
    instead of our more specific `EventInterface`, IDEs are not aware that the
    `$event` variable has the methods `getTitle()` and `getDate()` in this case.
    To inform IDEs that these methods are in fact available an inline type hint
    can be added to the `$event` variable.

  * Entity links:

    ```php
    $event->toLink()
    ```

    Entities have a `toLink()` method to generate links with a specified link
    text to a specified link relation of the entity. By default a link with the
    entity label as link text to the `canonical` link relation is generated
    which is precisely what we want here.

  * Date formatting:

    ```php
    $row['date'] = $event->getDate()->format('m/d/y h:i:s a');
    ```

    Because the `getDate()` method returns a date object we can attain the
    formatted date by using its `format()` method. If the same date format is to
    be used in multiple places on the site, hardcoding it here can lead to
    duplication or worse, inconsistent user interfaces. To prevent this, Drupal
    associates PHP date formats with machine-readable names to form a _Date
    format_ configuration entity. (More on configuration entities in general
    below.) That way the name, such as `short`, `medium` or `long` can be
    used without having to remember the associated PHP date format. This also
    allows changing the PHP date format later without having to update each
    place it is used. To utilize Drupal's date format system the
    `date.formatter` service can be used. Unfortunately, Drupal's date formatter
    cannot handle date objects but works with timestamps instead. It is not used
    above because it would be more verbose and introduce new concepts, such as
    services and dependency injection, even though it would be the preferred
    implementation. For reference, the respective parts of
    `EventListBuilder.php` would then be:

    ```php
    use Drupal\Core\Datetime\DateFormatterInterface;
    ...
    use Drupal\Core\Entity\EntityStorageInterface;
    use Drupal\Core\Entity\EntityTypeInterface;
    use Symfony\Component\DependencyInjection\ContainerInterface;

    class EventListBuilder extends EntityListBuilder {

      /**
       * @var \Drupal\Core\Datetime\DateFormatterInterface
       */
      protected $dateFormatter;

      public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, DateFormatterInterface $date_formatter) {
        parent::__construct($entity_type, $storage);
        $this->dateFormatter = $date_formatter;
      }

      public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
        return new static(
          $entity_type,
          $container->get('entity.manager')->getStorage($entity_type->id()),
          $container->get('date.formatter')
        );
      }

      ...

      public function buildRow(EntityInterface $event) {
        /** @var \Drupal\event\Entity\EventInterface $event */
        ...
        $row['date'] = $this->dateFormatter->format($event->getDate()->getTimestamp(), 'medium');
        ...
      }

    }
    ```

* Replace the value of the `list_builder` annotation key in the `handlers`
  section of the annotation in `src/Entity/Event.php` with
  `"Drupal\event\Controller\EventListBuilder"`.

* Rebuild caches

  Run `drush cache-rebuild`

* Visit `/admin/content/events`

  Note that the entity list now shows the event title and date.

* Delete an event again

  Visit `/admin/content/events/manage/2/delete` and press _Delete_.

  Note that this time you are redirected to the administrative event listing.
  The redirect to the front page that happened above is only a fallback in case
  no `collection` route exists.

#### 4.3. Add an administrative view

While a specialized entity list builder has the benefit of being re-usable one
can also take advantage of Drupal's _Views_ module to create an administrative
listing of events.

* Add the following to the `handlers` section of the annotation in
  `src/Entity/Event.php`:

  ```php
   *     "views_data" = "Drupal\views\EntityViewsData",
  ```

  Note that the views data that is provided by the default views data handler is
  partially incomplete so - in particular when dealing with date or entity
  reference fields - using Views for entity listings should be evaluated
  carefully.

* Rebuild caches

  Run `drush cache-rebuild`

* Add an _Event_ view to replace the list builder

  * Add a _Page_ views display with the path `admin/content/events`

    This will make Views replace the previously existing collection route.

    Note that the path is entered without a leading slash in Views.

  * Use the _Table_ style for the display

    Check the _Show the empty text in the table_ checkbox.

  * Add an empty text field to the _No results behavior_ area

  * Add _Date_ and _Published_ fields

  * Add _Link to edit Event_ and _Link to delete Event_ fields and check the
    _Exclude from display_ checkbox

  * Add a _Dropbutton_ field for the operations and enable the edit and delete
    links

  Views provides a number of features that increase the usability of
  administrative listings when compared to the stock entity list builder. These
  include:

  * A `destination`query parameter in the operation links

    This returns you back to the event listing after editing or deleting an
    event.

  * Exposed filters

  * Using formatters for fields

    This allows using Drupal's date formatting system for the date field (as
    discussed above), for example, and using check (✔) and cross (✖) marks for
    the published field.

  * A click-sortable table header

  * An Ajax-enabled pager

  * A "sticky" table header

### 5. Adding administrative links

To provide a usable and integrated administration experience the different pages
need to be connected and enriched with Drupal's standard administrative links.

#### 5.1. Add a menu link for the event listing

For the event listing to show up in the toolbar menu under _Content_, we need
to provide a menu link for it.

* Add an `event.links.menu.yml` file with the following:

  ```yaml
  entity.event.collection:
    title: 'Events'
    route_name: entity.event.collection
    parent: system.admin_content
  ```

* Rebuild caches

  Run `drush cache-rebuild`

* Verify that there is an _Events_ link in the toolbar menu.

  Note that there is no _Event_ local task on `/admin/content`.

#### 5.2. Add a local task for the event listing

* Add an `event.links.task.yml` file with the following:

  ```yaml
  entity.event.collection:
    title: 'Events'
    route_name: entity.event.collection
    base_route: system.admin_content
  ```

* Rebuild caches

  Run `drush cache-rebuild`

* Verify that the _Events_ local task appears on `/admin/content`

#### 5.3. Add an action link for the add form

* Add an `event.links.action.yml` file with the following:

  ```ỳaml
  entity.event.add_form:
    title: 'Add event'
    route_name: entity.event.add_form
    appears_on: [entity.event.collection]
  ```

* Rebuild caches

  Run `drush cache-rebuild`

* Verify that the _Add event_ action link appears on `/admin/content/events`

* Add an event

#### 5.4. Add local tasks for the edit and delete forms

* Add the following to `event.links.task.yml`:

  ```yaml
  entity.event.canonical:
    title: 'View'
    route_name: entity.event.canonical
    base_route: entity.event.canonical
  entity.event.edit_form:
    title: 'Edit'
    route_name: entity.event.edit_form
    base_route: entity.event.canonical
  entity.event.delete_form:
    title: 'Delete'
    route_name: entity.event.delete_form
    base_route: entity.event.canonical
  ```

* Rebuild caches

  Run `drush cache-rebuild`

* Visit `/events/4`

  Verify that _View_, _Edit_ and _Delete_ local tasks are shown.

<!-- TODO: Add contextual links -->

### 6. Adding permission-based access-control

#### 6.1. Add permissions

Add the following to `event.permissions.yml`:

```yaml
create events:
  title: 'Create events'
delete events:
  title: 'Delete events'
edit events:
  title: 'Edit events'
view events:
  title: 'View events'
```

#### 6.2. Add an access control handler

* Add a `src/Access` directory

* Add a `src/Access/EventAccessControlHandler.php` with the following:

  ```php
  <?php

  namespace Drupal\event\Access;

  use Drupal\Core\Access\AccessResult;
  use Drupal\Core\Entity\EntityAccessControlHandler;
  use Drupal\Core\Entity\EntityInterface;
  use Drupal\Core\Session\AccountInterface;

  class EventAccessControlHandler extends EntityAccessControlHandler {

    protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
      $access_result = AccessResult::allowedIfHasPermission($account, 'create events');
      return $access_result->orIf(parent::checkCreateAccess($account, $context, $entity_bundle));
    }

    protected function checkAccess(EntityInterface $event, $operation, AccountInterface $account) {
      /** @var \Drupal\event\Entity\EventInterface $event */
      // The parent class grants access based on the administrative permission.
      $access_result = parent::checkAccess($event, $operation, $account);
      switch ($operation) {
        case "view":
          // Only allow administrators to view unpublished events.
          if ($event->isPublished()) {
            $permission = 'view events';
          }
          else {
            $permission = 'administer events';
          }
          $access_result->addCacheableDependency($event);
          break;

        case "update":
          $permission = 'edit events';
          break;

        case "delete":
          $permission = 'delete events';
          break;

      }
      return $access_result->orIf(AccessResult::allowedIfHasPermission($account, $permission));
    }

  }
  ```

<!-- TODO: Explain cacheability metadata -->

* Add the following to the `handlers` section of the annotation in
  `src/Entity/Event.php`:

  ```php
   *     "access" = "Drupal\event\Access\EventAccessControlHandler",
  ```

* Rebuild caches

  Run `drush cache-rebuild`

* Test permissions

  * Add an _Event creator_ role and assign the `create events` permission

    Add an _Event creator_ user and assign the respective role.

    Login as the _Event creator_ user and verify that access is granted to
     `/admin/content/events/add` but not granted to `/admin/content/events`,
     `/admin/content/events/4` and `/admin/content/events/4/delete`

  * Add an _Event editor_ role and assign the `edit events` permission

    Add an _Event editor_ user and assign the respective role.

    Login as the _Event editor_ user and verify that access is granted to
     `/admin/content/events/events/4` but not granted to
     `/admin/content/events`, `/admin/content/events/add` and
     `/admin/content/events/4/delete`

  * Add an _Event deletor_ role and assign the `delete events` permission

    Add an _Event deletor_ user and assign the respective role.

    Login as the _Event deletor_ user and verify that access is granted to
     `/admin/content/events/events/4/delete` but not granted to
     `/admin/content/events`, `/admin/content/events/add` and
     `/admin/content/events/4`

### 7. Adding additional fields

Now that our basic implementation of _Events_ is functional and usable from the
user interface, we can add some more fields. This is both to recap the above
chapters and to show some additional features that are often used for content
entities.

#### 7.1. Add the field definitions

* Add the following to `src/Entity/Event.php`:

  ```php
  public static function getCurrentUser() {
    return \Drupal::currentUser()->id();
  }
  ```

* Add the following to the `use` statements at the top of
  `src/Entity/Event.php`:

  ```php
  use Drupal\Core\Field\FieldStorageDefinitionInterface;
  ```

* Add the following to the `baseFieldDefinitions()` method in
  `src/Entity/Event.php` above `return $fields;`:

  ```php
  $fields['path'] = BaseFieldDefinition::create('path')
    ->setLabel(t('Path'))
    ->setDisplayOptions('form', ['weight' => 5]);

  $fields['attendees'] = BaseFieldDefinition::create('entity_reference')
    ->setLabel(t('Attendees'))
    ->setSetting('target_type', 'user')
    ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
    ->setDisplayOptions('form', ['weight' => 25])
    ->setDisplayOptions('view', ['weight' => 20]);

  $fields['owner'] = BaseFieldDefinition::create('entity_reference')
    ->setLabel(t('Owner'))
    ->setSetting('target_type', 'user')
    ->setDefaultValueCallback(static::class . '::getCurrentUser');

  $fields['changed'] = BaseFieldDefinition::create('changed')
    ->setLabel(t('Changed'));
  ```

  Parts of this code block are explained below:

  * Multiple-value fields:

    ```php
    ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
    ```

    Fields can be allowed to have multiple values by changing the _cardinality_
    of the field definition. If an unlimited amount of field values should be
    possible, the constant
    `FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED` should be used as
    the cardinality value.

  * Default value callbacks:

    ```php
    ->setDefaultValueCallback(static::class . '::getCurrentUser')
    ```

    Instead of setting a static default value, a callback can be specified that
    will calculate the default value of a field at runtime.

  Note that the `owner` and `changed` fields are not exposed on the form.

#### 7.2. Install the fields

* Run `drush entity-updates`

  * Note that the `{event__attendees}` table was created and `owner` and
    `changed` fields have been created in the `{event}` table.

    There is no `path` column because path aliases are stored separately in the
    `{url_alias}` table.

#### 7.3. Add additional field methods

* Add the following to the use statements at the top of `src/Entity/Event.php`:

  ```php
  use Drupal\Core\Entity\EntityChangedTrait;
  use Drupal\user\UserInterface;
  ```

* Add the following to the `Event` class in `src/Entity/Event.php`:

  ```php
  use EntityChangedTrait;

  public function getAttendees() {
    return $this->get('attendees')->referencedEntities();
  }

  public function addAttendee(UserInterface $attendee) {
    $field_items = $this->get('attendees');

    $exists = FALSE;
    foreach ($field_items as $field_item) {
      if ($field_item->target_id === $attendee->id()) {
        $exists = TRUE;
      }
    }

    if (!$exists) {
      $field_items->appendItem($attendee);
    }

    return $this;
  }

  public function removeAttendee(UserInterface $attendee) {
    $field_items = $this->get('attendees');
    foreach ($field_items as $delta => $field_item) {
      if ($field_item->target_id === $attendee->id()) {
        $field_items->set($delta, NULL);
      }
    }
    $field_items->filterEmptyItems();
    return $this;
  }

  public function getOwner() {
    return $this->get('owner')->entity;
  }

  public function setOwner(UserInterface $account) {
    return $this->set('owner', $account->id());
  }

  public function getOwnerId() {
    return $this->get('owner')->target_id;
  }

  public function setOwnerId($uid) {
    return $this->set('owner', $uid);
  }
  ```

  Parts of this code block are explained below:

  * Traits:

    ```php
    use EntityChangedTrait;
    ```

  * Field item lists:

    ```php
    $this->get('attendees')->referencedEntities();
    ```

  * Object traversal:

    ```php
    foreach ($field_items as $field_item)
    ```

* Add the following to the use statements at the top of
  `src/Entity/EventInterface.php`:

  ```php
  use Drupal\Core\Entity\EntityChangedInterface;
  use Drupal\user\EntityOwnerInterface;
  use Drupal\user\UserInterface;
  ```

* Add `EntityOwnerInterface` and `EntityChangedInterface` the `extends`
  section in `src/Entity/EventInterface.php`:

  Such entity interfaces in general allow entity-related features to be
  implemented generically so that any entity type can opt-in to using them. In
  particular these interfaces are used for the following:

  * Changed time tracking:

    ```php
    EntityChangedInterface
    ```

    When an entity that supports changed time tracking is being saved, Drupal
    checks whether the entity has been updated by someone else in the meantime
    so that the changes do not get overwritten.

  * Entity ownership:

    ```php
    EntityOwnerInterface
    ```

    When creating an entity with owner support from an entity reference widget,
    the owner of the host entity is taken over.

* Add the following to the `EventInterface` interface in
  `src/Entity/EventInterface.php`:

  ```php
  /**
   * @return \Drupal\user\UserInterface[]
   */
  public function getAttendees();

  /**
   * @param \Drupal\user\UserInterface $attendee
   *
   * @return $this
   */
  public function addAttendee(UserInterface $attendee);

  /**
   * @param \Drupal\user\UserInterface $attendee
   *
   * @return $this
   */
  public function removeAttendee(UserInterface $attendee);
  ```

* Try out the new methods

  Run the following PHP code:

  ```
  use Drupal\event\Entity\Event;
  use Drupal\user\Entity\User;

  $event = Event::load(4);
  $user = User::load(1);

  $event->getAttendees();

  $event
    ->addAttendee($user)
    ->getAttendees();

  $event
    ->removeAttendee($user)
    ->getAttendees();
  ```

  Note that an empty array is returned initially, then an array with one user,
  then an empty array again.

* Try out the new fields in the user interface

  Visit `/admin/content/events/manage/4` and add a path and an attendee.

  Note that you are redirected to the path and that the attendee is correctly
  displayed.

The event entities are feature complete for our purposes as of now.

### 8. Storing dynamic data in configuration

Apart from content entities there is a second type of entities in Drupal, the
configuration entities. These have a machine-readable string ID and can be
deployed between different environments along with the rest of the site
configuration.

#### 8.1. Create an entity class

While there are some distinctions, creating a configuration entity type is very
similar to creating a content entity type.

* Create a `src/Entity/EventType.php` with the following:

  ```php
  <?php

  namespace Drupal\event\Entity;

  use Drupal\Core\Config\Entity\ConfigEntityBase;

  /**
   * @ConfigEntityType(
   *   id = "event_type",
   *   label = @Translation("Event type"),
   *   label_singular = @Translation("event type"),
   *   label_plural = @Translation("event types"),
   *   label_count = @PluralTranslation(
   *     singular = "@count event type",
   *     plural = "@count event types"
   *   ),
   *   config_prefix = "type",
   *   config_export = {
   *     "id",
   *     "label",
   *   },
   *   entity_keys = {
   *     "id" = "id",
   *     "label" = "label",
   *   },
   * )
   */
  class EventType extends ConfigEntityBase {

    protected $id;

    protected $label;

  }
  ```

  Parts of this code block are explained below:

  * Configuration prefix:

    ```php
    config_prefix = "type"
    ```

    To clearly identify the source of all configuration, the names of the
    respective configuration files of configuration entities are automatically
    prefixed with the module name (`event` in this case) and a period (`.`) as a
    separator. To distinguish different configuration entity types from the same
    module, each configuration entity type specifies a _configuration prefix_
    which is the second part of the configuration file name prefix followed by
    an additional period. The full name of a configuration entity's
    configuration file is, thus, `"$module_name.$config_prefix.$entity_id"`.

  * Export properties:

    ```php
    config_export = {
      "id",
      "label",
    }
    ```

    ```php
    protected $id;

    protected $label;
    ```

    Configuration entities do not have a notion of (base) field definitions like
    content entities. Instead simple PHP properties can be declared in the
    entity class to hold the values of the entity. The names of those properties
    need to be specified as export properties in the entity annotation.

#### 8.2. Provide a configuration schema

To ensure that the structure of each configuration object is correct, a _schema_
is provided. When importing configuration from another environment, each
configuration object is validated against this schema.

* Add a `config/schema/event.schema.yml` with the following:

  ```yaml
  event.type.*:
    type: config_object
    mapping:
      id:
        type: string
        label: 'ID'
      label:
        type: label
        label: 'Label'
  ```

#### 8.3. Install the entity type

* Run `drush entity-updates`

  Note that there is no schema change

* Create and save an event type

  Run the following PHP code:

  ```php
  use Drupal\event\Entity\EventType;

  EventType::create([
    'id' => 'webinar',
    'label' => 'Webinar',
  ])->save();
  ```

  Note that there is a new row in the `{config}` table with the name
  `event.type.webinar`

* Load the event type by its ID

  Run the following PHP code:

  ```php
  use Drupal\event\Entity\EventType;

  $event_type = EventType::load('webinar');
  $event_type->label();
  ```

  Note that the proper label is returned.

* Update the label of the event type

  Run the following PHP code:

  ```php
  use Drupal\event\Entity\EventType;

  $event_type = EventType::load('webinar');
  $event_type
    ->set('label', 'Online webinar')
    ->save();
  ```

* Delete the event type

  Run the following PHP code:

  ```php
  use Drupal\event\Entity\EventType;

  $event_type = EventType::load('webinar')
  $event_type->delete();
  ```

  Note that the row in the `{config}` table is gone.

### 9. Providing a user interface for configuration entities

#### 9.1. Add a list of event types

* Add the following to `event.permissions.yml`:

  ```yaml
  administer event types:
    title: 'Administer event types'
  ```

* Add a `src/Controller/EventTypeListBuilder.php` file with the following:

  ```php
  <?php

  namespace Drupal\event\Controller;

  use Drupal\Core\Entity\EntityInterface;
  use Drupal\Core\Entity\EntityListBuilder;

  class EventTypeListBuilder extends EntityListBuilder {

    public function buildHeader() {
      $header = [];
      $header['label'] = $this->t('Label');
      return $header + parent::buildHeader();
    }

    public function buildRow(EntityInterface $event) {
      $row = [];
      $row['label'] = $event->label();
      return $row + parent::buildRow($event);
    }

  }
  ```

* Add the following to the annotation in `src/Entity/EventType.php`:

  ```php
   *   handlers = {
   *     "list_builder" = "Drupal\event\Controller\EventTypeListBuilder",
   *     "route_provider" = {
   *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
   *     },
   *   },
   *   links = {
   *     "collection" = "/admin/structure/event-types",
   *   },
   *   admin_permission = "administer event types",
  ```

* Add the following to `event.links.menu.yml`:

  ```yaml
  entity.event_type.collection:
    title: 'Event types'
    route_name: entity.event_type.collection
    parent: system.admin_structure
  ```

* Rebuild caches

  Run `drush cache-rebuild`

* Verify that there is a _Event types_ menu link in the toolbar menu

* Visit `/admin/structure/event-types`

  Note that a listing of event types is shown.

#### 9.2. Add forms for event types

In contrast to content entities, configuration entities do not have the ability
to use widgets for their forms, so we need to provide the respective form
elements ourselves.

* Add a `src/Form/EventTypeForm.php` file with the
  following:

  ```php
  <?php

  namespace Drupal\event\Form;

  use Drupal\Core\Entity\EntityForm;
  use Drupal\Core\Entity\EntityTypeInterface;
  use Drupal\Core\Form\FormStateInterface;

  class EventTypeForm extends EntityForm {

    public function form(array $form, FormStateInterface $form_state) {
      $form = parent::form($form, $form_state);

      $event_type = $this->getEntity();

      $form['label'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Label'),
        '#default_value' => $event_type->label(),
        '#required' => TRUE,
      ];

      $form['id'] = [
        '#type' => 'machine_name',
        '#title' => $this->t('ID'),
        '#maxlength' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
        '#default_value' => $event_type->id(),
        '#machine_name' => [
          'exists' => [$event_type->getEntityType()->getClass(), 'load'],
        ],
        '#disabled' => !$event_type->isNew(),
      ];

      return $form;
    }

    public function save(array $form, FormStateInterface $form_state) {
      parent::save($form, $form_state);

      $entity = $this->getEntity();
      $entity_type = $entity->getEntityType();

      $arguments = [
        '@entity_type' => $entity_type->getLowercaseLabel(),
        '%entity' => $entity->label(),
        'link' => $entity->toLink($this->t('Edit'), 'edit-form')->toString(),
      ];

      $this->logger($entity->getEntityTypeId())->notice('The @entity_type %entity has been saved.', $arguments);
      drupal_set_message($this->t('The @entity_type %entity has been saved.', $arguments));

      $form_state->setRedirectUrl($entity->toUrl('collection'));
    }

  }
  ```

<!-- TODO: Explain callables -->

* Add the following to the `handlers` section of the annotation in
  `src/Entity/EventType.php`:

  ```php
   *     "form" = {
   *       "add" = "Drupal\event\Form\EventTypeForm",
   *       "edit" = "Drupal\event\Form\EventTypeForm",
   *       "delete" = "Drupal\Core\Entity\EntityDeleteForm",
   *     },
  ```

* Add the following to the `links` section of the annotation in
  `src/Entity/EventType.php`:

  ```php
   *     "add-form" = "/admin/structure/event-types/add",
   *     "edit-form" = "/admin/structure/event-types/manage/{event_type}",
   *     "delete-form" = "/admin/structure/event-types/manage/{event_type}/delete",
  ```

* Add the following to `event.links.action.yml`:

  ```ỳaml
  entity.event_type.collection:
    title: 'Add event type'
    route_name: entity.event_type.add_form
    appears_on: [entity.event_type.collection]
  ```

* Add the following to `event.links.task.yml`:

  ```yaml
  entity.event_type.edit_form:
    title: 'Edit'
    route_name: entity.event_type.edit_form
    base_route: entity.event_type.edit_form
  entity.event_type.delete_form:
    title: 'Delete'
    route_name: entity.event_type.delete_form
    base_route: entity.event_type.edit_form
  ```

* Rebuild caches

  Run `drush cache-rebuild`

* Verify that a local action appears to add an event type

  Add an event type.

  Edit an event type.

  Verify that _Edit_ and _Delete_ local tasks are shown.

  Delete an event type.

### 10. Categorizing different entities of the same entity type

Drupal provides a mechanism to distinguish content entities of the same type
and attach different behavior to the entities based on this distinction. In the
case of event entities, for example, it allows events to have different behavior
based on the type of event they are. The nomenclature is that entity types can
have _bundles_ where each entity of that entity type belongs to a certain
bundle.

Generally a configuration entity type is used to provide the bundles for a
content entity type. In this case each _Event type_ entity will be a bundle for
the _Event_ entity type.

#### 10.1. Add the bundle field

* Delete the existing event(s)

  Visit `/admin/content/event/manage/4/delete` and press _Delete_.

  Adding a bundle field cannot be done when there are existing entities.

* Add the following to the `entity_keys` section of the annotation in
  `src/Entity/Event.php`:

  ```php
   *     "bundle" = "type",
  ```

* Add the following to the annotation in `src/Entity/Event.php`:

  ```php
   *   bundle_entity_type = "event_type",
  ```

* Replace the `add-form` link in the annotation in `src/Entity/Event.php` with:

  ```php
  "/admin/content/events/add/{event_type}"
  ```

* Add the following to the `links` section of the annotation in
  `src/Entity/Event.php` with:

  ```php
   *     "add-page" = "/admin/content/events/add",
  ```

* Replace the `entity.event.add_form` section in `event.links.action.yml` with the following:
  ```yaml
  entity.event.add_page:
    title: 'Add event'
    route_name: entity.event.add_page
    appears_on: [entity.event.collection]
  ```

* Add the following to the annotation in `src/Entity/EventType.php`:

  ```php
   *   bundle_of = "event",
  ```

Like for the `id` and `uuid` fields, the field definition for the `type` field
is automatically generated by `ContentEntityBase::baseFieldDefinitions()`.

#### 10.2. Install the bundle field

* Run `drush entity-updates`

  * Note that the `type` column has been added to the `{event}` table.

* Visit `/admin/structure/event-types/add` and add a _Conference_ event type

* Visit `/admin/content/events/add`

  Note that the event types are displayed as options.

* Create an event

### 11. Configuring bundles in the user interface

#### 11.1. Enable Field UI for events

* Add the following to the annotation in `src/Entity/Event.php`:

  ```php?start_inline
   *   field_ui_base_route = "entity.event_type.edit_form",
  ```

* Rebuild caches

  Run `drush cache-rebuild`

* Visit `/admin/structure/event-types`

  Note that there is a _Manage fields_, _Manage form display_ and _Manage
  display_ operation for each event type.

#### 11.2. Add dynamic fields to events

The ability to have comments is managed as a field in Drupal, so we can use
Field UI to add a _Comments_ field to an event type.

* Add a comment type on `/admin/structure/comment/types/add`

  Select _Event_ as the target entity type

* Add a _Comments_ field to an event type

#### 11.3. Configure view modes

* Visit the _Manage display_ page

  Note that only the _Comments_ field appears

* Add the following to all field definitions in the `baseFieldDefinitions()`
  method of `src/Entity/Event.php` before the semicolon:

  ```php
  ->setDisplayConfigurable('view', TRUE)
  ```

* Rebuild caches

  Run `drush cache-rebuild`

* Verify that all fields now appear

* Upload user pictures for the existing users

* Use _Rendered entity_ for the _Attendees_ field on the _Manage display_ page

* Add a _Teaser_ view mode on `/admin/structure/display-modes/view/add/event`

* Make the _Teaser_ view mode configurable on the _Manage display_ page

* Rebuild caches

  Run `drush cache-rebuild`

* Configure the _Teaser_ view mode

* Add an _Event teasers_ view

  * Add a _Page_ views display with the path `events`

    Note that the path is entered without a leading slash in Views.

  * Use the _Unformatted list_ style for the display

    Display _Events_ in the _Teaser_ view mode

* Verify that the event teasers are displayed correctly

#### 11.4. Configure the form

* Visit the _Manage form display_ page

  Note that only the _Comments_ field appears

* Add the following to all field definitions in the `baseFieldDefinitions()`
  method of `src/Entity/Event.php` before the semicolon:

  ```php
  ->setDisplayConfigurable('form', TRUE)
  ```

* Note that all fields now appear

* Configure the form display

  * Use the _Select list_ widget for the _Date_ field

  * Use the _Check boxes/radio buttons_ widget for the _Attendees_ field

### 12. Translating content

Content entities can be made translatable in the storage by amending the entity
type annotation. However, this by itself does not make the content entity
translatable in the user interface. It only _allows_ site builders to make it
translatable in the user interface with the _Content Translation_ module.

#### 12.1. Install the Content Translation module

* Install the _Content Translation_ module on `/admin/modules`

* Add a second language on `/admin/config/regional/language`

* Visit `/admin/config/regional/content-language`

  Note that events cannot be selected for translation.

#### 12.2. Make events translatable

* Delete all existing events

* Delete the _Events_ and _Event teaser_ views

* Add the following to the annotation in `src/Entity/Event.php`:

  ```php?start_inline
   *   translatable = TRUE,
   *   data_table = "event_field_data",
  ```

<!-- TODO: Explain data table -->

* Add the following to the `entity_keys` part of the annotation in
  `src/Entity/Event.php`:

  ```php
   *     "langcode" = "langcode",
  ```

  Like for the `id`, `uuid` and `type` fields, the field definition for the
  `langcode` field is automatically generated by
  `ContentEntityBase::baseFieldDefinitions()`.

* Run `drush entity-updates`

  Note that the `{event_field_data}` table has been created and the `type`
  column has been added to the `{event}` table.

* Verify that _Events_ can be marked as translatable

  Note that only the _Comments_ field is translatable.

* Add the following to field definitions for the `title`, `description`,
  `published`, `path` and `changed` fields in the `baseFieldDefinitions()`
  method of `src/Entity/Event.php` before the semicolon:

  ```php
  ->setTranslatable(TRUE)
  ```

* Run `drush entity-updates`

* Mark all fields of all event types as translatable

* Add an event entity

  Note there is a _Translate_ local task

  Notice the following exception is thrown when visiting the _Translate_ local
  task:

  ```
  The "event" entity type did not specify a "default" form class.
  ````

* Add the following to the form handlers part of the annotation in
  `src/Entity/Event.php`:

  ```php
   *     "default" = "Drupal\event\Form\EventForm"
  ```

* Rebuild caches

  Run `drush cache-rebuild`

* Translate entity

  Note that non-translatable fields are still shown. Editing these will change
  the values in the source translation

  Verify that translation works

<!-- TODO: Add _Event translator_ role and user and login and note that
  non-translatable fields are not shown
-->

* Re-add the _Events_ view

### 13. Translating configuration

#### 13.1. Install the Configuration Translation module

* Install the _Content Translation_ module on `/admin/modules`

* Verify that a _Translate_ operation appears for event types

* Verify that translation works

[guide-short-url]: https://git.io/d8entity
[repository]: https://github.com/drupal-entity-training/event
[contrib-entity-api]: https://www.drupal.org/project/entity
[drush]: http://docs.drush.org/en/master
[api-oop]: https://api.drupal.org/api/drupal/core%21core.api.php/group/oo_conventions/8.2.x
[api-annotations]: https://api.drupal.org/api/drupal/core%21core.api.php/group/annotation/8.2.x
[api-field-types]: https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Field%21Annotation%21FieldType.php/class/annotations/FieldType/8.2.x
[api-field-formatters]: https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Field%21Annotation%21FieldFormatter.php/class/annotations/FieldFormatter/8.2.x
[wikipedia-link-relation]: https://en.wikipedia.org/wiki/Link_relation
[iana-link-relations]: https://www.iana.org/assignments/link-relations/link-relations.xml

*[IANA]: Internet Assigned Numbers Authority
