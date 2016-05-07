# About

This module aims to showcase the features of Drupal 8's Entity API by providing
an _Event_ entity type.


## Branches

The different branches in this repository showcase the process of creating a
custom entity type step by step.

* `00-empty-module`: Only the basic module info file to register this module is
  provided.

* `01-minimal-entity-type`: Provides a minimal _Event_ entity type with the least
  amount of code possible while still providing full CRUD support.

* `02-base-field-definitions`: Adds some basic field definitions to the entity
  type so the entity type can actually store information that makes up an event.

* `03-interface`: Adds getter and setter methods and an accompanying interface
  for better developer experience.

* `04-view-builder`: Adds a view builder so events can be rendered.

* `05-forms`: Adds add, edit, and delete forms.

* `06-list-builder`: Adds a list builder so events can be managed in the
  administration area.

* `07-views-data`: Adds _Views_ data for events and an administrative view to
  replace the list builder and make the event administration more powerful and
  flexible.

* `08-admin-links`: Adds menu links, local tasks, local actions, and contextual
  links, and form redirects to provide a usable event administration interface.

* `09-access`: Adds permissions for events so administration can be done more
  granular and role-based.

* `10-additional-fields`: Adds a path field and an attendees fields to
  demonstrate multiple-value fields.

* `11-bundles`: Adds an _Event type_ configuration entity type that acts as a
  bundle for events.

* `12-field-ui`: Enables _Field UI_ integration so that fields, view displays
  and form displays can be managed in the user interface.

* `13-revisions`: Enables revisions including a view of revisions per event and
  a page to display a particular event revision.

* `14-translation`: Enables translation of events.

* `15-rest`: Enables customized RESTful out- and input.

* `16-validation`: Adds a custom validation constraint that is used both for
  RESTful web services and forms.

When switching branches you need to update entity/field definitions and rebuild
the cache. Because configuration is provided in some of the branches you may
want to reinstall the module for a better out-of-the-box experience.


## Event Development Helper

The _Event Development Helper_ module that is provided is part of this
can aid in the development of the entity type. See the accompanying README.txt
in the `event_devel` directory.
