use App\Models\Role;
use App\Policies\RolePolicy;
use App\Models\AdminUser;
use App\Policies\AdminUserPolicy;
use App\Models\Order;
use App\Policies\OrderPolicy;

protected $policies = [
    // Existing policy mappings...

    Role::class => RolePolicy::class,
    AdminUser::class => AdminUserPolicy::class,
    Order::class => OrderPolicy::class,

];