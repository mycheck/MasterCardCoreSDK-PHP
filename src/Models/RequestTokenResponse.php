<?php
namespace Masterpass\CoreSDK\Models;
/**
 *Holds data relevant to the Request Token
 * @package  MasterCardCoreSDK
 * @subpackage  model
 */
class RequestTokenResponse
{
	public $OauthCallbackConfirmed;
	public $OauthExpiresIn;
	public $OauthToken;
	public $OauthTokenSecret;
	public $XoauthRequestAuthUrl;
}?>
