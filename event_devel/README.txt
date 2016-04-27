# About

This module aids the development of a new entity type by providing the following
routes:

* **Evaluate test code**
  (Path: `/evaluate-test-code`)

  This route does not do anything by default but exists so test code can be
  placed in the
  `\Drupal\event_devel\Controller\TestController::evaluateTestCode()` method.
  Example code for each step of the development of the _Event_ entity type is
  provided but commented out by default.

  If [Drush](http://www.drush.org/en/master/) is available, `drush php-eval`,
  `drush php-script`, or `drush core-cli` can be used instead.

* **Update entity/field definitions**
  (Path: `/update-entity-field-definitions`)

  This route updates the entity and field definitions and adapts the database
  schemas of entities accordingly. This way the event module does not need to
  be uninstalled and reinstalled each time the entity definition is updated.

  If [Drush](http://www.drush.org/en/master/) is available,
  `drush entity-updates` can be used instead.

For each of the routes a menu link is provided in the _Tools_ menu, so that it
can be easily accessed from the first sidebar using a _Standard_ installation of
Drupal.
