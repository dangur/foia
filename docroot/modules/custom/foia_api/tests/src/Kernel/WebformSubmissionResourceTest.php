<?php

namespace Drupal\Tests\foia_api\Kernel;

use Drupal\Tests\token\Kernel\KernelTestBase;
use \GuzzleHttp;
use \GuzzleHttp\Exception\RequestException;

/**
 * Class WebformSubmissionResourceTest.
 *
 * Tests webform submission generation via API.
 *
 * @package Drupal\Tests\foia_api\Kernel
 *
 * @group api
 */
class WebformSubmissionResourceTest extends KernelTestBase {

  public static $modules = ['foia_api'];
  private $http;

  /**
   * @{inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installConfig('system', 'foia_api');

    // @todo get url from var.
    $host = 'http://local.dojfoia.gov';//\Drupal::request()->getSchemeAndHttpHost();
    $this->http = new GuzzleHttp\Client(['base_uri' => $host]);
  }

  /**
   * Tests Posting.
   *
   * @dataProvider providerWebformSubmissionResource
   */
  public function testPost($id, $first_name, $last_name, $email, $request_description, $request_fee_waiver, $request_expedited_processing) {

    $data = array(
      'id' => $id,
      'first_name' => $first_name,
      'last_name' => $last_name,
      'email' => $email,
      'request_description' => $request_description,
      'request_fee_waiver' => $request_fee_waiver,
      'request_expedited_processing' => $request_expedited_processing
    );

    $json = GuzzleHttp\json_encode($data);
    print_r($json);
    //$response = $this->http->request('POST', 'api/webform/submit');
    $request = $this->http->post('api/webform/submit', ['json' => [$data]]);
    $response = $request->send();


    $this->assertEquals(201, $response->getStatusCode());

  }

  /**
   * Tears environment down.
   */
  public function tearDown() {
    $this->http = NULL;
  }

  /**
   * Provides data for the WebformSubmissionResourceTest method.
   *
   * @return array
   */
  public function providerWebformSubmissionResource() {
    return [
      ['basic_request_submission_form', 'Another', 'Test', 'test@test.com', 'The best request', 'yes', 'no']
    ];
  }

}
