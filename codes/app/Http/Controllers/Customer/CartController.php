<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Actions\CartActions;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function get(CartActions $cartActions)
    {
        return response()->json(['cart' => $cartActions->getCartItems()]);
    }

    public function add(AddToCartRequest $request, CartActions $cartActions)
    {
        $cartItem = $request->validated();
        $added = $cartActions->addToCart($cartItem);

        return response()->json(['added' => $added]);
    }

    public function delete(Request $request, CartActions $cartActions)
    {
        $validated = $request->validate([
            'ids' => 'array',
            'ids.*' => 'exists:cart_items,id',
        ]);

        $deleted = $cartActions->deleteCartItems($validated['ids']);
        return response()->json(['deleted' => $deleted]);
    }

    public function update(int $id, UpdateCartItemRequest $request, CartActions $cartActions)
    {
        $data = $request->validated();

        $updated = $cartActions->editCartItem($id, $data);
        return response()->json(['updated' => $updated]);
    }
}