import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\MedicalOrderController::index
 * @see app/Http/Controllers/MedicalOrderController.php:20
 * @route '/medical-orders'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/medical-orders',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MedicalOrderController::index
 * @see app/Http/Controllers/MedicalOrderController.php:20
 * @route '/medical-orders'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalOrderController::index
 * @see app/Http/Controllers/MedicalOrderController.php:20
 * @route '/medical-orders'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MedicalOrderController::index
 * @see app/Http/Controllers/MedicalOrderController.php:20
 * @route '/medical-orders'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\MedicalOrderController::index
 * @see app/Http/Controllers/MedicalOrderController.php:20
 * @route '/medical-orders'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\MedicalOrderController::index
 * @see app/Http/Controllers/MedicalOrderController.php:20
 * @route '/medical-orders'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\MedicalOrderController::index
 * @see app/Http/Controllers/MedicalOrderController.php:20
 * @route '/medical-orders'
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
* @see \App\Http\Controllers\MedicalOrderController::create
 * @see app/Http/Controllers/MedicalOrderController.php:118
 * @route '/medical-orders/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/medical-orders/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MedicalOrderController::create
 * @see app/Http/Controllers/MedicalOrderController.php:118
 * @route '/medical-orders/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalOrderController::create
 * @see app/Http/Controllers/MedicalOrderController.php:118
 * @route '/medical-orders/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MedicalOrderController::create
 * @see app/Http/Controllers/MedicalOrderController.php:118
 * @route '/medical-orders/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\MedicalOrderController::create
 * @see app/Http/Controllers/MedicalOrderController.php:118
 * @route '/medical-orders/create'
 */
    const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: create.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\MedicalOrderController::create
 * @see app/Http/Controllers/MedicalOrderController.php:118
 * @route '/medical-orders/create'
 */
        createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: create.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\MedicalOrderController::create
 * @see app/Http/Controllers/MedicalOrderController.php:118
 * @route '/medical-orders/create'
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
* @see \App\Http\Controllers\MedicalOrderController::store
 * @see app/Http/Controllers/MedicalOrderController.php:224
 * @route '/medical-orders'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/medical-orders',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\MedicalOrderController::store
 * @see app/Http/Controllers/MedicalOrderController.php:224
 * @route '/medical-orders'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalOrderController::store
 * @see app/Http/Controllers/MedicalOrderController.php:224
 * @route '/medical-orders'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\MedicalOrderController::store
 * @see app/Http/Controllers/MedicalOrderController.php:224
 * @route '/medical-orders'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\MedicalOrderController::store
 * @see app/Http/Controllers/MedicalOrderController.php:224
 * @route '/medical-orders'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\MedicalOrderController::show
 * @see app/Http/Controllers/MedicalOrderController.php:251
 * @route '/medical-orders/{medical_order}'
 */
export const show = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/medical-orders/{medical_order}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MedicalOrderController::show
 * @see app/Http/Controllers/MedicalOrderController.php:251
 * @route '/medical-orders/{medical_order}'
 */
