<?php
declare(strict_types=1);
namespace Playstation\Entity;

use Playstation\Support\Fluent;
use Playstation\Wrapper;

class Game extends Fluent
{
	/** @var Wrapper $client */
	private $client;

	/** @var ?User $user */
	private $user;

	/**
	 * @param  Wrapper $client
	 * @param  array $attributes
	 * @return void
	 */
	public function __construct(
		Wrapper $client,
		array $attributes = [],
		User $user = null
	) {
		$this->client = $client;
		$this->user = $user;

		parent::__construct($attributes);
	}

	/**
	 * @return string
	 * @SuppressWarnings(PHPMD.ShortMethodName)
	 */
	public function id(): string
	{
		return $this->npCommunicationId;
	}

	/**
	 * @return string
	 */
	public function name(): string
	{
		return $this->trophyTitleName;
	}

	/**
	 * @return string
	 */
	public function detail(): string
	{
		return $this->trophyTitleDetail;
	}

	/**
	 * @return string
	 */
	public function iconUrl(): string
	{
		return $this->trophyTitleIconUrl;
	}

	/**
	 * @return string
	 */
	public function smallIconUrl(): string
	{
		return $this->trophyTitleSmallIconUrl;
	}

	/**
	 * @return array
	 */
	public function platform(): array
	{
		return explode(',', $this->trophyTitlePlatfrom);
	}

	/**
	 * @return Game[]
	 */
	public function trophyGroups(): array
	{
		$endpoint = vsprintf('/trophyTitles/%s/trophyGroups', [$this->id()]);

		$groups = [];
		$response = $this->client->get(Trophy::TROPHY_ENDPOINT . $endpoint, [
			'fields' => '@default,trophyGroupSmallIconUrl',
			'npLanguage' => 'en',
			'comparedUser' => $this->getComparedUser()
		]);

		foreach ($response['trophyGroups'] as $group) {
			array_push($groups, new TrophyGroup($this->client, $group, $this));
		}

		return $groups;
	}

	/**
	 * @return ?User
	 */
	public function user(): ?User
	{
		return $this->user;
	}

	/**
	 * @return bool
	 */
	public function hasTrophies(): bool
	{
		return !empty($this->definedTrophies);
	}

	/**
	 * @return ?string
	 */
	public function getComparedUser(): ?string
	{
		return $this->isComparing() ? $this->user()->getUsername() : null;
	}

	/**
	 * @return bool
	 */
	public function isComparing(): bool
	{
		if ($this->user() === null) {
			return false;
		}

		return $this->user()->getUsername() !== null;
	}
}
