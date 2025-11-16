# Authentication & Authorization Reference

This document summarizes the auth system implemented in this Laravel + Inertia + Vue application using Spatie Laravel Permission.

## Backend (Laravel)

### Packages & Setup

- **Spatie Laravel Permission**: Installed via Composer (`spatie/laravel-permission`).
- **User Model**: Located at `app/Models/User.php`, uses `HasRoles` trait.
- **Seeding**: Permissions and roles are seeded via `database/seeders/SpatiePermissionSeeder.php`.
  - Roles: `admin`, `doctor`, `nurse`, `patient`.
  - Permissions: Various like `view_users`, `create_patients`, `view_billing`, etc. (see seeder for full list).

### Middleware

- **ShareInertiaData**: Located at `app/Http/Middleware/ShareInertiaData.php`.
  - Shares user roles and permissions to Inertia via `Inertia::share()`.
  - Data: `auth.user.roles` (array of role names), `auth.user.permissions` (array of permission names).
  - Registered in `bootstrap/app.php` in the `web` middleware group.

## Frontend (Vue + Inertia)

### Composable

- **useAuth**: Located at `resources/js/composables/useAuth.ts`.
  - Provides reactive helpers: `roles`, `permissions`, `hasRole(name)`, `hasPermission(name)`, `hasAnyPermission(array)`, `hasAllPermissions(array)`, `isAdmin`.
  - Handles missing auth data gracefully (e.g., guest users).
  - Uses `computed` for reactivity.

### Types

- **NavItem**: Extended in `resources/js/types/index.d.ts` with optional `permissions?: string | string[]` for per-item permission checks.

### Components

- **AppSidebar.vue**: Uses `useAuth` to filter nav items based on permissions.
  - Each nav item has a `permissions` property.
  - Filtered arrays (e.g., `filteredCoreNavItems`) use `isAllowed()` helper, which checks permissions and allows admin override.
  - Groups hide if no items are visible.

## Usage Examples

### In Vue Components

```vue
<script setup lang="ts">
import { useAuth } from '@/composables/useAuth';

const { hasRole, hasPermission } = useAuth();
</script>

<template>
  <div v-if="hasRole('admin')">
    Admin Panel
  </div>
  <button v-if="hasPermission('edit_users')">
    Edit User
  </button>
</template>
```

### In PHP Controllers/Policies

```php
// Check permission
if ($user->can('view_patients')) {
    // Show patients
}

// Check role
if ($user->hasRole('doctor')) {
    // Doctor-specific logic
}
```

### Assigning Roles/Permissions (via Tinker or Seeders)

```php
$user = User::find(1);
$user->assignRole('admin');
$user->givePermissionTo('view_billing');
```

## Testing

- Seed permissions/roles: `php artisan db:seed --class=SpatiePermissionSeeder`
- Assign to users via Tinker.
- Login and verify sidebar/UI hides/shows items based on permissions.

## Notes

- Admins see everything (override in `isAllowed`).
- Permissions are granular; roles bundle permissions.
- Extend `NavItem` for roles if needed: `roles?: string | string[]`.
- For guest pages, auth data is empty arrays.
