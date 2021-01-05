<?php

namespace App\Services;

use App\Traits\SweetStaticApiTrait;
use Illuminate\Support\Facades\Log;

class SsiSweetService
{
    use SweetStaticApiTrait;

    public static function updateSurvey(int $points = 0, array $data = null)
    {
        if ($points && $data) {
            $_source_data = self::getSourceData($data);
            $_respondent_id = self::getRespondentId($data);

            // Get Survey
            $survey = $_source_data ? self::getSurveyApi($_source_data) : self::getSurveyByRespodentApi($_respondent_id);

            // Update
            if (empty($survey)) {
                return false;
            }

            if (1 != $survey->status) {
                $survey = self::updateSurveyApi($points, $survey);
                if (!empty($survey) && property_exists($survey, 'data')) {
                    $data = $survey->data;
                    return $data->point === $points;
                }
                return false;
            }

            return false;
        }
    }

    private static function getSurveyApi(int $_rId = 0)
    {
        $survey = self::executeSweetApi(
            'GET',
            env('APP_SWEET_API') . "/api/ssi/v1/frontend/ssi-respondent?where[id]={$_rId}",
            []
        );
        if (empty($survey) && !property_exists($survey, 'data')) {
            return array();
        }
        if (empty($survey->data)) {
            return array();
        }
        return $survey->data[0];
    }

    private static function getSurveyByRespodentApi(int $_rId = 0)
    {
        $survey = self::executeSweetApi(
            'GET',
            env('APP_SWEET_API') . "/api/ssi/v1/frontend/ssi-respondent?where[respondentID]={$_rId}&where[status]=2",
            []
        );
        if (empty($survey) && !property_exists($survey, 'data')) {
            return array();
        }
        if (empty($survey->data)) {
            return array();
        }
        return $survey->data[0];
    }

    private static function updateSurveyApi(int $points = 0, $survey = null)
    {
        if (null !== $survey && !empty($survey)) {
            $survey->point += $points;
            $survey->status = 1;
            $survey->postback = 1;

            $url = env('APP_SWEET_API') . "/api/ssi/v1/frontend/ssi-respondent/{$survey->id}";
            // $param = (array) $survey;
            $survey = self::executeSweetApi('PUT', $url, $survey);
            if (empty($survey)) {
                return array();
            }
            return $survey;
        }
        return array();
    }

    private static function getRespondentId(array $data = null)
    {
        if ($data) {
            return (int) $data['sourcePID'];
        }
        return 0;
    }

    private static function getSourceData(array $data = null)
    {
        if ($data) {
            return (int) $data['sourceData'];
        }
        return 0;
    }
}
