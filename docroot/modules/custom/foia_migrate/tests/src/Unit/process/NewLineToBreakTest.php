<?php

/**
 * @file
 * Contains \Drupal\Tests\foia_migrate\Unit\process\NewLineToBreakTest.
 */

namespace Drupal\Tests\foia_migrate\Unit\process;

use Drupal\foia_migrate\Plugin\migrate\process\NewlineToLineBreak;
use Drupal\Tests\migrate\Unit\process\MigrateProcessTestCase;

/**
 * Tests the conversion of line breaks to <br /> HTML tags plugin.
 *
 * @group foia_migrate
 */
class NewLineToBreakTest extends MigrateProcessTestCase {

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    $this->plugin = new TestNewLineToLineBreak([TRUE], 'nl2br', 'def');
    parent::setUp();
  }

  /**
   * Test new line to break.
   */
  public function testNewLineToBreak() {
    $this->plugin->setIshtml(TRUE);
    $value = $this->plugin->transform('\n', $this->migrateExecutable,$this->row, 'destinationproperty');
    $this->assertSame($value, '<br />');
  }

}

/**
 * Class TestNewLineToLineBreak.
 *
 * @package Drupal\Tests\foia_migrate\Unit\process
 */
class TestNewLineToLineBreak extends NewlineToLineBreak {
  /**
   * Constructor.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * Pass config.
   */
  public function setIshtml($is_xhtml) {
    $this->configuration['is_xhtml'] = $is_xhtml;
  }

}
