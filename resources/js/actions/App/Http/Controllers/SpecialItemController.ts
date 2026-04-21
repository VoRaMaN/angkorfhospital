import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\SpecialItemController::index
 * @see app/Http/Controllers/SpecialItemController.php:14
 * @route '/special-items'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/special-items',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SpecialItemController::index
 * @see app/Http/Controllers/SpecialItemController.php:14
 * @route '/special-items'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SpecialItemController::index
 * @see app/Http/Controllers/SpecialItemController.php:14
 * @route '/special-items'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\SpecialItemController::index
 * @see app/Http/Controllers/SpecialItemController.php:14
 * @route '/special-items'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\SpecialItemController::index
 * @see app/Http/Controllers/SpecialItemController.php:14
 * @route '/special-items'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\SpecialItemController::index
 * @see app/Http/Controllers/SpecialItemController.php:14
 * @route '/special-items'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\SpecialItemController::index
 * @see app/Http/Controllers/SpecialItemController.php:14
 * @route '/special-items'
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
* @see \App\Http\Controllers\SpecialItemController::create
 * @see app/Http/Controllers/SpecialItemController.php:41
 * @route '/special-items/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/special-items/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SpecialItemController::create
 * @see app/Http/Controllers/SpecialItemController.php:41
 * @route '/special-items/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SpecialItemController::create
 * @see app/Http/Controllers/SpecialItemController.php:41
 * @route '/special-items/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\SpecialItemController::create
 * @see app/Http/Controllers/SpecialItemController.php:41
 * @route '/special-items/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\SpecialItemController::create
 * @see app/Http/Controllers/SpecialItemController.php:41
 * @route '/special-items/create'
 */
    const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: create.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\SpecialItemController::create
 * @see app/Http/Controllers/SpecialItemController.php:41
 * @route '/special-items/create'
 */
        createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: create.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\SpecialItemController::create
 * @see app/Http/Controllers/SpecialItemController.php:41
 * @route '/special-items/create'
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
* @see \App\Http\Controllers\SpecialItemController::store
 * @see app/Http/Controllers/SpecialItemController.php:59
 * @route '/special-items'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/special-items',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\SpecialItemController::store
 * @see app/Http/Controllers/SpecialItemController.php:59
 * @route '/special-items'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SpecialItemController::store
 * @see app/Http/Controllers/SpecialItemController.php:59
 * @route '/special-items'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\SpecialItemController::store
 * @see app/Http/Controllers/SpecialItemController.php:59
 * @route '/special-items'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\SpecialItemController::store
 * @see app/Http/Controllers/SpecialItemController.php:59
 * @route '/special-items'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\SpecialItemController::show
 * @see app/Http/Controllers/SpecialItemController.php:0
 * @route '/special-items/{special_item}'
 */
