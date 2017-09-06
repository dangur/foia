<?php

namespace Api\Test;

use GuzzleHttp\Client;

/**
 * Class ApiTest.
 *
 * @package Api\Test
 */
class ApiTest extends \PHPUnit_Framework_TestCase {

  private $http;

  /**
   * Establishes base URI.
   */
  public function setUp() {
    $this->http = new Client(['base_uri' => 'https://api.foia.gov']);
  }

  /**
   * Tests production environment status and content type.
   */
  public function testGet() {

    $response = $this->http->request('GET', '/jsonapi/node/agency_component', ['_format' => 'api_json']);
    $this->assertEquals(200, $response->getStatusCode());

    $contentType = $response->getHeaders()["Content-Type"][0];
    $this->assertEquals("application/json", $contentType);

    $webform = \GuzzleHttp\json_decode($response->getBody())->{"field_request_submission_form"};
    $this->assertRegexp('/webform/', $webform;

  }

  /**
   * Tears down.
   */
  public function tearDown() {
    $this->http = NULL;
  }

}
