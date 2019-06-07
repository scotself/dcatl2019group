<?php

namespace Drupal\dgc_utils\Commands;

use Drush\Commands\DrushCommands;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\key\KeyRepository;
use Firebase\JWT\JWT;
use OAuth\Common\Http\Uri\Uri;
use Drupal\salesforce\Client\HttpClientWrapper;
use Drupal\salesforce\Storage\SalesforceAuthTokenStorage;

/**
 * A Drush commands file.
 */
class DgcCommands extends DrushCommands {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $etm;

  /**
   * The key repository.
   *
   * @var \Drupal\key\KeyRepository
   */
  protected $keyRepository;

  /**
   * The http request client.
   *
   * @var \Drupal\salesforce\Client\HttpClientWrapper
   */
  protected $httpClient;

  /**
   * The auth token storage service.
   *
   * @var \Drupal\salesforce\Storage\SalesforceAuthTokenStorage
   */
  protected $authTokenStorage;

  /**
   * SalesforceCommandsBase constructor.
   *
   * @param \Drupal\key\KeyRepository $key_repository
   *   SF client.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $etm
   *   Entity type manager.
   * @param \Drupal\salesforce\Client\HttpClientWrapper $http_client
   *   The http request client.
   * @param \Drupal\salesforce\Storage\SalesforceAuthTokenStorage $auth_token_storage
   *   The auth token storage service.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(KeyRepository $key_repository, EntityTypeManagerInterface $etm, HttpClientWrapper $http_client, SalesforceAuthTokenStorage $auth_token_storage) {
    $this->keyRepository = $key_repository;
    $this->etm = $etm;
    $this->httpClient = $http_client;
    $this->authTokenStorage = $auth_token_storage;
  }

  /**
   * Retrieve all the data for an object with a specific ID.
   *
   * @param string $id
   *   A auth provider ID.
   *
   * @command dgc_utils:auth-provider
   * @aliases vbap
   */
  public function readObject($id) {
    $jwt = $this->etm->getStorage('salesforce_auth')->load($id);
    $oauth = $jwt->getPlugin();
    if (!$oauth->hasAccessToken()) {
      $key = $this->keyRepository->getKey($oauth->getCredentials()->getKeyId())->getKeyValue();
      $cred = $oauth->getCredentials();
      $token = [
        'iss' => $cred->getConsumerKey(),
        'sub' => $cred->getLoginUser(),
        'aud' => $cred->getLoginUrl(),
        'exp' => \Drupal::time()->getCurrentTime() + 60,
      ];
      $oauth->requestAccessToken(JWT::encode($token, $key, 'RS256'));
    }
    $token = $oauth->getAccessToken();
    $headers = [
      'Authorization' => 'OAuth ' . $token->getAccessToken(),
      'Content-type' => 'application/json',
    ];
    $data = $token->getExtraParams();
    $response = $this->httpClient->retrieveResponse(new Uri($data['id']), [], $headers);
    $identity = json_decode($response, TRUE);
    $this->authTokenStorage->storeIdentity($oauth->service(), $identity);
    $this->output()->writeln('Authorized ¯\_(ツ)_/¯');
  }

}
