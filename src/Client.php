<?php
declare(strict_types=1);
namespace Playstation;

use Playstation\Entity\User;
use Playstation\Support\Fluent;
use Carbon\Carbon;

class Client extends Fluent
{
	private const AUTH_API = 'https://auth.api.sonyentertainmentnetwork.com/2.0';
	private const CLIENT_ID = 'ebee17ac-99fd-487c-9b1e-18ef50c39ab5';
	private const CLIENT_SECRET = 'e4Ru_s*LrL4_B2BD';
	private const DUID = '0000000d00040080027BC1C3FBB84112BFC9A4300A78E96A';
	private const SCOPE = 'kamaji:get_players_met kamaji:get_account_hash kamaji:activity_feed_submit_feed_story kamaji:activity_feed_internal_feed_submit_story kamaji:activity_feed_get_news_feed kamaji:communities kamaji:game_list kamaji:ugc:distributor oauth:manage_device_usercodes psn:sceapp user:account.profile.get user:account.attributes.validate user:account.settings.privacy.get kamaji:activity_feed_set_feed_privacy kamaji:satchel kamaji:satchel_delete user:account.profile.update';

	/** @var Wrapper $client */
	private $client;

	/**
	 * @param  array $defaults
	 * @return void
	 */
	public function __construct(array $defaults = [])
	{
		$this->client = new Wrapper($defaults);
	}

	/**
	 * @param  string $token
	 * @param  string $code
	 * @return self
	 */
	public function login(string $token = null, string $code = null): self
	{
		if ($code === null) {
			$response = $this->client->post(self::AUTH_API . '/oauth/token', [
				'app_context' => 'inapp_ios',
				'client_id' => self::CLIENT_ID,
				'client_secret' => self::CLIENT_SECRET,
				'refresh_token' => $token,
				'duid' => self::DUID,
				'grant_type' => 'refresh_token',
				'scope' => self::SCOPE
			]);
		} else {
			$response = $this->client->post(self::AUTH_API . '/ssocookie', [
				'authentication_type' => 'two_step',
				'ticket_uuid' => $token,
				'code' => $code,
				'client_id' => self::CLIENT_ID
			]);

			$response = $this->client->get(
				self::AUTH_API . '/oauth/authorize',
				[
					'duid' => self::DUID,
					'client_id' => self::CLIENT_ID,
					'response_type' => 'code',
					'scope' => self::SCOPE,
					'redirect_uri' =>
						'com.playstation.PlayStationApp://redirect'
				],
				[
					'Cookie' => "npsso={$response['npsso']}"
				]
			);

			if ($response instanceof Response === false) {
				throw new \Exception('Unexpected response');
			}

			$grant = $response->getHeaderLine('X-NP-GRANT-CODE');

			if (empty($grant)) {
				throw new \Exception('Unable to get X-NP-GRANT-CODE');
			}

			$response = $this->client->post(self::AUTH_API . '/oauth/token', [
				'client_id' => self::CLIENT_ID,
				'client_secret' => self::CLIENT_SECRET,
				'duid' => self::DUID,
				'scope' => self::SCOPE,
				'redirect_uri' => 'com.playstation.PlayStationApp://redirect',
				'code' => $grant,
				'grant_type' => 'authorization_code'
			]);
		}

		$this->handleResponse($response);
		$this->client->setBearer($this->getAccessToken());

		return $this;
	}

	/**
	 * @param  array $response
	 * @return self
	 */
	private function handleResponse(array $response): self
	{
		foreach (['access_token', 'refresh_token', 'expires_in'] as $key) {
			$this->{$key} = $response[$key];
		}

		return $this;
	}

	/**
	 * @return void
	 */
	public function user(string $username): User
	{
		return new User($this->client, $username);
	}

	/**
	 * @return ?string
	 */
	public function getAccessToken(): ?string
	{
		return $this->access_token;
	}

	/**
	 * @return ?string
	 */
	public function getRefreshToken(): ?string
	{
		return $this->refresh_token;
	}

	/**
	 * @return Carbon
	 */
	public function getTokenExpiration(): Carbon
	{
		return Carbon::now()->addSeconds($this->expires_in);
	}
}
