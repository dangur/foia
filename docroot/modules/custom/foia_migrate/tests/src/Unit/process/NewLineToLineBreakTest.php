<?php

/**
 * @file
 * Contains \Drupal\Tests\foia_migrate\Unit\process\NewLineToLineBreakTest.
 */

namespace Drupal\Tests\foia_migrate\Unit\process;

use Drupal\foia_migrate\Plugin\migrate\process\NewlineToLineBreak;
use Drupal\Tests\migrate\Unit\process\MigrateProcessTestCase;

/**
 * Tests the conversion of line breaks to <br /> HTML tags process plugin.
 *
 * @group foia_migrate
 */
class NewLineToLineBreakTest extends MigrateProcessTestCase {

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    $configuration = [
      'is_xhtml' => TRUE,
    ];
    $this->plugin = new NewlineToLineBreak($configuration, 'nl2br', []);
    parent::setUp();
  }

  /**
   * Tests transformation.
   *
   */
  public function testTransform() {
    $source = '\n';
    $expected = '<br>';
    $value = $this->plugin->transform($source, $this->migrateExecutable, $this->row, 'body');

    $this->assertEquals($value, $expected);
  }

}
