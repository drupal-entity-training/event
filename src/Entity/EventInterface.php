<?php

namespace Drupal\event\Entity;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Provides an interface for events.
 */
interface EventInterface extends ContentEntityInterface {

  /**
   * Gets the title of an event.
   *
   * @return string
   *   The title of the event.
   */
  public function getTitle();

  /**
   * Sets the title of the event.
   *
   * @param string $title
   *   The title to set.
   *
   * @return $this
   */
  public function setTitle($title);

  /**
   * Gets the date of the event.
   *
   * @return \DateTimeInterface
   *   The date of the event.
   */
  public function getDate();

  /**
   * Sets the date of the event.
   *
   * @param \DateTimeInterface $date
   *   The date to set.
   *
   * @return $this
   */
  public function setDate(\DateTimeInterface $date);

  /**
   * Gets the description of the event.
   *
   * @return \Drupal\Component\Render\MarkupInterface
   *   The description of the event.
   */
  public function getDescription();

  /**
   * Sets the description text of the event.
   *
   * @param string $text
   *   The description text.
   * @param string $format
   *   The ID of the text format to use for the description.
   *
   * @return $this
   */
  public function setDescription($text, $format);

}
