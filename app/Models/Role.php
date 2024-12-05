<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'slug',
        'description',
        'is_admin',
        'active'
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'active' => 'boolean'
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    // Check if role has specific permission
    public function hasPermission($permission)
    {
        if ($this->is_admin) {
            return true;
        }

        if (is_string($permission)) {
            return $this->permissions->contains('slug', $permission);
        }

        return $permission->intersect($this->permissions)->count();
    }

    // Assign permissions to role
    public function assignPermissions($permissions)
    {
        if (is_string($permissions)) {
            $permissions = Permission::whereSlug($permissions)->get();
        }

        if ($permissions instanceof Permission) {
            $permissions = collect([$permissions]);
        }

        $this->permissions()->syncWithoutDetaching($permissions);
    }

    // Remove permissions from role
    public function removePermissions($permissions)
    {
        if (is_string($permissions)) {
            $permissions = Permission::whereSlug($permissions)->get();
        }

        if ($permissions instanceof Permission) {
            $permissions = collect([$permissions]);
        }

        $this->permissions()->detach($permissions);
    }
}
