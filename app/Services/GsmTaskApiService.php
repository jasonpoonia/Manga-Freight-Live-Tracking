<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GsmTaskApiService
{
    const API_URL = "https://api.gsmtasks.com";
    static array $endpoints = [
        "auth" => "/authenticate/",
        "tasks" => "/tasks",
        "task_events" => '/tasks/{task_id}/events/'
    ];
    private string $username;
    private string $password;
    private ?string $token;

    public function __construct()
    {
        $this->username = getenv('GSM_TASKS_USERNAME');
        $this->password = getenv('GSM_TASKS_PASSWORD');
    }

    public function getEndpoint($endpoint, $data = [], $query = []): string
    {
        $endpoint = self::API_URL.self::$endpoints[$endpoint];

        foreach ($data as $key => $value) {
            $endpoint = str_replace('{'.$key.'}', $value, $endpoint);
        }

        $queryString = collect($query)->map(function ($value, $key) {
            return join('=', [$key, $value]);
        })->join('&');

        if (!empty($queryString)) {
            return $endpoint."?".$queryString;
        }

        return $endpoint;
    }

    public function authenticate(): object|array|bool|null
    {
        $response = Http::post($this->getEndpoint('auth'), [
            'username' => $this->username,
            'password' => $this->password,
        ]);

        if ($response->ok()) {
            return $response->object();
        }

        return false;
    }

    public function getCacheKey(): string
    {
        return 'GSM_TASKS_ACCESS_TOKEN-'.session()->getId();
    }

    public function getToken(): bool
    {
        if (!empty(cache($this->getCacheKey()))) {
            $this->token = cache($this->getCacheKey());
            return true;
        }

        $token = Cache::remember($this->getCacheKey(), now()->addMinutes(15), function () {
            $authResponse = $this->authenticate();
            if (!empty($authResponse->token)) {
                return $authResponse->token;
            }
            return null;
        });

        if ($token) {
            $this->token = $token;
            return true;
        }

        return false;
    }

    public function getHeaders(): array
    {
        return [
            'Authorization' => "Token " . $this->token,
            'Accept' => 'application/json; version=2.4.34'
        ];
    }

    /**
     * @throws HttpClientException
     */
    private function getApiClient(): PendingRequest
    {
        if (!$this->getToken()) {
            throw new HttpClientException("Failed to fetch api token.");
        }

        return Http::withHeaders($this->getHeaders());
    }

    /**
     * @throws Exception
     */
    private function getApiResponse($endpoint, $params = [], $method = 'get')
    {
        $client = $this->getApiClient();

        $response = match ($method) {
            'post' => $client->post($endpoint, $params),
            default => $client->get($endpoint, $params),
        };

        if ($response->ok()) {
            return $response->json();
        }

        throw new Exception("Failed to retrieve tasks.");
    }

    /**
     * @throws HttpClientException
     * @throws Exception
     */
    public function getTasks()
    {
        return $this->getApiResponse(
            $this->getEndpoint('tasks')
        );
    }
    /**
     * Attempts to retrieve a task by order number. If no task is found with the initial order number,
     * it retries with the order number appended by "/1". If still no task is found, it returns a message
     * indicating that no tasks were returned.
     *
     * @param string $orderNumber The initial order number to search for.
     * @return array|string An array of task(s) if found, otherwise a message indicating no tasks were returned.
     * @throws HttpClientException
     * @throws Exception
     */
    public function getTaskByOrderNumber($orderNumber)
    {
        $endpoint = $this->getEndpoint('tasks');
    
        // First attempt with the original order number
        $tasks = $this->getApiResponse($endpoint, [
            'order__reference' => $orderNumber
        ]);
    
        // Check if tasks were returned, if not, retry with "/1" appended
        if (empty($tasks)) {
            $retryOrderNumber = $orderNumber . '/1';
            $tasks = $this->getApiResponse($endpoint, [
                'order__reference' => $retryOrderNumber
            ]);
    
            // If still no tasks, return a message indicating no results
            if (empty($tasks)) {
                return "No tasks found for order number {$orderNumber} or {$retryOrderNumber}.";
            }
        }
    
        return $tasks;
    }
    /**
     * @throws HttpClientException
     * @throws Exception
     
    public function getTaskByOrderNumber($orderNumber)
    {
        $endpoint = $this->getEndpoint('tasks');

        return $this->getApiResponse($endpoint, [
            'order__reference' => $orderNumber
        ]);
    }*/

    /**
     * @throws Exception
     */
    public function getTaskEventsByOrderNumber($orderNumber)
    {
        $tasks = $this->getTaskByOrderNumber($orderNumber);
        $taskId = $tasks[0]['id'] ?? null;
        if(!$taskId) return [];

        return $this->getTaskEvents($taskId);
    }

    /**
     * @throws Exception
     */
    public function getTaskEvents($taskId)
    {
        $endpoint = $this->getEndpoint('task_events', ['task_id' => $taskId]);

        $taskEvents = $this->getApiResponse($endpoint);
        
        // Fetch tasks only for the last one
        $events = $this->getApiResponse($taskEvents[0]['task']);
        $taskEvents[0]['tasks'] = $events ?? [];
        
        return $taskEvents;
    }
}