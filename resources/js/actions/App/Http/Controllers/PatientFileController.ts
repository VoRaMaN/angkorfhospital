import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\PatientFileController::index
 * @see app/Http/Controllers/PatientFileController.php:18
 * @route '/patient-files'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/patient-files',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PatientFileController::index
 * @see app/Http/Controllers/PatientFileController.php:18
 * @route '/patient-files'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PatientFileController::index
 * @see app/Http/Controllers/PatientFileController.php:18
 * @route '/patient-files'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\PatientFileController::index
 * @see app/Http/Controllers/PatientFileController.php:18
 * @route '/patient-files'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\PatientFileController::index
 * @see app/Http/Controllers/PatientFileController.php:18
 * @route '/patient-files'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\PatientFileController::index
 * @see app/Http/Controllers/PatientFileController.php:18
 * @route '/patient-files'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\PatientFileController::index
 * @see app/Http/Controllers/PatientFileController.php:18
 * @route '/patient-files'
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
* @see \App\Http\Controllers\PatientFileController::create
 * @see app/Http/Controllers/PatientFileController.php:57
 * @route '/patient-files/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/patient-files/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PatientFileController::create
 * @see app/Http/Controllers/PatientFileController.php:57
 * @route '/patient-files/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PatientFileController::create
 * @see app/Http/Controllers/PatientFileController.php:57
 * @route '/patient-files/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\PatientFileController::create
 * @see app/Http/Controllers/PatientFileController.php:57
 * @route '/patient-files/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\PatientFileController::create
 * @see app/Http/Controllers/PatientFileController.php:57
 * @route '/patient-files/create'
 */
    const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: create.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\PatientFileController::create
 * @see app/Http/Controllers/PatientFileController.php:57
 * @route '/patient-files/create'
 */
        createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: create.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\PatientFileController::create
 * @see app/Http/Controllers/PatientFileController.php:57
 * @route '/patient-files/create'
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
* @see \App\Http\Controllers\PatientFileController::store
 * @see app/Http/Controllers/PatientFileController.php:70
 * @route '/patient-files'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/patient-files',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PatientFileController::store
 * @see app/Http/Controllers/PatientFileController.php:70
 * @route '/patient-files'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PatientFileController::store
 * @see app/Http/Controllers/PatientFileController.php:70
 * @route '/patient-files'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\PatientFileController::store
 * @see app/Http/Controllers/PatientFileController.php:70
 * @route '/patient-files'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\PatientFileController::store
 * @see app/Http/Controllers/PatientFileController.php:70
 * @route '/patient-files'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\PatientFileController::show
 * @see app/Http/Controllers/PatientFileController.php:125
 * @route '/patient-files/{patient_file}'
 */
