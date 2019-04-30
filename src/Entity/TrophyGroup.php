<?php
declare(strict_types=1);
namespace Playstation\Entity;

use Playstation\Support\Fluent;
use Playstation\Wrapper;

class TrophyGroup extends Fluent
{
	/** @var Wrapper $client */
	private $client;

	/** @var Game $game */
	private $game;

	/**
	 * @param  Wrapper $client
	 * @param  array $attributes
	 * @param  Game $game
	 * @return void
	 */
	public function __construct(
		Wrapper $client,
		array $attributes = [],
		Game $game
	) {
		$this->client = $client;
		$this->game = $game;

		parent::__construct($attributes);
	}

	/**
	 * @return string
	 * @SuppressWarnings(PHPMD.ShortMethodName)
	 */
	public function id(): string
	{
		return $this->trophyGroupId;
	}

	/**
	 * @return string
	 */
	public function name(): string
	{
		return $this->trophyGroupName;
	}

	/**
	 * @return string
	 */
	public function detail(): string
	{
		return $this->trophyGroupDetail;
	}

	/**
	 * @return string
	 */
	public function iconUrl(): string
	{
		return $this->trophyGroupIconUrl;
	}

	/**
	 * @return string
	 */
	public function smallIconUrl(): string
	{
		return $this->trophyGroupSmallIconUrl;
	}

	/**
	 * @return Game
	 */
	public function game(): Game
	{
		return $this->game;
	}

	/**
	 * @return int
	 */
	public function trophyCount(): int
	{
		return array_sum($this->definedTrophies);
	}

	/**
	 * @return ?string
	 */
	public function getComparedUser(): ?string
	{
		return $this->game()->getComparedUser();
	}

	/**
	 * @return bool
	 */
	public function isComparing(): bool
	{
		return $this->game()->isComparing();
	}

	/**
	 * @return array
	 */
	public function trophies(): array
	{
		$endpoint = vsprintf('/trophyTitles/%s/trophyGroups/%s/trophies', [
			$this->game()->id(),
			$this->id()
		]);

		$trophies = [];
		$response = $this->client->get(Trophy::TROPHY_ENDPOINT . $endpoint, [
			'fields' =>
				'@default,trophyRare,trophyEarnedRate,trophySmallIconUrl',
			'visibleType' => '1',
			'npLanguage' => 'en',
			'comparedUser' => $this->getComparedUser()
		]);

		foreach ($response['trophies'] as $trophy) {
			$trophies[] = new Trophy($this->client, $trophy, $this);
		}

		return $trophies;
	}
}
