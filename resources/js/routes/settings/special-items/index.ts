import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Settings\SpecialItemController::store
 * @see app/Http/Controllers/Settings/SpecialItemController.php:12
 * @route '/settings/special-items'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/settings/special-items',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Settings\SpecialItemController::store
 * @see app/Http/Controllers/Settings/SpecialItemController.php:12
 * @route '/settings/special-items'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\SpecialItemController::store
 * @see app/Http/Controllers/Settings/SpecialItemController.php:12
 * @route '/settings/special-items'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Settings\SpecialItemController::store
 * @see app/Http/Controllers/Settings/SpecialItemController.php:12
 * @route '/settings/special-items'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Settings\SpecialItemController::store
 * @see app/Http/Controllers/Settings/SpecialItemController.php:12
 * @route '/settings/special-items'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\Settings\SpecialItemController::update
 * @see app/Http/Controllers/Settings/SpecialItemController.php:19
 * @route '/settings/special-items/{special_item}'
 */
export const update = (args: { special_item: string | number } | [special_item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/settings/special-items/{special_item}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Settings\SpecialItemController::update
 * @see app/Http/Controllers/Settings/SpecialItemController.php:19
 * @route '/settings/special-items/{special_item}'
 */
update.url = (args: { special_item: string | number } | [special_item: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { special_item: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    special_item: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        special_item: args.special_item,
                }

    return update.definition.url
            .replace('{special_item}', parsedArgs.special_item.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\SpecialItemController::update
 * @see app/Http/Controllers/Settings/SpecialItemController.php:19
 * @route '/settings/special-items/{special_item}'
 */
update.put = (args: { special_item: string | number } | [special_item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\Settings\SpecialItemController::update
 * @see app/Http/Controllers/Settings/SpecialItemController.php:19
 * @route '/settings/special-items/{special_item}'
 */
update.patch = (args: { special_item: string | number } | [special_item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\Settings\SpecialItemController::update
 * @see app/Http/Controllers/Settings/SpecialItemController.php:19
 * @route '/settings/special-items/{special_item}'
 */
    const updateForm = (args: { special_item: string | number } | [special_item: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Settings\SpecialItemController::update
 * @see app/Http/Controllers/Settings/SpecialItemController.php:19
 * @route '/settings/special-items/{special_item}'
 */
        updateForm.put = (args: { special_item: string | number } | [special_item: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
            /**
* @see \App\Http\Controllers\Settings\SpecialItemController::update
 * @see app/Http/Controllers/Settings/SpecialItemController.php:19
 * @route '/settings/special-items/{special_item}'
 */
        updateForm.patch = (args: { special_item: string | number } | [special_item: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Settings\SpecialItemController::destroy
 * @see app/Http/Controllers/Settings/SpecialItemController.php:26
 * @route '/settings/special-items/{special_item}'
 */
export const destroy = (args: { special_item: string | number } | [special_item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/settings/special-items/{special_item}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Settings\SpecialItemController::destroy
 * @see app/Http/Controllers/Settings/SpecialItemController.php:26
 * @route '/settings/special-items/{special_item}'
 */
destroy.url = (args: { special_item: string | number } | [special_item: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { special_item: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    special_item: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        special_item: args.special_item,
                }

    return destroy.definition.url
            .replace('{special_item}', parsedArgs.special_item.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\SpecialItemController::destroy
 * @see app/Http/Controllers/Settings/SpecialItemController.php:26
 * @route '/settings/special-items/{special_item}'
 */
destroy.delete = (args: { special_item: string | number } | [special_item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\Settings\SpecialItemController::destroy
 * @see app/Http/Controllers/Settings/SpecialItemController.php:26
 * @route '/settings/special-items/{special_item}'
 */
    const destroyForm = (args: { special_item: string | number } | [special_item: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Settings\SpecialItemController::destroy
 * @see app/Http/Controllers/Settings/SpecialItemController.php:26
 * @route '/settings/special-items/{special_item}'
 */
        destroyForm.delete = (args: { special_item: string | number } | [special_item: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
const specialItems = {
    store: Object.assign(store, store),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
}

export default specialItems