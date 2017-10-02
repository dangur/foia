<?php

namespace Drupal\Tests\foia_api\Functional;


use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Url;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Tests\EntityReference\EntityReferenceTestTrait;
use Drupal\file\Entity\File;
use Drupal\taxonomy\Entity\Term;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\image\Kernel\ImageFieldCreationTrait;
use Drupal\user\Entity\Role;
use Drupal\user\RoleInterface;
use Drupal\webform\Entity\Webform;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

use Drupal\Component\Serialization\Json;
use Drupal\Tests\jsonapi\Functional\JsonApiFunctionalTestBase;

class FoiaApiFunctionalTest extends BrowserTestBase{

  use EntityReferenceTestTrait;
  use ImageFieldCreationTrait;

  protected static $modules = [
    'basic_auth',
    'jsonapi',
    'serialization',
    'node',
    'image',
    'taxonomy',
    'link',
    'foia_api',
    'webform',
    'webform_template',
    'rest',
  ];

  /**
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Set up a HTTP client that accepts relative URLs.
    $this->httpClient = $this->container->get('http_client_factory')
      ->fromOptions(['base_uri' => $this->baseUrl]);

    // Create node types
    if ($this->profile != 'standard') {
      $this->drupalCreateContentType([
        'type' => 'agency_component',
        'name' => 'Agency Component',
      ]);
    }

    // Setup vocabulary
    Vocabulary::create([
      'vid' => 'agency',
      'name' => 'Agency',
    ])->save();

    // Add Agency to Agency Component
    $this->createEntityReferenceField(
      'node',
      'agency_component',
      'field_agency',
      'Agency',
      'taxonomy_term',
      'default',
      [
        'target_bundles' => [
          'agency' => 'agency',
        ],
        'auto_create' => TRUE,
      ],
      FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED
    );

    // Add Request Submission Form to Agency Component
    $this->createEntityReferenceField(
      'node',
      'agency_component',
      'field_request_submission_form',
      'Request Submission Form	',
      'webform',
      'default',
      [
        'target_bundles' => [
          'basic_request_submission_form' => 'basic_request_submission_form',
        ],
      ],
      FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED
    );

    $this->grantPermissions(Role::load(RoleInterface::ANONYMOUS_ID), [
      'View published content',
      'Access POST on Webform submission resource',
    ]);

    //$this->installConfig(['webform', 'webform_template']);

    // Gets template elements from module config.
    $config = \Drupal::config('webform_template.settings')->get('webform_template_elements');
    $templateElements = yaml_parse($config);
//print_r($templateElements);
    // Creates webform and specifies to use the template fields.
    $templateElements['id'] = 'basic_request_submission_form';
    print_r($templateElements);
    $webformWithTemplate = Webform::create($templateElements);
    //$webformWithTemplate->set('foia_template', 1);
    //print_r($webformWithTemplate);
    $webformWithTemplate->save();

    $term = Term::create([
      'vid' => 'agency',
      'name' => 'test',
    ]);
    $term->save();

    $node = $this->drupalCreateNode($values);
    $node->save();

    drupal_flush_all_caches();

  }


    /**
   *
   */
  public function testPost () {
    //$this->createDefaultContent(5,5,FALSE,FALSE,FALSE);
    //$this->createContent();
    $collection_url = Url::fromUri('internal:/api/webform/submit');
    $body = [
      //'data' => [
        'id' => 'basic_request_submission_form',
        'email' => 'user@example.com',
        'first_name' => 'Furst',
        'last_name' => 'Lust',
        'request_fee_waiver' => 'no',
        'request_expedited_processing' => 'no',
        'request_description' => 'desc'
      //]
    ];
    //print_r($collection_url);
    $response = $this->request('POST', $collection_url, [
      'body' => Json::encode($body),
      'headers' => ['Content-Type' => 'application/vnd.api+json'],
    ]);
    //print_r($response);
    $created_response = Json::decode($response->getBody()->__toString());
    $this->assertEquals(201, $response->getStatusCode());
  }

  /**
   * Performs a HTTP request. Wraps the Guzzle HTTP client.
   *
   * Why wrap the Guzzle HTTP client? Because any error response is returned via
   * an exception, which would make the tests unnecessarily complex to read.
   *
   * @see \GuzzleHttp\ClientInterface::request()
   *
   * @param string $method
   *   HTTP method.
   * @param \Drupal\Core\Url $url
   *   URL to request.
   * @param array $request_options
   *   Request options to apply.
   *
   * @return \Psr\Http\Message\ResponseInterface
   */
  protected function request($method, Url $url, array $request_options) {
    /*print_r($method);
    print_r($url);
    print_r($request_options);*/
    try {
      $response = $this->httpClient->request($method, $url->toString(), $request_options);
    }
    catch (ClientException $e) {
      $response = $e->getResponse();
    }
    catch (ServerException $e) {
      $response = $e->getResponse();
    }

    return $response;
  }

  protected function createContent() {

    $elements = \Drupal::configFactory()->getEditable('webform_template.settings')->get('webform_template_elements');//ConfigFactory::getEditable(webform_template.settings)->webform_template_elements
print_r($elements);
    $webform = Webform::create($elements);
    $webform->save();
  }

  function foia_api_allowed_values_function()
}

