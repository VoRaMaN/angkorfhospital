import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\BillingController::exportMethod
 * @see app/Http/Controllers/BillingController.php:702
 * @route '/billings-export'
 */
export const exportMethod = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})

exportMethod.definition = {
    methods: ["get","head"],
    url: '/billings-export',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BillingController::exportMethod
 * @see app/Http/Controllers/BillingController.php:702
 * @route '/billings-export'
 */
exportMethod.url = (options?: RouteQueryOptions) => {
    return exportMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BillingController::exportMethod
 * @see app/Http/Controllers/BillingController.php:702
 * @route '/billings-export'
 */
exportMethod.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\BillingController::exportMethod
 * @see app/Http/Controllers/BillingController.php:702
 * @route '/billings-export'
 */
exportMethod.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportMethod.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\BillingController::exportMethod
 * @see app/Http/Controllers/BillingController.php:702
 * @route '/billings-export'
 */
    const exportMethodForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: exportMethod.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\BillingController::exportMethod
 * @see app/Http/Controllers/BillingController.php:702
 * @route '/billings-export'
 */
        exportMethodForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: exportMethod.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\BillingController::exportMethod
 * @see app/Http/Controllers/BillingController.php:702
 * @route '/billings-export'
 */
        exportMethodForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: exportMethod.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    exportMethod.form = exportMethodForm
/**
* @see \App\Http\Controllers\BillingController::index
 * @see app/Http/Controllers/BillingController.php:19
 * @route '/billings'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/billings',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BillingController::index
 * @see app/Http/Controllers/BillingController.php:19
 * @route '/billings'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BillingController::index
 * @see app/Http/Controllers/BillingController.php:19
 * @route '/billings'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\BillingController::index
 * @see app/Http/Controllers/BillingController.php:19
 * @route '/billings'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\BillingController::index
 * @see app/Http/Controllers/BillingController.php:19
 * @route '/billings'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\BillingController::index
 * @see app/Http/Controllers/BillingController.php:19
 * @route '/billings'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\BillingController::index
 * @see app/Http/Controllers/BillingController.php:19
 * @route '/billings'
 */
        indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    index.form = indexForm
/**
* @see \App\Http\Controllers\BillingController::create
 * @see app/Http/Controllers/BillingController.php:118
 * @route '/billings/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/billings/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BillingController::create
 * @see app/Http/Controllers/BillingController.php:118
 * @route '/billings/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BillingController::create
 * @see app/Http/Controllers/BillingController.php:118
 * @route '/billings/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\BillingController::create
 * @see app/Http/Controllers/BillingController.php:118
 * @route '/billings/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\BillingController::create
 * @see app/Http/Controllers/BillingController.php:118
 * @route '/billings/create'
 */
    const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: create.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\BillingController::create
 * @see app/Http/Controllers/BillingController.php:118
 * @route '/billings/create'
 */
        createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: create.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\BillingController::create
 * @see app/Http/Controllers/BillingController.php:118
 * @route '/billings/create'
 */
        createForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: create.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    create.form = createForm
/**
* @see \App\Http\Controllers\BillingController::store
 * @see app/Http/Controllers/BillingController.php:153
 * @route '/billings'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/billings',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\BillingController::store
 * @see app/Http/Controllers/BillingController.php:153
 * @route '/billings'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BillingController::store
 * @see app/Http/Controllers/BillingController.php:153
 * @route '/billings'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\BillingController::store
 * @see app/Http/Controllers/BillingController.php:153
 * @route '/billings'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\BillingController::store
 * @see app/Http/Controllers/BillingController.php:153
 * @route '/billings'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\BillingController::show
 * @see app/Http/Controllers/BillingController.php:203
 * @route '/billings/{billing}'
 */
export const show = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/billings/{billing}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BillingController::show
 * @see app/Http/Controllers/BillingController.php:203
 * @route '/billings/{billing}'
 */
show.url = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { billing: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { billing: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    billing: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        billing: typeof args.billing === 'object'
                ? args.billing.id
                : args.billing,
                }

    return show.definition.url
            .replace('{billing}', parsedArgs.billing.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BillingController::show
 * @see app/Http/Controllers/BillingController.php:203
 * @route '/billings/{billing}'
 */
show.get = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\BillingController::show
 * @see app/Http/Controllers/BillingController.php:203
 * @route '/billings/{billing}'
 */
show.head = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\BillingController::show
 * @see app/Http/Controllers/BillingController.php:203
 * @route '/billings/{billing}'
 */
    const showForm = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\BillingController::show
 * @see app/Http/Controllers/BillingController.php:203
 * @route '/billings/{billing}'
 */
        showForm.get = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\BillingController::show
 * @see app/Http/Controllers/BillingController.php:203
 * @route '/billings/{billing}'
 */
        showForm.head = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    show.form = showForm
/**
* @see \App\Http\Controllers\BillingController::edit
 * @see app/Http/Controllers/BillingController.php:245
 * @route '/billings/{billing}/edit'
 */
export const edit = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/billings/{billing}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BillingController::edit
 * @see app/Http/Controllers/BillingController.php:245
 * @route '/billings/{billing}/edit'
 */
edit.url = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { billing: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { billing: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    billing: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        billing: typeof args.billing === 'object'
                ? args.billing.id
                : args.billing,
                }

    return edit.definition.url
            .replace('{billing}', parsedArgs.billing.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BillingController::edit
 * @see app/Http/Controllers/BillingController.php:245
 * @route '/billings/{billing}/edit'
 */
edit.get = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\BillingController::edit
 * @see app/Http/Controllers/BillingController.php:245
 * @route '/billings/{billing}/edit'
 */
edit.head = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\BillingController::edit
 * @see app/Http/Controllers/BillingController.php:245
 * @route '/billings/{billing}/edit'
 */
    const editForm = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: edit.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\BillingController::edit
 * @see app/Http/Controllers/BillingController.php:245
 * @route '/billings/{billing}/edit'
 */
        editForm.get = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: edit.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\BillingController::edit
 * @see app/Http/Controllers/BillingController.php:245
 * @route '/billings/{billing}/edit'
 */
        editForm.head = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: edit.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    edit.form = editForm
/**
* @see \App\Http\Controllers\BillingController::update
 * @see app/Http/Controllers/BillingController.php:287
 * @route '/billings/{billing}'
 */
export const update = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/billings/{billing}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\BillingController::update
 * @see app/Http/Controllers/BillingController.php:287
 * @route '/billings/{billing}'
 */
update.url = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { billing: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { billing: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    billing: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        billing: typeof args.billing === 'object'
                ? args.billing.id
                : args.billing,
                }

    return update.definition.url
            .replace('{billing}', parsedArgs.billing.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BillingController::update
 * @see app/Http/Controllers/BillingController.php:287
 * @route '/billings/{billing}'
 */
update.put = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\BillingController::update
 * @see app/Http/Controllers/BillingController.php:287
 * @route '/billings/{billing}'
 */
update.patch = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\BillingController::update
 * @see app/Http/Controllers/BillingController.php:287
 * @route '/billings/{billing}'
 */
    const updateForm = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\BillingController::update
 * @see app/Http/Controllers/BillingController.php:287
 * @route '/billings/{billing}'
 */
        updateForm.put = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
            /**
* @see \App\Http\Controllers\BillingController::update
 * @see app/Http/Controllers/BillingController.php:287
 * @route '/billings/{billing}'
 */
        updateForm.patch = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\BillingController::destroy
 * @see app/Http/Controllers/BillingController.php:366
 * @route '/billings/{billing}'
 */
export const destroy = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/billings/{billing}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\BillingController::destroy
 * @see app/Http/Controllers/BillingController.php:366
 * @route '/billings/{billing}'
 */
destroy.url = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { billing: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { billing: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    billing: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        billing: typeof args.billing === 'object'
                ? args.billing.id
                : args.billing,
                }

    return destroy.definition.url
            .replace('{billing}', parsedArgs.billing.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BillingController::destroy
 * @see app/Http/Controllers/BillingController.php:366
 * @route '/billings/{billing}'
 */
destroy.delete = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\BillingController::destroy
 * @see app/Http/Controllers/BillingController.php:366
 * @route '/billings/{billing}'
 */
    const destroyForm = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\BillingController::destroy
 * @see app/Http/Controllers/BillingController.php:366
 * @route '/billings/{billing}'
 */
        destroyForm.delete = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
/**
* @see \App\Http\Controllers\BillingController::updateStatus
 * @see app/Http/Controllers/BillingController.php:316
 * @route '/billings/{billing}/status'
 */
export const updateStatus = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateStatus.url(args, options),
    method: 'patch',
})

updateStatus.definition = {
    methods: ["patch"],
    url: '/billings/{billing}/status',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\BillingController::updateStatus
 * @see app/Http/Controllers/BillingController.php:316
 * @route '/billings/{billing}/status'
 */
updateStatus.url = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { billing: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { billing: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    billing: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        billing: typeof args.billing === 'object'
                ? args.billing.id
                : args.billing,
                }

    return updateStatus.definition.url
            .replace('{billing}', parsedArgs.billing.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BillingController::updateStatus
 * @see app/Http/Controllers/BillingController.php:316
 * @route '/billings/{billing}/status'
 */
updateStatus.patch = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateStatus.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\BillingController::updateStatus
 * @see app/Http/Controllers/BillingController.php:316
 * @route '/billings/{billing}/status'
 */
    const updateStatusForm = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: updateStatus.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PATCH',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\BillingController::updateStatus
 * @see app/Http/Controllers/BillingController.php:316
 * @route '/billings/{billing}/status'
 */
        updateStatusForm.patch = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: updateStatus.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PATCH',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    updateStatus.form = updateStatusForm
/**
* @see \App\Http\Controllers\BillingController::completePayment
 * @see app/Http/Controllers/BillingController.php:340
 * @route '/billings/{billing}/complete-payment'
 */
export const completePayment = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: completePayment.url(args, options),
    method: 'patch',
})

completePayment.definition = {
    methods: ["patch"],
    url: '/billings/{billing}/complete-payment',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\BillingController::completePayment
 * @see app/Http/Controllers/BillingController.php:340
 * @route '/billings/{billing}/complete-payment'
 */
completePayment.url = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { billing: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { billing: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    billing: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        billing: typeof args.billing === 'object'
                ? args.billing.id
                : args.billing,
                }

    return completePayment.definition.url
            .replace('{billing}', parsedArgs.billing.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BillingController::completePayment
 * @see app/Http/Controllers/BillingController.php:340
 * @route '/billings/{billing}/complete-payment'
 */
completePayment.patch = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: completePayment.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\BillingController::completePayment
 * @see app/Http/Controllers/BillingController.php:340
 * @route '/billings/{billing}/complete-payment'
 */
    const completePaymentForm = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: completePayment.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PATCH',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\BillingController::completePayment
 * @see app/Http/Controllers/BillingController.php:340
 * @route '/billings/{billing}/complete-payment'
 */
        completePaymentForm.patch = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: completePayment.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PATCH',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    completePayment.form = completePaymentForm
/**
* @see \App\Http\Controllers\BillingController::sendBackToNurse
 * @see app/Http/Controllers/BillingController.php:564
 * @route '/billings/{billing}/send-back-to-nurse'
 */
export const sendBackToNurse = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: sendBackToNurse.url(args, options),
    method: 'patch',
})

sendBackToNurse.definition = {
    methods: ["patch"],
    url: '/billings/{billing}/send-back-to-nurse',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\BillingController::sendBackToNurse
 * @see app/Http/Controllers/BillingController.php:564
 * @route '/billings/{billing}/send-back-to-nurse'
 */
sendBackToNurse.url = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { billing: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { billing: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    billing: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        billing: typeof args.billing === 'object'
                ? args.billing.id
                : args.billing,
                }

    return sendBackToNurse.definition.url
            .replace('{billing}', parsedArgs.billing.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BillingController::sendBackToNurse
 * @see app/Http/Controllers/BillingController.php:564
 * @route '/billings/{billing}/send-back-to-nurse'
 */
sendBackToNurse.patch = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: sendBackToNurse.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\BillingController::sendBackToNurse
 * @see app/Http/Controllers/BillingController.php:564
 * @route '/billings/{billing}/send-back-to-nurse'
 */
    const sendBackToNurseForm = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: sendBackToNurse.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PATCH',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\BillingController::sendBackToNurse
 * @see app/Http/Controllers/BillingController.php:564
 * @route '/billings/{billing}/send-back-to-nurse'
 */
        sendBackToNurseForm.patch = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: sendBackToNurse.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PATCH',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    sendBackToNurse.form = sendBackToNurseForm
/**
* @see \App\Http\Controllers\BillingController::receive
 * @see app/Http/Controllers/BillingController.php:623
 * @route '/billings/{billing}/receive'
 */
export const receive = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: receive.url(args, options),
    method: 'patch',
})

receive.definition = {
    methods: ["patch"],
    url: '/billings/{billing}/receive',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\BillingController::receive
 * @see app/Http/Controllers/BillingController.php:623
 * @route '/billings/{billing}/receive'
 */
receive.url = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { billing: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { billing: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    billing: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        billing: typeof args.billing === 'object'
                ? args.billing.id
                : args.billing,
                }

    return receive.definition.url
            .replace('{billing}', parsedArgs.billing.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BillingController::receive
 * @see app/Http/Controllers/BillingController.php:623
 * @route '/billings/{billing}/receive'
 */
receive.patch = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: receive.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\BillingController::receive
 * @see app/Http/Controllers/BillingController.php:623
 * @route '/billings/{billing}/receive'
 */
    const receiveForm = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: receive.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PATCH',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\BillingController::receive
 * @see app/Http/Controllers/BillingController.php:623
 * @route '/billings/{billing}/receive'
 */
        receiveForm.patch = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: receive.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PATCH',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    receive.form = receiveForm
/**
* @see \App\Http\Controllers\BillingController::recoverStuckRevision
 * @see app/Http/Controllers/BillingController.php:657
 * @route '/billings/{billing}/recover-stuck-revision'
 */
export const recoverStuckRevision = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: recoverStuckRevision.url(args, options),
    method: 'patch',
})

recoverStuckRevision.definition = {
    methods: ["patch"],
    url: '/billings/{billing}/recover-stuck-revision',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\BillingController::recoverStuckRevision
 * @see app/Http/Controllers/BillingController.php:657
 * @route '/billings/{billing}/recover-stuck-revision'
 */
recoverStuckRevision.url = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { billing: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { billing: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    billing: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        billing: typeof args.billing === 'object'
                ? args.billing.id
                : args.billing,
                }

    return recoverStuckRevision.definition.url
            .replace('{billing}', parsedArgs.billing.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BillingController::recoverStuckRevision
 * @see app/Http/Controllers/BillingController.php:657
 * @route '/billings/{billing}/recover-stuck-revision'
 */
recoverStuckRevision.patch = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: recoverStuckRevision.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\BillingController::recoverStuckRevision
 * @see app/Http/Controllers/BillingController.php:657
 * @route '/billings/{billing}/recover-stuck-revision'
 */
    const recoverStuckRevisionForm = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: recoverStuckRevision.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PATCH',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\BillingController::recoverStuckRevision
 * @see app/Http/Controllers/BillingController.php:657
 * @route '/billings/{billing}/recover-stuck-revision'
 */
        recoverStuckRevisionForm.patch = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: recoverStuckRevision.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PATCH',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    recoverStuckRevision.form = recoverStuckRevisionForm
/**
* @see \App\Http\Controllers\BillingController::recalculate
 * @see app/Http/Controllers/BillingController.php:683
 * @route '/billings/{billing}/recalculate'
 */
export const recalculate = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: recalculate.url(args, options),
    method: 'patch',
})

recalculate.definition = {
    methods: ["patch"],
    url: '/billings/{billing}/recalculate',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\BillingController::recalculate
 * @see app/Http/Controllers/BillingController.php:683
 * @route '/billings/{billing}/recalculate'
 */
recalculate.url = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { billing: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { billing: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    billing: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        billing: typeof args.billing === 'object'
                ? args.billing.id
                : args.billing,
                }

    return recalculate.definition.url
            .replace('{billing}', parsedArgs.billing.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BillingController::recalculate
 * @see app/Http/Controllers/BillingController.php:683
 * @route '/billings/{billing}/recalculate'
 */
recalculate.patch = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: recalculate.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\BillingController::recalculate
 * @see app/Http/Controllers/BillingController.php:683
 * @route '/billings/{billing}/recalculate'
 */
    const recalculateForm = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: recalculate.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PATCH',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\BillingController::recalculate
 * @see app/Http/Controllers/BillingController.php:683
 * @route '/billings/{billing}/recalculate'
 */
        recalculateForm.patch = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: recalculate.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PATCH',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    recalculate.form = recalculateForm
/**
* @see \App\Http\Controllers\BillingController::applyDiscount
 * @see app/Http/Controllers/BillingController.php:513
 * @route '/billings/{billing}/discount'
 */
export const applyDiscount = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: applyDiscount.url(args, options),
    method: 'patch',
})

applyDiscount.definition = {
    methods: ["patch"],
    url: '/billings/{billing}/discount',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\BillingController::applyDiscount
 * @see app/Http/Controllers/BillingController.php:513
 * @route '/billings/{billing}/discount'
 */
applyDiscount.url = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { billing: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { billing: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    billing: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        billing: typeof args.billing === 'object'
                ? args.billing.id
                : args.billing,
                }

    return applyDiscount.definition.url
            .replace('{billing}', parsedArgs.billing.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BillingController::applyDiscount
 * @see app/Http/Controllers/BillingController.php:513
 * @route '/billings/{billing}/discount'
 */
applyDiscount.patch = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: applyDiscount.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\BillingController::applyDiscount
 * @see app/Http/Controllers/BillingController.php:513
 * @route '/billings/{billing}/discount'
 */
    const applyDiscountForm = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: applyDiscount.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PATCH',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\BillingController::applyDiscount
 * @see app/Http/Controllers/BillingController.php:513
 * @route '/billings/{billing}/discount'
 */
        applyDiscountForm.patch = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: applyDiscount.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PATCH',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    applyDiscount.form = applyDiscountForm
/**
* @see \App\Http\Controllers\BillingController::generateReport
 * @see app/Http/Controllers/BillingController.php:377
 * @route '/billings/{billing}/report'
 */
export const generateReport = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: generateReport.url(args, options),
    method: 'get',
})

generateReport.definition = {
    methods: ["get","head"],
    url: '/billings/{billing}/report',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BillingController::generateReport
 * @see app/Http/Controllers/BillingController.php:377
 * @route '/billings/{billing}/report'
 */
generateReport.url = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { billing: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { billing: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    billing: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        billing: typeof args.billing === 'object'
                ? args.billing.id
                : args.billing,
                }

    return generateReport.definition.url
            .replace('{billing}', parsedArgs.billing.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BillingController::generateReport
 * @see app/Http/Controllers/BillingController.php:377
 * @route '/billings/{billing}/report'
 */
generateReport.get = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: generateReport.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\BillingController::generateReport
 * @see app/Http/Controllers/BillingController.php:377
 * @route '/billings/{billing}/report'
 */
generateReport.head = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: generateReport.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\BillingController::generateReport
 * @see app/Http/Controllers/BillingController.php:377
 * @route '/billings/{billing}/report'
 */
    const generateReportForm = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: generateReport.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\BillingController::generateReport
 * @see app/Http/Controllers/BillingController.php:377
 * @route '/billings/{billing}/report'
 */
        generateReportForm.get = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: generateReport.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\BillingController::generateReport
 * @see app/Http/Controllers/BillingController.php:377
 * @route '/billings/{billing}/report'
 */
        generateReportForm.head = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: generateReport.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    generateReport.form = generateReportForm
/**
* @see \App\Http\Controllers\BillingController::generateLetter
 * @see app/Http/Controllers/BillingController.php:526
 * @route '/billings/{billing}/letter'
 */
export const generateLetter = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: generateLetter.url(args, options),
    method: 'get',
})

generateLetter.definition = {
    methods: ["get","head"],
    url: '/billings/{billing}/letter',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BillingController::generateLetter
 * @see app/Http/Controllers/BillingController.php:526
 * @route '/billings/{billing}/letter'
 */
generateLetter.url = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { billing: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { billing: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    billing: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        billing: typeof args.billing === 'object'
                ? args.billing.id
                : args.billing,
                }

    return generateLetter.definition.url
            .replace('{billing}', parsedArgs.billing.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BillingController::generateLetter
 * @see app/Http/Controllers/BillingController.php:526
 * @route '/billings/{billing}/letter'
 */
generateLetter.get = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: generateLetter.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\BillingController::generateLetter
 * @see app/Http/Controllers/BillingController.php:526
 * @route '/billings/{billing}/letter'
 */
generateLetter.head = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: generateLetter.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\BillingController::generateLetter
 * @see app/Http/Controllers/BillingController.php:526
 * @route '/billings/{billing}/letter'
 */
    const generateLetterForm = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: generateLetter.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\BillingController::generateLetter
 * @see app/Http/Controllers/BillingController.php:526
 * @route '/billings/{billing}/letter'
 */
        generateLetterForm.get = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: generateLetter.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\BillingController::generateLetter
 * @see app/Http/Controllers/BillingController.php:526
 * @route '/billings/{billing}/letter'
 */
        generateLetterForm.head = (args: { billing: number | { id: number } } | [billing: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: generateLetter.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    generateLetter.form = generateLetterForm
const BillingController = { exportMethod, index, create, store, show, edit, update, destroy, updateStatus, completePayment, sendBackToNurse, receive, recoverStuckRevision, recalculate, applyDiscount, generateReport, generateLetter, export: exportMethod }

export default BillingController