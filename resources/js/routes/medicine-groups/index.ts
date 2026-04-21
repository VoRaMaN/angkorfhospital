import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\MedicineGroupController::index
 * @see app/Http/Controllers/MedicineGroupController.php:14
 * @route '/medicine-groups'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/medicine-groups',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MedicineGroupController::index
 * @see app/Http/Controllers/MedicineGroupController.php:14
 * @route '/medicine-groups'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicineGroupController::index
 * @see app/Http/Controllers/MedicineGroupController.php:14
 * @route '/medicine-groups'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MedicineGroupController::index
 * @see app/Http/Controllers/MedicineGroupController.php:14
 * @route '/medicine-groups'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\MedicineGroupController::index
 * @see app/Http/Controllers/MedicineGroupController.php:14
 * @route '/medicine-groups'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\MedicineGroupController::index
 * @see app/Http/Controllers/MedicineGroupController.php:14
 * @route '/medicine-groups'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\MedicineGroupController::index
 * @see app/Http/Controllers/MedicineGroupController.php:14
 * @route '/medicine-groups'
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
* @see \App\Http\Controllers\MedicineGroupController::create
 * @see app/Http/Controllers/MedicineGroupController.php:55
 * @route '/medicine-groups/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/medicine-groups/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MedicineGroupController::create
 * @see app/Http/Controllers/MedicineGroupController.php:55
 * @route '/medicine-groups/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicineGroupController::create
 * @see app/Http/Controllers/MedicineGroupController.php:55
 * @route '/medicine-groups/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MedicineGroupController::create
 * @see app/Http/Controllers/MedicineGroupController.php:55
 * @route '/medicine-groups/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\MedicineGroupController::create
 * @see app/Http/Controllers/MedicineGroupController.php:55
 * @route '/medicine-groups/create'
 */
    const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: create.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\MedicineGroupController::create
 * @see app/Http/Controllers/MedicineGroupController.php:55
 * @route '/medicine-groups/create'
 */
        createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: create.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\MedicineGroupController::create
 * @see app/Http/Controllers/MedicineGroupController.php:55
 * @route '/medicine-groups/create'
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
* @see \App\Http\Controllers\MedicineGroupController::store
 * @see app/Http/Controllers/MedicineGroupController.php:75
 * @route '/medicine-groups'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/medicine-groups',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\MedicineGroupController::store
 * @see app/Http/Controllers/MedicineGroupController.php:75
 * @route '/medicine-groups'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicineGroupController::store
 * @see app/Http/Controllers/MedicineGroupController.php:75
 * @route '/medicine-groups'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\MedicineGroupController::store
 * @see app/Http/Controllers/MedicineGroupController.php:75
 * @route '/medicine-groups'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\MedicineGroupController::store
 * @see app/Http/Controllers/MedicineGroupController.php:75
 * @route '/medicine-groups'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\MedicineGroupController::show
 * @see app/Http/Controllers/MedicineGroupController.php:0
 * @route '/medicine-groups/{medicine_group}'
 */
