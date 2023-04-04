<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Response as IlluminateResponse;

trait ResponsableTrait
{
    protected int $statusCode = 200;
    protected bool $status = true;

    public function getStatus(): bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function respondWithPagination(mixed $data, LengthAwarePaginator $paginator, string $message = 'success'): JsonResponse
    {
        $code = $this->getStatusCode();

        return response()->json([
            'status_code' => $code,
            'status'      => $this->status,
            'message'     => $message,
            'data'        => $data,
            'meta'        => [
                'current_page' => $paginator->currentPage(),
                'from'         => $paginator->firstItem(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'to'           => $paginator->lastItem(),
                'total'        => $paginator->total(),
            ]
        ], $code);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode): static
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function respondNotFound(string $message = 'Not Found'): JsonResponse
    {
        return $this
            ->setStatus(false)
            ->setStatusCode(Response::HTTP_NOT_FOUND)
            ->respondWithError($message);
    }

    public function respondWithError(string $message): JsonResponse
    {
        $this->setStatus(false);

        if ($this->getStatusCode() == 200) {
            $this->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        return $this->respond([
            'message' => $message,
        ], 'Request failed');
    }

    public function respond(mixed $data, string $message = 'success'): JsonResponse
    {
        $code = $this->getStatusCode();

        return response()->json([
            'status_code' => $code,
            'status'      => $this->status,
            'message'     => $message,
            'data'        => $data,
        ], $code);
    }

    protected function respondUnauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return $this
            ->setStatus(false)
            ->setStatusCode(Response::HTTP_UNAUTHORIZED)
            ->respondWithError($message);
    }

    protected function respondForbidden(string $message = 'Forbidden'): JsonResponse
    {
        return $this
            ->setStatus(false)
            ->setStatusCode(Response::HTTP_FORBIDDEN)
            ->respondWithError($message);
    }
}
