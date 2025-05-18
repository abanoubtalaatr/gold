<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
    /**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Fudex API Documentation",
 *      description="This is the API documentation for the Fudex application",
 *      @OA\Contact(
 *          email="support@Fudex.com"
 *      ),
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *      )
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;


}