export const show = (args: { special_item: string | number } | [special_item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/special-items/{special_item}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SpecialItemController::show
 * @see app/Http/Controllers/SpecialItemController.php:0
 * @route '/special-items/{special_item}'
 */
show.url = (args: { special_item: string | number } | [special_item: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return show.definition.url
            .replace('{special_item}', parsedArgs.special_item.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SpecialItemController::show
 * @see app/Http/Controllers/SpecialItemController.php:0
 * @route '/special-items/{special_item}'
 */
show.get = (args: { special_item: string | number } | [special_item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\SpecialItemController::show
 * @see app/Http/Controllers/SpecialItemController.php:0
 * @route '/special-items/{special_item}'
 */
show.head = (args: { special_item: string | number } | [special_item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\SpecialItemController::show
 * @see app/Http/Controllers/SpecialItemController.php:0
 * @route '/special-items/{special_item}'
 */
    const showForm = (args: { special_item: string | number } | [special_item: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\SpecialItemController::show
 * @see app/Http/Controllers/SpecialItemController.php:0
 * @route '/special-items/{special_item}'
 */
        showForm.get = (args: { special_item: string | number } | [special_item: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\SpecialItemController::show
 * @see app/Http/Controllers/SpecialItemController.php:0
 * @route '/special-items/{special_item}'
 */
        showForm.head = (args: { special_item: string | number } | [special_item: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\SpecialItemController::edit
 * @see app/Http/Controllers/SpecialItemController.php:91
 * @route '/special-items/{special_item}/edit'
 */
export const edit = (args: { special_item: string | number } | [special_item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/special-items/{special_item}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SpecialItemController::edit
 * @see app/Http/Controllers/SpecialItemController.php:91
 * @route '/special-items/{special_item}/edit'
 */
edit.url = (args: { special_item: string | number } | [special_item: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return edit.definition.url
            .replace('{special_item}', parsedArgs.special_item.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SpecialItemController::edit
 * @see app/Http/Controllers/SpecialItemController.php:91
 * @route '/special-items/{special_item}/edit'
 */
edit.get = (args: { special_item: string | number } | [special_item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\SpecialItemController::edit
 * @see app/Http/Controllers/SpecialItemController.php:91
 * @route '/special-items/{special_item}/edit'
 */
edit.head = (args: { special_item: string | number } | [special_item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\SpecialItemController::edit
 * @see app/Http/Controllers/SpecialItemController.php:91
 * @route '/special-items/{special_item}/edit'
 */
    const editForm = (args: { special_item: string | number } | [special_item: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: edit.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\SpecialItemController::edit
 * @see app/Http/Controllers/SpecialItemController.php:91
 * @route '/special-items/{special_item}/edit'
 */
        editForm.get = (args: { special_item: string | number } | [special_item: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: edit.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\SpecialItemController::edit
 * @see app/Http/Controllers/SpecialItemController.php:91
 * @route '/special-items/{special_item}/edit'
 */
        editForm.head = (args: { special_item: string | number } | [special_item: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\SpecialItemController::update
 * @see app/Http/Controllers/SpecialItemController.php:128
 * @route '/special-items/{special_item}'
 */
export const update = (args: { special_item: string | number } | [special_item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/special-items/{special_item}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\SpecialItemController::update
 * @see app/Http/Controllers/SpecialItemController.php:128
 * @route '/special-items/{special_item}'
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
* @see \App\Http\Controllers\SpecialItemController::update
 * @see app/Http/Controllers/SpecialItemController.php:128
 * @route '/special-items/{special_item}'
 */
update.put = (args: { special_item: string | number } | [special_item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\SpecialItemController::update
 * @see app/Http/Controllers/SpecialItemController.php:128
 * @route '/special-items/{special_item}'
 */
update.patch = (args: { special_item: string | number } | [special_item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\SpecialItemController::update
 * @see app/Http/Controllers/SpecialItemController.php:128
 * @route '/special-items/{special_item}'
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
* @see \App\Http\Controllers\SpecialItemController::update
 * @see app/Http/Controllers/SpecialItemController.php:128
 * @route '/special-items/{special_item}'
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
* @see \App\Http\Controllers\SpecialItemController::update
 * @see app/Http/Controllers/SpecialItemController.php:128
 * @route '/special-items/{special_item}'
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
* @see \App\Http\Controllers\SpecialItemController::destroy
 * @see app/Http/Controllers/SpecialItemController.php:162
 * @route '/special-items/{special_item}'
 */
export const destroy = (args: { special_item: string | number } | [special_item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/special-items/{special_item}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\SpecialItemController::destroy
 * @see app/Http/Controllers/SpecialItemController.php:162
 * @route '/special-items/{special_item}'
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
* @see \App\Http\Controllers\SpecialItemController::destroy
 * @see app/Http/Controllers/SpecialItemController.php:162
 * @route '/special-items/{special_item}'
 */
destroy.delete = (args: { special_item: string | number } | [special_item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\SpecialItemController::destroy
 * @see app/Http/Controllers/SpecialItemController.php:162
 * @route '/special-items/{special_item}'
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
* @see \App\Http\Controllers\SpecialItemController::destroy
 * @see app/Http/Controllers/SpecialItemController.php:162
 * @route '/special-items/{special_item}'
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
const SpecialItemController = { index, create, store, show, edit, update, destroy }

export default SpecialItemController