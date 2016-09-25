<?php

namespace Drupal\event\Entity;

use Drupal\Core\Entity\ContentEntityInterface;

interface EventInterface extends ContentEntityInterface {

  /**
   * @return string
   */
  public function getTitle();

  /**
   * @param string $title
   *
   * @return $this
   */
  public function setTitle($title);

  /**
   * @return \DateTimeInterface
   */
  public function getDate();

  /**
   * @param \DateTimeInterface $date
   *
   * @return $this
   */
  public function setDate(\DateTimeInterface $date);

  /**
   * @return string
   */
  public function getDescription();

  /**
   * @param string $description
   * @param string $format
   *
   * @return $this
   */
  public function setDescription($description, $format);

  /**
   * @return bool
   */
  public function isPublished();

  /**
   * @return $this
   */
  public function publish();

  /**
   * @return $this
   */
  public function unpublish();

}