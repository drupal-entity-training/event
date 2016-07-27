---
layout: default
title: {{ site.name }}
---

This guide documents the process of creating a custom entity type in Drupal 8
using the example of an  _Event_ entity type.

You can reach this guide at [https://git.io/d8entity][guide-short-url].

The starting point is a stock Drupal 8.2 core _Standard_ installation with an
empty module named `event`. The state at the end of any given step can be seen
in the corresponding branch in the [repository][repository].

Having [Drush][drush] available is required to follow along. When Drush commands
are to be run, run them from within the Drupal installation. When PHP code is to
executed, this can be done by running `drush core-cli` (preferred) or by
creating a `test.php` script and then running `drush php-script test.php`.

**Table of contents**

1. Table of contents
{:toc}

### Using entities for data storage

#### Create an entity class

_Classes_ allow categorizing objects as being of a certain type. Event
entities, that will be created below, will be _instances_ of the entity
class. In terms of code organization, classes can be used to group related
functionality.

* Create a `src` directory

  In Drupal 8 the `src` directory contains all object-oriented code (classes,
  interfaces, traits). Procedural code (functions) is placed in the `.module`
  file (or other files) outside of the `src` directory.

* Create a `src/Entity` directory

  As modules often contain many classes, they can be placed into arbitrary
  subdirectories for organizational purposes. Certain directory names have a
  special meaning in Drupal and are required for certain things. In
  particular, Drupal looks in `Entity` for entity types.

* Create a `src/Entity/Event.php` file with the following:

  ```php
  <?php

  namespace Drupal\event\Entity;

  use Drupal\Core\Entity\ContentEntityBase;

  class Event extends ContentEntityBase {

  }
  ```

  Parts of this code block are explained below:

  * Class declaration:

    ```php?start_inline=1
    class Event {

    }
    ```

    The file name must correspond to class name (including capitalization).

  * Namespace:

    ```php?start_inline=1
    namespace Drupal\event\Entity;
    ```

    Namespaces allow code from different frameworks (Drupal, Symfony, …) to be
    used simultaneously without risking naming conflicts. Namespaces can have
    multiple parts. All classes in Drupal core and modules have `Drupal` as the
    top-level namespace. The second part of module classes must be the module
    name. Further sub-namespaces correspond to directory structure within the
    `src` directory of the module.

  * Base class:

    ```php?start_inline=1
    extends ContentEntityBase
    ```

    Base classes can be used to implement functionality that is generic and
    useful for many classes. Classes inherit all functionality from their base
    class and only need to provide functionality specific to them. This avoids
    code duplication.

  * Import:

    ```php?start_inline=1
    use Drupal\Core\Entity\ContentEntityBase;
    ```

    In the same way we declare a namespace for the `Event` class the
    `ContentEntityBase` class also belongs to a namespace. Thus, in order to use
    it below, we need to import the class using the full namespace.

  See [Drupal API: Object-oriented programming conventions][api-oop] for more
  information.

#### Add an annotation to the class

_Annotations_ are a way to provide metadata about code. Because the annotation
is placed right next to the code itself, this makes classes truly self-contained
as both functionality and metadata are in the same file.

Add the following comment block to the `Event` class:

```php?start_inline=1
/**
 * @ContentEntityType(
 *   id = "event",
 *   label = @Translation("Event"),
 *   label_singular = @Translation("event"),
 *   label_plural = @Translation("events"),
 *   label_count = @PluralTranslation(
 *     singular = "@count event",
 *     plural = "@count events"
 *   ),
 *   base_table = "event",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *   },
 * )
 */
```

Even though the annotation is part of a comment block, it is required for the
entity type to function.

Each part of this code block is explained below:

* ID:

  ```php?start_inline=1
  *   id = "event",
  ```

  This is the ID of the entity type that is needed whenever interacting with
  a specific entity type in code.

* Labels:

  ```php?start_inline=1
  *   label = @Translation("Event"),
  *   label_singular = @Translation("event"),
  *   label_plural = @Translation("events"),
  *   label_count = @PluralTranslation(
  *     singular = "@count event",
  *     plural = "@count events"
  *   ),
  ```

  Because the label of this entity type might be used in a sentence and when
  referencing multiple entities we need to provide different labels for the
  different possible usages.

  To make the values we provide in the annotation translatable we need to
  wrap them in `@Translation` or `@PluralTranslation` which are themselves
  annotations.
  
  Note that the keys in the `@PluralTranslation` annotation are not quoted and a
  trailing comma after the `plural = "@count events"` line is not permitted.

* Storage information:

  ```php?start_inline=1
  *   base_table = "event",
  *   entity_keys = {
  *     "id" = "id",
  *     "uuid" = "uuid",
  *   },
  ```

  We need to specify the name of the database table we want the event data to
  be stored. (This is called _base_ table, as there can be multiple tables
  that store entity information, as will be seen below.)

  Entities are required to have an ID which they can be loaded by. We need to
  specify what the ID field will be called for our entity. This will also
  determine the name of the database column that will hold the entity IDs.
  Similarly entity types can (and are encouraged to) provide a UUID field.
  Again, we can specify the name of the UUID field.

  Note that top-level keys of the annotation are not quoted, but keys in
  mappings (such as the `entity_keys` declaration) _are_ quoted and trailing
  commas are allowed in mappings.

  See [Drupal API: Annotations][api-annotations] for more information.

#### Install the entity type

Drupal can be create the database schema for our entity type automatically but
this needs to be done explicitly. The preferred way of doing this is with Drush.

* Run `drush entity-updates`

  Note that the `{event}` table has been created in the database with `id`
  and `uuid` columns.

* Create and save an event

  Run the following PHP code:

  ```php?start_inline=1
  use Drupal\event\Entity\Event;

  $event = Event::create();
  $event->save();
  ```

  Note that there is a new row in the `{event}` table with an ID and a UUID.

  The `Event` class inherits the `create()` and `save()` methods from
  `ContentEntityBase` so they can be called without being present in the
  `Event` class itself.

  `create()` is a _static_ method so it is called by using the class name and
  the `::` syntax. `save()` is not a static method so it is used with an
  instance of the class and the `->` syntax.

* Load an event fetch its ID and UUID

  Run the following PHP code:

  ```php?start_inline=1
  use Drupal\event\Entity\Event;

  $event = Event::load(1);
  $event->id();
  $event->uuid();
  ```

  Note that the returned values match the values in the database.

* Delete the event

  Run the following PHP code:

  ```php?start_inline=1
  use Drupal\event\Entity\Event;

  $event = Event::load(1);
  $event->delete();
  ```

  Note that the row in the `{event}` table is gone.

#### Add field definitions

Fields are the pieces of data that make up an entity. The ID and UUID that
were saved as part of the event above are examples of field values. To be
able to store actual event data in our entities, we need to declare
additional fields.

* Add the following method to `src/Entity/Event.php`:

  ```php?start_inline=1
  use Drupal\Core\Entity\EntityTypeInterface;
  use Drupal\Core\Field\BaseFieldDefinition;

  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setRequired(TRUE);
    $fields['date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Date'))
      ->setRequired(TRUE);
    $fields['description'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Description'));

    return $fields;
  }
  ```

  Parts of this code block are explained below:

  * Type hint:

    ```php?start_inline=1
    EntityTypeInterface $entity_type
    ```

    _Interfaces_ are contracts that specify the methods a class must have in
    order to fulfill it.

    The interface name in front of the `$entity_type` parameter is a _type
    hint_. It dictates what type of object must be passed. Type hinting an
    interface allows any class that _implements_ the interface to be passed.

  * Field definition:

    ```php?start_inline=1
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

    ```php?start_inline=1
    ->setLabel(t('Title'))
    ->setRequired(TRUE)
    ```

    Many set methods return the object they were called on to allow
    _chaining_ multiple set methods after another. The setting up of the
    `title` field definition above is functionally equivalent to the
    following code block which avoids chaining:

    ```php?start_inline=1
    $fields['title'] = BaseFieldDefinition::create('string');
    $fields['title']->setLabel(t('Title'));
    $fields['title']->setRequired(TRUE);
    ```

* Add the following to the `entity_keys` part of the annotation in
  `src/Entity/Event.php`:

  ```php?start_inline=1
  *     "label" = "title",
  ```

  Declaring a `label` key makes the (inherited) `label()` method on the `Event`
  class work and also allows autocompletion of events by their title.

#### Install the fields

Drupal notices changes to the entity type that affect the database schema and can
update it automatically.

* Run `drush entity-updates`

  Note that `title`, `date`, `description__value` and `description__format`
  columns have been created in the `{event}` table.

  Although most field types consist of a single `value` _property_, text
  fields, for example, have an additional `format` property. Therefore
  two database columns are required for text fields.

* Create and save an event

  Run the following PHP code:

  ```php?start_inline=1
  use Drupal\event\Entity\Event;

  $event = Event::create([
    'title' => 'Drupal User Group',
    'date' => (new \DateTime())->format(DATETIME_DATETIME_STORAGE_FORMAT),
    'description' => [
      'value' => '<p>The monthly meeting of Drupalists is happening today!</p>',
      'format' => 'basic_html',
    ],
  ]);
  $event->save();
  ```

  Note that there is a new row in the `{event}` table with the proper field
  values.

* Load an event fetch its field values.

  Run the following PHP code:

  ```php?start_inline=1
  use Drupal\event\Entity\Event;

  $event = Event::load(2);

  $event->get('title')->value;

  $event->get('date')->value;
  $event->get('date')->date;

  $event->get('description')->value;
  $event->get('description')->format;
  $event->get('description')->processed;
  ```

  Note that the returned values match the values in the database.

  In addition to the stored properties field types can also declare
  _computed_ properties, such as the `date` property of a datetime field or
  the `processed` property of text fields.

* Update an event's field values and save them.

  Run the following PHP code:

  ```php?start_inline=1
  use Drupal\event\Entity\Event;

  $event = Event::load(2);

  $event
    ->set('title', 'DrupalCon')
    ->set('date', (new \DateTime('yesterday'))->format(DATETIME_DATETIME_STORAGE_FORMAT))
    ->set('description', [
      'value' => '<p>DrupalCon is a great place to meet international Drupal superstars.</p>',
      'format' => 'restricted_html',
    ])
    ->save();
  ```

  Note that the values in the database have been updated accordingly.

#### Add field methods

Instead of relying on the generic `get()` and `set()` methods it is recommended
to add field-specific methods that wrap them. This makes interacting with
events in code more convenient. Futhermore, it is recommended to add an
interface

* Add the following methods to `src/Entity/Event.php`:

  ```php?start_inline=1
  public function getTitle() {
    return $this->get('title')->value;
  }

  public function setTitle($title) {
    $this->set('title', $title);
  }

  public function getDate() {
    return $this->get('date')->date;
  }

  public function setDate(\DateTimeInterface $date) {
    $this->set('date', $date->format(DATETIME_DATETIME_STORAGE_FORMAT));
  }

  public function getDescription() {
    return $this->get('description')->processed;
  }

  public function setDescription($description, $format) {
    $this->set('description', [
      'value' => $description,
      'format' => $format,
    ]);
  }
  ```

  Field methods not only provide autocompletion, but also allow designing richer
  APIs than the bare field types provide. The `setDate()` method, for example,
  hides the internal storage format of datetime values from anyone working with
  events. Similarly the `setDescription()` method requires setting the descrtption
  and the text format simultaneously for security.

* Create a `src/Event/EventInterface.php` with the following code:

  ```php
  <?php

  namespace Drupal\event\Entity;

  use Drupal\Core\Entity\ContentEntityInterface;

  interface EventInterface extends ContentEntityInterface {

    public function getTitle();

    public function setTitle($title);

    public function getDate();

    public function setDate(\DateTimeInterface $date);

    public function getDescription();

    public function setDescription($description, $format);

  }
  ```

* Add the following to the class declaration in `src/Entity/Event.php`:

  ```php?start_inline=1
  implements EventInterface
  ```

* Try out the new methods

  Run the following PHP code:

  ```php?start_inline=1
  use Drupal\event\Entity\Event;

  $event = Event::load(2);

  $event->getTitle();
  $event->getDate();
  $event->getDescription();

  $event
    ->setTitle('Drupal Developer Days')
    ->setDate(new \DateTime('tomorrow'))
    ->setDescription(
      '<p>The Drupal Developer Days are a great place to nerd out about all things Drupal!</p>',
      'basic_html'
    )
    ->save();
  ```

  Note that the returned values match the values in the database before and that
  the values in the database have been updated accordingly.

### Adding a view page

Viewing an entity on a page requires a _view builder_ that is responsible for
constructing a renderable array from an entity object. Futhermore, a route is
needed that utilizes the view builder to output the entity's fields on a given
path. All of this can be automated by amending the entity annotation.

#### Add a route

* Add the following to the annotation in `src/Entity/Event.php`:

  ```php?start_inline=1
  *   handlers = {
  *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
  *     "route_provider" = {
  *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
  *     },
  *   },
  *   links = {
  *     "canonical" = "/event/{event}"
  *   },
  ```

  Parts of this code block are explained below:

  * Entity handlers:

    ```php?start_inline=1
    handlers
    ```

    Entity _handlers_ are objects that take over certain tasks related to
    entities. Each entity type can declare which handler it wants to use for which
    task. In many cases - as can be seen above - Drupal core provides generic
    handlers that can be used as is. In other cases or when more advanced
    functionality is required, custom handlers can be used instead.

  * Route providers:

    ```php?start_inline=1
    *     "route_provider" = {
    *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
    *     },
    ```

    Instead of declaring routes belonging to entities in a `*.routing.yml` file
    like other routes, they can be provided by a handler, as well. This has the
    benefit of being able to re-use the same route provider for multiple entity
    types, as is proven by the usage of the generic route provider provided by
    core.

  * Links:

    ```php?start_inline?1
    *   links = {
    *     "canonical" = "/event/{event}"
    *   },
    ```

    Entity links denote at which paths on the website we can see an entity (or
    multiple entities) of the given type. They are used by the default route
    provider to set the path of the generated route. The usage of `canonical`
    (instead of `view`, for example) stems from the specification of link
    relations in the web by the IANA.

    See [Wikipedia: Link relation][wikipedia-link-relation] and
    [IANA: Link relations][iana-link-relations] for more information.

* Rebuild caches

  Run `drush cache-rebuild`

* Verify the route has been generated

  Visit `/event/2`

  Note that an _Access denied_ page is shown. This means a route exists at this
  path (otherwise a _Not found_ page would be shown). However, access has not
  been defined, so that - even for the administrative user - the page will not
  be shown.

#### Add an administrative permission

An administrative permission is used by the default entity access control
handler for all operations as a fallback. More granular permissions together
with an enhanced entity access control handler will be added below.

* Add a `event.permissions.yml` with the following:

  ```yaml
  administer events:
    title: 'Administer events'
  ```

* Add the following to the annotation in `src/Entity/Event.php`:

  ```php?start_inline=1
  *   admin_permission = "administer events",
  ```

* Rebuild caches

  Run `drush cache-rebuild`

* Verify that access is granted

  Visit `/event/2`

  Note that an empty page is shown (and no longer an _Access denied_ page).
  However, no field values are shown.

#### Configure fields for display

Which fields to display when rendering the entity, as well as how to display
them, can be configured as part of the field definitions. Fields are not
displayed unless explicitly configured to.

* Add the following to the `$fields['date']` section of the
  `baseFieldDefinitions()` method of `src/Entity/Event.php` before the semicolon:

  ```php?start_inline=1
  ->setDisplayOptions('view', [
    'label' => 'inline',
    'settings' => [
      'format_type' => 'html_date',
    ],
    'weight' => 0,
  ])
  ```

  Add the following to the `$fields['description']` section of the
  `baseFieldDefinitions()` method of `src/Entity/Event.php` before the semicolon:

  ```php?start_inline=1
  ->setDisplayOptions('view', [
    'label' => 'hidden',
    'weight' => 10,
  ])
  ```

  <!-- @todo: Explain why the title field is not displayed -->

  Parts of this code block are explained below:

  * Display mode:

    ```php?start_inline=1
    ->setDisplayOptions('view'
    ```

    Display options can be set for two different display _modes_: `view` and
    `form`. Form display options will be set below.

  * Label display:

    ```php?start_inline=1
    'label' => 'inline',
    ```

    The field label can be configured to be displayed above the field value (the
    default), inline in front of the field value or hidden altogether. The
    respective values of the `label` setting are `above`, `inline` and `hidden`.

  * Formatter settings:

    ```php?start_inline=1
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

    ```php?start_inline=1
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

* Rebuild caches

  Run `drush cache-rebuild`

* Visit _Recent log messages_ page

  Note there is a warning due to a missing `event` theme hook.

  Delete the recent log messages.

#### Add a theme function

* Add an `event.module` with the following:

  ```php
  <?php

  function event_theme($existing, $type, $theme, $path) {
    return [
      'event' => [
        'render element' => 'content',
      ],
    ];
  }
  ```

  This registers the `event` theme hook and makes it so that the rendered entity
  output is placed in a `content` variable that is available to preprocess
  functions and templates.

  Note that entity types in core use an `elements` variable by default and then
  (selectively) copy that over to a `content` variable manually in a preprocess
  function. That `content` variable is then used in the templates. The above
  avoids having to provide a preprocess function while retaining the ability to
  use the `content` variable in templates.

* Add a `templates` directory

* Add a `templates/event.html.twig` with the following:

  {% raw %}
  ```twig
  <div{{ attributes }}>
    {{ content }}
  </div>
  ```
  {% endraw %}

* Rebuild caches

  Run `drush cache-rebuild`

* Visit `/event/2`

  Note the additional `div` element.

* Visit _Recent log messages_ page

  Note there is no warning.

### Adding add, edit and delete forms

#### Add the routes

* Add the following to the `handlers` section of the annotation in
  `src/Entity/Event.php`:

  ```php?start_inline=1
  *     "form" = {
  *       "add" = "Drupal\Core\Entity\ContentEntityForm",
  *       "edit" = "Drupal\Core\Entity\ContentEntityForm",
  *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
  *     },
  ```

  <!-- TODO: Explain this -->

* Add the following to the `links` section of the annotation in 
  `src/Entity/Event.php`:

  ```php?start_inline=1
   *     "add-form" = "/admin/content/events/add",
   *     "edit-form" = "/admin/content/events/manage/{event}",
   *     "delete-form" = "/admin/content/events/manage/{event}/delete",
  ```

* Rebuild caches

  Run `drush cache-rebuild`

* Visit `/admin/content/events/add`

  Note that a route exists and _Save_ and _Delete_ buttons are shown, but no
  actual form fields are shown.

#### Configure fields for display


* Add the following to the `$fields['title']` section of the
  `baseFieldDefinitions()` method of `src/Entity/Event.php` before the
  semicolon:

  ```php?start_inline=1
  ->setDisplayOptions('form', ['weight' => 0])
  ```

* Add the following to the `$fields['date']` section of the
  `baseFieldDefinitions()` method of `src/Entity/Event.php` before the
  semicolon:

  ```php?start_inline=1
  ->setDisplayOptions('form', ['weight' => 10])
  ```

* Add the following to the `$fields['description']` section of the
  `baseFieldDefinitions()` method of `src/Entity/Event.php` before the
  semicolon:

  ```php?start_inline=1
  ->setDisplayOptions('form', ['weight' => 20])
  ```

* Rebuild caches

  Run `drush cache-rebuild`

* Add an event in the user interface

  Visit `/admin/content/events/add`

  Note that the form fields are displayed.

  Enter a title, date and description and press _Save_.

  Note that no message is displayed and no redirect is performed. This will be
  added later by providing a specialized form handler.

  Verify that the event was saved by checking that a new row was created in the
  `{event}` table.

* Edit an event in the user interface

  Visit `/admin/content/events/manage/3`

  Note that a route exists and form fields are displayed including proper
  default values.

  Modify the title, date and description and press _Save_.

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

### Adding an administrative entity listing

#### Add a route

* Add the following to the `handlers` section of the annotation in
  `src/Entity/Event.php`:

  ```php?start_inline=1
  *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
  ```

* Add the following to the `links` section of the annotation in
  `src/Entity/Event.php`:

  ```php?start_inline=1
  *     "collection" = "/admin/content/events",
  ```

* Visit `/admin/content/events`

  Note that a route is provided and a list of entities is provided with
  _Edit_ and _Delete_ operation links for each entity.

  By not showing at least the title of each event the list is not actually
  usable so we need to provide a specialized list builder.

#### Add a specialized list builder

* Add a `src/Entity/EventListBuilder` with the following:

  ```php
  <?php

  namespace Drupal\event\Entity;

  use Drupal\Core\Entity\EntityInterface;
  use Drupal\Core\Entity\EntityListBuilder;

  class EventListBuilder extends EntityListBuilder {

    public function buildHeader() {
      $header = [];
      $header['title'] = $this->t('Title');
      $header['date'] = $this->t('Date');
      return $header + parent::buildHeader();
    }

    public function buildRow(EntityInterface $entity) {
      /** @var \Drupal\event\Entity\EventInterface $event */
      $row = [];
      $row['title'] = $event->toLink();
      $row['date'] = $event->getDate()->format(DATETIME_DATETIME_STORAGE_FORMAT);
      return $row + parent::buildRow($entity);
    }

  }
  ```

  Parts of this code block are explained below:

  * Separate methods:

    ```php?start_inline=1
    public function buildHeader() {
    ```

    List builders build the table header and the table rows in separate methods.

  * Translation:

    ```php?start_inline=1
    $this->t('Title')
    ```

  * Array merging:

    ```php?start_inline=1
    $header + parent::buildHeader()
    ```

  * Inline type hint:

    ```php?start_inline=1
    /** @var \Drupal\event\Entity\EventInterface $event */
    ```

  * Entity links:

    ```php?start_inline=1
    $event->toLink()
    ```

  * Date formatting:

    Instead of hardcoding the format the `date.formatter` service should be
    injected

* Replace the value of the `list_builder` annotation key in the `handlers`
  section of the annotation in `src/Entity/Event.php` with
  `Drupal\event\Entity\EventListBuilder`.

* Rebuild caches

  Run `drush cache-rebuild`

* Visit `/admin/content/events`

  Note that the entity list now shows the event title and date.

* Delete an event again

  Visit `/admin/content/events/manage/2/delete` and press _Delete_.

  Note that this time you are redirected to the administrative event listing.
  The redirect to the front page that happened above is only a fallback in case
  no `collection` route exists.

## Views data

* Add the following to `src/Entity/Event.php`:

  ```php?start_inline=1
  *     "views_data" = "Drupal\views\EntityViewsData",
  ```

<!-- TODO: Mention views data sucks -->

* Add a _Event_ view to replace the list builder

## Administration links

* Add a `event.links.menu.yml` with the following:

  ```yaml
  entity.event.collection:
    title: 'Events'
    route_name: entity.event.collection
    parent: system.admin_content
  ```

  * Routes are separate from menu links

  * `hook_menu()` in D7 → multiple `event.links.*.yml` files

* Rebuild caches

  Run `drush cache-rebuild`

* Add a `event.links.task.yml` with the following:

  ```yaml
  entity.event.collection:
    title: 'Events'
    route_name: entity.event.collection
    base_route: system.admin_content
  ```

* Rebuild caches

  Run `drush cache-rebuild`

* Visit `/admin/content/events`

* Add a `event.links.action.yml` with the following:

  ```ỳaml
  entity.event.collection:
    title: 'Add'
    route_name: entity.event.add_form
    appears_on: [entity.event.collection]
  ```

* Rebuild caches

  Run `drush cache-rebuild`

* Visit `/admin/content/events`

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

* Visit `/events/{event}`

<!-- TODO: Add contextual links and form redirects -->

## Access control

* Add the following to `event.permissions.yml`:

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

    protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
      switch ($operation) {
        case "view":
          $access_result = AccessResult::allowedIfHasPermission($account, 'view events');
          break;

        case "update":
          $access_result = AccessResult::allowedIfHasPermission($account, 'edit events');
          break;

        case "delete":
          $access_result = AccessResult::allowedIfHasPermission($account, 'delete events');
          break;

        default:
          $access_result = AccessResult::neutral();
          break;
      }
      return $access_result->orIf(parent::checkAccess($entity, $operation, $account));
    }

  }
  ```

* Add the following to `src/Entity/Event.php`:

  ```php?start_inline=1
  *     "access" = "Drupal\event\Access\EventAccessControlHandler",
  ```

* Rebuild caches

  Run `drush cache-rebuild`

* Test permissions

  * `create events`, `edit events`, or `delete events` do not grant
    access to `/admin/content/events`

## Additional fields

* Add the following to `src/Entity/Event.php`:

  ```php?start_inline=1
  use Drupal\Core\Field\FieldStorageDefinitionInterface;

  $fields['path'] = BaseFieldDefinition::create('path')
    ->setLabel(t('Path'))
    ->setDisplayOptions('form', ['weight' => 15]);

  $fields['attendees'] = BaseFieldDefinition::create('entity_reference')
    ->setLabel(t('Attendees'))
    ->setSetting('target_type', 'user')
    ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
    ->setDisplayOptions('form', ['weight' => 20]);
  ```

* Update entity/field definitions

  * `{event__attendees}` table created

  * `deleted`, `langcode`, `bundle`, `revision_id` not optional currently

<!-- TODO: Add methods for managing attendees -->

* Add the following to `src/Entity/EventInterface.php`:

  ```php
  use Drupal\Core\Entity\EntityChangedInterface;
  use Drupal\user\EntityOwnerInterface;

  , EntityChangedInterface, EntityOwnerInterface
  ```

  * Changed tracking allows edit-locking

  * Owners are used in entity reference, comment statistics, ...

* Add the following to `src/Entity/Event.php`:

  ```php?start_inline=1
  use Drupal\Core\Entity\EntityChangedTrait;

  use EntityChangedTrait;

  public function getOwner() {
    $this->get('owner')->entity;
  }
  public function setOwner(UserInterface $account) {
    $this->set('owner', $account->id());
  }
  public function getOwnerId() {
    $this->get('owner')->target_id;
  }
  public function setOwnerId($uid) {
    $this->set('owner', $uid);
  }

  $fields['changed'] = BaseFieldDefinition::create('changed')
    ->setLabel(t('Changed'));
  $fields['owner'] = BaseFieldDefinition::create('entity_reference')
    ->setLabel(t('Owner'))
    ->setSetting('target_type', 'user')
    ->setDefaultValueCallback(static::class . '::getDefaultOwnerIds');

    public static function getDefaultOwnerIds() {
      return [\Drupal::currentUser()->id()];
    }
  ```

<!-- TODO: Add status field -->

* Update entity/field definitions

  * `changed` and `owner` columns created

## Configuration entities

* Create a `src/Entity/EventType.php` with the following:

  ```php
  <?php

  namespace Drupal\event\Entity;

  use Drupal\Core\Config\Entity\ConfigEntityBase;

  /**
   * @ConfigEntityType(
   *   id = "event_type",
   *   label = @Translation("Event type"),
   *   config_prefix = "type",
   *   config_export = {
   *     "id",
   *     "label",
   *   }
   * )
   */
  class EventType extends ConfigEntityBase{

    protected $id;

    protected $label;

  }
  ```

* Update entity/field definitions

  * No schema change

* Try out event type CRUD

  * Create and save an event type

    * Row in `{config}` table

  * Load an event type by ID and print label

  * Delete an event type

    * Row in `{config}` table gone

<!-- TODO: Config Translation -->
<!-- TODO: Switch Translation & Revisions -->

[guide-short-url]: https://git.io/d8entity
[repository]: https://github.com/drupal-entity-training/event
[drush]: http://docs.drush.org/en/master
[api-oop]: https://api.drupal.org/api/drupal/core%21core.api.php/group/oo_conventions/8.2.x
[api-annotations]: https://api.drupal.org/api/drupal/core%21core.api.php/group/annotation/8.2.x
[api-field-types]: https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Field%21Annotation%21FieldType.php/class/annotations/FieldType/8.2.x
[api-field-formatters]: https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Field%21Annotation%21FieldFormatter.php/class/annotations/FieldFormatter/8.2.x
[wikipedia-link-relation]: https://en.wikipedia.org/wiki/Link_relation
[iana-link-relations]: https://www.iana.org/assignments/link-relations/link-relations.xml

*[IANA]: Internet Assigned Numbers Authority
