<?php

namespace http23\MailRu;

use GuzzleHttp\Exception\GuzzleException;

/**
 * Trait AuthTrait
 * @package http23\MailRu
 * @mixin CloudMail
 */
trait AuthTrait
{
    protected $userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.173';
    protected $isAuth;
    protected $email;
    protected $login;
    protected $password;
    protected $domain;

    protected $token;
    protected $tokenLifeTime;

    /**
     * @return $this
     * @throws GuzzleException
     */
    protected function auth()
    {

        $this->request('https://auth.mail.ru/cgi-bin/auth', 'POST', [
            'username'    => $this->login,
            'Login'    => $this->login,
            'password' => $this->password,
            'Password' => $this->password,
			// 'Domain'   => $this->domain,
			// 'saveauth'   => 1,
			// 'new_auth_form'   => 1,
			// 'FromAccount'   => "opener=account&allow_external=1&twoSteps=1",
			// 'act_token'   => "848a979f72564a83a37ce105c24aa504",
			// 'page'   => "https://account.mail.ru/login?authid=keujoz5c.gk&dwhsplit=s3319.n1s&fail=1&from=login",
			// 'lang'   => "en_US",

			
        ], 'multipart', false);

        $this->isAuth = true;
        $this->client->request('GET', 'https://cloud.mail.ru');
        $this->fetchToken();

        return $this;
    }


    /**
     * @throws GuzzleException
     */
    public function fetchToken()
    {
        $res = json_decode($this->client->request('GET', self::FETCH_TOKEN_URL, [
            'form_params' => [
                'api'     => 'v2',
                'email'   => $this->login,
                'x-email' => $this->login,
            ],
        ])->getBody()->getContents());

        $this->token         = $res->body->token;
        $this->tokenLifeTime = $res->time;
        $this->email         = $res->email;
    }
}
