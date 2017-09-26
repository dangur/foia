<?php

namespace Drupal\Tests\foia_api\Functional;

use Drupal\Tests\rest\Functional\ResourceTestBase;

/**
 * Class WebformSubmissionResourceTest.
 *
 * @package Drupal\Tests\foia_api\Functional
 */
class WebformSubmissionResourceTest extends ResourceTestBase {

  private $http;

  /**
   * Tests webform submission resource.
   *
   * @dataProvider providerWebformSubmissionResource
   */
  public function testWebformSubmissionResource($id, $first_name, $last_name, $email, $request_description, $request_fee_waiver, $request_expedited_processing) {


    //$base_url = global $base_url;


    $data = array(
      'id' => $id,
      'first_name' => $first_name,
      'last_name' => $last_name,
      'email' => $email,
      'request_description' => $request_description,
      'request_fee_waiver' => $request_fee_waiver,
      'request_expedited_processing' => $request_expedited_processing
    );

    $json = \GuzzleHttp\json_encode($data);

    $request = $this->http->request('POST', 'http://local.dojfoia.gov/api/webform/submit', $json);

    $response = $request->send();

    $this->http->assertResourceResponse(201, 'submission_id', $response);

  }

  public function providerWebformSubmissionResource() {
    return [
      ['basic_request_submission_form', 'Another', 'Test', 'test@test.com', 'The best request', 'yes', 'no']
    ];
  }

}
