# About

This module aims to showcase the features of Drupal 8's Entity API by providing
an _Event_ entity type.

## Branches

The different branches in this repository showcase the process of creating a
custom entity type step by step.

The current branch is `0-empty-module`. In addition to this file and the basic
module info file (`event.info.yml`) to register this module to Drupal it only
contains a test controller (`src/Controller/TestController.php`) and a routing
YAML file (`event.routing.yml`) to register the test controller to the `/test`
path.

The other branches are:
* `1-minimal-entity-type`: Provides a minimal entity type with the least amount
  of code possible.
