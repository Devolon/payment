<?php

/**
 * @OA\Post (
 *     path="/api/app/payment/transaction",
 *     tags={"Payment [Transaction]"},
 *     operationId="payment/transaction.create",
 *     summary="Create transaction endpoint",
 *     description="To create new transaction this endpoint should be called.",
 *     @OA\RequestBody (ref="#/components/requestBodies/PaymentTransactionRequestBody"),
 *     @OA\Response (
 *         response=201,
 *         ref="#/components/responses/PaymentTransactionResultResourceResponse"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         ref="#/components/responses/ForbiddenResponse"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         ref="#/components/responses/UnauthorizedResponse"
 *     ),
 *     @OA\Response (
 *         response=422,
 *         description="Unprocessable Entity.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 ref="#/components/schemas/InvalidTransactionRequest"
 *             )
 *         )
 *     ),
 *    security={
 *         {"bearerAuth": {}}
 *     }
 * )
 */

/**
 * @OA\Get (
 *     path="/api/app/payment/transaction",
 *     tags={"Payment [Transaction]"},
 *     operationId="payment/transaction.index",
 *     summary="retrieve available payment gateways",
 *     description="this end point will return all user transaction information.",
 *     @OA\Parameter(ref="#/components/parameters/PerPage"),
 *     @OA\Parameter(ref="#/components/parameters/Page"),
 *     @OA\Response(
 *         response=200,
 *         ref="#/components/responses/PaymentTransactionCollectionResponse"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         ref="#/components/responses/UnauthorizedResponse"
 *     ),
 *     security={
 *         {"bearerAuth": {}}
 *     }
 * )
 */

/**
 * @OA\Patch (
 *     path="/api/app/payment/transaction/{transaction_id}",
 *     tags={"Payment [Transaction]"},
 *     operationId="payment/transaction.update",
 *     summary="Update transaction endpoint",
 *     description="To change status of transaction to failed or done this endpoint should be called.",
 *     @OA\Parameter(ref="#/components/parameters/TransactionID"),
 *     @OA\RequestBody (ref="#/components/requestBodies/PaymentUpdateTransactionRequestBody"),
 *     @OA\Response (
 *         response=200,
 *         ref="#/components/responses/PaymentTransactionResourceResponse"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         ref="#/components/responses/UnauthorizedResponse"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         ref="#/components/responses/UnauthorizedResponse"
 *     ),
 *     @OA\Response (
 *         response=422,
 *         description="Unprocessable Entity.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 ref="#/components/schemas/InvalidUpdateTransactionRequest"
 *             )
 *         )
 *     ),
 *    security={
 *         {"bearerAuth": {}}
 *     }
 * )
 */

/**
 * @OA\Get (
 *     path="/api/app/payment/gateway",
 *     tags={"Payment [Gateway]"},
 *     operationId="payment/gateway.list",
 *     summary="retrieve available payment gateways",
 *     description="this end point will return list of available payment gateways.",
 *     @OA\Response(
 *         response=200,
 *         ref="#/components/responses/PaymentGatewayListResourceResponse"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         ref="#/components/responses/UnauthorizedResponse"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         ref="#/components/responses/UnauthorizedResponse"
 *     ),
 *     security={
 *         {"bearerAuth": {}}
 *     }
 * )
 *
 * Class GetAdminController
 * @package App\Modules\Admin\Auth\Controllers
 */

/**
 * @OA\Get (
 *     path="/api/app/payment/transaction",
 *     tags={"Payment [Transaction]"},
 *     operationId="payment/transaction.index",
 *     summary="retrieve available payment gateways",
 *     description="this end point will return all user transaction information.",
 *     @OA\Parameter(ref="#/components/parameters/PerPage"),
 *     @OA\Parameter(ref="#/components/parameters/Page"),
 *     @OA\Response(
 *         response=200,
 *         ref="#/components/responses/PaymentTransactionCollectionResponse"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         ref="#/components/responses/UnauthorizedResponse"
 *     ),
 *     security={
 *         {"bearerAuth": {}}
 *     }
 * )
 */
