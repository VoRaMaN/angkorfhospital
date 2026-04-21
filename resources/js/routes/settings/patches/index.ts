import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Settings\PatchController::index
 * @see app/Http/Controllers/Settings/PatchController.php:16
 * @route '/settings/patches'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/settings/patches',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Settings\PatchController::index
 * @see app/Http/Controllers/Settings/PatchController.php:16
 * @route '/settings/patches'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\PatchController::index
 * @see app/Http/Controllers/Settings/PatchController.php:16
 * @route '/settings/patches'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Settings\PatchController::index
 * @see app/Http/Controllers/Settings/PatchController.php:16
 * @route '/settings/patches'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Settings\PatchController::index
 * @see app/Http/Controllers/Settings/PatchController.php:16
 * @route '/settings/patches'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Settings\PatchController::index
 * @see app/Http/Controllers/Settings/PatchController.php:16
 * @route '/settings/patches'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Settings\PatchController::index
 * @see app/Http/Controllers/Settings/PatchController.php:16
 * @route '/settings/patches'
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
* @see \App\Http\Controllers\Settings\PatchController::create
 * @see app/Http/Controllers/Settings/PatchController.php:0
 * @route '/settings/patches/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/settings/patches/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Settings\PatchController::create
 * @see app/Http/Controllers/Settings/PatchController.php:0
 * @route '/settings/patches/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\PatchController::create
 * @see app/Http/Controllers/Settings/PatchController.php:0
 * @route '/settings/patches/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Settings\PatchController::create
 * @see app/Http/Controllers/Settings/PatchController.php:0
 * @route '/settings/patches/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Settings\PatchController::create
 * @see app/Http/Controllers/Settings/PatchController.php:0
 * @route '/settings/patches/create'
 */
    const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: create.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Settings\PatchController::create
 * @see app/Http/Controllers/Settings/PatchController.php:0
 * @route '/settings/patches/create'
 */
        createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: create.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Settings\PatchController::create
 * @see app/Http/Controllers/Settings/PatchController.php:0
 * @route '/settings/patches/create'
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
* @see \App\Http\Controllers\Settings\PatchController::store
 * @see app/Http/Controllers/Settings/PatchController.php:43
 * @route '/settings/patches'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/settings/patches',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Settings\PatchController::store
 * @see app/Http/Controllers/Settings/PatchController.php:43
 * @route '/settings/patches'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\PatchController::store
 * @see app/Http/Controllers/Settings/PatchController.php:43
 * @route '/settings/patches'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Settings\PatchController::store
 * @see app/Http/Controllers/Settings/PatchController.php:43
 * @route '/settings/patches'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Settings\PatchController::store
 * @see app/Http/Controllers/Settings/PatchController.php:43
 * @route '/settings/patches'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\Settings\PatchController::show
 * @see app/Http/Controllers/Settings/PatchController.php:0
 * @route '/settings/patches/{patch}'
 */
