# Drupal Entity Type Walkthrough

Step-by-step instructions to create a custom entity type in Drupal 11.

This guide documents the process of creating a custom entity type in Drupal 11 using the example of an Event entity type.

You can reach this guide at https://git.io/d8entity.

The starting point is a stock Drupal 11 core Standard installation with the contributed [Entity API][contrib-entity-api] available at `modules/entity` and an empty module directory at `modules/event`.

Having [Drush 13][drush] available is required to follow along. When Drush commands are to be run, run them from within the Drupal installation. When PHP code is to be executed, this can be done by running `drush core:cli` or by creating a test script and then running `drush php:script <name-of-script>`.

[contrib-entity-api]: https://www.drupal.org/project/entity
[drush]: https://www.drush.org/13.x