show.url = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { medical_order: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    medical_order: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        medical_order: args.medical_order,
                }

    return show.definition.url
            .replace('{medical_order}', parsedArgs.medical_order.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalOrderController::show
 * @see app/Http/Controllers/MedicalOrderController.php:251
 * @route '/medical-orders/{medical_order}'
 */
show.get = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MedicalOrderController::show
 * @see app/Http/Controllers/MedicalOrderController.php:251
 * @route '/medical-orders/{medical_order}'
 */
show.head = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\MedicalOrderController::show
 * @see app/Http/Controllers/MedicalOrderController.php:251
 * @route '/medical-orders/{medical_order}'
 */
    const showForm = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\MedicalOrderController::show
 * @see app/Http/Controllers/MedicalOrderController.php:251
 * @route '/medical-orders/{medical_order}'
 */
        showForm.get = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\MedicalOrderController::show
 * @see app/Http/Controllers/MedicalOrderController.php:251
 * @route '/medical-orders/{medical_order}'
 */
        showForm.head = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\MedicalOrderController::edit
 * @see app/Http/Controllers/MedicalOrderController.php:344
 * @route '/medical-orders/{medical_order}/edit'
 */
export const edit = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/medical-orders/{medical_order}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MedicalOrderController::edit
 * @see app/Http/Controllers/MedicalOrderController.php:344
 * @route '/medical-orders/{medical_order}/edit'
 */
edit.url = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { medical_order: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    medical_order: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        medical_order: args.medical_order,
                }

    return edit.definition.url
            .replace('{medical_order}', parsedArgs.medical_order.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalOrderController::edit
 * @see app/Http/Controllers/MedicalOrderController.php:344
 * @route '/medical-orders/{medical_order}/edit'
 */
edit.get = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MedicalOrderController::edit
 * @see app/Http/Controllers/MedicalOrderController.php:344
 * @route '/medical-orders/{medical_order}/edit'
 */
edit.head = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\MedicalOrderController::edit
 * @see app/Http/Controllers/MedicalOrderController.php:344
 * @route '/medical-orders/{medical_order}/edit'
 */
    const editForm = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: edit.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\MedicalOrderController::edit
 * @see app/Http/Controllers/MedicalOrderController.php:344
 * @route '/medical-orders/{medical_order}/edit'
 */
        editForm.get = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: edit.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\MedicalOrderController::edit
 * @see app/Http/Controllers/MedicalOrderController.php:344
 * @route '/medical-orders/{medical_order}/edit'
 */
        editForm.head = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\MedicalOrderController::update
 * @see app/Http/Controllers/MedicalOrderController.php:545
 * @route '/medical-orders/{medical_order}'
 */
export const update = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/medical-orders/{medical_order}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\MedicalOrderController::update
 * @see app/Http/Controllers/MedicalOrderController.php:545
 * @route '/medical-orders/{medical_order}'
 */
update.url = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { medical_order: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    medical_order: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        medical_order: args.medical_order,
                }

    return update.definition.url
            .replace('{medical_order}', parsedArgs.medical_order.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalOrderController::update
 * @see app/Http/Controllers/MedicalOrderController.php:545
 * @route '/medical-orders/{medical_order}'
 */
update.put = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\MedicalOrderController::update
 * @see app/Http/Controllers/MedicalOrderController.php:545
 * @route '/medical-orders/{medical_order}'
 */
update.patch = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\MedicalOrderController::update
 * @see app/Http/Controllers/MedicalOrderController.php:545
 * @route '/medical-orders/{medical_order}'
 */
    const updateForm = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\MedicalOrderController::update
 * @see app/Http/Controllers/MedicalOrderController.php:545
 * @route '/medical-orders/{medical_order}'
 */
        updateForm.put = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
            /**
* @see \App\Http\Controllers\MedicalOrderController::update
 * @see app/Http/Controllers/MedicalOrderController.php:545
 * @route '/medical-orders/{medical_order}'
 */
        updateForm.patch = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\MedicalOrderController::destroy
 * @see app/Http/Controllers/MedicalOrderController.php:590
 * @route '/medical-orders/{medical_order}'
 */
export const destroy = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/medical-orders/{medical_order}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\MedicalOrderController::destroy
 * @see app/Http/Controllers/MedicalOrderController.php:590
 * @route '/medical-orders/{medical_order}'
 */
destroy.url = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { medical_order: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    medical_order: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        medical_order: args.medical_order,
                }

    return destroy.definition.url
            .replace('{medical_order}', parsedArgs.medical_order.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalOrderController::destroy
 * @see app/Http/Controllers/MedicalOrderController.php:590
 * @route '/medical-orders/{medical_order}'
 */
destroy.delete = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\MedicalOrderController::destroy
 * @see app/Http/Controllers/MedicalOrderController.php:590
 * @route '/medical-orders/{medical_order}'
 */
    const destroyForm = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\MedicalOrderController::destroy
 * @see app/Http/Controllers/MedicalOrderController.php:590
 * @route '/medical-orders/{medical_order}'
 */
        destroyForm.delete = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\MedicalOrderController::processPage
 * @see app/Http/Controllers/MedicalOrderController.php:665
 * @route '/medical-orders/{medical_order}/process'
 */
export const processPage = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: processPage.url(args, options),
    method: 'get',
})

processPage.definition = {
    methods: ["get","head"],
    url: '/medical-orders/{medical_order}/process',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MedicalOrderController::processPage
 * @see app/Http/Controllers/MedicalOrderController.php:665
 * @route '/medical-orders/{medical_order}/process'
 */
processPage.url = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { medical_order: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    medical_order: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        medical_order: args.medical_order,
                }

    return processPage.definition.url
            .replace('{medical_order}', parsedArgs.medical_order.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalOrderController::processPage
 * @see app/Http/Controllers/MedicalOrderController.php:665
 * @route '/medical-orders/{medical_order}/process'
 */
processPage.get = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: processPage.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MedicalOrderController::processPage
 * @see app/Http/Controllers/MedicalOrderController.php:665
 * @route '/medical-orders/{medical_order}/process'
 */
processPage.head = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: processPage.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\MedicalOrderController::processPage
 * @see app/Http/Controllers/MedicalOrderController.php:665
 * @route '/medical-orders/{medical_order}/process'
 */
    const processPageForm = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: processPage.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\MedicalOrderController::processPage
 * @see app/Http/Controllers/MedicalOrderController.php:665
 * @route '/medical-orders/{medical_order}/process'
 */
        processPageForm.get = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: processPage.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\MedicalOrderController::processPage
 * @see app/Http/Controllers/MedicalOrderController.php:665
 * @route '/medical-orders/{medical_order}/process'
 */
        processPageForm.head = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: processPage.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    processPage.form = processPageForm
/**
* @see \App\Http\Controllers\MedicalOrderController::processingPage
 * @see app/Http/Controllers/MedicalOrderController.php:890
 * @route '/medical-orders/{medical_order}/processing'
 */
export const processingPage = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: processingPage.url(args, options),
    method: 'get',
})

processingPage.definition = {
    methods: ["get","head"],
    url: '/medical-orders/{medical_order}/processing',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MedicalOrderController::processingPage
 * @see app/Http/Controllers/MedicalOrderController.php:890
 * @route '/medical-orders/{medical_order}/processing'
 */
processingPage.url = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { medical_order: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    medical_order: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        medical_order: args.medical_order,
                }

    return processingPage.definition.url
            .replace('{medical_order}', parsedArgs.medical_order.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalOrderController::processingPage
 * @see app/Http/Controllers/MedicalOrderController.php:890
 * @route '/medical-orders/{medical_order}/processing'
 */
processingPage.get = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: processingPage.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MedicalOrderController::processingPage
 * @see app/Http/Controllers/MedicalOrderController.php:890
 * @route '/medical-orders/{medical_order}/processing'
 */
processingPage.head = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: processingPage.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\MedicalOrderController::processingPage
 * @see app/Http/Controllers/MedicalOrderController.php:890
 * @route '/medical-orders/{medical_order}/processing'
 */
    const processingPageForm = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: processingPage.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\MedicalOrderController::processingPage
 * @see app/Http/Controllers/MedicalOrderController.php:890
 * @route '/medical-orders/{medical_order}/processing'
 */
        processingPageForm.get = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: processingPage.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\MedicalOrderController::processingPage
 * @see app/Http/Controllers/MedicalOrderController.php:890
 * @route '/medical-orders/{medical_order}/processing'
 */
        processingPageForm.head = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: processingPage.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    processingPage.form = processingPageForm
/**
* @see \App\Http\Controllers\MedicalOrderController::processWithUpdate
 * @see app/Http/Controllers/MedicalOrderController.php:598
 * @route '/medical-orders/{medical_order}/process-with-update'
 */
export const processWithUpdate = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: processWithUpdate.url(args, options),
    method: 'patch',
})

processWithUpdate.definition = {
    methods: ["patch"],
    url: '/medical-orders/{medical_order}/process-with-update',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\MedicalOrderController::processWithUpdate
 * @see app/Http/Controllers/MedicalOrderController.php:598
 * @route '/medical-orders/{medical_order}/process-with-update'
 */
processWithUpdate.url = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { medical_order: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    medical_order: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        medical_order: args.medical_order,
                }

    return processWithUpdate.definition.url
            .replace('{medical_order}', parsedArgs.medical_order.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalOrderController::processWithUpdate
 * @see app/Http/Controllers/MedicalOrderController.php:598
 * @route '/medical-orders/{medical_order}/process-with-update'
 */
processWithUpdate.patch = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: processWithUpdate.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\MedicalOrderController::processWithUpdate
 * @see app/Http/Controllers/MedicalOrderController.php:598
 * @route '/medical-orders/{medical_order}/process-with-update'
 */
    const processWithUpdateForm = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: processWithUpdate.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PATCH',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\MedicalOrderController::processWithUpdate
 * @see app/Http/Controllers/MedicalOrderController.php:598
 * @route '/medical-orders/{medical_order}/process-with-update'
 */
        processWithUpdateForm.patch = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: processWithUpdate.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PATCH',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    processWithUpdate.form = processWithUpdateForm
/**
* @see \App\Http\Controllers\MedicalOrderController::completePage
 * @see app/Http/Controllers/MedicalOrderController.php:962
 * @route '/medical-orders/{medical_order}/complete'
 */
export const completePage = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: completePage.url(args, options),
    method: 'get',
})

completePage.definition = {
    methods: ["get","head"],
    url: '/medical-orders/{medical_order}/complete',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MedicalOrderController::completePage
 * @see app/Http/Controllers/MedicalOrderController.php:962
 * @route '/medical-orders/{medical_order}/complete'
 */
completePage.url = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { medical_order: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    medical_order: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        medical_order: args.medical_order,
                }

    return completePage.definition.url
            .replace('{medical_order}', parsedArgs.medical_order.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalOrderController::completePage
 * @see app/Http/Controllers/MedicalOrderController.php:962
 * @route '/medical-orders/{medical_order}/complete'
 */
completePage.get = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: completePage.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MedicalOrderController::completePage
 * @see app/Http/Controllers/MedicalOrderController.php:962
 * @route '/medical-orders/{medical_order}/complete'
 */
completePage.head = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: completePage.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\MedicalOrderController::completePage
 * @see app/Http/Controllers/MedicalOrderController.php:962
 * @route '/medical-orders/{medical_order}/complete'
 */
    const completePageForm = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: completePage.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\MedicalOrderController::completePage
 * @see app/Http/Controllers/MedicalOrderController.php:962
 * @route '/medical-orders/{medical_order}/complete'
 */
        completePageForm.get = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: completePage.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\MedicalOrderController::completePage
 * @see app/Http/Controllers/MedicalOrderController.php:962
 * @route '/medical-orders/{medical_order}/complete'
 */
        completePageForm.head = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: completePage.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    completePage.form = completePageForm
/**
* @see \App\Http\Controllers\MedicalOrderController::complete
 * @see app/Http/Controllers/MedicalOrderController.php:1025
 * @route '/medical-orders/{medical_order}/complete'
 */
export const complete = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: complete.url(args, options),
    method: 'patch',
})

complete.definition = {
    methods: ["patch"],
    url: '/medical-orders/{medical_order}/complete',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\MedicalOrderController::complete
 * @see app/Http/Controllers/MedicalOrderController.php:1025
 * @route '/medical-orders/{medical_order}/complete'
 */
complete.url = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { medical_order: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    medical_order: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        medical_order: args.medical_order,
                }

    return complete.definition.url
            .replace('{medical_order}', parsedArgs.medical_order.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalOrderController::complete
 * @see app/Http/Controllers/MedicalOrderController.php:1025
 * @route '/medical-orders/{medical_order}/complete'
 */
complete.patch = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: complete.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\MedicalOrderController::complete
 * @see app/Http/Controllers/MedicalOrderController.php:1025
 * @route '/medical-orders/{medical_order}/complete'
 */
    const completeForm = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: complete.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PATCH',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\MedicalOrderController::complete
 * @see app/Http/Controllers/MedicalOrderController.php:1025
 * @route '/medical-orders/{medical_order}/complete'
 */
        completeForm.patch = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: complete.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PATCH',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    complete.form = completeForm
/**
* @see \App\Http\Controllers\MedicalOrderController::completeItem
 * @see app/Http/Controllers/MedicalOrderController.php:1190
 * @route '/medical-orders/{medical_order}/items/{item}/complete'
 */
export const completeItem = (args: { medical_order: string | number, item: number | { id: number } } | [medical_order: string | number, item: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: completeItem.url(args, options),
    method: 'patch',
})

completeItem.definition = {
    methods: ["patch"],
    url: '/medical-orders/{medical_order}/items/{item}/complete',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\MedicalOrderController::completeItem
 * @see app/Http/Controllers/MedicalOrderController.php:1190
 * @route '/medical-orders/{medical_order}/items/{item}/complete'
 */
completeItem.url = (args: { medical_order: string | number, item: number | { id: number } } | [medical_order: string | number, item: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    medical_order: args[0],
                    item: args[1],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        medical_order: args.medical_order,
                                item: typeof args.item === 'object'
                ? args.item.id
                : args.item,
                }

    return completeItem.definition.url
            .replace('{medical_order}', parsedArgs.medical_order.toString())
            .replace('{item}', parsedArgs.item.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalOrderController::completeItem
 * @see app/Http/Controllers/MedicalOrderController.php:1190
 * @route '/medical-orders/{medical_order}/items/{item}/complete'
 */
completeItem.patch = (args: { medical_order: string | number, item: number | { id: number } } | [medical_order: string | number, item: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: completeItem.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\MedicalOrderController::completeItem
 * @see app/Http/Controllers/MedicalOrderController.php:1190
 * @route '/medical-orders/{medical_order}/items/{item}/complete'
 */
    const completeItemForm = (args: { medical_order: string | number, item: number | { id: number } } | [medical_order: string | number, item: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: completeItem.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PATCH',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\MedicalOrderController::completeItem
 * @see app/Http/Controllers/MedicalOrderController.php:1190
 * @route '/medical-orders/{medical_order}/items/{item}/complete'
 */
        completeItemForm.patch = (args: { medical_order: string | number, item: number | { id: number } } | [medical_order: string | number, item: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: completeItem.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PATCH',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    completeItem.form = completeItemForm
/**
* @see \App\Http\Controllers\MedicalOrderController::saveLabResult
 * @see app/Http/Controllers/MedicalOrderController.php:1216
 * @route '/medical-orders/{medical_order}/items/{item}/lab-result'
 */
export const saveLabResult = (args: { medical_order: string | number, item: number | { id: number } } | [medical_order: string | number, item: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: saveLabResult.url(args, options),
    method: 'patch',
})

saveLabResult.definition = {
    methods: ["patch"],
    url: '/medical-orders/{medical_order}/items/{item}/lab-result',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\MedicalOrderController::saveLabResult
 * @see app/Http/Controllers/MedicalOrderController.php:1216
 * @route '/medical-orders/{medical_order}/items/{item}/lab-result'
 */
saveLabResult.url = (args: { medical_order: string | number, item: number | { id: number } } | [medical_order: string | number, item: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    medical_order: args[0],
                    item: args[1],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        medical_order: args.medical_order,
                                item: typeof args.item === 'object'
                ? args.item.id
                : args.item,
                }

    return saveLabResult.definition.url
            .replace('{medical_order}', parsedArgs.medical_order.toString())
            .replace('{item}', parsedArgs.item.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalOrderController::saveLabResult
 * @see app/Http/Controllers/MedicalOrderController.php:1216
 * @route '/medical-orders/{medical_order}/items/{item}/lab-result'
 */
saveLabResult.patch = (args: { medical_order: string | number, item: number | { id: number } } | [medical_order: string | number, item: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: saveLabResult.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\MedicalOrderController::saveLabResult
 * @see app/Http/Controllers/MedicalOrderController.php:1216
 * @route '/medical-orders/{medical_order}/items/{item}/lab-result'
 */
    const saveLabResultForm = (args: { medical_order: string | number, item: number | { id: number } } | [medical_order: string | number, item: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: saveLabResult.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PATCH',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\MedicalOrderController::saveLabResult
 * @see app/Http/Controllers/MedicalOrderController.php:1216
 * @route '/medical-orders/{medical_order}/items/{item}/lab-result'
 */
        saveLabResultForm.patch = (args: { medical_order: string | number, item: number | { id: number } } | [medical_order: string | number, item: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: saveLabResult.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PATCH',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    saveLabResult.form = saveLabResultForm
/**
* @see \App\Http\Controllers\MedicalOrderController::processAndBill
 * @see app/Http/Controllers/MedicalOrderController.php:1037
 * @route '/medical-orders/{medical_order}/process-and-bill'
 */
export const processAndBill = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: processAndBill.url(args, options),
    method: 'patch',
})

processAndBill.definition = {
    methods: ["patch"],
    url: '/medical-orders/{medical_order}/process-and-bill',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\MedicalOrderController::processAndBill
 * @see app/Http/Controllers/MedicalOrderController.php:1037
 * @route '/medical-orders/{medical_order}/process-and-bill'
 */
processAndBill.url = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { medical_order: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    medical_order: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        medical_order: args.medical_order,
                }

    return processAndBill.definition.url
            .replace('{medical_order}', parsedArgs.medical_order.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalOrderController::processAndBill
 * @see app/Http/Controllers/MedicalOrderController.php:1037
 * @route '/medical-orders/{medical_order}/process-and-bill'
 */
processAndBill.patch = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: processAndBill.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\MedicalOrderController::processAndBill
 * @see app/Http/Controllers/MedicalOrderController.php:1037
 * @route '/medical-orders/{medical_order}/process-and-bill'
 */
    const processAndBillForm = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: processAndBill.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PATCH',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\MedicalOrderController::processAndBill
 * @see app/Http/Controllers/MedicalOrderController.php:1037
 * @route '/medical-orders/{medical_order}/process-and-bill'
 */
        processAndBillForm.patch = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: processAndBill.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PATCH',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    processAndBill.form = processAndBillForm
/**
* @see \App\Http\Controllers\MedicalOrderController::confirmProcessed
 * @see app/Http/Controllers/MedicalOrderController.php:1123
 * @route '/medical-orders/{medical_order}/confirm-processed'
 */
export const confirmProcessed = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: confirmProcessed.url(args, options),
    method: 'patch',
})

confirmProcessed.definition = {
    methods: ["patch"],
    url: '/medical-orders/{medical_order}/confirm-processed',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\MedicalOrderController::confirmProcessed
 * @see app/Http/Controllers/MedicalOrderController.php:1123
 * @route '/medical-orders/{medical_order}/confirm-processed'
 */
confirmProcessed.url = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { medical_order: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    medical_order: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        medical_order: args.medical_order,
                }

    return confirmProcessed.definition.url
            .replace('{medical_order}', parsedArgs.medical_order.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalOrderController::confirmProcessed
 * @see app/Http/Controllers/MedicalOrderController.php:1123
 * @route '/medical-orders/{medical_order}/confirm-processed'
 */
confirmProcessed.patch = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: confirmProcessed.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\MedicalOrderController::confirmProcessed
 * @see app/Http/Controllers/MedicalOrderController.php:1123
 * @route '/medical-orders/{medical_order}/confirm-processed'
 */
    const confirmProcessedForm = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: confirmProcessed.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PATCH',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\MedicalOrderController::confirmProcessed
 * @see app/Http/Controllers/MedicalOrderController.php:1123
 * @route '/medical-orders/{medical_order}/confirm-processed'
 */
        confirmProcessedForm.patch = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: confirmProcessed.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PATCH',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    confirmProcessed.form = confirmProcessedForm
/**
* @see \App\Http\Controllers\MedicalOrderController::getCostBreakdown
 * @see app/Http/Controllers/MedicalOrderController.php:1149
 * @route '/medical-orders/{medical_order}/cost-breakdown'
 */
export const getCostBreakdown = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCostBreakdown.url(args, options),
    method: 'get',
})

getCostBreakdown.definition = {
    methods: ["get","head"],
    url: '/medical-orders/{medical_order}/cost-breakdown',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MedicalOrderController::getCostBreakdown
 * @see app/Http/Controllers/MedicalOrderController.php:1149
 * @route '/medical-orders/{medical_order}/cost-breakdown'
 */
getCostBreakdown.url = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { medical_order: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    medical_order: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        medical_order: args.medical_order,
                }

    return getCostBreakdown.definition.url
            .replace('{medical_order}', parsedArgs.medical_order.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalOrderController::getCostBreakdown
 * @see app/Http/Controllers/MedicalOrderController.php:1149
 * @route '/medical-orders/{medical_order}/cost-breakdown'
 */
getCostBreakdown.get = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCostBreakdown.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MedicalOrderController::getCostBreakdown
 * @see app/Http/Controllers/MedicalOrderController.php:1149
 * @route '/medical-orders/{medical_order}/cost-breakdown'
 */
getCostBreakdown.head = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getCostBreakdown.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\MedicalOrderController::getCostBreakdown
 * @see app/Http/Controllers/MedicalOrderController.php:1149
 * @route '/medical-orders/{medical_order}/cost-breakdown'
 */
    const getCostBreakdownForm = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getCostBreakdown.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\MedicalOrderController::getCostBreakdown
 * @see app/Http/Controllers/MedicalOrderController.php:1149
 * @route '/medical-orders/{medical_order}/cost-breakdown'
 */
        getCostBreakdownForm.get = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getCostBreakdown.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\MedicalOrderController::getCostBreakdown
 * @see app/Http/Controllers/MedicalOrderController.php:1149
 * @route '/medical-orders/{medical_order}/cost-breakdown'
 */
        getCostBreakdownForm.head = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getCostBreakdown.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getCostBreakdown.form = getCostBreakdownForm
/**
* @see \App\Http\Controllers\MedicalOrderController::cancelProcessed
 * @see app/Http/Controllers/MedicalOrderController.php:0
 * @route '/medical-orders/{medical_order}/cancel-processed'
 */
export const cancelProcessed = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: cancelProcessed.url(args, options),
    method: 'patch',
})

cancelProcessed.definition = {
    methods: ["patch"],
    url: '/medical-orders/{medical_order}/cancel-processed',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\MedicalOrderController::cancelProcessed
 * @see app/Http/Controllers/MedicalOrderController.php:0
 * @route '/medical-orders/{medical_order}/cancel-processed'
 */
cancelProcessed.url = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { medical_order: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    medical_order: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        medical_order: args.medical_order,
                }

    return cancelProcessed.definition.url
            .replace('{medical_order}', parsedArgs.medical_order.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalOrderController::cancelProcessed
 * @see app/Http/Controllers/MedicalOrderController.php:0
 * @route '/medical-orders/{medical_order}/cancel-processed'
 */
cancelProcessed.patch = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: cancelProcessed.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\MedicalOrderController::cancelProcessed
 * @see app/Http/Controllers/MedicalOrderController.php:0
 * @route '/medical-orders/{medical_order}/cancel-processed'
 */
    const cancelProcessedForm = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: cancelProcessed.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PATCH',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\MedicalOrderController::cancelProcessed
 * @see app/Http/Controllers/MedicalOrderController.php:0
 * @route '/medical-orders/{medical_order}/cancel-processed'
 */
        cancelProcessedForm.patch = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: cancelProcessed.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PATCH',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    cancelProcessed.form = cancelProcessedForm
/**
* @see \App\Http\Controllers\MedicalOrderController::sendBack
 * @see app/Http/Controllers/MedicalOrderController.php:1159
 * @route '/medical-orders/{medical_order}/send-back'
 */
export const sendBack = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: sendBack.url(args, options),
    method: 'patch',
})

sendBack.definition = {
    methods: ["patch"],
    url: '/medical-orders/{medical_order}/send-back',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\MedicalOrderController::sendBack
 * @see app/Http/Controllers/MedicalOrderController.php:1159
 * @route '/medical-orders/{medical_order}/send-back'
 */
sendBack.url = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { medical_order: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    medical_order: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        medical_order: args.medical_order,
                }

    return sendBack.definition.url
            .replace('{medical_order}', parsedArgs.medical_order.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalOrderController::sendBack
 * @see app/Http/Controllers/MedicalOrderController.php:1159
 * @route '/medical-orders/{medical_order}/send-back'
 */
sendBack.patch = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: sendBack.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\MedicalOrderController::sendBack
 * @see app/Http/Controllers/MedicalOrderController.php:1159
 * @route '/medical-orders/{medical_order}/send-back'
 */
    const sendBackForm = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: sendBack.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PATCH',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\MedicalOrderController::sendBack
 * @see app/Http/Controllers/MedicalOrderController.php:1159
 * @route '/medical-orders/{medical_order}/send-back'
 */
        sendBackForm.patch = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: sendBack.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PATCH',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    sendBack.form = sendBackForm
/**
* @see \App\Http\Controllers\MedicalOrderController::generateReport
 * @see app/Http/Controllers/MedicalOrderController.php:1253
 * @route '/medical-orders/{medical_order}/report'
 */
export const generateReport = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: generateReport.url(args, options),
    method: 'get',
})

generateReport.definition = {
    methods: ["get","head"],
    url: '/medical-orders/{medical_order}/report',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MedicalOrderController::generateReport
 * @see app/Http/Controllers/MedicalOrderController.php:1253
 * @route '/medical-orders/{medical_order}/report'
 */
generateReport.url = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { medical_order: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    medical_order: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        medical_order: args.medical_order,
                }

    return generateReport.definition.url
            .replace('{medical_order}', parsedArgs.medical_order.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalOrderController::generateReport
 * @see app/Http/Controllers/MedicalOrderController.php:1253
 * @route '/medical-orders/{medical_order}/report'
 */
generateReport.get = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: generateReport.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MedicalOrderController::generateReport
 * @see app/Http/Controllers/MedicalOrderController.php:1253
 * @route '/medical-orders/{medical_order}/report'
 */
generateReport.head = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: generateReport.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\MedicalOrderController::generateReport
 * @see app/Http/Controllers/MedicalOrderController.php:1253
 * @route '/medical-orders/{medical_order}/report'
 */
    const generateReportForm = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: generateReport.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\MedicalOrderController::generateReport
 * @see app/Http/Controllers/MedicalOrderController.php:1253
 * @route '/medical-orders/{medical_order}/report'
 */
        generateReportForm.get = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: generateReport.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\MedicalOrderController::generateReport
 * @see app/Http/Controllers/MedicalOrderController.php:1253
 * @route '/medical-orders/{medical_order}/report'
 */
        generateReportForm.head = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\MedicalOrderController::generateMedicalRecordReport
 * @see app/Http/Controllers/MedicalOrderController.php:1543
 * @route '/medical-orders/{medical_order}/medical-record-report'
 */
export const generateMedicalRecordReport = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: generateMedicalRecordReport.url(args, options),
    method: 'get',
})

generateMedicalRecordReport.definition = {
    methods: ["get","head"],
    url: '/medical-orders/{medical_order}/medical-record-report',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MedicalOrderController::generateMedicalRecordReport
 * @see app/Http/Controllers/MedicalOrderController.php:1543
 * @route '/medical-orders/{medical_order}/medical-record-report'
 */
generateMedicalRecordReport.url = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { medical_order: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    medical_order: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        medical_order: args.medical_order,
                }

    return generateMedicalRecordReport.definition.url
            .replace('{medical_order}', parsedArgs.medical_order.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalOrderController::generateMedicalRecordReport
 * @see app/Http/Controllers/MedicalOrderController.php:1543
 * @route '/medical-orders/{medical_order}/medical-record-report'
 */
generateMedicalRecordReport.get = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: generateMedicalRecordReport.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MedicalOrderController::generateMedicalRecordReport
 * @see app/Http/Controllers/MedicalOrderController.php:1543
 * @route '/medical-orders/{medical_order}/medical-record-report'
 */
generateMedicalRecordReport.head = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: generateMedicalRecordReport.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\MedicalOrderController::generateMedicalRecordReport
 * @see app/Http/Controllers/MedicalOrderController.php:1543
 * @route '/medical-orders/{medical_order}/medical-record-report'
 */
    const generateMedicalRecordReportForm = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: generateMedicalRecordReport.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\MedicalOrderController::generateMedicalRecordReport
 * @see app/Http/Controllers/MedicalOrderController.php:1543
 * @route '/medical-orders/{medical_order}/medical-record-report'
 */
        generateMedicalRecordReportForm.get = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: generateMedicalRecordReport.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\MedicalOrderController::generateMedicalRecordReport
 * @see app/Http/Controllers/MedicalOrderController.php:1543
 * @route '/medical-orders/{medical_order}/medical-record-report'
 */
        generateMedicalRecordReportForm.head = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: generateMedicalRecordReport.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    generateMedicalRecordReport.form = generateMedicalRecordReportForm
const MedicalOrderController = { index, create, store, show, edit, update, destroy, processPage, processingPage, processWithUpdate, completePage, complete, completeItem, saveLabResult, processAndBill, confirmProcessed, getCostBreakdown, cancelProcessed, sendBack, generateReport, generateMedicalRecordReport }

export default MedicalOrderController