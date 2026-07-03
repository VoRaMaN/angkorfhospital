import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\LabPanelController::index
 * @see app/Http/Controllers/LabPanelController.php:21
 * @route '/lab-panels'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/lab-panels',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\LabPanelController::index
 * @see app/Http/Controllers/LabPanelController.php:21
 * @route '/lab-panels'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LabPanelController::index
 * @see app/Http/Controllers/LabPanelController.php:21
 * @route '/lab-panels'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\LabPanelController::index
 * @see app/Http/Controllers/LabPanelController.php:21
 * @route '/lab-panels'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\LabPanelController::index
 * @see app/Http/Controllers/LabPanelController.php:21
 * @route '/lab-panels'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\LabPanelController::index
 * @see app/Http/Controllers/LabPanelController.php:21
 * @route '/lab-panels'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\LabPanelController::index
 * @see app/Http/Controllers/LabPanelController.php:21
 * @route '/lab-panels'
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
* @see \App\Http\Controllers\LabPanelController::create
 * @see app/Http/Controllers/LabPanelController.php:129
 * @route '/lab-panels/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/lab-panels/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\LabPanelController::create
 * @see app/Http/Controllers/LabPanelController.php:129
 * @route '/lab-panels/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LabPanelController::create
 * @see app/Http/Controllers/LabPanelController.php:129
 * @route '/lab-panels/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\LabPanelController::create
 * @see app/Http/Controllers/LabPanelController.php:129
 * @route '/lab-panels/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\LabPanelController::create
 * @see app/Http/Controllers/LabPanelController.php:129
 * @route '/lab-panels/create'
 */
    const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: create.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\LabPanelController::create
 * @see app/Http/Controllers/LabPanelController.php:129
 * @route '/lab-panels/create'
 */
        createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: create.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\LabPanelController::create
 * @see app/Http/Controllers/LabPanelController.php:129
 * @route '/lab-panels/create'
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
* @see \App\Http\Controllers\LabPanelController::store
 * @see app/Http/Controllers/LabPanelController.php:141
 * @route '/lab-panels'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/lab-panels',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\LabPanelController::store
 * @see app/Http/Controllers/LabPanelController.php:141
 * @route '/lab-panels'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LabPanelController::store
 * @see app/Http/Controllers/LabPanelController.php:141
 * @route '/lab-panels'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\LabPanelController::store
 * @see app/Http/Controllers/LabPanelController.php:141
 * @route '/lab-panels'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\LabPanelController::store
 * @see app/Http/Controllers/LabPanelController.php:141
 * @route '/lab-panels'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\LabPanelController::show
 * @see app/Http/Controllers/LabPanelController.php:178
 * @route '/lab-panels/{lab_panel}'
 */
