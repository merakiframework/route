<?php
declare(strict_types=1);

namespace Meraki\Route;

/**
 * The Collector is responsible for finding route rules
 * from different data sources (json, yml, php, etc.)and
 * adding them to a collection to be used for matching.
 *
 * @author Nathan Bishop <nbish11@hotmail.com> (https://nathanbishop.name)
 * @copyright 2019 Nathan Bishop
 * @license The MIT license.
 */
interface Collector
{
	/**
	 * Return the rules that the collector has found.
	 *
	 * @return Collection The collected route rules.
	 */
	public function getRules(): Collection;
}
