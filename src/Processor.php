<?php

namespace KnowledgeBaseMcs;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException as GuzzleClientException;
use GuzzleHttp\Psr7\Request;

/**
 * Class Processor
 *
 * @package KnowledgeBaseMcs
 */
class Processor
{
    protected $endpoint;

    public function __construct($endpoint)
    {
        if (null === $endpoint) {
            throw new \Exception(
                'Knowledge Base service: endpoint is null'
            );
        }

        $this->endpoint = $endpoint;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function getPath($path)
    {
        return $this->endpoint . $path;
    }

    /**
     * @param GuzzleClient $client
     * @param Request      $request
     *
     * @return \Psr\Http\Message\StreamInterface
     * @throws \Exception
     */
    public function send(GuzzleClient $client, Request $request)
    {
        try {
            $response = $client->send($request);
            $data = [
                'body'       => json_decode($response->getBody()),
                'headers'    => [],
                'statusCode' => $response->getStatusCode()
            ];

            if (!empty($total = $response->getHeader('X-Total-Count'))) {
                $data['headers']['X-Total-Count'] = $total;
            }
            if (!empty($rate = $response->getHeader('X-Ratelimit-Remaining'))) {
                $data['headers']['X-Ratelimit-Remaining'] = $rate;
            }

            return $data;
        } catch (GuzzleClientException $e) {
            $message = $this->formatErrorMessage($e);
            throw new \Exception(json_encode($message), 0, $e);
        }
    }

    /**
     * @param GuzzleClientException $httpException
     *
     * @return array
     */
    public function formatErrorMessage($httpException)
    {
        $message = [
            'message'  => 'Something bad happened with Knowledge Base service',
            'request'  => [
                'headers' => $httpException->getRequest()->getHeaders(),
                'body'    => $httpException->getRequest()->getBody()
            ],
            'response' => [
                'headers' => $httpException->getResponse()->getHeaders(),
                'body'    => $httpException->getResponse()->getBody()
                    ->getContents(),
                'status'  => $httpException->getResponse()->getStatusCode()
            ]
        ];

        return $message;
    }

    /**
     * @param array  $filter
     * @param string $locationGroup
     *
     * @return mixed
     * @throws \Exception
     */
    public function read($filter = [], $locationGroup)
    {
        $client = new GuzzleClient();
        if (!empty($filter)) {
            $query = ['filter' => json_encode($filter)];
            $query = http_build_query($query);
            $request = new Request(
                'get',
                $this->getPath(sprintf('/knowledge/articles?%s', $query)),
                [
                    'content-type'     => 'application/json',
                    'x-location-group' => $locationGroup
                ]
            );
        } else {
            $request = new Request(
                'get',
                $this->getPath('/knowledge/articles')
                ,
                [
                    'content-type'     => 'application/json',
                    'x-location-group' => $locationGroup
                ]
            );
        }
        $response = $this->send($client, $request);
        return $response;
    }

    /**
     * @param string $articleId
     * @param string $locationGroup
     *
     * @return mixed
     * @throws \Exception
     */
    public function readOne($articleId, $locationGroup)
    {
        $client = new GuzzleClient();
        $request = new Request(
            'get',
            $this->getPath(sprintf('/knowledge/articles/%s', $articleId)),
            [
                'content-type'     => 'application/json',
                'x-location-group' => $locationGroup
            ]
        );

        $response = $this->send($client, $request);
        return $response;
    }

    /**
     * @param array  $data
     * @param string $locationGroup
     *
     * @return mixed
     * @throws \Exception
     */
    public function create($data, $locationGroup)
    {
        $client = new GuzzleClient();
        $request = new Request(
            'post',
            $this->getPath('/knowledge/articles'),
            [
                'content-type'     => 'application/json',
                'x-location-group' => $locationGroup
            ],
            json_encode($data)
        );
        $response = $this->send($client, $request);
        return $response;
    }

    /**
     * @param string $articleId
     * @param array  $data
     * @param string $locationGroup
     *
     * @return mixed
     * @throws \Exception
     */
    public function update($articleId, $data, $locationGroup)
    {
        $client = new GuzzleClient();
        $request = new Request(
            'put',
            $this->getPath(sprintf('/knowledge/articles/%s', $articleId)),
            [
                'content-type'     => 'application/json',
                'x-location-group' => $locationGroup
            ],
            json_encode($data)
        );
        $response = $this->send($client, $request);
        return $response;
    }

    /**
     * @param string $articleId
     * @param string $locationGroup
     *
     * @return mixed
     * @throws \Exception
     */
    public function delete($articleId, $locationGroup)
    {
        $client = new GuzzleClient();
        $request = new Request(
            'delete',
            $this->getPath(sprintf('/knowledge/articles/%s', $articleId)),
            [
                'content-type'     => 'application/json',
                'x-location-group' => $locationGroup
            ]
        );
        $response = $this->send($client, $request);
        return $response;
    }

    /**
     * @param string $locationGroup
     *
     * @return mixed
     * @throws \Exception
     */
    public function tags($locationGroup)
    {
        $client = new GuzzleClient();
        $request = new Request(
            'get',
            $this->getPath('/knowledge/tags'),
            [
                'content-type'     => 'application/json',
                'x-location-group' => $locationGroup
            ]
        );
        $response = $this->send($client, $request);
        return $response;
    }
}
