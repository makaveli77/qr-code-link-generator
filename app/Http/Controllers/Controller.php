<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "QR Code Link Generator API",
    version: "1.0.0",
    description: "API documentation for the QR Code Link Generator application."
)]
#[OA\Server(
    url: "http://localhost:8001/api",
    description: "API Server"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
class Controller extends \Illuminate\Routing\Controller
{
    use \Illuminate\Foundation\Auth\Access\AuthorizesRequests, 
        \Illuminate\Foundation\Bus\DispatchesJobs, 
        \Illuminate\Foundation\Validation\ValidatesRequests;
}
