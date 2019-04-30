<?php
declare(strict_types=1);
namespace Playstation\Entity;

use Playstation\Support\Fluent;
use Playstation\Wrapper;

class Trophy extends Fluent
{
	/** string TROPHY_ENDPOINT */
	const TROPHY_ENDPOINT = 'https://us-tpy.np.community.playstation.net/trophy/v1';

	/** @var Wrapper $client */
	private $client;

	/** @var TrophyGroup $trophyGroup */
	private $trophyGroup;

	/**
	 * @param  Wrapper $client
	 * @param  array $attributes
	 * @return void
	 */
	public function __construct(
		Wrapper $client,
		array $attributes = [],
		TrophyGroup $trophyGroup
	) {
		$this->client = $client;
		$this->trophyGroup = $trophyGroup;

		parent::__construct($attributes);
	}

	/**
	 * @return string
	 * @SuppressWarnings(PHPMD.ShortMethodName)
	 */
	public function id(): string
	{
		return $this->trophyId;
	}

	/**
	 * @return bool
	 */
	public function isHidden(): bool
	{
		return $this->trophyHidden;
	}

	/**
	 * @return string
	 */
	public function type(): string
	{
		return $this->trophyType;
	}

	/**
	 * @return string
	 */
	public function name(): string
	{
		return $this->trophyName;
	}

	/**
	 * @return string
	 */
	public function detail(): string
	{
		return $this->trophyDetail;
	}

	/**
	 * @return string
	 */
	public function iconUrl(): string
	{
		return $this->trophyIconUrl;
	}

	/**
	 * @return string
	 */
	public function smallIconUrl(): string
	{
		return $this->trophySmallIconUrl;
	}

	/**
	 * @return float
	 */
	public function earnedRate(): float
	{
		return floatval($this->trpohyEarnedRate());
	}

	/**
	 * @return bool
	 */
	public function isRare(): bool
	{
		return !!$this->trophyRare;
	}

	/**
	 * @return Game
	 */
	public function game(): Game
	{
		return $this->trophyGroup()->game();
	}

	/**
	 * @return TrophyGroup
	 */
	public function trophyGroup(): TrophyGroup
	{
		return $this->trophyGroup;
	}

	/**
	 * @return bool
	 */
	public function earned(): bool
	{
		if ($this->isComparing()) {
			return $this->comparedUser['earned'];
		}

		return isset($this->fromUser['earned'])
			? $this->fromUser['earned']
			: false;
	}

	/**
	 * @return bool
	 */
	public function isComparing(): bool
	{
		return $this->game()->isComparing();
	}
}
