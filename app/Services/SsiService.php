<?php

namespace App\Services;

use App\Models\Customer;

class SsiService
{
    use \App\Traits\SiiIntegrationTrait, \App\Traits\SweetApiTrait;

    private $customer_id;
    private $ssi_guzzle_client;
    private $ssi_lead;

    public function __construct(int $customer_id = 0)
    {
        $this->customer_id = $customer_id;
        // instanciar o cliente guzzle do ssi
        $this->ssi_guzzle_client = $this->getSsiGuzzleClient();
        $this->ssi_lead = $this->findLead();
    }

    public function leadDispatch()
    {
        if ($this->submitSingleLead()) {
            return true;
        }

        return false;
    }

    private function findLead()
    {
        // Call find the SsiLead on Trait
        $lead = $this->executeSweetApi('GET', env('APP_SWEET_API') . "/api/ssi/v1/frontend/ssi-leads/" . $this->customer_id);
        if (empty($lead)) {
            return array();
        }
        return $lead;
    }

    private function getSiiParams($type = 1)
    {
        // convert objet to paramentrs format to ssi request
        return $this->getArraySsiParams($this->ssi_lead, $type);
    }

    private function validateSigleLead()
    {
        if (!env('SSI_VALIDATE')) {
            return false;
        }

        if (env('SSI_VALIDATE')) {
            $ssi_return = $this->ssiRequest($this->ssi_guzzle_client, 'POST', 'validate/single', $this->getSiiParams(1))->getBody()->getContents();
            if (empty($ssi_return) || "[]" === $ssi_return) {
                return true;
            }
            //Tratar o error da validação
            $this->updateSsiFlagCustomer($this->customer_id, 2, $ssi_return);
        }
        return false;
    }

    private function submitSingleLead()
    {
        if (!env('SSI_SUBMIT')) {
            return false;
        }

        if ($this->validateSigleLead()) {
            $ssi_return = $this->ssiRequest($this->ssi_guzzle_client, 'POST', 'submit', $this->getSiiParams(0))->getBody()->getContents();
            if (empty($ssi_return) || "[]" === $ssi_return) {
                $this->updateSsiFlagCustomer($this->customer_id, 1, $ssi_return);
                return true;
            }
            return false;
            //Tratar o error da submissão
        }
        return false;
    }

    private function updateSsiFlagCustomer(int $customer_id = null, int $ssi_status = 0, string $ssi_return = '')
    {
        $c = Customer::find($customer_id);
        $c->ssi_status = $ssi_status;
        $c->ssi_return = $ssi_return;
        $c->update();
    }
}
