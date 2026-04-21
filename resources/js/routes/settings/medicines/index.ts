import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Settings\MedicineController::store
 * @see app/Http/Controllers/Settings/MedicineController.php:12
 * @route '/settings/medicines'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/settings/medicines',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Settings\MedicineController::store
 * @see app/Http/Controllers/Settings/MedicineController.php:12
 * @route '/settings/medicines'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\MedicineController::store
 * @see app/Http/Controllers/Settings/MedicineController.php:12
 * @route '/settings/medicines'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Settings\MedicineController::store
 * @see app/Http/Controllers/Settings/MedicineController.php:12
 * @route '/settings/medicines'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Settings\MedicineController::store
 * @see app/Http/Controllers/Settings/MedicineController.php:12
 * @route '/settings/medicines'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\Settings\MedicineController::update
 * @see app/Http/Controllers/Settings/MedicineController.php:19
 * @route '/settings/medicines/{medicine}'
 */
export const update = (args: { medicine: number | { id: number } } | [medicine: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/settings/medicines/{medicine}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Settings\MedicineController::update
 * @see app/Http/Controllers/Settings/MedicineController.php:19
 * @route '/settings/medicines/{medicine}'
 */
update.url = (args: { medicine: number | { id: number } } | [medicine: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { medicine: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { medicine: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    medicine: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        medicine: typeof args.medicine === 'object'
                ? args.medicine.id
                : args.medicine,
                }

    return update.definition.url
            .replace('{medicine}', parsedArgs.medicine.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\MedicineController::update
 * @see app/Http/Controllers/Settings/MedicineController.php:19
 * @route '/settings/medicines/{medicine}'
 */
update.put = (args: { medicine: number | { id: number } } | [medicine: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\Settings\MedicineController::update
 * @see app/Http/Controllers/Settings/MedicineController.php:19
 * @route '/settings/medicines/{medicine}'
 */
update.patch = (args: { medicine: number | { id: number } } | [medicine: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\Settings\MedicineController::update
 * @see app/Http/Controllers/Settings/MedicineController.php:19
 * @route '/settings/medicines/{medicine}'
 */
    const updateForm = (args: { medicine: number | { id: number } } | [medicine: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Settings\MedicineController::update
 * @see app/Http/Controllers/Settings/MedicineController.php:19
 * @route '/settings/medicines/{medicine}'
 */
        updateForm.put = (args: { medicine: number | { id: number } } | [medicine: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
            /**
* @see \App\Http\Controllers\Settings\MedicineController::update
 * @see app/Http/Controllers/Settings/MedicineController.php:19
 * @route '/settings/medicines/{medicine}'
 */
        updateForm.patch = (args: { medicine: number | { id: number } } | [medicine: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Settings\MedicineController::destroy
 * @see app/Http/Controllers/Settings/MedicineController.php:26
 * @route '/settings/medicines/{medicine}'
 */
export const destroy = (args: { medicine: number | { id: number } } | [medicine: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/settings/medicines/{medicine}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Settings\MedicineController::destroy
 * @see app/Http/Controllers/Settings/MedicineController.php:26
 * @route '/settings/medicines/{medicine}'
 */
destroy.url = (args: { medicine: number | { id: number } } | [medicine: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { medicine: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { medicine: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    medicine: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        medicine: typeof args.medicine === 'object'
                ? args.medicine.id
                : args.medicine,
                }

    return destroy.definition.url
            .replace('{medicine}', parsedArgs.medicine.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\MedicineController::destroy
 * @see app/Http/Controllers/Settings/MedicineController.php:26
 * @route '/settings/medicines/{medicine}'
 */
destroy.delete = (args: { medicine: number | { id: number } } | [medicine: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\Settings\MedicineController::destroy
 * @see app/Http/Controllers/Settings/MedicineController.php:26
 * @route '/settings/medicines/{medicine}'
 */
    const destroyForm = (args: { medicine: number | { id: number } } | [medicine: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Settings\MedicineController::destroy
 * @see app/Http/Controllers/Settings/MedicineController.php:26
 * @route '/settings/medicines/{medicine}'
 */
        destroyForm.delete = (args: { medicine: number | { id: number } } | [medicine: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
const medicines = {
    store: Object.assign(store, store),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
}

export default medicines