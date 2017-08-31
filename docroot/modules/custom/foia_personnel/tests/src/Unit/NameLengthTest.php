<?php

namespace Drupal\Tests\foia_personnel\Unit;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\foia_personnel\Entity\FoiaPersonnel;
use Drupal\Tests\UnitTestCase;

class FoiaPersonnelGenerate {
  public function __construct(FoiaPersonnel $foia_personnel) {
    $this->foia_personnel = $foia_personnel;
  }

  public function createFoiaPersonnel() {
    $this->foia_personel = FoiaPersonnel::create(array('name' => 'otto'));
  }
}

/**
 * Class NameLengthTest
 *
 * @package Drupal\Tests\foia_personnel\Unit
 *
 * @group foia_personnel
 */
class NameLengthTest extends UnitTestCase {
  public function testAddMaxLengthName() {

    /*$query = $this->getMockBuilder('\Drupal\Core\Entity\Query\QueryInterface')
      ->disableOriginalConstructor()
      ->getMock();*/

    //$entity_type = 'foia_personnel';
    //$entity = entity_create($entity_type, ['name' => 'otto']);
    //FoiaPersonnel::preCreate('\Drupal',)
    //$entity = FoiaPersonnel::create(array('name' => 'otto'));
    //$entity->save();

    $foia_personnel = $this->prophesize(FoiaPersonnel::class);



  }
}
