import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Settings\PackageItemController::store
 * @see app/Http/Controllers/Settings/PackageItemController.php:12
 * @route '/settings/package-items'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/settings/package-items',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Settings\PackageItemController::store
 * @see app/Http/Controllers/Settings/PackageItemController.php:12
 * @route '/settings/package-items'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\PackageItemController::store
 * @see app/Http/Controllers/Settings/PackageItemController.php:12
 * @route '/settings/package-items'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Settings\PackageItemController::store
 * @see app/Http/Controllers/Settings/PackageItemController.php:12
 * @route '/settings/package-items'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Settings\PackageItemController::store
 * @see app/Http/Controllers/Settings/PackageItemController.php:12
 * @route '/settings/package-items'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\Settings\PackageItemController::update
 * @see app/Http/Controllers/Settings/PackageItemController.php:19
 * @route '/settings/package-items/{package_item}'
 */
export const update = (args: { package_item: string | number } | [package_item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/settings/package-items/{package_item}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Settings\PackageItemController::update
 * @see app/Http/Controllers/Settings/PackageItemController.php:19
 * @route '/settings/package-items/{package_item}'
 */
update.url = (args: { package_item: string | number } | [package_item: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { package_item: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    package_item: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        package_item: args.package_item,
                }

    return update.definition.url
            .replace('{package_item}', parsedArgs.package_item.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\PackageItemController::update
 * @see app/Http/Controllers/Settings/PackageItemController.php:19
 * @route '/settings/package-items/{package_item}'
 */
update.put = (args: { package_item: string | number } | [package_item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\Settings\PackageItemController::update
 * @see app/Http/Controllers/Settings/PackageItemController.php:19
 * @route '/settings/package-items/{package_item}'
 */
update.patch = (args: { package_item: string | number } | [package_item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\Settings\PackageItemController::update
 * @see app/Http/Controllers/Settings/PackageItemController.php:19
 * @route '/settings/package-items/{package_item}'
 */
    const updateForm = (args: { package_item: string | number } | [package_item: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Settings\PackageItemController::update
 * @see app/Http/Controllers/Settings/PackageItemController.php:19
 * @route '/settings/package-items/{package_item}'
 */
        updateForm.put = (args: { package_item: string | number } | [package_item: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
            /**
* @see \App\Http\Controllers\Settings\PackageItemController::update
 * @see app/Http/Controllers/Settings/PackageItemController.php:19
 * @route '/settings/package-items/{package_item}'
 */
        updateForm.patch = (args: { package_item: string | number } | [package_item: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PATCH',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    update.form = updateForm
/**
* @see \App\Http\Controllers\Settings\PackageItemController::destroy
 * @see app/Http/Controllers/Settings/PackageItemController.php:26
 * @route '/settings/package-items/{package_item}'
 */
export const destroy = (args: { package_item: string | number } | [package_item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/settings/package-items/{package_item}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Settings\PackageItemController::destroy
 * @see app/Http/Controllers/Settings/PackageItemController.php:26
 * @route '/settings/package-items/{package_item}'
 */
destroy.url = (args: { package_item: string | number } | [package_item: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { package_item: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    package_item: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        package_item: args.package_item,
                }

    return destroy.definition.url
            .replace('{package_item}', parsedArgs.package_item.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\PackageItemController::destroy
 * @see app/Http/Controllers/Settings/PackageItemController.php:26
 * @route '/settings/package-items/{package_item}'
 */
destroy.delete = (args: { package_item: string | number } | [package_item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\Settings\PackageItemController::destroy
 * @see app/Http/Controllers/Settings/PackageItemController.php:26
 * @route '/settings/package-items/{package_item}'
 */
    const destroyForm = (args: { package_item: string | number } | [package_item: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Settings\PackageItemController::destroy
 * @see app/Http/Controllers/Settings/PackageItemController.php:26
 * @route '/settings/package-items/{package_item}'
 */
        destroyForm.delete = (args: { package_item: string | number } | [package_item: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
const PackageItemController = { store, update, destroy }

export default PackageItemController