export const show = (args: { medicine_group: string | number } | [medicine_group: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/medicine-groups/{medicine_group}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MedicineGroupController::show
 * @see app/Http/Controllers/MedicineGroupController.php:0
 * @route '/medicine-groups/{medicine_group}'
 */
show.url = (args: { medicine_group: string | number } | [medicine_group: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { medicine_group: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    medicine_group: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        medicine_group: args.medicine_group,
                }

    return show.definition.url
            .replace('{medicine_group}', parsedArgs.medicine_group.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicineGroupController::show
 * @see app/Http/Controllers/MedicineGroupController.php:0
 * @route '/medicine-groups/{medicine_group}'
 */
show.get = (args: { medicine_group: string | number } | [medicine_group: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MedicineGroupController::show
 * @see app/Http/Controllers/MedicineGroupController.php:0
 * @route '/medicine-groups/{medicine_group}'
 */
show.head = (args: { medicine_group: string | number } | [medicine_group: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\MedicineGroupController::show
 * @see app/Http/Controllers/MedicineGroupController.php:0
 * @route '/medicine-groups/{medicine_group}'
 */
    const showForm = (args: { medicine_group: string | number } | [medicine_group: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\MedicineGroupController::show
 * @see app/Http/Controllers/MedicineGroupController.php:0
 * @route '/medicine-groups/{medicine_group}'
 */
        showForm.get = (args: { medicine_group: string | number } | [medicine_group: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\MedicineGroupController::show
 * @see app/Http/Controllers/MedicineGroupController.php:0
 * @route '/medicine-groups/{medicine_group}'
 */
        showForm.head = (args: { medicine_group: string | number } | [medicine_group: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\MedicineGroupController::edit
 * @see app/Http/Controllers/MedicineGroupController.php:110
 * @route '/medicine-groups/{medicine_group}/edit'
 */
export const edit = (args: { medicine_group: string | number } | [medicine_group: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/medicine-groups/{medicine_group}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MedicineGroupController::edit
 * @see app/Http/Controllers/MedicineGroupController.php:110
 * @route '/medicine-groups/{medicine_group}/edit'
 */
edit.url = (args: { medicine_group: string | number } | [medicine_group: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { medicine_group: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    medicine_group: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        medicine_group: args.medicine_group,
                }

    return edit.definition.url
            .replace('{medicine_group}', parsedArgs.medicine_group.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicineGroupController::edit
 * @see app/Http/Controllers/MedicineGroupController.php:110
 * @route '/medicine-groups/{medicine_group}/edit'
 */
edit.get = (args: { medicine_group: string | number } | [medicine_group: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MedicineGroupController::edit
 * @see app/Http/Controllers/MedicineGroupController.php:110
 * @route '/medicine-groups/{medicine_group}/edit'
 */
edit.head = (args: { medicine_group: string | number } | [medicine_group: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\MedicineGroupController::edit
 * @see app/Http/Controllers/MedicineGroupController.php:110
 * @route '/medicine-groups/{medicine_group}/edit'
 */
    const editForm = (args: { medicine_group: string | number } | [medicine_group: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: edit.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\MedicineGroupController::edit
 * @see app/Http/Controllers/MedicineGroupController.php:110
 * @route '/medicine-groups/{medicine_group}/edit'
 */
        editForm.get = (args: { medicine_group: string | number } | [medicine_group: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: edit.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\MedicineGroupController::edit
 * @see app/Http/Controllers/MedicineGroupController.php:110
 * @route '/medicine-groups/{medicine_group}/edit'
 */
        editForm.head = (args: { medicine_group: string | number } | [medicine_group: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\MedicineGroupController::update
 * @see app/Http/Controllers/MedicineGroupController.php:149
 * @route '/medicine-groups/{medicine_group}'
 */
export const update = (args: { medicine_group: string | number } | [medicine_group: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/medicine-groups/{medicine_group}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\MedicineGroupController::update
 * @see app/Http/Controllers/MedicineGroupController.php:149
 * @route '/medicine-groups/{medicine_group}'
 */
update.url = (args: { medicine_group: string | number } | [medicine_group: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { medicine_group: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    medicine_group: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        medicine_group: args.medicine_group,
                }

    return update.definition.url
            .replace('{medicine_group}', parsedArgs.medicine_group.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicineGroupController::update
 * @see app/Http/Controllers/MedicineGroupController.php:149
 * @route '/medicine-groups/{medicine_group}'
 */
update.put = (args: { medicine_group: string | number } | [medicine_group: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\MedicineGroupController::update
 * @see app/Http/Controllers/MedicineGroupController.php:149
 * @route '/medicine-groups/{medicine_group}'
 */
update.patch = (args: { medicine_group: string | number } | [medicine_group: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\MedicineGroupController::update
 * @see app/Http/Controllers/MedicineGroupController.php:149
 * @route '/medicine-groups/{medicine_group}'
 */
    const updateForm = (args: { medicine_group: string | number } | [medicine_group: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\MedicineGroupController::update
 * @see app/Http/Controllers/MedicineGroupController.php:149
 * @route '/medicine-groups/{medicine_group}'
 */
        updateForm.put = (args: { medicine_group: string | number } | [medicine_group: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
            /**
* @see \App\Http\Controllers\MedicineGroupController::update
 * @see app/Http/Controllers/MedicineGroupController.php:149
 * @route '/medicine-groups/{medicine_group}'
 */
        updateForm.patch = (args: { medicine_group: string | number } | [medicine_group: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\MedicineGroupController::destroy
 * @see app/Http/Controllers/MedicineGroupController.php:188
 * @route '/medicine-groups/{medicine_group}'
 */
export const destroy = (args: { medicine_group: string | number } | [medicine_group: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/medicine-groups/{medicine_group}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\MedicineGroupController::destroy
 * @see app/Http/Controllers/MedicineGroupController.php:188
 * @route '/medicine-groups/{medicine_group}'
 */
destroy.url = (args: { medicine_group: string | number } | [medicine_group: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { medicine_group: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    medicine_group: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        medicine_group: args.medicine_group,
                }

    return destroy.definition.url
            .replace('{medicine_group}', parsedArgs.medicine_group.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicineGroupController::destroy
 * @see app/Http/Controllers/MedicineGroupController.php:188
 * @route '/medicine-groups/{medicine_group}'
 */
destroy.delete = (args: { medicine_group: string | number } | [medicine_group: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\MedicineGroupController::destroy
 * @see app/Http/Controllers/MedicineGroupController.php:188
 * @route '/medicine-groups/{medicine_group}'
 */
    const destroyForm = (args: { medicine_group: string | number } | [medicine_group: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\MedicineGroupController::destroy
 * @see app/Http/Controllers/MedicineGroupController.php:188
 * @route '/medicine-groups/{medicine_group}'
 */
        destroyForm.delete = (args: { medicine_group: string | number } | [medicine_group: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
const medicineGroups = {
    index: Object.assign(index, index),
create: Object.assign(create, create),
store: Object.assign(store, store),
show: Object.assign(show, show),
edit: Object.assign(edit, edit),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
}

export default medicineGroups