<?php

/**
 * @file
 * Contains preprocess functions for Event module.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for event templates.
 *
 * Default template: event.html.twig
 *
 * @param array $variables
 *   An array of template variables.
 */
function template_preprocess_event(&$variables) {
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
