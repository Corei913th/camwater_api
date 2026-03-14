<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "API CAMWATER PRO",
    description: "Documentation de l' api CAMWATER PRO (Abonnés,Factures,Reclammations)",
    contact: new OA\Contact(email: "support@camwaterpro.com"),
    license: new OA\License(name: "Apache 2.0", url: "http://www.apache.org/licenses/LICENSE-2.0.html")
)]
#[OA\Server(url: "http://localhost:8000", description: "Serveur Local")]
#[OA\SecurityScheme(
    securityScheme: "sanctum",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
abstract class Controller
{
    //
}
