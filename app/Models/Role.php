<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['name'];

    public static function createDefaultRoles()
    {
        $roles = [ 'personnel', 'client'];

        foreach ($roles as $role) {
            if(!$roles){
            $roles::create(['name' => $role]);
        }}
    }
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
