<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Role extends Model
{
    public $timestamps = true;
    protected $guarded = [];
    use HasFactory;

    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->BelongsToMany(User::class);
    }

    public static function getRoleNumber(string $roleName): int
    {
        $selectedRole = self::where('name', '=', $roleName)->first();
        if ($selectedRole === null) {
            throw new \RuntimeException('El rol buscado no existe');
        }
        return $selectedRole->customId;
    }

    public static function getRoleIdByName(string $roleName): int
    {
        $selectedRole = self::where('name', '=', $roleName)->first();
        if ($selectedRole === null) {
            throw new \RuntimeException('El rol buscado no existe');
        }
        return $selectedRole->id;
    }


    public static function assignRole(int $userId, int $roleId): void
    {
        DB::table('role_user')->insert(
            [
                'user_id' => $userId,
                'role_id' => $roleId,
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ]
        );
    }

}
