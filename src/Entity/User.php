<?php
declare(strict_types=1);
namespace Playstation\Entity;

use Playstation\Wrapper;

class User
{
	/** @var Wrapper $client */
	private $client;

	/** @var ?string $username */
	private $username;

	/**
	 * @param  string $username
	 * @return void
	 */
	public function __construct(Wrapper $client, string $username = null)
	{
		$this->client = $client;
		$this->username = $username;
	}

	/**
	 * getGames
	 *
	 * @return array
	 */
	public function games(): array
	{
		$games = [];
		$response = $this->client->get(
			'https://us-tpy.np.community.playstation.net/trophy/v1/trophyTitles',
			[
				'fields' => '@default,trophyTitleSmallIconUrl',
				'platform' => 'PS3,PS4,PSVITA',
				'limit' => 100,
				'offset' => 0,
				'comparedUser' => $this->getUsername(),
				'npLanguage' => 'en'
			]
		);

		foreach ($response['trophyTitles'] as $game) {
			$games[] = new Game($this->client, $game, $this);
		}

		return $games;
	}

	/**
	 * @return string
	 */
	public function getUsername(): ?string
	{
		return $this->username;
	}
}
