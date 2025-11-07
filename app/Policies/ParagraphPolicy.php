<?php

namespace App\Policies;

use App\Models\Paragraph;
use App\Models\User;

class ParagraphPolicy
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
    public function view(User $user, Paragraph $paragraph): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->type === User::TYPE_ADMIN || $user->type === User::TYPE_PROFESSIONAL || $user->type === User::TYPE_SELLER;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Paragraph $paragraph): bool
    {
        return $user->type === User::TYPE_ADMIN || $user->id === $paragraph->subsection->section->article->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Paragraph $paragraph): bool
    {
        return $user->type === User::TYPE_ADMIN || $user->id === $paragraph->subsection->section->article->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Paragraph $paragraph): bool
    {
        return $user->type === User::TYPE_ADMIN || $user->id === $paragraph->subsection->section->article->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Paragraph $paragraph): bool
    {
        return $user->type === User::TYPE_ADMIN || $user->id === $paragraph->subsection->section->article->user_id;
    }
}
