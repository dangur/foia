<?php

namespace Drupal\Tests\foia_request\Kernel;

use Drupal\foia_request\Entity\FoiaRequest;
use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;

/**
 * Class FoiaRequestTest.
 *
 * @package Drupal\Tests\foia_request\Kernel
 */
class FoiaRequestTest extends EntityKernelTestBase {

  public static $modules = ['foia_request', 'options'];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->installConfig('foia_request');
    $this->installEntitySchema('foia_request');

  }

  /**
   * Tests FOIA Request creation.
   */
  public function testFoiaRequest() {

    $foiaRequest = FoiaRequest::create([
      'field_agency_component' => 1,
      'field_case_management_id' => '1',
      'field_error_code' => '1',
      'field_error_message' => 'error message',
      'field_http_code' => 200,
      'field_requester_email' => 'requester@example.com',
      'field_submission_method' => 'api',
      'field_submission_time' => 1508432427,
      'field_tracking_number' => '1',
      'field_webform_submission_id' => 1,
    ]);
print_r($foiaRequest->getSubmissionMethod());


    $this->assertEquals('foia_request', $foiaRequest->getEntityTypeId());

    $foiaEntity = \Drupal::entityTypeManager()
      ->getStorage('foia_request')
      ->loadByProperties(['field_case_management_id' => '1']);
    print_r($foiaEntity);

    /*$query = \Drupal::entityQuery('foia_request')->execute();
    print_r($query);*/

    //print_r($foiaRequest->get('field_agency_component')->getValue());
    //print_r($time);

  }

}
