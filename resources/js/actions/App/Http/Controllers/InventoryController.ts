import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\InventoryController::index
 * @see app/Http/Controllers/InventoryController.php:14
 * @route '/inventory'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/inventory',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\InventoryController::index
 * @see app/Http/Controllers/InventoryController.php:14
 * @route '/inventory'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\InventoryController::index
 * @see app/Http/Controllers/InventoryController.php:14
 * @route '/inventory'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\InventoryController::index
 * @see app/Http/Controllers/InventoryController.php:14
 * @route '/inventory'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\InventoryController::index
 * @see app/Http/Controllers/InventoryController.php:14
 * @route '/inventory'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\InventoryController::index
 * @see app/Http/Controllers/InventoryController.php:14
 * @route '/inventory'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\InventoryController::index
 * @see app/Http/Controllers/InventoryController.php:14
 * @route '/inventory'
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
* @see \App\Http\Controllers\InventoryController::create
 * @see app/Http/Controllers/InventoryController.php:64
 * @route '/inventory/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/inventory/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\InventoryController::create
 * @see app/Http/Controllers/InventoryController.php:64
 * @route '/inventory/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\InventoryController::create
 * @see app/Http/Controllers/InventoryController.php:64
 * @route '/inventory/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\InventoryController::create
 * @see app/Http/Controllers/InventoryController.php:64
 * @route '/inventory/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\InventoryController::create
 * @see app/Http/Controllers/InventoryController.php:64
 * @route '/inventory/create'
 */
    const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: create.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\InventoryController::create
 * @see app/Http/Controllers/InventoryController.php:64
 * @route '/inventory/create'
 */
        createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: create.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\InventoryController::create
 * @see app/Http/Controllers/InventoryController.php:64
 * @route '/inventory/create'
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
* @see \App\Http\Controllers\InventoryController::store
 * @see app/Http/Controllers/InventoryController.php:73
 * @route '/inventory'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/inventory',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\InventoryController::store
 * @see app/Http/Controllers/InventoryController.php:73
 * @route '/inventory'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\InventoryController::store
 * @see app/Http/Controllers/InventoryController.php:73
 * @route '/inventory'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\InventoryController::store
 * @see app/Http/Controllers/InventoryController.php:73
 * @route '/inventory'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\InventoryController::store
 * @see app/Http/Controllers/InventoryController.php:73
 * @route '/inventory'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\InventoryController::show
 * @see app/Http/Controllers/InventoryController.php:80
 * @route '/inventory/{inventory}'
 */
export const show = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/inventory/{inventory}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\InventoryController::show
 * @see app/Http/Controllers/InventoryController.php:80
 * @route '/inventory/{inventory}'
 */
