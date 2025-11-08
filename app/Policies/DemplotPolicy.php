<?php
// app/Policies/DemplotPolicy.php

namespace App\Policies;

use App\Models\User;
use App\Models\Demplot;
use Illuminate\Auth\Access\HandlesAuthorization;

class DemplotPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Semua user yang login bisa melihat daftar demplot
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Demplot $demplot): bool
    {
        // Admin dan petugas bisa melihat semua demplot
        if ($user->isAdmin() || $user->isPetugas()) {
            return true;
        }

        // DPD bisa melihat semua demplot
        if ($user->isDpd()) {
            return true;
        }

        // Poktan hanya bisa melihat demplot milik anggota poktan mereka
        if ($user->isPoktan()) {
            return $demplot->petani->poktan_id === $user->poktan_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Admin, petugas, dan poktan bisa membuat demplot
        return $user->isAdmin() || $user->isPetugas() || $user->isPoktan();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Demplot $demplot): bool
    {
        // Admin dan petugas bisa update semua demplot
        if ($user->isAdmin() || $user->isPetugas()) {
            return true;
        }

        // Poktan hanya bisa update demplot milik anggota poktan mereka
        if ($user->isPoktan()) {
            return $demplot->petani->poktan_id === $user->poktan_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Demplot $demplot): bool
    {
        // Hanya admin dan petugas yang bisa menghapus demplot
        if ($user->isAdmin() || $user->isPetugas()) {
            return true;
        }

        // Poktan tidak boleh menghapus demplot
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Demplot $demplot): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Demplot $demplot): bool
    {
        return $user->isAdmin();
    }
}