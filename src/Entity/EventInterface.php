<?php

namespace Drupal\event\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\user\UserInterface;

/**
 * Provides an interface for events.
 */
interface EventInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface, RevisionLogInterface {

  /**
   * Gets the type of the event.
   *
   * @return \Drupal\event\Entity\EventTypeInterface
   */
  public function getType();

  /**
   * Sets the type of the event.
   *
   * @param \Drupal\event\Entity\EventTypeInterface $type
   *   The event type.
   *
   * @return $this
   */
  public function setType(EventTypeInterface $type);

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

  /**
   * Gets the list of attendees for this event.
   *
   * @return \Drupal\user\UserInterface[]
   *   The list of attendees for this event.
   */
  public function getAttendees();

  /**
   * Adds an attendee to the event.
   *
   * @param \Drupal\user\UserInterface $attendee
   *   The attendee to add to the event.
   *
   * @return $this
   */
  public function addAttendee(UserInterface $attendee);

  /**
   * Removes an attendee from the event.
   *
   * @param \Drupal\user\UserInterface $attendee
   *   The attendee to remove from the event.
   *
   * @return $this
   */
  public function removeAttendee(UserInterface $attendee);

}
