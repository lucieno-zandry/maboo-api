<?php

namespace App\Actions;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CartActions extends Actions
{
    protected $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    public function getSimilarCartItem(array $cartItem): CartItem|Model|null
    {
        $CartItem = CartItem::where('product_id', $cartItem['product_id'])->where('ordered_at', null);

        if (isset($cartItem['product_variant_id']) && !empty($cartItem['product_variant_id'])) {
            $CartItem = $CartItem->where('product_variant_id', $cartItem['product_variant_id']);
        }

        return $CartItem->first();
    }

    public function editCartItem(int $id, array $cartItem)
    {
        $item = CartItem::find($id);

        if ($cartItem['quantity']) {
            $subtotal = $this->getRowSubtotal($item->product_id, [
                'quantity' => $cartItem['quantity'],
                'product_variant_id' => $item->product_variant_id,
                'product_color_id' => $item->product_color_id,
            ]);

            $cartItem['subtotal'] = $subtotal;
        }

        $updated = $item->update($cartItem);
        return $updated;
    }

    public function addToCart(array $cartItem): bool
    {
        $cartItem['user_id'] = $this->user->id;
        $existingCartItem = $this->getSimilarCartItem($cartItem);

        if ($existingCartItem) {
            return $this->editCartItem($existingCartItem->id, $cartItem);
        }

        $options = [];

        $options['quantity'] = $cartItem['quantity'];

        if (isset($cartItem['product_variant_id'])) {
            $options['product_variant_id'] = $cartItem['product_variant_id'];
        }

        if (isset($cartItem['product_color_id'])) {
            $options['product_color_id'] = $cartItem['product_color_id'];
        }

        $subtotal = $this->getRowSubtotal($cartItem['product_id'], $options);
        $cartItem['subtotal'] = $subtotal;

        $added = CartItem::create($cartItem);
        return boolval($added);
    }

    public function getCartItems(?array $ids = null): Collection
    {
        $CartItem = CartItem::where('user_id', $this->user->id)
            ->where('ordered_at', null);

        if ($ids && !empty($ids)) {
            $CartItem = $CartItem->whereIn('id', $ids);
        }

        return $CartItem->get();
    }

    public function deleteCartItems(array $cartItemIds): bool
    {
        $deleted = CartItem::whereIn('id', $cartItemIds)->delete();
        return $deleted;
    }

    private function findVariant($variants, int $variant_id): Model|ProductVariant
    {
        $key = $variants->search(function ($variant) use ($variant_id) {
            return $variant->id === $variant_id;
        });

        return $variants[$key];
    }

    public function getProductPrice(Model|Product $product, array $options)
    {
        $quantity = $options['quantity'];
        $product_variant_id = isset($options['product_variant_id']) ? $options['product_variant_id'] : null;
        $productPrice = ($product->sale_price > 0 ? $product->sale_price : $product->price);

        if ($product->variants && $product_variant_id) {
            $variant = $this->findVariant($product->variants, $product_variant_id);

            if ($variant) {
                $productPrice = $variant->price;
            }
        }

        return $productPrice * $quantity;
    }

    public function getRowSubtotal(int $productId, array $options): int
    {
        $product = Product::find($productId);

        if ($product) {
            $subtotal = $this->getProductPrice($product, $options);
            return $subtotal;
        } else {
            throw ValidationException::withMessages([
                'product_id' => 'The product does not exist'
            ]);
        }
    }
}