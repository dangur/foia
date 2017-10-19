<?php

namespace Drupal\Tests\foia_request\Kernel;

use Drupal\foia_request\Entity\FoiaRequest;
use Drupal\foia_request\Entity\FoiaRequestInterface;
use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;

/**
 * Class FoiaRequestTest.
 *
 * @package Drupal\Tests\foia_request\Kernel
 */
class FoiaRequestTest extends EntityKernelTestBase implements FoiaRequestInterface {

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

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
      'field_submission_id' => 1,
      'field_submission_method' => 'api',
      'field_tracking_number' => '1',
    ]);

  }

}
