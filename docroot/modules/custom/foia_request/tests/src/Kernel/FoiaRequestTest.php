<?php

namespace Drupal\Tests\foia_request\Kernel;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\foia_request\Entity\FoiaRequest;
use Drupal\foia_request\Entity\FoiaRequestInterface;
use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;

/**
 * Class FoiaRequestTest.
 *
 * @package Drupal\Tests\foia_request\Kernel
 */
class FoiaRequestTest extends EntityKernelTestBase {

  public static $modules = ['foia_request', 'options', 'field_permissions'];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->installConfig('system');
    $this->installEntitySchema('foia_request');

    $path = DRUPAL_ROOT . '/' . drupal_get_path('module', 'foia_request') . '/config/install/';
    $yml = yaml_parse(file_get_contents($path . 'field.storage.foia_request.field_submission_method.yml'));
    FieldStorageConfig::create($yml)->save();
    $yml = yaml_parse(file_get_contents($path . 'field.field.foia_request.foia_request.field_submission_method.yml'));
    print_r($yml);
    FieldConfig::create($yml)->save();

  }

  /**
   * Tests FOIA Requests are created with appropriate defaults.
   */
  public function testFoiaRequest() {
    $foiaRequest = FoiaRequest::create();

    $this->assertEquals('foia_request', $foiaRequest->getEntityTypeId());
    $this->assertEquals(FoiaRequestInterface::STATUS_QUEUED, $foiaRequest->getRequestStatus());
    $this->assertNotEmpty($foiaRequest->get('created')->value);

  }

  /**
   * Tests that invalid request status becomes default and submitted passes.
   */
  public function testSetRequestStatus() {
    $foiaRequest = FoiaRequest::create();

    $foiaRequest->setRequestStatus(5);
    $this->assertEquals(FoiaRequestInterface::STATUS_QUEUED, $foiaRequest->getRequestStatus());

    $foiaRequest->setRequestStatus(FoiaRequestInterface::STATUS_SUBMITTED);
    $this->assertEquals(FoiaRequestInterface::STATUS_SUBMITTED, $foiaRequest->getRequestStatus());
  }

  /**
   * Tests that invalid submission method defaults to email & api method passes.
   */
  public function testSetSubmissionMethod() {

    $path = DRUPAL_ROOT . '/' . drupal_get_path('module', 'foia_request') . '/config/install/';
    $yml = yaml_parse(file_get_contents($path . 'field.storage.foia_request.field_http_code.yml'));
    FieldStorageConfig::create($yml)->save();
    $yml = yaml_parse(file_get_contents($path . 'field.field.foia_request.foia_request.field_http_code.yml'));
    FieldConfig::create($yml)->save();

    FoiaRequest::create(['field_http_code' => 418])->save();

    // Get the request.
    $query = \Drupal::entityQuery('foia_request')
      ->condition('field_http_code', 418);
    $foiaRequestArray = $query->execute();

    $foiaRequestEntity = FoiaRequest::load(current($foiaRequestArray));

    $foiaRequestEntity->setSubmissionMethod(5);
    $this->assertEquals(FoiaRequestInterface::METHOD_EMAIL, $foiaRequestEntity->getSubmissionMethod());

    $foiaRequestEntity->setSubmissionMethod(FoiaRequestInterface::METHOD_API);
    $this->assertEquals(FoiaRequestInterface::METHOD_API, $foiaRequestEntity->getSubmissionMethod());

  }

}
