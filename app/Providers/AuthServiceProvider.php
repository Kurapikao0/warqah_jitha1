use App\Models\Role;
use App\Policies\RolePolicy;
use App\Models\AdminUser;
use App\Policies\AdminUserPolicy;

protected $policies = [
    // Existing policy mappings...

    Role::class => RolePolicy::class,
    AdminUser::class => AdminUserPolicy::class,
];