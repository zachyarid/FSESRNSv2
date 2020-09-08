<?php
/**
 * Created by PhpStorm.
 * User: zachyarid
 * Date: 5/27/18
 * Time: 5:23 PM
 */

namespace App\Libraries;

use App\Group;
use Illuminate\Support\Facades\Log;

class FSEData
{
    protected $servicekey = 'ZL752FUGN';
    protected $servicekey2 = 'D1DK685Q3';
    protected $userkey = 'IIPMIJSTPW';

    public function getAssignments($icaos)
    {
        $link = 'http://server.fseconomy.net/data?servicekey='.$this->servicekey.'&format=xml&query=icao&search=jobsfrom&icaos=' . $icaos;
        $xml = simplexml_load_file($link);

        return $xml;
    }

    public function getAircraftByMakeModel($makemodel)
    {
        $link = 'http://server.fseconomy.net/data?servicekey='.$this->servicekey2.'&format=xml&query=aircraft&search=makemodel&makemodel=' . $makemodel;
        $xml = simplexml_load_file($link);

        return $xml;
    }

    public function getGroupData($ak, $personalKey) {
        $group = array();

        $link = 'http://server.fseconomy.net/data?userkey='.$personalKey.'&format=xml&query=group&search=members&readaccesskey='.$ak;

        $xml = simplexml_load_file($link);
        $count = $xml->count();

        if ($count > 0) {
            $group['name'] = (string)$xml['name'];

            foreach ($xml->Member as $member) {
                $group['users'][] = array(
                    'name' => (string)$member->Name,
                    'status' => (string)$member->Status
                );

                if ($member->Status == 'owner') {
                    $group['owner'] = (string)$member->Name;
                }

                if ($member->Status == 'staff') {
                    $group['staff'][] = (string)$member->Name;
                }
            }

            return array('success' => true, 'message' => $group);
        } else {
            Log::debug('FSEData -> getGroupData returned empty result');
            return array('success' => false, 'message' => 'No group information found with the provided access key');
        }
    }

    public function getQueuedGroupData($groupID)
    {
        $group = Group::findOrFail($groupID);
        $userKey = $group->user->personal_key;

        $groupInfo = $this->getGroupData($group->access_key, $userKey);

        if ($groupInfo['success'])
        {
            $groupData = $groupInfo['message'];

            $group->name = $groupData['name'];
            $group->owner = $groupData['owner'];

            $group->save();

            return array('success' => true, 'message' => 'Group data updated');
        }
        else
        {
            Log::debug('FSEData -> getGroupData returned empty result');
            return array('success' => false, 'message' => $groupInfo['message']);
        }
    }

    public function getPaymentsByMonth($personal, $group)
    {
        $month = session()->get('month');
        $year = session()->get('year');

        $link = "http://server.fseconomy.net/data?userkey=$personal&format=xml&query=payments&search=monthyear&readaccesskey=$group&month=$month&year=$year";
        Log::debug($link);
        $xml = simplexml_load_file($link);

        if ($xml->Payment->count() > 0) {
            return array('success' => true, 'message' => $xml);
        }
        else
        {
            return array('success' => false, 'message' => 'Payment count: ' . $xml->Payment->count());
        }
    }

    public function getPersonalData($ak) {
        $user = array();

        $link = 'http://server.fseconomy.net/data?userkey='.$ak.'&format=xml&query=statistics&search=key&readaccesskey='.$ak;

        $xml = simplexml_load_file($link);
        $count = $xml->count();

        if ($count > 0) {

            foreach ($xml->Statistic as $s) {
                $user['owner'] = (string)$s['account'];
                $user['flights'] = (string)$s->flights;
                $user['total_miles'] = (string)$s->Total_Miles;
                $user['time_flown'] = (string)$s->Time_Flown;
                $user['name'] = (string)$s['account'];
            }

            return $user;
        } else {
            return false;
        }
    }

    public function getPayments($access_key, $fromID)
    {
        $link = 'http://server.fseconomy.net/data?servicekey='.$this->servicekey.'&format=xml&query=payments&search=id&readaccesskey='.$access_key.'&fromid='.$fromID;
	    Log::debug($link);
        $xml = simplexml_load_file($link);

        if ($xml['total'] > 0)
        {
            return array('success' => true, 'message' => $xml, 'mp_only' => false);
        }
        else if (!isset($xml['total']))
        {
            return array('success' => false, 'message' => (string) $xml["0"], 'mp_only' => false);
        }
        else if ($xml['total'] == 0)
        {
            return array('success' => false, 'message' => 'Valid Request. No records found', 'mp_only' => false);
        }
    }

