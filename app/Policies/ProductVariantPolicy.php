<?php

namespace App\Policies;

use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductVariantPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProductVariant $productVariant): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return ($user->type === User::TYPE_ADMIN || $user->type === User::TYPE_SELLER);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ProductVariant $productVariant): bool
    {
        $product = $productVariant->product()->first();
        return ($user->id === $product->user_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ProductVariant $productVariant): bool
    {
        $product = $productVariant->product()->first();
        return ($user->id === $product->user_id);
    }
}
