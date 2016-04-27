# About

This module aims to showcase the features of Drupal 8's Entity API by providing
an _Event_ entity type.


## Branches

The different branches in this repository showcase the process of creating a
custom entity type step by step.

* `0-empty-module`: Only this and the basic module info file (`event.info.yml`)
  to register this module is provided.

* `1-minimal-entity-type`: Provides a minimal _Event_ entity type with the least
  amount of code possible while still providing full CRUD support.

* `2-base-field-definitions`: Adds some basic field definitions to the entity
  type so the entity type can actually store information that makes up an event.

* `3-interface`: Adds getter and setter methods and an accompanying interface.

* `4-view-builder`: Adds a view builder so events can be rendered.

* `5-forms`: Adds add, edit, and delete forms.

* `6-list-builder`: Adds a list builder so events can be managed in the
  administration area.

* `7-admin-links`: Adds menu links, local tasks, and local actions to provide a
  usable event administration interface.


## Event Development Helper

The _Event Development Helper_ module that is provided is part of this
can aid in the development of the entity type. See the accompanying README.txt
in the `event_devel` directory.
