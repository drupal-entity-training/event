<?php

namespace Drupal\event\Plugin\Validation\Constraint;

use Drupal\Core\TypedData\Validation\TypedDataAwareValidatorTrait;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates that a date lies in the future.
 */
class FutureDateConstraintValidator extends ConstraintValidator {

  use TypedDataAwareValidatorTrait;

  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {
    if (!isset($value)) {
      return;
    }

    /** @var \Drupal\Core\TypedData\Type\DateTimeInterface $typed_data */
    $typed_data = $this->getTypedData();
    if ($_SERVER['REQUEST_TIME'] > $typed_data->getDateTime()->format('U')) {
      /** @var \Drupal\event\Plugin\Validation\Constraint\FutureDateConstraint $constraint */
      $this->context->addViolation($constraint->message);
    }
  }

}
