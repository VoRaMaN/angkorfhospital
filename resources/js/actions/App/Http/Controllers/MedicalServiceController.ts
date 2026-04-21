import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\MedicalServiceController::index
 * @see app/Http/Controllers/MedicalServiceController.php:11
 * @route '/medical-services'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/medical-services',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MedicalServiceController::index
 * @see app/Http/Controllers/MedicalServiceController.php:11
 * @route '/medical-services'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalServiceController::index
 * @see app/Http/Controllers/MedicalServiceController.php:11
 * @route '/medical-services'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MedicalServiceController::index
 * @see app/Http/Controllers/MedicalServiceController.php:11
 * @route '/medical-services'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\MedicalServiceController::index
 * @see app/Http/Controllers/MedicalServiceController.php:11
 * @route '/medical-services'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\MedicalServiceController::index
 * @see app/Http/Controllers/MedicalServiceController.php:11
 * @route '/medical-services'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\MedicalServiceController::index
 * @see app/Http/Controllers/MedicalServiceController.php:11
 * @route '/medical-services'
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
* @see \App\Http\Controllers\MedicalServiceController::create
 * @see app/Http/Controllers/MedicalServiceController.php:22
 * @route '/medical-services/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/medical-services/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MedicalServiceController::create
 * @see app/Http/Controllers/MedicalServiceController.php:22
 * @route '/medical-services/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalServiceController::create
 * @see app/Http/Controllers/MedicalServiceController.php:22
 * @route '/medical-services/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MedicalServiceController::create
 * @see app/Http/Controllers/MedicalServiceController.php:22
 * @route '/medical-services/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\MedicalServiceController::create
 * @see app/Http/Controllers/MedicalServiceController.php:22
 * @route '/medical-services/create'
 */
    const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: create.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\MedicalServiceController::create
 * @see app/Http/Controllers/MedicalServiceController.php:22
 * @route '/medical-services/create'
 */
        createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: create.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\MedicalServiceController::create
 * @see app/Http/Controllers/MedicalServiceController.php:22
 * @route '/medical-services/create'
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
* @see \App\Http\Controllers\MedicalServiceController::store
 * @see app/Http/Controllers/MedicalServiceController.php:29
 * @route '/medical-services'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/medical-services',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\MedicalServiceController::store
 * @see app/Http/Controllers/MedicalServiceController.php:29
 * @route '/medical-services'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalServiceController::store
 * @see app/Http/Controllers/MedicalServiceController.php:29
 * @route '/medical-services'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\MedicalServiceController::store
 * @see app/Http/Controllers/MedicalServiceController.php:29
 * @route '/medical-services'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\MedicalServiceController::store
 * @see app/Http/Controllers/MedicalServiceController.php:29
 * @route '/medical-services'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\MedicalServiceController::show
 * @see app/Http/Controllers/MedicalServiceController.php:49
 * @route '/medical-services/{medical_service}'
 */
