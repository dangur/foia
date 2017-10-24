<?php

namespace Drupal\Tests\foia_webform\Kernel;

use Drupal\foia_request\Entity\FoiaRequest;

/**
 * Class FoiaSubmissionQueueHandlerTest
 *
 * @package Drupal\Tests\foia_webform\Kernel
 *
 * @group request
 */
class FoiaSubmissionQueueHandlerTest extends FoiaSubmissionServiceApiTest {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'foia_request',
    'options',
    'views',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installConfig(['foia_request']);
    $this->installEntitySchema('foia_request');
  }

  public function testCreateFoiaRequest() {

    $time = time();

    $foiaRequestValues = [
      'field_agency_component' => 1,
      'field_case_management_id' => '2',
      'field_error_code' => '3',
      'field_error_message' => 'To err is human',
      'field_requester_email' => 'requesteur@example.com',
      'field_response_code' => 418,
      'field_submission_method' => 'api',
      'field_submission_time' => $time,
      'field_tracking_number' => '5',
      'field_webform_submission_id' => 6,
    ];

    FoiaRequest::create($foiaRequestValues)->save();

    $query = \Drupal::entityTypeManager()->getStorage('foia_request')->getQuery();

    foreach ($foiaRequestValues as $key => $foiaRequestValue) {
      $query->condition($key, $foiaRequestValue);

    }
    $query->execute();

    $foiaRequest = FoiaRequest::load(1);

      // check that request was created
    $entityType = $foiaRequest->getEntityType();
    $entityTypeId = $entityType->id();

    $this->assertEquals('foia_request', $entityTypeId);

    // check that request has values
    $foiaRequestReturnedValues = [];
    foreach ($foiaRequestValues as $key => $value) {
      $field = $foiaRequest->$key->getValue()[0];

      if (array_key_exists('target_id', $field)){
        $foiaRequestReturnedValues[$key] = $field['target_id'];
      } elseif (array_key_exists('value', $field)) {
        $foiaRequestReturnedValues[$key] = $field['value'];
      }

    }


    $this->assertEquals($foiaRequestValues, $foiaRequestReturnedValues);
    
  }

  public function testQueueFoiaRequest () {

  }


}
