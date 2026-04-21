import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Settings\LabItemController::store
 * @see app/Http/Controllers/Settings/LabItemController.php:12
 * @route '/settings/lab-items'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/settings/lab-items',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Settings\LabItemController::store
 * @see app/Http/Controllers/Settings/LabItemController.php:12
 * @route '/settings/lab-items'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\LabItemController::store
 * @see app/Http/Controllers/Settings/LabItemController.php:12
 * @route '/settings/lab-items'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Settings\LabItemController::store
 * @see app/Http/Controllers/Settings/LabItemController.php:12
 * @route '/settings/lab-items'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Settings\LabItemController::store
 * @see app/Http/Controllers/Settings/LabItemController.php:12
 * @route '/settings/lab-items'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\Settings\LabItemController::update
 * @see app/Http/Controllers/Settings/LabItemController.php:19
 * @route '/settings/lab-items/{lab_item}'
 */
export const update = (args: { lab_item: string | number } | [lab_item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/settings/lab-items/{lab_item}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Settings\LabItemController::update
 * @see app/Http/Controllers/Settings/LabItemController.php:19
 * @route '/settings/lab-items/{lab_item}'
 */
update.url = (args: { lab_item: string | number } | [lab_item: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { lab_item: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    lab_item: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        lab_item: args.lab_item,
                }

    return update.definition.url
            .replace('{lab_item}', parsedArgs.lab_item.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\LabItemController::update
 * @see app/Http/Controllers/Settings/LabItemController.php:19
 * @route '/settings/lab-items/{lab_item}'
 */
update.put = (args: { lab_item: string | number } | [lab_item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\Settings\LabItemController::update
 * @see app/Http/Controllers/Settings/LabItemController.php:19
 * @route '/settings/lab-items/{lab_item}'
 */
update.patch = (args: { lab_item: string | number } | [lab_item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\Settings\LabItemController::update
 * @see app/Http/Controllers/Settings/LabItemController.php:19
 * @route '/settings/lab-items/{lab_item}'
 */
    const updateForm = (args: { lab_item: string | number } | [lab_item: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Settings\LabItemController::update
 * @see app/Http/Controllers/Settings/LabItemController.php:19
 * @route '/settings/lab-items/{lab_item}'
 */
        updateForm.put = (args: { lab_item: string | number } | [lab_item: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
            /**
* @see \App\Http\Controllers\Settings\LabItemController::update
 * @see app/Http/Controllers/Settings/LabItemController.php:19
 * @route '/settings/lab-items/{lab_item}'
 */
        updateForm.patch = (args: { lab_item: string | number } | [lab_item: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Settings\LabItemController::destroy
 * @see app/Http/Controllers/Settings/LabItemController.php:26
 * @route '/settings/lab-items/{lab_item}'
 */
export const destroy = (args: { lab_item: string | number } | [lab_item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/settings/lab-items/{lab_item}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Settings\LabItemController::destroy
 * @see app/Http/Controllers/Settings/LabItemController.php:26
 * @route '/settings/lab-items/{lab_item}'
 */
destroy.url = (args: { lab_item: string | number } | [lab_item: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { lab_item: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    lab_item: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        lab_item: args.lab_item,
                }

    return destroy.definition.url
            .replace('{lab_item}', parsedArgs.lab_item.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\LabItemController::destroy
 * @see app/Http/Controllers/Settings/LabItemController.php:26
 * @route '/settings/lab-items/{lab_item}'
 */
destroy.delete = (args: { lab_item: string | number } | [lab_item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\Settings\LabItemController::destroy
 * @see app/Http/Controllers/Settings/LabItemController.php:26
 * @route '/settings/lab-items/{lab_item}'
 */
    const destroyForm = (args: { lab_item: string | number } | [lab_item: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Settings\LabItemController::destroy
 * @see app/Http/Controllers/Settings/LabItemController.php:26
 * @route '/settings/lab-items/{lab_item}'
 */
        destroyForm.delete = (args: { lab_item: string | number } | [lab_item: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
const labItems = {
    store: Object.assign(store, store),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
}

export default labItems