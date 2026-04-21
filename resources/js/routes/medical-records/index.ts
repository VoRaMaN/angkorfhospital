import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\MedicalRecordController::index
 * @see app/Http/Controllers/MedicalRecordController.php:18
 * @route '/medical-records'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/medical-records',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MedicalRecordController::index
 * @see app/Http/Controllers/MedicalRecordController.php:18
 * @route '/medical-records'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalRecordController::index
 * @see app/Http/Controllers/MedicalRecordController.php:18
 * @route '/medical-records'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MedicalRecordController::index
 * @see app/Http/Controllers/MedicalRecordController.php:18
 * @route '/medical-records'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\MedicalRecordController::index
 * @see app/Http/Controllers/MedicalRecordController.php:18
 * @route '/medical-records'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\MedicalRecordController::index
 * @see app/Http/Controllers/MedicalRecordController.php:18
 * @route '/medical-records'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\MedicalRecordController::index
 * @see app/Http/Controllers/MedicalRecordController.php:18
 * @route '/medical-records'
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
* @see \App\Http\Controllers\MedicalRecordController::create
 * @see app/Http/Controllers/MedicalRecordController.php:115
 * @route '/medical-records/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/medical-records/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MedicalRecordController::create
 * @see app/Http/Controllers/MedicalRecordController.php:115
 * @route '/medical-records/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalRecordController::create
 * @see app/Http/Controllers/MedicalRecordController.php:115
 * @route '/medical-records/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MedicalRecordController::create
 * @see app/Http/Controllers/MedicalRecordController.php:115
 * @route '/medical-records/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\MedicalRecordController::create
 * @see app/Http/Controllers/MedicalRecordController.php:115
 * @route '/medical-records/create'
 */
    const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: create.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\MedicalRecordController::create
 * @see app/Http/Controllers/MedicalRecordController.php:115
 * @route '/medical-records/create'
 */
        createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: create.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\MedicalRecordController::create
 * @see app/Http/Controllers/MedicalRecordController.php:115
 * @route '/medical-records/create'
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
* @see \App\Http\Controllers\MedicalRecordController::store
 * @see app/Http/Controllers/MedicalRecordController.php:148
 * @route '/medical-records'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/medical-records',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\MedicalRecordController::store
 * @see app/Http/Controllers/MedicalRecordController.php:148
 * @route '/medical-records'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalRecordController::store
 * @see app/Http/Controllers/MedicalRecordController.php:148
 * @route '/medical-records'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\MedicalRecordController::store
 * @see app/Http/Controllers/MedicalRecordController.php:148
 * @route '/medical-records'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\MedicalRecordController::store
 * @see app/Http/Controllers/MedicalRecordController.php:148
 * @route '/medical-records'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\MedicalRecordController::show
 * @see app/Http/Controllers/MedicalRecordController.php:159
 * @route '/medical-records/{medical_record}'
 */