    public function getPaymentsMonth($access_key, $month, $year)
    {
        $link = 'http://server.fseconomy.net/data?servicekey='.$this->servicekey.'&format=xml&query=payments&search=monthyear&readaccesskey='.$access_key.'&month='.$month.'&year='.$year;
        Log::debug($link);
        $xml = simplexml_load_file($link);

        if ($xml['total'] > 0)
        {
            return array('success' => true, 'message' => $xml, 'mp_only' => false);
        }
        else if (!isset($xml['total']))
        {
            return array('success' => false, 'message' => (string) $xml["0"], 'mp_only' => false);
        }
        else if ($xml['total'] == 0)
        {
            return array('success' => true, 'message' => 'Valid Request. No records found', 'mp_only' => true);
        }
    }

    public function getAircraft($access_key)
    {
        $link = 'http://server.fseconomy.net/data?servicekey=' . $this->servicekey . '&format=xml&query=aircraft&search=key&readaccesskey=' . $access_key;
        $xml = simplexml_load_file($link);
        $ac = [];

        if (isset($xml['total']) && $xml['total'] == 0)
        {
            return array('success' => false, 'message' => 'Valid Request. No records found', 'mp_only' => false);
        }
        else if (!isset($xml['total']))
        {
            return array('success' => false, 'message' => (string) $xml["0"], 'mp_only' => false);
        }
        else if (isset($xml['total']) && $xml['total'] > 0)
        {
            foreach ($xml->Aircraft as $a)
            {
                $acObj = new \stdClass();
                $acObj->SerialNumber = $a->SerialNumber;
                $acObj->MakeModel = $a->MakeModel;
                $acObj->Registration = $a->Registration;
                $acObj->Owner = $a->Owner;
                $acObj->Location = $a->Location;
                $acObj->LocationName = $a->LocationName;
                $acObj->Home = $a->Home;
                $acObj->SalePrice = $a->SalePrice;
                $acObj->SellbackPrice = $a->SellbackPrice;
                $acObj->Equipment = $a->Equipment;
                $acObj->RentalDry = $a->RentalDry;
                $acObj->RentalWet = $a->RentalWet;
                $acObj->Bonus = $a->Bonus;
                $acObj->RentalTime = $a->RentalTime;
                $acObj->RentedBy = $a->RentedBy;
                $acObj->FuelPct = $a->FuelPct;
                $acObj->NeedsRepair = $a->NeedsRepair;
                $acObj->AirframeTime = $a->AirframeTime;
                $acObj->EngineTime = $a->EngineTime;
                $acObj->TimeLast100hr = $a->TimeLast100hr;
                $acObj->LeasedFrom = $a->LeasedFrom;
                $acObj->MonthlyFee = $a->MonthlyFee;
                $acObj->FeeOwed = $a->FeeOwed;

                $ac[] = $acObj;
            }

            return array('success' => true, 'message' => $ac, 'mp_only' => false);
        }
    }

    public function getFbos($access_key)
    {
        $link = 'http://server.fseconomy.net/data?servicekey='.$this->servicekey.'&format=xml&query=fbos&search=key&readaccesskey='.$access_key;
        $xml = simplexml_load_file($link);
        $fbos = [];

        if (isset($xml['total']) && $xml['total'] == 0)
        {
            return array('success' => false, 'message' => 'Valid Request. No records found', 'mp_only' => false);
        }
        else if (!isset($xml['total']))
        {
            return array('success' => false, 'message' => (string) $xml["0"], 'mp_only' => false);
        }
        else if (isset($xml['total']) && $xml['total'] > 0)
        {
            foreach ($xml->FBO as $fbo)
            {
                $fboObj = new \stdClass();
                $fboObj->FboId = (int)$fbo->FboId;
                $fboObj->Status = (string)$fbo->Status;
                $fboObj->Airport = (string)$fbo->Airport;
                $fboObj->Name = (string)$fbo->Name;
                $fboObj->Owner = (string)$fbo->Owner;
                $fboObj->Icao = (string)$fbo->Icao;
                $fboObj->Location = (string)$fbo->Location;
                $fboObj->Lots = (int)$fbo->Lots;
                $fboObj->RepairShop = (string)$fbo->RepairShop;
                $fboObj->Gates = (int)$fbo->Gates;
                $fboObj->GatesRented = (int)$fbo->GatesRented;
                $fboObj->Fuel100LL = (int)$fbo->Fuel100LL;
                $fboObj->FuelJetA = (int)$fbo->FuelJetA;
                $fboObj->BuildingMaterials = (int)$fbo->BuildingMaterials;
                $fboObj->Supplies = (int)$fbo->Supplies;
                $fboObj->SuppliesPerDay = (int)$fbo->SuppliesPerDay;
                $fboObj->SuppliedDays = (int)$fbo->SuppliedDays;
                $fboObj->SellPrice = (float)$fbo->SellPrice;
                
                $fbos[] = $fboObj;
            }

            return array('success' => true, 'message' => $fbos, 'mp_only' => false);
        }
    }

