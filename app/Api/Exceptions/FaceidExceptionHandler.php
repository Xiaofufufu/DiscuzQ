<?php


/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace App\Api\Exceptions;

use App\Exceptions\FaceidException;
use Exception;
use Tobscure\JsonApi\Exception\Handler\ExceptionHandlerInterface;
use Tobscure\JsonApi\Exception\Handler\ResponseBag;

class FaceidExceptionHandler implements ExceptionHandlerInterface
{
    /**
     * If the exception handler is able to format a response for the provided exception,
     * then the implementation should return true.
     *
     * @param \Exception $e
     *
     * @return bool
     */
    public function manages(Exception $e)
    {
        // TODO: Implement manages() method.
        return $e instanceof FaceidException;
    }

    /**
     * Handle the provided exception.
     *
     * @param  $e
     *
     * @return ResponseBag
     */
    public function handle(Exception $e)
    {
        $status = $e->getCode();
        $error  = [
            'status'  => $status,
            'code'    => 'faceid_error',
            'detail' => $e->getMessage(),
        ];
        return new ResponseBag($status, [$error]);
    }
}