export const show = (args: { lab_panel: string | number } | [lab_panel: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/lab-panels/{lab_panel}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\LabPanelController::show
 * @see app/Http/Controllers/LabPanelController.php:178
 * @route '/lab-panels/{lab_panel}'
 */
show.url = (args: { lab_panel: string | number } | [lab_panel: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { lab_panel: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    lab_panel: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        lab_panel: args.lab_panel,
                }

    return show.definition.url
            .replace('{lab_panel}', parsedArgs.lab_panel.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LabPanelController::show
 * @see app/Http/Controllers/LabPanelController.php:178
 * @route '/lab-panels/{lab_panel}'
 */
show.get = (args: { lab_panel: string | number } | [lab_panel: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\LabPanelController::show
 * @see app/Http/Controllers/LabPanelController.php:178
 * @route '/lab-panels/{lab_panel}'
 */
show.head = (args: { lab_panel: string | number } | [lab_panel: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\LabPanelController::show
 * @see app/Http/Controllers/LabPanelController.php:178
 * @route '/lab-panels/{lab_panel}'
 */
    const showForm = (args: { lab_panel: string | number } | [lab_panel: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\LabPanelController::show
 * @see app/Http/Controllers/LabPanelController.php:178
 * @route '/lab-panels/{lab_panel}'
 */
        showForm.get = (args: { lab_panel: string | number } | [lab_panel: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\LabPanelController::show
 * @see app/Http/Controllers/LabPanelController.php:178
 * @route '/lab-panels/{lab_panel}'
 */
        showForm.head = (args: { lab_panel: string | number } | [lab_panel: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\LabPanelController::edit
 * @see app/Http/Controllers/LabPanelController.php:211
 * @route '/lab-panels/{lab_panel}/edit'
 */
export const edit = (args: { lab_panel: string | number } | [lab_panel: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/lab-panels/{lab_panel}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\LabPanelController::edit
 * @see app/Http/Controllers/LabPanelController.php:211
 * @route '/lab-panels/{lab_panel}/edit'
 */
edit.url = (args: { lab_panel: string | number } | [lab_panel: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { lab_panel: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    lab_panel: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        lab_panel: args.lab_panel,
                }

    return edit.definition.url
            .replace('{lab_panel}', parsedArgs.lab_panel.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LabPanelController::edit
 * @see app/Http/Controllers/LabPanelController.php:211
 * @route '/lab-panels/{lab_panel}/edit'
 */
edit.get = (args: { lab_panel: string | number } | [lab_panel: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\LabPanelController::edit
 * @see app/Http/Controllers/LabPanelController.php:211
 * @route '/lab-panels/{lab_panel}/edit'
 */
edit.head = (args: { lab_panel: string | number } | [lab_panel: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\LabPanelController::edit
 * @see app/Http/Controllers/LabPanelController.php:211
 * @route '/lab-panels/{lab_panel}/edit'
 */
    const editForm = (args: { lab_panel: string | number } | [lab_panel: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: edit.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\LabPanelController::edit
 * @see app/Http/Controllers/LabPanelController.php:211
 * @route '/lab-panels/{lab_panel}/edit'
 */
        editForm.get = (args: { lab_panel: string | number } | [lab_panel: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: edit.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\LabPanelController::edit
 * @see app/Http/Controllers/LabPanelController.php:211
 * @route '/lab-panels/{lab_panel}/edit'
 */
        editForm.head = (args: { lab_panel: string | number } | [lab_panel: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\LabPanelController::update
 * @see app/Http/Controllers/LabPanelController.php:245
 * @route '/lab-panels/{lab_panel}'
 */
export const update = (args: { lab_panel: string | number } | [lab_panel: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/lab-panels/{lab_panel}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\LabPanelController::update
 * @see app/Http/Controllers/LabPanelController.php:245
 * @route '/lab-panels/{lab_panel}'
 */
update.url = (args: { lab_panel: string | number } | [lab_panel: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { lab_panel: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    lab_panel: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        lab_panel: args.lab_panel,
                }

    return update.definition.url
            .replace('{lab_panel}', parsedArgs.lab_panel.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LabPanelController::update
 * @see app/Http/Controllers/LabPanelController.php:245
 * @route '/lab-panels/{lab_panel}'
 */
update.put = (args: { lab_panel: string | number } | [lab_panel: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\LabPanelController::update
 * @see app/Http/Controllers/LabPanelController.php:245
 * @route '/lab-panels/{lab_panel}'
 */
update.patch = (args: { lab_panel: string | number } | [lab_panel: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\LabPanelController::update
 * @see app/Http/Controllers/LabPanelController.php:245
 * @route '/lab-panels/{lab_panel}'
 */
    const updateForm = (args: { lab_panel: string | number } | [lab_panel: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\LabPanelController::update
 * @see app/Http/Controllers/LabPanelController.php:245
 * @route '/lab-panels/{lab_panel}'
 */
        updateForm.put = (args: { lab_panel: string | number } | [lab_panel: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
            /**
* @see \App\Http\Controllers\LabPanelController::update
 * @see app/Http/Controllers/LabPanelController.php:245
 * @route '/lab-panels/{lab_panel}'
 */
        updateForm.patch = (args: { lab_panel: string | number } | [lab_panel: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\LabPanelController::destroy
 * @see app/Http/Controllers/LabPanelController.php:286
 * @route '/lab-panels/{lab_panel}'
 */
export const destroy = (args: { lab_panel: string | number } | [lab_panel: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/lab-panels/{lab_panel}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\LabPanelController::destroy
 * @see app/Http/Controllers/LabPanelController.php:286
 * @route '/lab-panels/{lab_panel}'
 */
destroy.url = (args: { lab_panel: string | number } | [lab_panel: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { lab_panel: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    lab_panel: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        lab_panel: args.lab_panel,
                }

    return destroy.definition.url
            .replace('{lab_panel}', parsedArgs.lab_panel.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LabPanelController::destroy
 * @see app/Http/Controllers/LabPanelController.php:286
 * @route '/lab-panels/{lab_panel}'
 */
destroy.delete = (args: { lab_panel: string | number } | [lab_panel: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\LabPanelController::destroy
 * @see app/Http/Controllers/LabPanelController.php:286
 * @route '/lab-panels/{lab_panel}'
 */
    const destroyForm = (args: { lab_panel: string | number } | [lab_panel: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\LabPanelController::destroy
 * @see app/Http/Controllers/LabPanelController.php:286
 * @route '/lab-panels/{lab_panel}'
 */
        destroyForm.delete = (args: { lab_panel: string | number } | [lab_panel: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
const LabPanelController = { index, create, store, show, edit, update, destroy }

export default LabPanelController