export const show = (args: { medical_service: string | number } | [medical_service: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/medical-services/{medical_service}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MedicalServiceController::show
 * @see app/Http/Controllers/MedicalServiceController.php:49
 * @route '/medical-services/{medical_service}'
 */
show.url = (args: { medical_service: string | number } | [medical_service: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { medical_service: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    medical_service: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        medical_service: args.medical_service,
                }

    return show.definition.url
            .replace('{medical_service}', parsedArgs.medical_service.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalServiceController::show
 * @see app/Http/Controllers/MedicalServiceController.php:49
 * @route '/medical-services/{medical_service}'
 */
show.get = (args: { medical_service: string | number } | [medical_service: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MedicalServiceController::show
 * @see app/Http/Controllers/MedicalServiceController.php:49
 * @route '/medical-services/{medical_service}'
 */
show.head = (args: { medical_service: string | number } | [medical_service: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\MedicalServiceController::show
 * @see app/Http/Controllers/MedicalServiceController.php:49
 * @route '/medical-services/{medical_service}'
 */
    const showForm = (args: { medical_service: string | number } | [medical_service: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\MedicalServiceController::show
 * @see app/Http/Controllers/MedicalServiceController.php:49
 * @route '/medical-services/{medical_service}'
 */
        showForm.get = (args: { medical_service: string | number } | [medical_service: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\MedicalServiceController::show
 * @see app/Http/Controllers/MedicalServiceController.php:49
 * @route '/medical-services/{medical_service}'
 */
        showForm.head = (args: { medical_service: string | number } | [medical_service: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\MedicalServiceController::edit
 * @see app/Http/Controllers/MedicalServiceController.php:58
 * @route '/medical-services/{medical_service}/edit'
 */
export const edit = (args: { medical_service: string | number } | [medical_service: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/medical-services/{medical_service}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MedicalServiceController::edit
 * @see app/Http/Controllers/MedicalServiceController.php:58
 * @route '/medical-services/{medical_service}/edit'
 */
edit.url = (args: { medical_service: string | number } | [medical_service: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { medical_service: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    medical_service: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        medical_service: args.medical_service,
                }

    return edit.definition.url
            .replace('{medical_service}', parsedArgs.medical_service.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalServiceController::edit
 * @see app/Http/Controllers/MedicalServiceController.php:58
 * @route '/medical-services/{medical_service}/edit'
 */
edit.get = (args: { medical_service: string | number } | [medical_service: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MedicalServiceController::edit
 * @see app/Http/Controllers/MedicalServiceController.php:58
 * @route '/medical-services/{medical_service}/edit'
 */
edit.head = (args: { medical_service: string | number } | [medical_service: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\MedicalServiceController::edit
 * @see app/Http/Controllers/MedicalServiceController.php:58
 * @route '/medical-services/{medical_service}/edit'
 */
    const editForm = (args: { medical_service: string | number } | [medical_service: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: edit.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\MedicalServiceController::edit
 * @see app/Http/Controllers/MedicalServiceController.php:58
 * @route '/medical-services/{medical_service}/edit'
 */
        editForm.get = (args: { medical_service: string | number } | [medical_service: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: edit.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\MedicalServiceController::edit
 * @see app/Http/Controllers/MedicalServiceController.php:58
 * @route '/medical-services/{medical_service}/edit'
 */
        editForm.head = (args: { medical_service: string | number } | [medical_service: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\MedicalServiceController::update
 * @see app/Http/Controllers/MedicalServiceController.php:67
 * @route '/medical-services/{medical_service}'
 */
export const update = (args: { medical_service: string | number } | [medical_service: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/medical-services/{medical_service}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\MedicalServiceController::update
 * @see app/Http/Controllers/MedicalServiceController.php:67
 * @route '/medical-services/{medical_service}'
 */
update.url = (args: { medical_service: string | number } | [medical_service: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { medical_service: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    medical_service: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        medical_service: args.medical_service,
                }

    return update.definition.url
            .replace('{medical_service}', parsedArgs.medical_service.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalServiceController::update
 * @see app/Http/Controllers/MedicalServiceController.php:67
 * @route '/medical-services/{medical_service}'
 */
update.put = (args: { medical_service: string | number } | [medical_service: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\MedicalServiceController::update
 * @see app/Http/Controllers/MedicalServiceController.php:67
 * @route '/medical-services/{medical_service}'
 */
update.patch = (args: { medical_service: string | number } | [medical_service: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\MedicalServiceController::update
 * @see app/Http/Controllers/MedicalServiceController.php:67
 * @route '/medical-services/{medical_service}'
 */
    const updateForm = (args: { medical_service: string | number } | [medical_service: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\MedicalServiceController::update
 * @see app/Http/Controllers/MedicalServiceController.php:67
 * @route '/medical-services/{medical_service}'
 */
        updateForm.put = (args: { medical_service: string | number } | [medical_service: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
            /**
* @see \App\Http\Controllers\MedicalServiceController::update
 * @see app/Http/Controllers/MedicalServiceController.php:67
 * @route '/medical-services/{medical_service}'
 */
        updateForm.patch = (args: { medical_service: string | number } | [medical_service: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\MedicalServiceController::destroy
 * @see app/Http/Controllers/MedicalServiceController.php:87
 * @route '/medical-services/{medical_service}'
 */
export const destroy = (args: { medical_service: string | number } | [medical_service: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/medical-services/{medical_service}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\MedicalServiceController::destroy
 * @see app/Http/Controllers/MedicalServiceController.php:87
 * @route '/medical-services/{medical_service}'
 */
destroy.url = (args: { medical_service: string | number } | [medical_service: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { medical_service: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    medical_service: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        medical_service: args.medical_service,
                }

    return destroy.definition.url
            .replace('{medical_service}', parsedArgs.medical_service.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalServiceController::destroy
 * @see app/Http/Controllers/MedicalServiceController.php:87
 * @route '/medical-services/{medical_service}'
 */
destroy.delete = (args: { medical_service: string | number } | [medical_service: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\MedicalServiceController::destroy
 * @see app/Http/Controllers/MedicalServiceController.php:87
 * @route '/medical-services/{medical_service}'
 */
    const destroyForm = (args: { medical_service: string | number } | [medical_service: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\MedicalServiceController::destroy
 * @see app/Http/Controllers/MedicalServiceController.php:87
 * @route '/medical-services/{medical_service}'
 */
        destroyForm.delete = (args: { medical_service: string | number } | [medical_service: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
const MedicalServiceController = { index, create, store, show, edit, update, destroy }

export default MedicalServiceController