    public function getFlightLogs($access_key, $fromID)
    {
        $link = 'http://server.fseconomy.net/data?servicekey='.$this->servicekey.'&format=xml&query=flightlogs&search=id&readaccesskey='.$access_key.'&fromid='.$fromID;
        $xml = simplexml_load_file($link);

        if ($xml['total'] > 0)
        {
            return array('success' => true, 'message' => $xml, 'mp_only' => false);
        }
        else if (!isset($xml['total']))
        {
            return array('success' => false, 'message' => (string) $xml["0"], 'mp_only' => false);
        }
        else if ($xml['total'] == 0)
        {
            return array('success' => false, 'message' => 'Valid Request. No records found', 'mp_only' => false);
        }
    }

    public function getFlightLogsMonth($access_key, $month, $year)
    {
        $link = 'http://server.fseconomy.net/data?servicekey='.$this->servicekey.'&format=xml&query=flightlogs&search=monthyear&readaccesskey='.$access_key.'&month='.$month.'&year='.$year;
        $xml = simplexml_load_file($link);

        if ($xml['total'] > 0)
        {
            return array('success' => true, 'message' => $xml, 'mp_only' => false);
        }
        else if (!isset($xml['total']))
        {
            return array('success' => false, 'message' => (string) $xml["0"], 'mp_only' => false);
        }
        else if ($xml['total'] == 0)
        {
            return array('success' => true, 'message' => 'Valid Request. No records found', 'mp_only' => true);
        }
    }

    public function getAircraftByRegistration($registration, $ak)
    {
        $link = 'http://server.fseconomy.net/data?userkey='.$ak.'&format=xml&query=aircraft&search=registration&aircraftreg=' . $registration;
        $xml = simplexml_load_file($link);

        if ($xml->Aircraft->count() == 1) {
            foreach ($xml->Aircraft as $ac) {
                return array('success' => true, 'message' => $ac);
            }
        } else {
            return array('success' => false, 'message' => 'No aircraft found with that registration');
        }
    }

    public function getAircraftConfig()
    {
        $link = 'http://server.fseconomy.net/data?servicekey='.$this->servicekey.'&format=xml&query=aircraft&search=configs';
        $xml = simplexml_load_file($link);

        if ($xml['total'] > 0)
        {
            return array('success' => true, 'message' => $xml, 'mp_only' => false);
        }
        else if (!isset($xml['total']))
        {
            return array('success' => false, 'message' => (string) $xml["0"], 'mp_only' => false);
        }
        else if ($xml['total'] == 0)
        {
            return array('success' => false, 'message' => 'Valid Request. No records found', 'mp_only' => false);
        }
    }

    /*
     * Get payments by month/year by access key
     * @return as array
    */
    public function getPaymentsMonthYear($access_key, $month, $year)
    {
        $link = 'http://server.fseconomy.net/data?servicekey='.$this->servicekey.'&format=xml&query=payments&search=monthyear&readaccesskey='.$access_key.'&month='.$month.'&year='.$year;
        $xml = simplexml_load_file($link);

        if ($xml['total'] > 0)
        {
            return array('success' => true, 'message' => $xml, 'mp_only' => false);
        }
        else if (!isset($xml['total']))
        {
            return array('success' => false, 'message' => (string) $xml["0"], 'mp_only' => false);
        }
        else if ($xml['total'] == 0)
        {
            return array('success' => false, 'message' => 'Valid Request. No records found', 'mp_only' => false);
        }
    }
}
