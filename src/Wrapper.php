<?php
declare(strict_types=1);
namespace Playstation;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class Wrapper
{
	/** @var Client $client */
	private $client;

	/** @var array $defaults */
	private $defaults;

	/**
	 * __construct
	 *
	 * @param  array $defaults
	 * @return void
	 */
	public function __construct(array $defaults = [])
	{
		$this->defaults = $defaults;
		$this->client = new Client();
	}

	/**
	 * @param  string $bearer
	 * @return self
	 */
	public function setBearer(string $bearer): self
	{
		$this->bearer = $bearer;
		return $this;
	}

	/**
	 * @param  string $path
	 * @param  array $body
	 * @return array|Response
	 */
	public function get(string $path, array $body = [], array $headers = [])
	{
		$path .= strpos($path, '?') === false ? '?' : '&';
		$path .= urldecode(http_build_query($this->touchBody($body)));

		return $this->request('GET', $path, [
			'allow_redirects' => false,
			'headers' => $this->getHeaders($headers)
		]);
	}

	/**
	 * @param  string $path
	 * @param  array $body
	 * @param  array $headers
	 * @return array|Response
	 */
	public function post(string $path, array $body = [], array $headers = [])
	{
		return $this->request('POST', $path, [
			'allow_redirects' => false,
			'form_params' => $this->touchBody($body),
			'headers' => $this->getHeaders($headers)
		]);
	}

	/**
	 * @param  string $method
	 * @param  string $path
	 * @param  array $attrs
	 * @return array|Response
	 */
	private function request(string $method, string $path, array $attrs)
	{
		return $this->formatResponse(
			$this->client->request($method, $path, $attrs)
		);
	}

	/**
	 * @param  array $headers
	 * @return array
	 */
	private function getHeaders(array $headers = []): array
	{
		if ($this->bearer !== null) {
			$headers = array_merge($headers, [
				'Authorization' => "Bearer {$this->bearer}"
			]);
		}

		return $headers;
	}

	/**
	 * @param  array $body
	 * @return array
	 */
	private function touchBody(array $body): array
	{
		return array_merge($this->defaults, array_filter($body));
	}

	/**
	 * @param  Response $response
	 * @return array|Response
	 */
	private function formatResponse(Response $response)
	{
		$contents = $response->getBody()->getContents();
		$data = json_decode($contents, true);

		if (json_last_error() === JSON_ERROR_NONE) {
			return $data;
		}

		return $response;
	}
}
