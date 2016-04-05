<?php

namespace Drupal\event\Controller;

use Drupal\event\Entity\Event;

/**
 * Provides a test controller.
 */
class TestController {

  /**
   * Provides an empty test controller to easily execute arbitrary code.
   *
   * This is exposed at the '/test' path on your site.
   *
   * @return array
   *   A renderable array that contains instruction text for this controller.
   *
   * @see event.routing.yml
   */
  public function test() {

    // This creates a new event and saves it to the database:
    // Event::create()->save();

    // This loads an event by its ID and displays its UUID in a message.
    // $id = 1;
    // $uuid = Event::load($id)->uuid();
    // drupal_set_message('The UUID for event with ID ' . $id . ' is ' . $uuid);

    return ['content' => ['#markup' => 'Any code placed in \\' . __METHOD__ . '() is executed on this page.']];
  }

}