export const show = (args: { patch: string | number } | [patch: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/settings/patches/{patch}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Settings\PatchController::show
 * @see app/Http/Controllers/Settings/PatchController.php:0
 * @route '/settings/patches/{patch}'
 */
show.url = (args: { patch: string | number } | [patch: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { patch: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    patch: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        patch: args.patch,
                }

    return show.definition.url
            .replace('{patch}', parsedArgs.patch.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\PatchController::show
 * @see app/Http/Controllers/Settings/PatchController.php:0
 * @route '/settings/patches/{patch}'
 */
show.get = (args: { patch: string | number } | [patch: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Settings\PatchController::show
 * @see app/Http/Controllers/Settings/PatchController.php:0
 * @route '/settings/patches/{patch}'
 */
show.head = (args: { patch: string | number } | [patch: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Settings\PatchController::show
 * @see app/Http/Controllers/Settings/PatchController.php:0
 * @route '/settings/patches/{patch}'
 */
    const showForm = (args: { patch: string | number } | [patch: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Settings\PatchController::show
 * @see app/Http/Controllers/Settings/PatchController.php:0
 * @route '/settings/patches/{patch}'
 */
        showForm.get = (args: { patch: string | number } | [patch: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Settings\PatchController::show
 * @see app/Http/Controllers/Settings/PatchController.php:0
 * @route '/settings/patches/{patch}'
 */
        showForm.head = (args: { patch: string | number } | [patch: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\Settings\PatchController::edit
 * @see app/Http/Controllers/Settings/PatchController.php:63
 * @route '/settings/patches/{patch}/edit'
 */
export const edit = (args: { patch: number | { id: number } } | [patch: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/settings/patches/{patch}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Settings\PatchController::edit
 * @see app/Http/Controllers/Settings/PatchController.php:63
 * @route '/settings/patches/{patch}/edit'
 */
edit.url = (args: { patch: number | { id: number } } | [patch: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { patch: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { patch: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    patch: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        patch: typeof args.patch === 'object'
                ? args.patch.id
                : args.patch,
                }

    return edit.definition.url
            .replace('{patch}', parsedArgs.patch.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\PatchController::edit
 * @see app/Http/Controllers/Settings/PatchController.php:63
 * @route '/settings/patches/{patch}/edit'
 */
edit.get = (args: { patch: number | { id: number } } | [patch: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Settings\PatchController::edit
 * @see app/Http/Controllers/Settings/PatchController.php:63
 * @route '/settings/patches/{patch}/edit'
 */
edit.head = (args: { patch: number | { id: number } } | [patch: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Settings\PatchController::edit
 * @see app/Http/Controllers/Settings/PatchController.php:63
 * @route '/settings/patches/{patch}/edit'
 */
    const editForm = (args: { patch: number | { id: number } } | [patch: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: edit.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Settings\PatchController::edit
 * @see app/Http/Controllers/Settings/PatchController.php:63
 * @route '/settings/patches/{patch}/edit'
 */
        editForm.get = (args: { patch: number | { id: number } } | [patch: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: edit.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Settings\PatchController::edit
 * @see app/Http/Controllers/Settings/PatchController.php:63
 * @route '/settings/patches/{patch}/edit'
 */
        editForm.head = (args: { patch: number | { id: number } } | [patch: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\Settings\PatchController::update
 * @see app/Http/Controllers/Settings/PatchController.php:91
 * @route '/settings/patches/{patch}'
 */
export const update = (args: { patch: number | { id: number } } | [patch: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/settings/patches/{patch}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Settings\PatchController::update
 * @see app/Http/Controllers/Settings/PatchController.php:91
 * @route '/settings/patches/{patch}'
 */
update.url = (args: { patch: number | { id: number } } | [patch: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { patch: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { patch: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    patch: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        patch: typeof args.patch === 'object'
                ? args.patch.id
                : args.patch,
                }

    return update.definition.url
            .replace('{patch}', parsedArgs.patch.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\PatchController::update
 * @see app/Http/Controllers/Settings/PatchController.php:91
 * @route '/settings/patches/{patch}'
 */
update.put = (args: { patch: number | { id: number } } | [patch: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\Settings\PatchController::update
 * @see app/Http/Controllers/Settings/PatchController.php:91
 * @route '/settings/patches/{patch}'
 */
update.patch = (args: { patch: number | { id: number } } | [patch: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\Settings\PatchController::update
 * @see app/Http/Controllers/Settings/PatchController.php:91
 * @route '/settings/patches/{patch}'
 */
    const updateForm = (args: { patch: number | { id: number } } | [patch: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Settings\PatchController::update
 * @see app/Http/Controllers/Settings/PatchController.php:91
 * @route '/settings/patches/{patch}'
 */
        updateForm.put = (args: { patch: number | { id: number } } | [patch: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
            /**
* @see \App\Http\Controllers\Settings\PatchController::update
 * @see app/Http/Controllers/Settings/PatchController.php:91
 * @route '/settings/patches/{patch}'
 */
        updateForm.patch = (args: { patch: number | { id: number } } | [patch: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Settings\PatchController::destroy
 * @see app/Http/Controllers/Settings/PatchController.php:111
 * @route '/settings/patches/{patch}'
 */
export const destroy = (args: { patch: number | { id: number } } | [patch: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/settings/patches/{patch}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Settings\PatchController::destroy
 * @see app/Http/Controllers/Settings/PatchController.php:111
 * @route '/settings/patches/{patch}'
 */
destroy.url = (args: { patch: number | { id: number } } | [patch: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { patch: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { patch: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    patch: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        patch: typeof args.patch === 'object'
                ? args.patch.id
                : args.patch,
                }

    return destroy.definition.url
            .replace('{patch}', parsedArgs.patch.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\PatchController::destroy
 * @see app/Http/Controllers/Settings/PatchController.php:111
 * @route '/settings/patches/{patch}'
 */
destroy.delete = (args: { patch: number | { id: number } } | [patch: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\Settings\PatchController::destroy
 * @see app/Http/Controllers/Settings/PatchController.php:111
 * @route '/settings/patches/{patch}'
 */
    const destroyForm = (args: { patch: number | { id: number } } | [patch: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Settings\PatchController::destroy
 * @see app/Http/Controllers/Settings/PatchController.php:111
 * @route '/settings/patches/{patch}'
 */
        destroyForm.delete = (args: { patch: number | { id: number } } | [patch: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
const patches = {
    index: Object.assign(index, index),
create: Object.assign(create, create),
store: Object.assign(store, store),
show: Object.assign(show, show),
edit: Object.assign(edit, edit),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
}

export default patches