export const show = (args: { patient_file: string | number } | [patient_file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/patient-files/{patient_file}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PatientFileController::show
 * @see app/Http/Controllers/PatientFileController.php:125
 * @route '/patient-files/{patient_file}'
 */
show.url = (args: { patient_file: string | number } | [patient_file: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { patient_file: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    patient_file: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        patient_file: args.patient_file,
                }

    return show.definition.url
            .replace('{patient_file}', parsedArgs.patient_file.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PatientFileController::show
 * @see app/Http/Controllers/PatientFileController.php:125
 * @route '/patient-files/{patient_file}'
 */
show.get = (args: { patient_file: string | number } | [patient_file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\PatientFileController::show
 * @see app/Http/Controllers/PatientFileController.php:125
 * @route '/patient-files/{patient_file}'
 */
show.head = (args: { patient_file: string | number } | [patient_file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\PatientFileController::show
 * @see app/Http/Controllers/PatientFileController.php:125
 * @route '/patient-files/{patient_file}'
 */
    const showForm = (args: { patient_file: string | number } | [patient_file: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\PatientFileController::show
 * @see app/Http/Controllers/PatientFileController.php:125
 * @route '/patient-files/{patient_file}'
 */
        showForm.get = (args: { patient_file: string | number } | [patient_file: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\PatientFileController::show
 * @see app/Http/Controllers/PatientFileController.php:125
 * @route '/patient-files/{patient_file}'
 */
        showForm.head = (args: { patient_file: string | number } | [patient_file: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\PatientFileController::edit
 * @see app/Http/Controllers/PatientFileController.php:141
 * @route '/patient-files/{patient_file}/edit'
 */
export const edit = (args: { patient_file: string | number } | [patient_file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/patient-files/{patient_file}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PatientFileController::edit
 * @see app/Http/Controllers/PatientFileController.php:141
 * @route '/patient-files/{patient_file}/edit'
 */
edit.url = (args: { patient_file: string | number } | [patient_file: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { patient_file: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    patient_file: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        patient_file: args.patient_file,
                }

    return edit.definition.url
            .replace('{patient_file}', parsedArgs.patient_file.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PatientFileController::edit
 * @see app/Http/Controllers/PatientFileController.php:141
 * @route '/patient-files/{patient_file}/edit'
 */
edit.get = (args: { patient_file: string | number } | [patient_file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\PatientFileController::edit
 * @see app/Http/Controllers/PatientFileController.php:141
 * @route '/patient-files/{patient_file}/edit'
 */
edit.head = (args: { patient_file: string | number } | [patient_file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\PatientFileController::edit
 * @see app/Http/Controllers/PatientFileController.php:141
 * @route '/patient-files/{patient_file}/edit'
 */
    const editForm = (args: { patient_file: string | number } | [patient_file: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: edit.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\PatientFileController::edit
 * @see app/Http/Controllers/PatientFileController.php:141
 * @route '/patient-files/{patient_file}/edit'
 */
        editForm.get = (args: { patient_file: string | number } | [patient_file: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: edit.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\PatientFileController::edit
 * @see app/Http/Controllers/PatientFileController.php:141
 * @route '/patient-files/{patient_file}/edit'
 */
        editForm.head = (args: { patient_file: string | number } | [patient_file: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\PatientFileController::update
 * @see app/Http/Controllers/PatientFileController.php:156
 * @route '/patient-files/{patient_file}'
 */
export const update = (args: { patient_file: string | number } | [patient_file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/patient-files/{patient_file}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\PatientFileController::update
 * @see app/Http/Controllers/PatientFileController.php:156
 * @route '/patient-files/{patient_file}'
 */
update.url = (args: { patient_file: string | number } | [patient_file: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { patient_file: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    patient_file: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        patient_file: args.patient_file,
                }

    return update.definition.url
            .replace('{patient_file}', parsedArgs.patient_file.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PatientFileController::update
 * @see app/Http/Controllers/PatientFileController.php:156
 * @route '/patient-files/{patient_file}'
 */
update.put = (args: { patient_file: string | number } | [patient_file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\PatientFileController::update
 * @see app/Http/Controllers/PatientFileController.php:156
 * @route '/patient-files/{patient_file}'
 */
update.patch = (args: { patient_file: string | number } | [patient_file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\PatientFileController::update
 * @see app/Http/Controllers/PatientFileController.php:156
 * @route '/patient-files/{patient_file}'
 */
    const updateForm = (args: { patient_file: string | number } | [patient_file: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\PatientFileController::update
 * @see app/Http/Controllers/PatientFileController.php:156
 * @route '/patient-files/{patient_file}'
 */
        updateForm.put = (args: { patient_file: string | number } | [patient_file: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
            /**
* @see \App\Http\Controllers\PatientFileController::update
 * @see app/Http/Controllers/PatientFileController.php:156
 * @route '/patient-files/{patient_file}'
 */
        updateForm.patch = (args: { patient_file: string | number } | [patient_file: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\PatientFileController::destroy
 * @see app/Http/Controllers/PatientFileController.php:198
 * @route '/patient-files/{patient_file}'
 */
export const destroy = (args: { patient_file: string | number } | [patient_file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/patient-files/{patient_file}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\PatientFileController::destroy
 * @see app/Http/Controllers/PatientFileController.php:198
 * @route '/patient-files/{patient_file}'
 */
destroy.url = (args: { patient_file: string | number } | [patient_file: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { patient_file: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    patient_file: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        patient_file: args.patient_file,
                }

    return destroy.definition.url
            .replace('{patient_file}', parsedArgs.patient_file.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PatientFileController::destroy
 * @see app/Http/Controllers/PatientFileController.php:198
 * @route '/patient-files/{patient_file}'
 */
destroy.delete = (args: { patient_file: string | number } | [patient_file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\PatientFileController::destroy
 * @see app/Http/Controllers/PatientFileController.php:198
 * @route '/patient-files/{patient_file}'
 */
    const destroyForm = (args: { patient_file: string | number } | [patient_file: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\PatientFileController::destroy
 * @see app/Http/Controllers/PatientFileController.php:198
 * @route '/patient-files/{patient_file}'
 */
        destroyForm.delete = (args: { patient_file: string | number } | [patient_file: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\PatientFileController::download
 * @see app/Http/Controllers/PatientFileController.php:105
 * @route '/patient-files/{patient_file}/download'
 */
export const download = (args: { patient_file: string | number } | [patient_file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: download.url(args, options),
    method: 'get',
})

download.definition = {
    methods: ["get","head"],
    url: '/patient-files/{patient_file}/download',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PatientFileController::download
 * @see app/Http/Controllers/PatientFileController.php:105
 * @route '/patient-files/{patient_file}/download'
 */
download.url = (args: { patient_file: string | number } | [patient_file: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { patient_file: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    patient_file: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        patient_file: args.patient_file,
                }

    return download.definition.url
            .replace('{patient_file}', parsedArgs.patient_file.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PatientFileController::download
 * @see app/Http/Controllers/PatientFileController.php:105
 * @route '/patient-files/{patient_file}/download'
 */
download.get = (args: { patient_file: string | number } | [patient_file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: download.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\PatientFileController::download
 * @see app/Http/Controllers/PatientFileController.php:105
 * @route '/patient-files/{patient_file}/download'
 */
download.head = (args: { patient_file: string | number } | [patient_file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: download.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\PatientFileController::download
 * @see app/Http/Controllers/PatientFileController.php:105
 * @route '/patient-files/{patient_file}/download'
 */
    const downloadForm = (args: { patient_file: string | number } | [patient_file: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: download.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\PatientFileController::download
 * @see app/Http/Controllers/PatientFileController.php:105
 * @route '/patient-files/{patient_file}/download'
 */
        downloadForm.get = (args: { patient_file: string | number } | [patient_file: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: download.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\PatientFileController::download
 * @see app/Http/Controllers/PatientFileController.php:105
 * @route '/patient-files/{patient_file}/download'
 */
        downloadForm.head = (args: { patient_file: string | number } | [patient_file: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: download.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    download.form = downloadForm
const PatientFileController = { index, create, store, show, edit, update, destroy, download }

export default PatientFileController