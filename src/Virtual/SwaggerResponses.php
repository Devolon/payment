<?php

/**
 * @OA\Response (
 *     response="PaymentGatewayListResourceResponse",
 *     description="payment gateway list resource response",
 *     @OA\JsonContent(ref="#/components/schemas/PaymentGatewayListResource")
 * )
 * @OA\Schema (
 *     schema="PaymentGatewayListResource",
 *     required={"payment_gateways"},
 *     @OA\Property(
 *         property="payment_gateways",
 *         readOnly=true,
 *         type="array",
 *         @OA\Items(type="string"),
 *         example={"dummy"}
 *     ),
 * )
 */

/**
 * @OA\Schema (
 *     schema="PaymentTransactionRedirectResource",
 *     required={"redirect_url", "redirect_method", "redirect_data"},
 *     @OA\Property(property="redirect_url", readOnly=true, type="string", maxLength=255, example="http://example.com"),
 *     @OA\Property(
 *         property="redirect_method",
 *         readOnly=true,
 *         type="string",
 *         maxLength=255,
 *         example="POST",
 *         enum={"POST", "GET"}
 *     ),
 *     @OA\Property(property="redirect_data", readOnly=true, type="object"),
 * )
 */

/**
 * @OA\Response (
 *     response="PaymentTransactionResourceResponse",
 *     description="transaction resource response",
 *     @OA\JsonContent(ref="#/components/schemas/PaymentTransactionResource")
 * )
 *
 * @OA\Schema (
 *     schema="PaymentTransactionResource",
 *     required={"id", "status", "product_type", "product_id", "payment_method", "created_at", "updated_at",
 *         "money_amount"},
 *     @OA\Property(property="id", readOnly=true, type="integer", example=10),
 *     @OA\Property(
 *         property="status",
 *         readOnly=true, type="string",
 *         enum={"in_process", "done", "failed"},
 *         example="dummy"
 *     ),
 *     @OA\Property(property="product_type", readOnly=true, type="string", maxLength=255, example="dummy"),
 *     @OA\Property(property="product_id", readOnly=true, type="integer", example=10),
 *     @OA\Property(property="payment_method", readOnly=true, type="string", maxLength=255, example="dummy"),
 *     @OA\Property(property="money_amount", readOnly=true, type="string", maxLength=255, example="68.99"),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Initial creation timestamp",
 *         readOnly=true
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Last update timestamp",
 *         readOnly=true
 *     ),
 *     @OA\Property(
 *         property="payment_method_data",
 *         type="object",
 *         oneOf={
 *             @OA\Schema(ref="#/components/schemas/BulkOrderCodeGatewayPaymentTransactionResponse")
 *         }
 *     ),
 * )
 */

/**
 * @OA\Response (
 *     response="PaymentTransactionResultResourceResponse",
 *     description="transaction resource response",
 *     @OA\JsonContent(ref="#/components/schemas/PaymentTransactionResultResource")
 * )
 * @OA\Schema (
 *     schema="PaymentTransactionResultResource",
 *     required={"transaction", "should_redirect", "redirect_to"},
 *     @OA\Property(
 *          property="transaction",
 *          type="object",
 *          ref="#/components/schemas/PaymentTransactionResource"
 *     ),
 *     @OA\Property(property="should_redirect", readOnly=true, type="boolean", example="true"),
 *     @OA\Property(
 *          property="redirect_to",
 *          type="object",
 *          ref="#/components/schemas/PaymentTransactionRedirectResource"
 *     ),
 * )
 */

/**
 * @OA\Response (
 *     response="PaymentTransactionCollectionResponse",
 *     description="App transaction collection response",
 *     @OA\JsonContent(ref="#/components/schemas/PaymentTransactionCollection")
 * )
 * @OA\Schema (
 *     schema="PaymentTransactionCollection",
 *     type="object",
 *     allOf={
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/PaymentTransactionResource")
 *             )
 *         ),
 *         @OA\Schema(ref="#/components/schemas/Pagination")
 *     },
 * )
 */
