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
   * Tests transformation.
   *
   * @dataProvider lineEndingsDataProvider
   *
   * @param $configuration
   *   The configuration of the migration process plugin.
   * @param $value
   *   The source value for the migration process plugin.
   * @param $expected
   *   The expected value of the migration process plugin.
   */
  public function testNewLineToLineBreak($configuration, $value, $expected) {
    $this->plugin = new NewlineToLineBreak($configuration, 'nl2br', []);
    $actual = $this->plugin->transform($value, $this->migrateExecutable, $this->row, 'body/value');

    $this->assertEquals($expected, $actual);
  }

  /**
   * Data provider of line endings.
   *
   * @return array
   *  Array of line endings actual/expected values.
   */
  public function lineEndingsDataProvider() {
    return [
      'new_line' => [
        'configuration' => [
          'is_xhtml' => TRUE,
        ],
        'value' => '\n',
        'expected' => '<br />',
      ],
    ];
  }
}