export const show = (args: { medical_record: string | number } | [medical_record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/medical-records/{medical_record}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MedicalRecordController::show
 * @see app/Http/Controllers/MedicalRecordController.php:159
 * @route '/medical-records/{medical_record}'
 */
show.url = (args: { medical_record: string | number } | [medical_record: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { medical_record: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    medical_record: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        medical_record: args.medical_record,
                }

    return show.definition.url
            .replace('{medical_record}', parsedArgs.medical_record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalRecordController::show
 * @see app/Http/Controllers/MedicalRecordController.php:159
 * @route '/medical-records/{medical_record}'
 */
show.get = (args: { medical_record: string | number } | [medical_record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MedicalRecordController::show
 * @see app/Http/Controllers/MedicalRecordController.php:159
 * @route '/medical-records/{medical_record}'
 */
show.head = (args: { medical_record: string | number } | [medical_record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\MedicalRecordController::show
 * @see app/Http/Controllers/MedicalRecordController.php:159
 * @route '/medical-records/{medical_record}'
 */
    const showForm = (args: { medical_record: string | number } | [medical_record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\MedicalRecordController::show
 * @see app/Http/Controllers/MedicalRecordController.php:159
 * @route '/medical-records/{medical_record}'
 */
        showForm.get = (args: { medical_record: string | number } | [medical_record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\MedicalRecordController::show
 * @see app/Http/Controllers/MedicalRecordController.php:159
 * @route '/medical-records/{medical_record}'
 */
        showForm.head = (args: { medical_record: string | number } | [medical_record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\MedicalRecordController::edit
 * @see app/Http/Controllers/MedicalRecordController.php:195
 * @route '/medical-records/{medical_record}/edit'
 */
export const edit = (args: { medical_record: string | number } | [medical_record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/medical-records/{medical_record}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MedicalRecordController::edit
 * @see app/Http/Controllers/MedicalRecordController.php:195
 * @route '/medical-records/{medical_record}/edit'
 */
edit.url = (args: { medical_record: string | number } | [medical_record: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { medical_record: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    medical_record: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        medical_record: args.medical_record,
                }

    return edit.definition.url
            .replace('{medical_record}', parsedArgs.medical_record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalRecordController::edit
 * @see app/Http/Controllers/MedicalRecordController.php:195
 * @route '/medical-records/{medical_record}/edit'
 */
edit.get = (args: { medical_record: string | number } | [medical_record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MedicalRecordController::edit
 * @see app/Http/Controllers/MedicalRecordController.php:195
 * @route '/medical-records/{medical_record}/edit'
 */
edit.head = (args: { medical_record: string | number } | [medical_record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\MedicalRecordController::edit
 * @see app/Http/Controllers/MedicalRecordController.php:195
 * @route '/medical-records/{medical_record}/edit'
 */
    const editForm = (args: { medical_record: string | number } | [medical_record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: edit.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\MedicalRecordController::edit
 * @see app/Http/Controllers/MedicalRecordController.php:195
 * @route '/medical-records/{medical_record}/edit'
 */
        editForm.get = (args: { medical_record: string | number } | [medical_record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: edit.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\MedicalRecordController::edit
 * @see app/Http/Controllers/MedicalRecordController.php:195
 * @route '/medical-records/{medical_record}/edit'
 */
        editForm.head = (args: { medical_record: string | number } | [medical_record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\MedicalRecordController::update
 * @see app/Http/Controllers/MedicalRecordController.php:238
 * @route '/medical-records/{medical_record}'
 */
export const update = (args: { medical_record: string | number } | [medical_record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/medical-records/{medical_record}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\MedicalRecordController::update
 * @see app/Http/Controllers/MedicalRecordController.php:238
 * @route '/medical-records/{medical_record}'
 */
update.url = (args: { medical_record: string | number } | [medical_record: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { medical_record: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    medical_record: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        medical_record: args.medical_record,
                }

    return update.definition.url
            .replace('{medical_record}', parsedArgs.medical_record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalRecordController::update
 * @see app/Http/Controllers/MedicalRecordController.php:238
 * @route '/medical-records/{medical_record}'
 */
update.put = (args: { medical_record: string | number } | [medical_record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\MedicalRecordController::update
 * @see app/Http/Controllers/MedicalRecordController.php:238
 * @route '/medical-records/{medical_record}'
 */
update.patch = (args: { medical_record: string | number } | [medical_record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\MedicalRecordController::update
 * @see app/Http/Controllers/MedicalRecordController.php:238
 * @route '/medical-records/{medical_record}'
 */
    const updateForm = (args: { medical_record: string | number } | [medical_record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\MedicalRecordController::update
 * @see app/Http/Controllers/MedicalRecordController.php:238
 * @route '/medical-records/{medical_record}'
 */
        updateForm.put = (args: { medical_record: string | number } | [medical_record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
            /**
* @see \App\Http\Controllers\MedicalRecordController::update
 * @see app/Http/Controllers/MedicalRecordController.php:238
 * @route '/medical-records/{medical_record}'
 */
        updateForm.patch = (args: { medical_record: string | number } | [medical_record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\MedicalRecordController::destroy
 * @see app/Http/Controllers/MedicalRecordController.php:249
 * @route '/medical-records/{medical_record}'
 */
export const destroy = (args: { medical_record: string | number } | [medical_record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/medical-records/{medical_record}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\MedicalRecordController::destroy
 * @see app/Http/Controllers/MedicalRecordController.php:249
 * @route '/medical-records/{medical_record}'
 */
destroy.url = (args: { medical_record: string | number } | [medical_record: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { medical_record: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    medical_record: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        medical_record: args.medical_record,
                }

    return destroy.definition.url
            .replace('{medical_record}', parsedArgs.medical_record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalRecordController::destroy
 * @see app/Http/Controllers/MedicalRecordController.php:249
 * @route '/medical-records/{medical_record}'
 */
destroy.delete = (args: { medical_record: string | number } | [medical_record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\MedicalRecordController::destroy
 * @see app/Http/Controllers/MedicalRecordController.php:249
 * @route '/medical-records/{medical_record}'
 */
    const destroyForm = (args: { medical_record: string | number } | [medical_record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\MedicalRecordController::destroy
 * @see app/Http/Controllers/MedicalRecordController.php:249
 * @route '/medical-records/{medical_record}'
 */
        destroyForm.delete = (args: { medical_record: string | number } | [medical_record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\MedicalRecordController::report
 * @see app/Http/Controllers/MedicalRecordController.php:262
 * @route '/medical-records/{medical_record}/report'
 */
export const report = (args: { medical_record: string | number } | [medical_record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: report.url(args, options),
    method: 'get',
})

report.definition = {
    methods: ["get","head"],
    url: '/medical-records/{medical_record}/report',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MedicalRecordController::report
 * @see app/Http/Controllers/MedicalRecordController.php:262
 * @route '/medical-records/{medical_record}/report'
 */
report.url = (args: { medical_record: string | number } | [medical_record: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { medical_record: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    medical_record: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        medical_record: args.medical_record,
                }

    return report.definition.url
            .replace('{medical_record}', parsedArgs.medical_record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicalRecordController::report
 * @see app/Http/Controllers/MedicalRecordController.php:262
 * @route '/medical-records/{medical_record}/report'
 */
report.get = (args: { medical_record: string | number } | [medical_record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: report.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MedicalRecordController::report
 * @see app/Http/Controllers/MedicalRecordController.php:262
 * @route '/medical-records/{medical_record}/report'
 */
report.head = (args: { medical_record: string | number } | [medical_record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: report.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\MedicalRecordController::report
 * @see app/Http/Controllers/MedicalRecordController.php:262
 * @route '/medical-records/{medical_record}/report'
 */
    const reportForm = (args: { medical_record: string | number } | [medical_record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: report.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\MedicalRecordController::report
 * @see app/Http/Controllers/MedicalRecordController.php:262
 * @route '/medical-records/{medical_record}/report'
 */
        reportForm.get = (args: { medical_record: string | number } | [medical_record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: report.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\MedicalRecordController::report
 * @see app/Http/Controllers/MedicalRecordController.php:262
 * @route '/medical-records/{medical_record}/report'
 */
        reportForm.head = (args: { medical_record: string | number } | [medical_record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: report.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    report.form = reportForm
const medicalRecords = {
    index: Object.assign(index, index),
create: Object.assign(create, create),
store: Object.assign(store, store),
show: Object.assign(show, show),
edit: Object.assign(edit, edit),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
report: Object.assign(report, report),
}

export default medicalRecords