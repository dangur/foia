<?php

namespace Drupal\Tests\foia_webform\Kernel;

use Drupal\Component\Serialization\Json;
use Drupal\foia_webform\FoiaSubmissionServiceApi;
use Drupal\KernelTests\KernelTestBase;
use Drupal\node\NodeTypeInterface;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\webform\Entity\Webform;
use Drupal\webform\Entity\WebformSubmission;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\node\Entity\NodeType;
use Drupal\node\Entity\Node;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Drupal\foia_webform\AgencyLookupService;
use Drupal\taxonomy\Entity\Term;

/**
 * Class FoiaSubmissionServiceApiTest.
 *
 * Tests the FoiaSubmissionServiceApi.
 *
 * @package Drupal\Tests\foia_webform\Kernel
 */
class FoiaSubmissionServiceApiTest extends KernelTestBase {

  /**
   * Test agency.
   *
   * @var \Drupal\taxonomy\Entity\Term
   */
  protected $agency;

  /**
   * Test webform to submit against.
   *
   * @var \Drupal\webform\WebformInterface
   */
  protected $webform;

  /**
   * Test webform submission.
   *
   * @var \Drupal\webform\WebformSubmissionInterface
   */
  protected $webformSubmission;

  /**
   * Test agency component we're submitting to.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected $agencyComponent;

  /**
   * Guzzle client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * Agency lookup service.
   *
   * @var \Drupal\foia_webform\AgencyLookupServiceInterface
   */
  protected $agencyLookupService;

  /**
   * Logger service.
   *
   * @var \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $logger;

  /**
   * Submission Service Api.
   *
   * @var \Drupal\foia_webform\FoiaSubmissionServiceApi
   */
  protected $submissionServiceApi;


  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'webform_template',
    'webform',
    'system',
    'user',
    'foia_webform',
    'node',
    'field',
    'taxonomy',
    'field_permissions',
    'text',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installConfig(['webform', 'webform_template', 'foia_webform']);
    $this->installEntitySchema('webform_submission');
    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
    $this->installEntitySchema('taxonomy_term');

    // Creates webform and specifies to use the template fields.
    $webformWithTemplate = Webform::create(['id' => 'webform_with_template']);
    $webformWithTemplate->set('foia_template', 1);
    $webformWithTemplate->save();
    $this->webform = $webformWithTemplate;

    // Check creating a submission with default data.
    $webformSubmission = WebformSubmission::create(['webform_id' => $this->webform->id(), 'data' => ['custom' => 'value']]);
    $webformSubmission->save();
    $this->webformSubmission = $webformSubmission;

    Vocabulary::create([
      'name' => 'Agency',
      'vid' => 'agency',
    ])->save();
    Term::create([
      'name' => 'A Test Agency',
      'vid' => 'agency',
    ])->save();

    $agency = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties(['name' => 'A Test Agency']);

    $this->agency = reset($agency);

    $this->setupAgencyComponent();
    $this->setupAgencyLookupServiceMock();
    $this->setupLoggerMock();
  }

  /**
   * Tests receiving an error response from an agency component.
   */
  public function testErrorResponseFromComponent() {
    $responseContents = [
      'code' => 'A234',
      'message' => 'agency component not found',
      'description' => 'description of the error that is specific to the case management system',
    ];
    $this->setupHttpClientErrorMock($responseContents, 404);
    $this->submissionServiceApi = new FoiaSubmissionServiceApi($this->httpClient, $this->agencyLookupService, $this->logger);
    $validSubmission = $this->submissionServiceApi->sendSubmissionToComponent($this->webformSubmission, $this->webform, $this->agencyComponent);
    $errorMessage = $this->submissionServiceApi->getSubmissionErrors();
    $this->assertEquals(404, $errorMessage['http_code']);
    $this->assertEquals($responseContents['message'], $errorMessage['message']);

  }

  /**
   * Adds agency component content type.
   */
  protected function setupAgencyComponent() {
    $agencyComponentTypeDefinition = [
      'type' => 'agency_component',
      'name' => t('Agency Component'),
      'description' => 'An agency component to which a request can be sent and which will be fulfilling requests.',
    ];
    $agencyComponentType = NodeType::create($agencyComponentTypeDefinition);
    $agencyComponentType->save();
    $this->addFieldsToComponentType($agencyComponentType);
    $this->createAgencyComponentNode();
  }

  /**
   * Adds fields to agency component content type.
   */
  protected function addFieldsToComponentType(NodeTypeInterface $agencyComponentType) {
    $this->addFieldToComponentType('field_request_submission_form');
    $this->addFieldToComponentType('field_submission_api');
    $this->addFieldToComponentType('field_agency');
  }

  /**
   * Adds field to agency component content type.
   */
  protected function addFieldToComponentType($fieldName) {
    $path = '/var/www/dojfoia/config/default';
    $yml = yaml_parse(file_get_contents($path . "/field.storage.node.{$fieldName}.yml"));
    FieldStorageConfig::create($yml)->save();
    $yml = yaml_parse(file_get_contents($path . "/field.field.node.agency_component.{$fieldName}.yml"));
    FieldConfig::create($yml)->save();
  }

  /**
   * Creates an agency component entity.
   */
  protected function createAgencyComponentNode() {
    /** @var \Drupal\node\NodeInterface $agencyComponent */
    $agencyComponent = Node::create([
      'type' => 'agency_component',
      'title' => t('A Test Agency Component'),
      'field_portal_submission_format' => 'api',
      'field_submission_api' => 'http://atest.com',
      'field_request_submission_form' => [
        'target_id' => $this->webform->id(),
      ],
      'field_agency' => [
        'target_id' => $this->agency->id(),
      ],
    ]);
    $agencyComponent->save();
    $this->agencyComponent = $agencyComponent;
  }

  /**
   * Sets up Guzzle mock.
   */
  protected function setupHttpClientErrorMock(array $responseContents, $responseStatusCode) {
    $testAgencyErrorResponse = Json::encode($responseContents);
    $guzzleMock = new MockHandler([
      new RequestException("Error communicating with component", new Request('POST', 'test'), new Response($responseStatusCode, [], $testAgencyErrorResponse)),
    ]);

    $guzzleHandlerMock = HandlerStack::create($guzzleMock);
    $this->httpClient = new Client(['handler' => $guzzleHandlerMock]);
  }

  /**
   * Sets up agency lookup service mock.
   */
  protected function setupAgencyLookupServiceMock() {
    $entityTypeManager = \Drupal::entityTypeManager();
    $this->agencyLookupService = new AgencyLookupService($entityTypeManager);
  }

  /**
   * Sets up logger mock.
   */
  protected function setupLoggerMock() {
    $this->logger = $this->getMock('\Psr\Log\LoggerInterface');
  }

}
