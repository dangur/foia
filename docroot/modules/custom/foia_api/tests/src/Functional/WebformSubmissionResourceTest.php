<?php

namespace Drupal\Tests\foia_api\Functional;


use Drupal\Tests\BrowserTestBase;
use \GuzzleHttp;
use Drupal\foia_api\Plugin\rest\resource;

class WebformSubmissionResourceTest extends BrowserTestBase {

  /**
   * @var
   */
  private $http;

  private $resource;

  /**
   * Tests Posting.
   *
   * @dataProvider providerWebformSubmissionResource
   */
  public function testPost($id, $first_name, $last_name, $email, $request_description, $request_fee_waiver, $request_expedited_processing) {


    $host = 'http://local.dojfoia.gov';

    $this->http = new GuzzleHttp\Client(['base_uri' => $host]);

    $this->resource = resource\WebformSubmissionResource::create(
      $container,
      $configuration,
      $plugin_id,
      $plugin_definition,
    );

    $data = array(
      'id' => $id,
      'first_name' => $first_name,
      'last_name' => $last_name,
      'email' => $email,
      'request_description' => $request_description,
      'request_fee_waiver' => $request_fee_waiver,
      'request_expedited_processing' => $request_expedited_processing
    );
    print_r('data');
    print_r($data);
    $json = GuzzleHttp\json_encode($data);
print_r('json');
print_r($json);

    $request = $this->resource->post($json);//('api/webform/submit', ['Content-Type' => 'application/json', 'json' => [$json], 'http_errors' => FALSE]);
    $this->assertEquals(400, $request->getStatusCode());
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