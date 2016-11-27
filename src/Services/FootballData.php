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
    public $filter = array();

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
     * @return mixed
     * @throws FootballDataException
     */
    public function getCompetitions()
    {
        $endpoint = 'competitions';
        $endpoint = $this->addFiltersToEndpoint($endpoint);
        return $this->returnResponse($this->client->get($endpoint)->getBody()->getContents());
    }


    /**
     * gets all the fixtures by competition
     * @param $id int - id of competition
     * @return Collection
     * @throws FootballDataException
     */
    public function getFixturesByCompetition($id)
    {
        $endpoint = 'competitions/'.$id.'/fixtures';
        $endpoint = $this->addFiltersToEndpoint($endpoint);
        return $this->returnResponse($this->client->get($endpoint)->getBody()->getContents());
    }

    /**
     * gets all the fixtures info
     * @param $id int - id of fixture (when it's passed only the fixture id is returned with all it's head2head history
     * @return Collection
     * @throws FootballDataException
     */
    public function getFixtureData($id = null)
    {
        $endpoint = 'fixtures/';
        if(!is_null($id))
            $endpoint .= $id;

        $endpoint = $this->addFiltersToEndpoint($endpoint);

        return $this->returnResponse($this->client->get($endpoint)->getBody()->getContents());
    }






    protected function returnResponse($data)
    {
        $response = json_decode($data, true);
        if ($response === null) {
            throw new FootballDataException('Not valid response from API!');
        }

        return $this->recursiveCollection($response);
    }

    /**
     * recursive collection converting
     * @param $array
     * @return Collection
     */
    private function recursiveCollection($array)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = $this->recursiveCollection($value);
                $array[$key] = $value;
            }
        }

        return new Collection($array);
    }

    /**
     * adds filters to a query
     * @param $name
     * @param $value
     * @internal param $filters
     */
    public function filter($name, $value)
    {
        $this->filter[] = '&'.$name.'='.$value;
        return $this;
    }

    /**
     * adds filters to a endpoint
     * @param $endpoint
     */
    private function addFiltersToEndpoint($endpoint)
    {
        if (!empty($this->filter)) {
            $endpoint .= '?';
            foreach($this->filter as $filter)
                $endpoint .= $filter;
        }

        return $endpoint;
    }




}