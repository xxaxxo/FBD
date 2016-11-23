<?php
/**
 * User: Michael Kumar <m.kumar@abv.bg>
 * Date: 19.11.16
 * Time: 14:07
 */

namespace xxaxxo\fbd\Services\FootballData;


use xxaxxo\fbd\Exceptions\FootballDataException;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;

class FootballData
{
    public $client;

    public function __construct()
    {
        $this->client = new Client(
            [
                'base_uri' => env('FBD_URL'),
                'headers'  => [
                    'X-Auth-Token'       => env('FBD_TOKEN'),
                    'X-Response-Control' => 'full',
                ],
            ]
        );
    }

    /**
     * gets a list of the competitions
     * @param null $year - if an year is setted the year is passed as a param to the api request
     * @return mixed
     * @throws FootballDataException
     */
    public function getCompetitions($year = null)
    {
        $endpoint = 'competitions';

        if (!is_null($year)) {
            $endpoint .= '?year='.$year;
        }
        return $this->returnResponse($this->client->get($endpoint)->getBody()->getContents());
    }


    /**
     * gets all the fixtures by competition
     * @param $id int - id of competition
     * @param null $matchday int - day number of the current competition
     * @param null $timeFrame string - p1|n1 p(ast)1(number of days from today) n(ext)1(number of days from today)
     * @return Collection
     * @throws FootballDataException
     */
    public function getFixturesByCompetition($id, $matchday = NULL, $timeFrame = NULL)
    {
        $endpoint = 'competitions/'.$id.'/fixtures';

        if(!is_null($matchday))
            $endpoint .= '?matchday='.$matchday;

        if(!is_null($timeFrame))
            $endpoint .= ((is_null($matchday))? '?':'&').'timeFrame='.$timeFrame;

        return $this->returnResponse($this->client->get($endpoint)->getBody()->getContents());
    }

    /**
     * gets all the fixtures info
     * @param $id int - id of fixture
     * @param null $matchday int - (if matchday is set it is added to the query
     * @return Collection
     * @throws FootballDataException
     */
    public function getFixtureData($id, $matchday = NULL)
    {
        $endpoint = 'fixtures/'.$id;

        if (!is_null($matchday)) {
            $endpoint .= '?matchday='.$matchday;
        }

        return $this->returnResponse($this->client->get($endpoint)->getBody()->getContents());
    }

    protected function returnResponse($data)
    {
        $response = json_decode($data, true);
        if ($response === null) {
            throw new FootballDataException('Not valid response from API!');
        }

        return r_collect($response);
    }

    /**
     * recursive collection converting
     * @param $array
     * @return Collection
     */
    function r_collect($array)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = r_collect($value);
                $array[$key] = $value;
            }
        }

        return new Collection($array);
    }


}