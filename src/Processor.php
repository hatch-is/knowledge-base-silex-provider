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
            return $response->getBody();
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
            'message' => 'Something bad happened with Knowledge Base service',
            'request' => [
                'headers' => $httpException->getRequest()->getHeaders(),
                'body' => $httpException->getRequest()->getBody()
            ],
            'response' => [
                'headers' => $httpException->getResponse()->getHeaders(),
                'body' => $httpException->getResponse()->getBody()->getContents(
                ),
                'status' => $httpException->getResponse()->getStatusCode()
            ]
        ];

        return $message;
    }

    /**
     * @param array $filter
     *
     * @return mixed
     * @throws \Exception
     */
    public function read($filter = [])
    {
        $client = new GuzzleClient();
        if (!empty($filter)) {
            $query = ['filter' => json_encode($filter)];
            $query = http_build_query($query);
            $request = new Request(
                'get',
                $this->getPath(sprintf('/knowledge/articles?filter=%s', $query))
            );
        } else {
            $request = new Request(
                'get',
                $this->getPath('/knowledge/articles')
            );
        }
        $response = $this->send($client, $request);
        return json_decode($response->getContents());
    }

    /**
     * @param string $articleId
     *
     * @return mixed
     * @throws \Exception
     */
    public function readOne($articleId)
    {
        $client = new GuzzleClient();
        $request = new Request(
            'get',
            $this->getPath(sprintf('/knowledge/articles/%s', $articleId))
        );

        $response = $this->send($client, $request);
        return json_decode($response->getContents());
    }

    /**
     * @param array $data
     *
     * @return mixed
     * @throws \Exception
     */
    public function create($data)
    {
        $client = new GuzzleClient();
        $request = new Request(
            'post',
            $this->getPath('/knowledge/articles'),
            ['content-type' => 'application/json'],
            json_encode($data)
        );
        $response = $this->send($client, $request);
        return json_decode($response->getContents());
    }

    /**
     * @param string $articleId
     * @param array $data
     *
     * @return mixed
     * @throws \Exception
     */
    public function update($articleId, $data)
    {
        $client = new GuzzleClient();
        $request = new Request(
            'put',
            $this->getPath(sprintf('/knowledge/articles/%s', $articleId)),
            ['content-type' => 'application/json'],
            json_encode($data)
        );
        $response = $this->send($client, $request);
        return json_decode($response->getContents());
    }

    /**
     * @param string $articleId
     *
     * @return mixed
     * @throws \Exception
     */
    public function delete($articleId)
    {
        $client = new GuzzleClient();
        $request = new Request(
            'delete',
            $this->getPath(sprintf('/knowledge/articles/%s', $articleId)),
            ['content-type' => 'application/json']
        );
        $response = $this->send($client, $request);
        return json_decode($response->getContents());
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function tags()
    {
        $client = new GuzzleClient();
        $request = new Request(
            'get',
            $this->getPath('/knowledge/tags')
        );
        $response = $this->send($client, $request);
        return json_decode($response->getContents());
    }
}
