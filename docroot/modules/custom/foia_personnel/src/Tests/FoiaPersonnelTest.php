<?php

namespace Drupal\foia_personnel\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Class FoiaPersonnelTest
 *
 * @package Drupal\Tests\foia_personnel\Unit
 *
 * @group foia_personnel
 */
class FoiaPersonnelTest extends WebTestBase {

  /**
   * Modules to install.
   *
   * @var array
   */
  public static $modules = array('foia_personnel');

  /**
   * A user.
   *
   * @var object
   */
  private $user;

  /**
   * Perform initial setup tasks.
   */
  public function setUp() {
    parent::setUp();
    $this->user = $this->DrupalCreateUser(array(
      'add foia personnel entities',
    ));
  }

  public function testFoiaPersonnelFormReachable() {
    $this->drupalLogin($this->user);

    $this->drupalGet('admin/structure/foia_personnel/add');
    $this->assertResponse(200);
  }
}