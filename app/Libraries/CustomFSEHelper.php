<?php
/**
 * Created by PhpStorm.
 * User: Zach
 * Date: 3/25/2019
 * Time: 12:46 PM
 */

namespace App\Libraries;
use GuzzleHttp\Client;


class CustomFSEHelper
{
    public $client;
    public $server;

    public function __construct($user, $password)
    {
        //$jar = new \GuzzleHttp\Cookie\CookieJar;
        /*$this->client = new Client([
            'base_uri' => 'http://www.fseconomy.net:81/',
            'timeout' => 2.0,
            'cookies' => true
        ]);*/

        $this->server = new Client([
            'base_uri' => 'http://server.fseconomy.net/',
            'timeout' => 2.0,
            'cookies' => true
        ]);

        //$this->client->request('GET', 'userctl?user=' . $user . '&password=' . $password . '&event=Agree+%26+Log+in');

        $options = [
            'form_params' => [
                'user' => $user,
                'password' => $password,
                'event' => 'Agree & Log in'
            ]
        ];
        $this->server->request('POST', 'userctl', $options);
    }

    public function DoGet($url)
    {
        $r = $this->server->request('GET', $url);

        if ($r->getStatusCode() == 200)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function DoPost($options)
    {
        $r = $this->server->request('POST', 'userctl', $options);

        if ($r->getStatusCode() == 200)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function addAssignmentToQueue($assignmentID)
    {
        //$uri = 'userctl?returnpage=/myflight.jsp&event=Assignment&type=add&select=' . $assignmentID;
        $options = [
            'form_params' => [
                'returnpage' => '/airport.jsp',
                'event' => 'Assignment',
                'type' => 'add',
                'select' => $assignmentID,
            ]
        ];

        return $this->DoPost($options);
    }

    public function buySupplies($amount, $account, $icao)
    {
        $options = [
            'form_params' => [
                'returnpage' => '/airport.jsp',
                'return' => 'airport.jsp',
                'event' => 'buyGoods',
                'owner' => '0',
                'type' => '2',
                'amount' => "$amount",
                'account' => $account,
                'icao' => $icao
            ]
        ];

        return $this->DoPost($options);
    }
}