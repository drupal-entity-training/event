<?php

namespace Drupal\event\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Provides a constraint for dates that lie in the future.
 *
 * @Constraint(
 *   id = "FutureDate",
 *   label = @Translation("Future date", context = "Validation")
 * )
 */
class FutureDateConstraint extends Constraint {

  public $message = 'The date must lie in the future.';

}
