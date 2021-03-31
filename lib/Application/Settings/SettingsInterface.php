<?php
declare(strict_types=1);

namespace App\Application\Settings;

interface SettingsInterface {
	public const displayErrorDetails = 'displayErrorDetails';
	public const logError = 'logError';
	public const logErrorDetails = 'logErrorDetails';
	public const logger = 'logger';

	/**
	 * @return mixed
	 */
	public function get(string $key = '');
}
