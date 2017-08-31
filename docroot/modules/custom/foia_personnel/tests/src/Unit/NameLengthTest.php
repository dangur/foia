<?php

namespace Drupal\Tests\foia_personnel\Unit;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Tests\Core\Entity\EntityUnitTest;
use Drupal\Tests\UnitTestCase;
use Drupal\Core\Field;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\simpletest\WebTestBase;

/**
 * Class NameLengthTest
 *
 * @package Drupal\Tests\foia_personnel\Unit
 *
 * @group foia_personnel
 */
class NameLengthTest extends FieldTestBase {
  public function testAddMaxLengthName() {

    /**
     * Create a mock.
     */
    $query = $this->getMockBuilder('\Drupal\Core\Entity\Query\QueryInterface')
      ->disableOriginalConstructor()
      ->getMock();
    //print_r($query);


    /**
     * Mock method.
     */
    $query->expects($this->any())
      ->method('execute')
      ->willReturn([1 => 1]);
    //print_r($query);

    /*$entity = Entity::create(array(
      'name' => 'Uncle Hogram',
    ));*/
    //$entity = entity_test_entity_base_field_info('foia_personnel');
    //$test = EntityUnitTest::assertAttributeEquals('otto', 'name', 'foia_personnel');
  /*$entity = \Drupal::entityQuery('foia_personnel');
    print_r($entity);*/
    /*$name = 'MAVIurAaQkyfWwpWImuhpKMePsWzmOmomReikiPzxNMmgTsarAuYfYPBfibQydEBoGpwmMzOANkeRthCFsekqfGGqLLqbEzZNwHnpLLpVQSDribbmHhCaFJZueCDXPoTzpyScWynylUMoHCllEWiJuLFTDrgwcDbbwnHyBzzygLjIDHFmHBvjGnwLxArLZVbBYDxkaCvHvQWYtMjcsBtrbVwCWnDAWUXvssUJKmvOgneNBamyzrvrKImLEqolZf';
    $this->assertEquals('name', $name);*/
    /*$map = field_entity_field_storage_info('foia_personnel');
    print_r($map);*/

    $field_definition = $this->getMockBuilder('\Drupal\Core\Field\BaseFieldDefinition')
      ->getMock();
    $field_definition->expects($this->any())
      ->method('get')
      ->willReturnMap([['field_name', 'name'], ['field_type', 'text'], ['field_settings', 'settings']]);
    //print_r($field_definition);

    //$this->assertEquals('name', $field_definition->get('field_name'));

  $this->assertFieldValues('foia_personnel', 'name', Language::LANGCODE_NOT_SPECIFIED, array('name'));

    /**
     * Assert that a field has the expected values in an entity.
     *
     * This function only checks a single column in the field values.
     *
     * @param EntityInterface $entity
     *   The entity to test.
     * @param $field_name
     *   The name of the field to test
     * @param $expected_values
     *   The array of expected values.
     * @param $langcode
     *   (Optional) The language code for the values. Defaults to
     *   \Drupal\Core\Language\LanguageInterface::LANGCODE_DEFAULT.
     * @param $column
     *   (Optional) The name of the column to check. Defaults to 'value'.
     */
  public function assertFieldValues(EntityInterface $entity, $field_name, $expected_values, $langcode = LanguageInterface::LANGCODE_DEFAULT, $column = 'value') {
      // Re-load the entity to make sure we have the latest changes.
      $storage = $this->container->get('entity_type.manager')
        ->getStorage($entity->getEntityTypeId());
      $storage->resetCache([$entity->id()]);
      $e = $storage->load($entity->id());

      $field = $values = $e->getTranslation($langcode)->$field_name;
      // Filter out empty values so that they don't mess with the assertions.
      $field->filterEmptyItems();
      $values = $field->getValue();
      $this->assertEqual(count($values), count($expected_values), 'Expected number of values were saved.');
      foreach ($expected_values as $key => $value) {
        $this->assertEqual($values[$key][$column], $value, format_string('Value @value was saved correctly.', ['@value' => $value]));
      }
    }

_generateTest

  }
}
