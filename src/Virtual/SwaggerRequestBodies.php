<?php

/**
 * @OA\RequestBody (
 *     request="PaymentTransactionRequestBody",
 *     required=true,
 *     description="Create transaction request body",
 *     @OA\JsonContent(ref="#/components/schemas/PaymentTransactionRequest")
 * )
 *
 * @OA\Schema (
 *     schema="PaymentTransactionRequest",
 *     required={"product_type", "payment_method"},
 *     @OA\Property(property="product_type", type="string", maxLength=255, example="dummy"),
 *     @OA\Property(property="payment_method", type="string", maxLength=255, example="dummy"),
 *     @OA\Property(property="payment_method_data", type="object"),
 * )
 *
 * @OA\Schema (
 *     schema="InvalidTransactionRequest",
 *     @OA\Property(
 *         property="product_type",
 *         type="array",
 *         @OA\Items(
 *             type="string",
 *             example={"product_type is not valid."},
 *         )
 *     ),
 *     @OA\Property(
 *         property="payment_method",
 *         type="array",
 *         @OA\Items(
 *             type="string",
 *             example={"payment_method is not valid."},
 *         )
 *     ),
 *     @OA\Property(
 *         property="payment_method_data",
 *         type="array",
 *         @OA\Items(
 *             type="string",
 *             example={"payment_method_data is not valid."},
 *         )
 *     ),
 * )
 */

/**
 * @OA\RequestBody (
 *     request="PaymentUpdateTransactionRequestBody",
 *     required=true,
 *     description="Update transaction request body",
 *     @OA\JsonContent(ref="#/components/schemas/PaymentUpdateTransactionRequest")
 * )
 *
 * @OA\Schema (
 *     schema="PaymentUpdateTransactionRequest",
 *     required={"status"},
 *     @OA\Property(property="status", type="string", maxLength=255, example="failed", enum={"failed", "done"}),
 *     @OA\Property(property="payment_method_data", type="object"),
 * )
 *
 * @OA\Schema (
 *     schema="InvalidUpdateTransactionRequest",
 *     @OA\Property(
 *         property="status",
 *         type="array",
 *         @OA\Items(
 *             type="string",
 *             example={"status is not valid."},
 *         )
 *     ),
 *     @OA\Property(
 *         property="payment_method_data",
 *         type="array",
 *         @OA\Items(
 *             type="string",
 *             example={"payment_method_data is not valid."},
 *         )
 *     ),
 * )
 *
 * @OA\RequestBody (
 *     request="PaymentChangeTransactionStatusRequestBody",
 *     required=true,
 *     description="Change transaction status request body",
 *     @OA\JsonContent(ref="#/components/schemas/PaymentChangeTransactionStatusRequest")
 * )
 *
 * @OA\Schema (
 *     schema="PaymentChangeTransactionStatusRequest",
 *     required={"value"},
 *     @OA\Property(property="value", type="string", maxLength=255, example="failed", enum={"failed", "done"}),
 * )
 *
 * @OA\Schema (
 *     schema="InvalidChangeTransactionStatusRequest",
 *     @OA\Property(
 *         property="value",
 *         type="array",
 *         @OA\Items(
 *             type="string",
 *             example={"status is not valid."},
 *         )
 *     ),
 * )
 */
