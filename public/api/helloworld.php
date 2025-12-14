<?php
/**
 * @api {get} /api/helloworld Saludo de la API
 * @apiName HelloWorld
 * @apiGroup Status
 * @apiSuccess {String} message Mensaje de bienvenida
 * @apiSuccessExample {json} Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "success": true,
 *       "message": "Success",
 *       "data": {
 *         "message": "Hello World! API is running."
 *       }
 *     }
 */
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once BASE_PATH . '/src/utils/Response.php';

Response::success(['message' => 'Hello World! API is running.']);