show.url = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { inventory: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { inventory: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    inventory: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        inventory: typeof args.inventory === 'object'
                ? args.inventory.id
                : args.inventory,
                }

    return show.definition.url
            .replace('{inventory}', parsedArgs.inventory.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\InventoryController::show
 * @see app/Http/Controllers/InventoryController.php:80
 * @route '/inventory/{inventory}'
 */
show.get = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\InventoryController::show
 * @see app/Http/Controllers/InventoryController.php:80
 * @route '/inventory/{inventory}'
 */
show.head = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\InventoryController::show
 * @see app/Http/Controllers/InventoryController.php:80
 * @route '/inventory/{inventory}'
 */
    const showForm = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\InventoryController::show
 * @see app/Http/Controllers/InventoryController.php:80
 * @route '/inventory/{inventory}'
 */
        showForm.get = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\InventoryController::show
 * @see app/Http/Controllers/InventoryController.php:80
 * @route '/inventory/{inventory}'
 */
        showForm.head = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\InventoryController::edit
 * @see app/Http/Controllers/InventoryController.php:90
 * @route '/inventory/{inventory}/edit'
 */
export const edit = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/inventory/{inventory}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\InventoryController::edit
 * @see app/Http/Controllers/InventoryController.php:90
 * @route '/inventory/{inventory}/edit'
 */
edit.url = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { inventory: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { inventory: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    inventory: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        inventory: typeof args.inventory === 'object'
                ? args.inventory.id
                : args.inventory,
                }

    return edit.definition.url
            .replace('{inventory}', parsedArgs.inventory.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\InventoryController::edit
 * @see app/Http/Controllers/InventoryController.php:90
 * @route '/inventory/{inventory}/edit'
 */
edit.get = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\InventoryController::edit
 * @see app/Http/Controllers/InventoryController.php:90
 * @route '/inventory/{inventory}/edit'
 */
edit.head = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\InventoryController::edit
 * @see app/Http/Controllers/InventoryController.php:90
 * @route '/inventory/{inventory}/edit'
 */
    const editForm = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: edit.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\InventoryController::edit
 * @see app/Http/Controllers/InventoryController.php:90
 * @route '/inventory/{inventory}/edit'
 */
        editForm.get = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: edit.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\InventoryController::edit
 * @see app/Http/Controllers/InventoryController.php:90
 * @route '/inventory/{inventory}/edit'
 */
        editForm.head = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\InventoryController::update
 * @see app/Http/Controllers/InventoryController.php:100
 * @route '/inventory/{inventory}'
 */
export const update = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/inventory/{inventory}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\InventoryController::update
 * @see app/Http/Controllers/InventoryController.php:100
 * @route '/inventory/{inventory}'
 */
update.url = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { inventory: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { inventory: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    inventory: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        inventory: typeof args.inventory === 'object'
                ? args.inventory.id
                : args.inventory,
                }

    return update.definition.url
            .replace('{inventory}', parsedArgs.inventory.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\InventoryController::update
 * @see app/Http/Controllers/InventoryController.php:100
 * @route '/inventory/{inventory}'
 */
update.put = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\InventoryController::update
 * @see app/Http/Controllers/InventoryController.php:100
 * @route '/inventory/{inventory}'
 */
update.patch = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\InventoryController::update
 * @see app/Http/Controllers/InventoryController.php:100
 * @route '/inventory/{inventory}'
 */
    const updateForm = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\InventoryController::update
 * @see app/Http/Controllers/InventoryController.php:100
 * @route '/inventory/{inventory}'
 */
        updateForm.put = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
            /**
* @see \App\Http\Controllers\InventoryController::update
 * @see app/Http/Controllers/InventoryController.php:100
 * @route '/inventory/{inventory}'
 */
        updateForm.patch = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\InventoryController::destroy
 * @see app/Http/Controllers/InventoryController.php:107
 * @route '/inventory/{inventory}'
 */
export const destroy = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/inventory/{inventory}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\InventoryController::destroy
 * @see app/Http/Controllers/InventoryController.php:107
 * @route '/inventory/{inventory}'
 */
destroy.url = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { inventory: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { inventory: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    inventory: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        inventory: typeof args.inventory === 'object'
                ? args.inventory.id
                : args.inventory,
                }

    return destroy.definition.url
            .replace('{inventory}', parsedArgs.inventory.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\InventoryController::destroy
 * @see app/Http/Controllers/InventoryController.php:107
 * @route '/inventory/{inventory}'
 */
destroy.delete = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\InventoryController::destroy
 * @see app/Http/Controllers/InventoryController.php:107
 * @route '/inventory/{inventory}'
 */
    const destroyForm = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\InventoryController::destroy
 * @see app/Http/Controllers/InventoryController.php:107
 * @route '/inventory/{inventory}'
 */
        destroyForm.delete = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\InventoryController::rxMedicine
 * @see app/Http/Controllers/InventoryController.php:115
 * @route '/rx-medicine'
 */
export const rxMedicine = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: rxMedicine.url(options),
    method: 'get',
})

rxMedicine.definition = {
    methods: ["get","head"],
    url: '/rx-medicine',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\InventoryController::rxMedicine
 * @see app/Http/Controllers/InventoryController.php:115
 * @route '/rx-medicine'
 */
rxMedicine.url = (options?: RouteQueryOptions) => {
    return rxMedicine.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\InventoryController::rxMedicine
 * @see app/Http/Controllers/InventoryController.php:115
 * @route '/rx-medicine'
 */
rxMedicine.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: rxMedicine.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\InventoryController::rxMedicine
 * @see app/Http/Controllers/InventoryController.php:115
 * @route '/rx-medicine'
 */
rxMedicine.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: rxMedicine.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\InventoryController::rxMedicine
 * @see app/Http/Controllers/InventoryController.php:115
 * @route '/rx-medicine'
 */
    const rxMedicineForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: rxMedicine.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\InventoryController::rxMedicine
 * @see app/Http/Controllers/InventoryController.php:115
 * @route '/rx-medicine'
 */
        rxMedicineForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: rxMedicine.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\InventoryController::rxMedicine
 * @see app/Http/Controllers/InventoryController.php:115
 * @route '/rx-medicine'
 */
        rxMedicineForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: rxMedicine.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    rxMedicine.form = rxMedicineForm
/**
* @see \App\Http\Controllers\InventoryController::labInventory
 * @see app/Http/Controllers/InventoryController.php:137
 * @route '/lab-inventory'
 */
export const labInventory = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: labInventory.url(options),
    method: 'get',
})

labInventory.definition = {
    methods: ["get","head"],
    url: '/lab-inventory',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\InventoryController::labInventory
 * @see app/Http/Controllers/InventoryController.php:137
 * @route '/lab-inventory'
 */
labInventory.url = (options?: RouteQueryOptions) => {
    return labInventory.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\InventoryController::labInventory
 * @see app/Http/Controllers/InventoryController.php:137
 * @route '/lab-inventory'
 */
labInventory.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: labInventory.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\InventoryController::labInventory
 * @see app/Http/Controllers/InventoryController.php:137
 * @route '/lab-inventory'
 */
labInventory.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: labInventory.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\InventoryController::labInventory
 * @see app/Http/Controllers/InventoryController.php:137
 * @route '/lab-inventory'
 */
    const labInventoryForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: labInventory.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\InventoryController::labInventory
 * @see app/Http/Controllers/InventoryController.php:137
 * @route '/lab-inventory'
 */
        labInventoryForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: labInventory.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\InventoryController::labInventory
 * @see app/Http/Controllers/InventoryController.php:137
 * @route '/lab-inventory'
 */
        labInventoryForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: labInventory.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    labInventory.form = labInventoryForm
/**
* @see \App\Http\Controllers\InventoryController::plasticWare
 * @see app/Http/Controllers/InventoryController.php:152
 * @route '/plastic-ware'
 */
export const plasticWare = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: plasticWare.url(options),
    method: 'get',
})

plasticWare.definition = {
    methods: ["get","head"],
    url: '/plastic-ware',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\InventoryController::plasticWare
 * @see app/Http/Controllers/InventoryController.php:152
 * @route '/plastic-ware'
 */
plasticWare.url = (options?: RouteQueryOptions) => {
    return plasticWare.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\InventoryController::plasticWare
 * @see app/Http/Controllers/InventoryController.php:152
 * @route '/plastic-ware'
 */
plasticWare.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: plasticWare.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\InventoryController::plasticWare
 * @see app/Http/Controllers/InventoryController.php:152
 * @route '/plastic-ware'
 */
plasticWare.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: plasticWare.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\InventoryController::plasticWare
 * @see app/Http/Controllers/InventoryController.php:152
 * @route '/plastic-ware'
 */
    const plasticWareForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: plasticWare.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\InventoryController::plasticWare
 * @see app/Http/Controllers/InventoryController.php:152
 * @route '/plastic-ware'
 */
        plasticWareForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: plasticWare.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\InventoryController::plasticWare
 * @see app/Http/Controllers/InventoryController.php:152
 * @route '/plastic-ware'
 */
        plasticWareForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: plasticWare.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    plasticWare.form = plasticWareForm
/**
* @see \App\Http\Controllers\InventoryController::cultureMedium
 * @see app/Http/Controllers/InventoryController.php:169
 * @route '/culture-medium'
 */
export const cultureMedium = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: cultureMedium.url(options),
    method: 'get',
})

cultureMedium.definition = {
    methods: ["get","head"],
    url: '/culture-medium',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\InventoryController::cultureMedium
 * @see app/Http/Controllers/InventoryController.php:169
 * @route '/culture-medium'
 */
cultureMedium.url = (options?: RouteQueryOptions) => {
    return cultureMedium.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\InventoryController::cultureMedium
 * @see app/Http/Controllers/InventoryController.php:169
 * @route '/culture-medium'
 */
cultureMedium.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: cultureMedium.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\InventoryController::cultureMedium
 * @see app/Http/Controllers/InventoryController.php:169
 * @route '/culture-medium'
 */
cultureMedium.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: cultureMedium.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\InventoryController::cultureMedium
 * @see app/Http/Controllers/InventoryController.php:169
 * @route '/culture-medium'
 */
    const cultureMediumForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: cultureMedium.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\InventoryController::cultureMedium
 * @see app/Http/Controllers/InventoryController.php:169
 * @route '/culture-medium'
 */
        cultureMediumForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: cultureMedium.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\InventoryController::cultureMedium
 * @see app/Http/Controllers/InventoryController.php:169
 * @route '/culture-medium'
 */
        cultureMediumForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: cultureMedium.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    cultureMedium.form = cultureMediumForm
const InventoryController = { index, create, store, show, edit, update, destroy, rxMedicine, labInventory, plasticWare, cultureMedium }

export default InventoryController