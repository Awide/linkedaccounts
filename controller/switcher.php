<?php
/**
*
* Linked Accounts extension for phpBB 3.2
*
* @copyright (c) 2018 Flerex
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace flerex\linkedaccounts\controller;

use \Symfony\Component\HttpFoundation\Response;

class switcher
{
	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\auth\auth $auth, */
	protected $auth;

	/** @var \flerex\linkedaccounts\service\utils $utils */
	protected $utils;

	/**
	 * Constructor
	 *
	 * @param \flerex\linkedaccounts\service\utils $utils
	 */
	public function __construct(\phpbb\user $user, \phpbb\auth\auth $auth, \flerex\linkedaccounts\service\utils $utils)
	{
		$this->user		= $user;
		$this->auth		= $auth;
		$this->utils	= $utils;
	}

	/**
	 * Demo controller for route /demo/{name}
	 *
	 * @param int $account_id
	 * @throws \phpbb\exception\http_exception
	 */
	public function handle($account_id)
	{

		if (!$this->auth->acl_get('u_link_accounts'))
		{
			throw new \phpbb\exception\http_exception(403, 'NO_AUTH_OPERATION');
		}

		if (!$this->utils->can_switch_to($account_id))
		{
			throw new \phpbb\exception\http_exception(403, 'INVALID_LINKED_ACCOUNT', array($account_id));
		}

		$this->user->session_kill(false);
		$this->user->session_begin();
		$this->user->session_create(
			$account_id,
			false, // for security reasons we always set this to false (admin login)
			(bool) $this->user->data['session_viewonline'],
			(bool) $this->user->data['session_autologin']
		);
		
		throw new \phpbb\exception\http_exception(200, 'ACCOUNTS_SWITCHED');

	}
}