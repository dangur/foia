<?php

namespace Drupal\Tests\foia_webform\Kernel;

/**
 * Trait ReflectionTrait.
 *
 * @package Drupal\Tests\foia_webform\Kernel
 */
trait ReflectionTrait {

  /**
   * Facilitates access to test private and protected methods.
   */
  public function invokeMethod(&$object, $methodName, array $parameters = []) {
    $reflection = new \ReflectionClass(get_class($object));
    $method = $reflection->getMethod($methodName);
    $method->setAccessible(TRUE);

    return $method->invokeArgs($object, $parameters);

  }

}
