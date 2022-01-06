<?php

/**
 * @OA\Parameter(
 *     parameter="TransactionID",
 *     name="transaction_id",
 *     in="path",
 *     required=true,
 *     description="Transaction identifier.",
 *     @OA\Schema(type="integer")
 * ),
 * @OA\Parameter(
 *     parameter="Statuses",
 *     name="statuses",
 *     in="query",
 *     required=false,
 *     description="Transaction statuses.",
 *     @OA\Schema(
 *          type="array",
 *          @OA\Items(
 *              type="string",
 *              enum={"in_process", "done", "failed", "refunded"},
 *              example="in_progress"
 *          )
 *     )
 * )
 */
