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
    $this->http = new Client(['base_uri' => 'https://httpbin.org/']);
  }

  /**
   * Tests status and content type.
   */
  public function testGet() {
    $response = $this->http->request('GET', 'user-agent');
    $this->assertEquals(200, $response->getStatusCode());

    $contentType = $response->getHeaders()["Content-Type"][0];
    $this->assertEquals("application/json", $contentType);

    $userAgent = \GuzzleHttp\json_decode($response->getBody())->{"user-agent"};
    $this->assertRegexp('/Guzzle/', $userAgent);
  }

  /**
   * Tears down.
   */
  public function tearDown() {
    $this->http = NULL;
  }

}
