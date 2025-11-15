import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export function useAuth() {
    const page = usePage();

    // Make sure these remain reactive by creating computed wrappers
    const roles = computed(() => page.props?.auth?.user?.roles ?? [] as string[]);
    const permissions = computed(() => page.props?.auth?.user?.permissions ?? [] as string[]);

    const hasRole = (name: string) => roles.value.includes(name);
    const hasPermission = (name: string) => permissions.value.includes(name);
    const hasAnyPermission = (perms: string[]) => perms.some((p) => hasPermission(p));
    const hasAllPermissions = (perms: string[]) => perms.every((p) => hasPermission(p));

    const isAdmin = computed(() => hasRole('admin'));

    return {
        roles,
        permissions,
        hasRole,
        hasPermission,
        hasAnyPermission,
        hasAllPermissions,
        isAdmin,
    };
}
