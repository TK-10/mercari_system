<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    // 旧: public function report(Exception $exception)
    public function report(Throwable $e)
    {
        parent::report($e);
    }

    // 旧: public function render($request, Exception $exception)
    public function render($request, Throwable $e)
    {
        return parent::render($request, $e);
    }
}
