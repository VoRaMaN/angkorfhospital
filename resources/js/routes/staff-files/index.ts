import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\StaffFileController::index
 * @see app/Http/Controllers/StaffFileController.php:18
 * @route '/staff-files'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/staff-files',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\StaffFileController::index
 * @see app/Http/Controllers/StaffFileController.php:18
 * @route '/staff-files'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\StaffFileController::index
 * @see app/Http/Controllers/StaffFileController.php:18
 * @route '/staff-files'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\StaffFileController::index
 * @see app/Http/Controllers/StaffFileController.php:18
 * @route '/staff-files'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\StaffFileController::index
 * @see app/Http/Controllers/StaffFileController.php:18
 * @route '/staff-files'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\StaffFileController::index
 * @see app/Http/Controllers/StaffFileController.php:18
 * @route '/staff-files'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\StaffFileController::index
 * @see app/Http/Controllers/StaffFileController.php:18
 * @route '/staff-files'
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
* @see \App\Http\Controllers\StaffFileController::create
 * @see app/Http/Controllers/StaffFileController.php:57
 * @route '/staff-files/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/staff-files/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\StaffFileController::create
 * @see app/Http/Controllers/StaffFileController.php:57
 * @route '/staff-files/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\StaffFileController::create
 * @see app/Http/Controllers/StaffFileController.php:57
 * @route '/staff-files/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\StaffFileController::create
 * @see app/Http/Controllers/StaffFileController.php:57
 * @route '/staff-files/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\StaffFileController::create
 * @see app/Http/Controllers/StaffFileController.php:57
 * @route '/staff-files/create'
 */
    const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: create.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\StaffFileController::create
 * @see app/Http/Controllers/StaffFileController.php:57
 * @route '/staff-files/create'
 */
        createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: create.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\StaffFileController::create
 * @see app/Http/Controllers/StaffFileController.php:57
 * @route '/staff-files/create'
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
* @see \App\Http\Controllers\StaffFileController::store
 * @see app/Http/Controllers/StaffFileController.php:73
 * @route '/staff-files'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/staff-files',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\StaffFileController::store
 * @see app/Http/Controllers/StaffFileController.php:73
 * @route '/staff-files'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\StaffFileController::store
 * @see app/Http/Controllers/StaffFileController.php:73
 * @route '/staff-files'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\StaffFileController::store
 * @see app/Http/Controllers/StaffFileController.php:73
 * @route '/staff-files'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\StaffFileController::store
 * @see app/Http/Controllers/StaffFileController.php:73
 * @route '/staff-files'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\StaffFileController::show
 * @see app/Http/Controllers/StaffFileController.php:128
 * @route '/staff-files/{staff_file}'
 */
