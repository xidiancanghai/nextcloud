<?php
declare(strict_types=1);
/**
 * @copyright Copyright (c) 2016, ownCloud, Inc.
 *
 * @author Arthur Schiwon <blizzz@arthur-schiwon.de>
 * @author Bjoern Schiessle <bjoern@schiessle.org>
 * @author Joas Schilling <coding@schilljs.com>
 * @author Lukas Reschke <lukas@statuscode.ch>
 * @author michag86 <micha_g@arcor.de>
 * @author Morris Jobke <hey@morrisjobke.de>
 * @author Roeland Jago Douma <roeland@famdouma.nl>
 * @author Thomas Müller <thomas.mueller@tmit.eu>
 * @author Tom Needham <tom@owncloud.com>
 * @author John Molakvoæ <skjnldsv@protonmail.com>
 * @author Thomas Citharel <tcit@tcit.fr>
 *
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */
namespace OCA\Provisioning_API\Controller;


use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\OCSController;
use OCP\IAppConfig;
use OCP\IConfig;
use OCP\IRequest;

class AdminController extends OCSController
{

	/**
	 * @param string $appName
	 * @param IRequest $request
	 * @param IConfig $config
	 * @param IAppConfig $appConfig
	 */
	public function __construct(string $appName,
								IRequest $request) {
		parent::__construct($appName, $request);
	}


    	/**
	 * @NoAdminRequired
	 * @NoSubAdminRequired
	 * @PasswordConfirmationRequired
	 *
	 * edit users
	 *
	 * @param string $userId
	 * @param int $page
	 * @param int $limit
	 * @return DataResponse
	 * @throws OCSException
	 */


	public function getLoginIp(string $userId, int $page = 0, int $limit = 50) {

		error_log("getLoginIp " . $userId . " " . $page . " " . $limit);
	}
}