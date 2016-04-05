<?php

namespace Drupal\event\Controller;

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

    // Place any test code here.

    return ['content' => ['#markup' => 'Any code placed in \\' . __METHOD__ . '() is executed on this page.']];
  }

}
