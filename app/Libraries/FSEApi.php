<?php
/**
 * Created by PhpStorm.
 * User: zachyarid
 * Date: 5/27/18
 * Time: 5:23 PM
 */

namespace App\Libraries;


class FSEApi
{
    protected $servicekey = 'ZL752FUGN';
    protected $servicekey2 = 'D1DK685Q3';

    protected $fse_base = 'https://server.fseconomy.net/rest/api';

    //http://server.fseconomy.net/static/dev/FSERestApiSamples.txt

    public function getFSEID($fseusername) {
        // The account name for which we will be getting the account ID
        $form['accountname'] = $fseusername;

        $data = $this->doPost($form, $this->fse_base . '/account/search/name');

        if ($data['meta']['code'] == 200)
        {
            return $data['data'];
        } else {
            return $data['meta']['error'] . ' ' . $data['info'];
        }
    }

    public function getFSEUsername($fseuserid) {
        // The account name for which we will be getting the account ID
        $form['id'] = "$fseuserid";

        $data = $this->doPost($form, $this->fse_base . '/account/search/id');

        if ($data['meta']['code'] == 200) {
            return $data['data'];
        }
        else {
            return $data['meta']['error'] . ' ' . $data['info'];
        }
    }

    public function getBalance($type, $id)
    {
        $submit = file_get_contents($this->fse_base . '/account/' . $type . '/' . $id);

        $data = json_decode($submit, TRUE);

        if ($data['meta']['code'] == 200)
        {
            return $data['data'];
        } else {
            return $data['meta']['error'] . ' ' . $data['info'];
        }
    }

    public function moveCash($type, $amount, $id)
    {
        if ($type == 'deposit')
        {
            $link = $this->fse_base . '/account/deposit/' . $id;
        }
        else if ($type == 'withdrawl')
        {
            $link = $this->fse_base . '/account/withdraw/' . $id;
        }
        else
        {
            return array('success' => false, 'message' => 'Type does not exist');
        }

        $form['id'] = "$amount";

        $data = $this->doPost($form, $link);

        if ($data['meta']['code'] == 200)
        {
            return true;
        } else {
            return $data['meta']['error'] . ' ' . $data['info'];
        }
    }

    public function leaseAircraft($group, $aircraft, $leaseTo, $note)
    {
        $values = array(
            'leaseto' => $leaseTo,
            'serialnumber' => $aircraft,
            'note' => $note
        );

        $data = $this->doPost($values, $this->fse_base . '/aircraft/lease/' . $group);

        if ($data['meta']['code'] == 200)
        {
            return true;
        } else {
            return $data['meta']['error'] . ' ' . $data['info'];
        }
    }

    public function returnAircraft($group, $aircraft, $note)
    {
        $values = array(
            'serialnumber' => $aircraft,
            'note' => $note
        );

        $data = $this->doPost($values, $this->fse_base . '/aircraft/returnlease/' . $group);

        if ($data['meta']['code'] == 200)
        {
            return true;
        } else {
            return $data['meta']['error'] . ' ' . $data['info'];
        }
    }

    public function doPost($values, $url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($values));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
            'servicekey: '. $this->servicekey
        ));

        $server_output = curl_exec($ch);
        curl_close($ch);

        return json_decode($server_output, true);
    }
}