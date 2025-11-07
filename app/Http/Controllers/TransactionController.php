<?php

namespace App\Http\Controllers;

use App\Events\OrderTransactionEvent;
use App\Models\Order;
use App\Models\Transaction;
use App\Actions\Transaction\OrangeMoneyActions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use VanillaPayActions;

class TransactionController extends Controller
{

    public function purchaseOrder(Request $request): JsonResponse
    {
        $transactionData = $response = [];

        $order_id = $request->order_id;
        $method = $request->method;

        $order = Order::find($order_id);
        $user = $request->user();

        if ($order->transaction?->status === Transaction::STATUS_SUCCESS)
            abort(403);

        $transactionData['transactionnable_id'] = $order_id;
        $transactionData['method'] = $method;
        $transactionData['user_id'] = $order->user_id;

        if ($method === Transaction::METHOD_ORANGEMONEY) {
            $details = (new OrangeMoneyActions)
                ->setAmount($order->total_price)
                ->setOrderId($order_id)
                ->initTransaction();

            $notificationToken = $details->notif_token;
            $paymentToken = $details->pay_token;

            $transactionData['description'] = json_encode([
                'notif_token' => $notificationToken,
                'payment_token' => $paymentToken,
            ]);

            $response['payment_url'] = $details->payment_url;
        } else {
            $vanillaPayActions = new VanillaPayActions;
            $vanillaPayActions
                ->setTotal($order->total_price)
                ->setUsername($user->get_name())
                ->setTransactionnableId($order_id);

            $payment_url = "";

            switch ($method) {
                case Transaction::METHOD_VANILLA_PAY_AIRTEL_MONEY:
                    $payment_url = $vanillaPayActions->get_airtel_money_payment_url();
                    break;

                case Transaction::METHOD_VANILLA_PAY_MVOLA:
                    $payment_url = $vanillaPayActions->get_mvola_payment_url();
                    break;

                case Transaction::METHOD_VANILLA_PAY_ORANGEMONEY:
                    $payment_url = $vanillaPayActions->get_orange_money_payment_url();
                    break;

                default:
                    $payment_url = $vanillaPayActions->get_payment_url();
                    break;
            }

            $response['payment_url'] = $payment_url;
            $transactionData['description'] = json_encode([
                'id' => $vanillaPayActions->getId()
            ]);
        }

        $transaction = Transaction::create($transactionData);
        $order->update([
            'transaction_id' => $transaction->id
        ]);

        return response()->json($response);
    }

    public function vanillaPayCallback(string $transactionnable_id, Request $request)
    {
        $transaction = Transaction::where('transactionnable_id', $transactionnable_id)
            ->with('user')
            ->latest()
            ->first();

        $status = (new VanillaPayActions())->decrypt($request->status);

        switch ($transaction->type) {
            case Transaction::TYPE_ORDER:
                $order = Order::find($transactionnable_id);
                event(new OrderTransactionEvent($order, $status));
                break;

            default:
                break;
        }
    }

    public function orangemoneyCallback(string $transactionnable_id, Request $request)
    {
        $transaction = Transaction::where('transactionnable_id', $transactionnable_id)
            ->with('user')
            ->latest()
            ->first();

        $description = json_decode($transaction?->description);

        if ($description?->notif_token !== $request->notif_token) {
            abort(403);
        }

        switch ($transaction->type) {
            case Transaction::TYPE_ORDER:
                $order = Order::find($transactionnable_id);
                event(new OrderTransactionEvent($order, $request->status));
                break;

            default:
                break;
        }

        $transaction->update([
            'status' => $request->status
        ]);
    }
}