export const show = (args: { staff_file: string | number } | [staff_file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/staff-files/{staff_file}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\StaffFileController::show
 * @see app/Http/Controllers/StaffFileController.php:128
 * @route '/staff-files/{staff_file}'
 */
show.url = (args: { staff_file: string | number } | [staff_file: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { staff_file: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    staff_file: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        staff_file: args.staff_file,
                }

    return show.definition.url
            .replace('{staff_file}', parsedArgs.staff_file.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\StaffFileController::show
 * @see app/Http/Controllers/StaffFileController.php:128
 * @route '/staff-files/{staff_file}'
 */
show.get = (args: { staff_file: string | number } | [staff_file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\StaffFileController::show
 * @see app/Http/Controllers/StaffFileController.php:128
 * @route '/staff-files/{staff_file}'
 */
show.head = (args: { staff_file: string | number } | [staff_file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\StaffFileController::show
 * @see app/Http/Controllers/StaffFileController.php:128
 * @route '/staff-files/{staff_file}'
 */
    const showForm = (args: { staff_file: string | number } | [staff_file: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\StaffFileController::show
 * @see app/Http/Controllers/StaffFileController.php:128
 * @route '/staff-files/{staff_file}'
 */
        showForm.get = (args: { staff_file: string | number } | [staff_file: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\StaffFileController::show
 * @see app/Http/Controllers/StaffFileController.php:128
 * @route '/staff-files/{staff_file}'
 */
        showForm.head = (args: { staff_file: string | number } | [staff_file: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\StaffFileController::edit
 * @see app/Http/Controllers/StaffFileController.php:144
 * @route '/staff-files/{staff_file}/edit'
 */
export const edit = (args: { staff_file: string | number } | [staff_file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/staff-files/{staff_file}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\StaffFileController::edit
 * @see app/Http/Controllers/StaffFileController.php:144
 * @route '/staff-files/{staff_file}/edit'
 */
edit.url = (args: { staff_file: string | number } | [staff_file: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { staff_file: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    staff_file: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        staff_file: args.staff_file,
                }

    return edit.definition.url
            .replace('{staff_file}', parsedArgs.staff_file.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\StaffFileController::edit
 * @see app/Http/Controllers/StaffFileController.php:144
 * @route '/staff-files/{staff_file}/edit'
 */
edit.get = (args: { staff_file: string | number } | [staff_file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\StaffFileController::edit
 * @see app/Http/Controllers/StaffFileController.php:144
 * @route '/staff-files/{staff_file}/edit'
 */
edit.head = (args: { staff_file: string | number } | [staff_file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\StaffFileController::edit
 * @see app/Http/Controllers/StaffFileController.php:144
 * @route '/staff-files/{staff_file}/edit'
 */
    const editForm = (args: { staff_file: string | number } | [staff_file: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: edit.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\StaffFileController::edit
 * @see app/Http/Controllers/StaffFileController.php:144
 * @route '/staff-files/{staff_file}/edit'
 */
        editForm.get = (args: { staff_file: string | number } | [staff_file: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: edit.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\StaffFileController::edit
 * @see app/Http/Controllers/StaffFileController.php:144
 * @route '/staff-files/{staff_file}/edit'
 */
        editForm.head = (args: { staff_file: string | number } | [staff_file: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\StaffFileController::update
 * @see app/Http/Controllers/StaffFileController.php:162
 * @route '/staff-files/{staff_file}'
 */
export const update = (args: { staff_file: string | number } | [staff_file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/staff-files/{staff_file}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\StaffFileController::update
 * @see app/Http/Controllers/StaffFileController.php:162
 * @route '/staff-files/{staff_file}'
 */
update.url = (args: { staff_file: string | number } | [staff_file: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { staff_file: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    staff_file: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        staff_file: args.staff_file,
                }

    return update.definition.url
            .replace('{staff_file}', parsedArgs.staff_file.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\StaffFileController::update
 * @see app/Http/Controllers/StaffFileController.php:162
 * @route '/staff-files/{staff_file}'
 */
update.put = (args: { staff_file: string | number } | [staff_file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\StaffFileController::update
 * @see app/Http/Controllers/StaffFileController.php:162
 * @route '/staff-files/{staff_file}'
 */
update.patch = (args: { staff_file: string | number } | [staff_file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\StaffFileController::update
 * @see app/Http/Controllers/StaffFileController.php:162
 * @route '/staff-files/{staff_file}'
 */
    const updateForm = (args: { staff_file: string | number } | [staff_file: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\StaffFileController::update
 * @see app/Http/Controllers/StaffFileController.php:162
 * @route '/staff-files/{staff_file}'
 */
        updateForm.put = (args: { staff_file: string | number } | [staff_file: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
            /**
* @see \App\Http\Controllers\StaffFileController::update
 * @see app/Http/Controllers/StaffFileController.php:162
 * @route '/staff-files/{staff_file}'
 */
        updateForm.patch = (args: { staff_file: string | number } | [staff_file: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\StaffFileController::destroy
 * @see app/Http/Controllers/StaffFileController.php:204
 * @route '/staff-files/{staff_file}'
 */
export const destroy = (args: { staff_file: string | number } | [staff_file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/staff-files/{staff_file}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\StaffFileController::destroy
 * @see app/Http/Controllers/StaffFileController.php:204
 * @route '/staff-files/{staff_file}'
 */
destroy.url = (args: { staff_file: string | number } | [staff_file: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { staff_file: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    staff_file: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        staff_file: args.staff_file,
                }

    return destroy.definition.url
            .replace('{staff_file}', parsedArgs.staff_file.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\StaffFileController::destroy
 * @see app/Http/Controllers/StaffFileController.php:204
 * @route '/staff-files/{staff_file}'
 */
destroy.delete = (args: { staff_file: string | number } | [staff_file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\StaffFileController::destroy
 * @see app/Http/Controllers/StaffFileController.php:204
 * @route '/staff-files/{staff_file}'
 */
    const destroyForm = (args: { staff_file: string | number } | [staff_file: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\StaffFileController::destroy
 * @see app/Http/Controllers/StaffFileController.php:204
 * @route '/staff-files/{staff_file}'
 */
        destroyForm.delete = (args: { staff_file: string | number } | [staff_file: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\StaffFileController::download
 * @see app/Http/Controllers/StaffFileController.php:108
 * @route '/staff-files/{staff_file}/download'
 */
export const download = (args: { staff_file: string | number } | [staff_file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: download.url(args, options),
    method: 'get',
})

download.definition = {
    methods: ["get","head"],
    url: '/staff-files/{staff_file}/download',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\StaffFileController::download
 * @see app/Http/Controllers/StaffFileController.php:108
 * @route '/staff-files/{staff_file}/download'
 */
download.url = (args: { staff_file: string | number } | [staff_file: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { staff_file: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    staff_file: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        staff_file: args.staff_file,
                }

    return download.definition.url
            .replace('{staff_file}', parsedArgs.staff_file.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\StaffFileController::download
 * @see app/Http/Controllers/StaffFileController.php:108
 * @route '/staff-files/{staff_file}/download'
 */
download.get = (args: { staff_file: string | number } | [staff_file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: download.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\StaffFileController::download
 * @see app/Http/Controllers/StaffFileController.php:108
 * @route '/staff-files/{staff_file}/download'
 */
download.head = (args: { staff_file: string | number } | [staff_file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: download.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\StaffFileController::download
 * @see app/Http/Controllers/StaffFileController.php:108
 * @route '/staff-files/{staff_file}/download'
 */
    const downloadForm = (args: { staff_file: string | number } | [staff_file: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: download.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\StaffFileController::download
 * @see app/Http/Controllers/StaffFileController.php:108
 * @route '/staff-files/{staff_file}/download'
 */
        downloadForm.get = (args: { staff_file: string | number } | [staff_file: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: download.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\StaffFileController::download
 * @see app/Http/Controllers/StaffFileController.php:108
 * @route '/staff-files/{staff_file}/download'
 */
        downloadForm.head = (args: { staff_file: string | number } | [staff_file: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: download.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    download.form = downloadForm
const staffFiles = {
    index: Object.assign(index, index),
create: Object.assign(create, create),
store: Object.assign(store, store),
show: Object.assign(show, show),
edit: Object.assign(edit, edit),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
download: Object.assign(download, download),
}

export default staffFiles