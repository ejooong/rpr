<?php
// app/Policies/ProduksiPolicy.php

namespace App\Policies;

use App\Models\User;
use App\Models\Produksi;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProduksiPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        // Semua user yang login bisa melihat daftar produksi
        return true;
    }

    public function view(User $user, Produksi $produksi): bool
    {
        // Admin dan petugas bisa melihat semua produksi
        if ($user->isAdmin() || $user->isPetugas()) {
            return true;
        }

        // DPD bisa melihat semua produksi
        if ($user->isDpd()) {
            return true;
        }

        // Poktan hanya bisa melihat produksi milik anggota poktan mereka
        if ($user->isPoktan()) {
            return $produksi->demplot->petani->poktan_id === $user->poktan_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        // Admin, petugas, dan poktan bisa membuat produksi
        return $user->isAdmin() || $user->isPetugas() || $user->isPoktan();
    }

    public function update(User $user, Produksi $produksi): bool
    {
        // Admin dan petugas bisa update semua produksi
        if ($user->isAdmin() || $user->isPetugas()) {
            return true;
        }

        // Poktan hanya bisa update produksi milik anggota poktan mereka
        if ($user->isPoktan()) {
            return $produksi->demplot->petani->poktan_id === $user->poktan_id;
        }

        return false;
    }

    public function delete(User $user, Produksi $produksi): bool
    {
        // Hanya admin dan petugas yang bisa menghapus produksi
        return $user->isAdmin() || $user->isPetugas();
    }

    public function restore(User $user, Produksi $produksi): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Produksi $produksi): bool
    {
        return $user->isAdmin();
    }
}