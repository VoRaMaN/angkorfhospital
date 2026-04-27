import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\PatientController::index
 * @see app/Http/Controllers/PatientController.php:22
 * @route '/patients'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/patients',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PatientController::index
 * @see app/Http/Controllers/PatientController.php:22
 * @route '/patients'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PatientController::index
 * @see app/Http/Controllers/PatientController.php:22
 * @route '/patients'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\PatientController::index
 * @see app/Http/Controllers/PatientController.php:22
 * @route '/patients'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\PatientController::index
 * @see app/Http/Controllers/PatientController.php:22
 * @route '/patients'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\PatientController::index
 * @see app/Http/Controllers/PatientController.php:22
 * @route '/patients'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\PatientController::index
 * @see app/Http/Controllers/PatientController.php:22
 * @route '/patients'
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
* @see \App\Http\Controllers\PatientController::create
 * @see app/Http/Controllers/PatientController.php:97
 * @route '/patients/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/patients/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PatientController::create
 * @see app/Http/Controllers/PatientController.php:97
 * @route '/patients/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PatientController::create
 * @see app/Http/Controllers/PatientController.php:97
 * @route '/patients/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\PatientController::create
 * @see app/Http/Controllers/PatientController.php:97
 * @route '/patients/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\PatientController::create
 * @see app/Http/Controllers/PatientController.php:97
 * @route '/patients/create'
 */
    const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: create.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\PatientController::create
 * @see app/Http/Controllers/PatientController.php:97
 * @route '/patients/create'
 */
        createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: create.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\PatientController::create
 * @see app/Http/Controllers/PatientController.php:97
 * @route '/patients/create'
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
* @see \App\Http\Controllers\PatientController::store
 * @see app/Http/Controllers/PatientController.php:109
 * @route '/patients'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/patients',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PatientController::store
 * @see app/Http/Controllers/PatientController.php:109
 * @route '/patients'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PatientController::store
 * @see app/Http/Controllers/PatientController.php:109
 * @route '/patients'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\PatientController::store
 * @see app/Http/Controllers/PatientController.php:109
 * @route '/patients'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\PatientController::store
 * @see app/Http/Controllers/PatientController.php:109
 * @route '/patients'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\PatientController::show
 * @see app/Http/Controllers/PatientController.php:123
 * @route '/patients/show'
 */
export const show = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/patients/show',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PatientController::show
 * @see app/Http/Controllers/PatientController.php:123
 * @route '/patients/show'
 */
show.url = (options?: RouteQueryOptions) => {
    return show.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PatientController::show
 * @see app/Http/Controllers/PatientController.php:123
 * @route '/patients/show'
 */
show.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\PatientController::show
 * @see app/Http/Controllers/PatientController.php:123
 * @route '/patients/show'
 */
show.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\PatientController::show
 * @see app/Http/Controllers/PatientController.php:123
 * @route '/patients/show'
 */
    const showForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\PatientController::show
 * @see app/Http/Controllers/PatientController.php:123
 * @route '/patients/show'
 */
        showForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\PatientController::show
 * @see app/Http/Controllers/PatientController.php:123
 * @route '/patients/show'
 */
        showForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    show.form = showForm
/**
* @see \App\Http\Controllers\PatientController::edit
 * @see app/Http/Controllers/PatientController.php:250
 * @route '/patients/edit'
 */
export const edit = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/patients/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PatientController::edit
 * @see app/Http/Controllers/PatientController.php:250
 * @route '/patients/edit'
 */
edit.url = (options?: RouteQueryOptions) => {
    return edit.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PatientController::edit
 * @see app/Http/Controllers/PatientController.php:250
 * @route '/patients/edit'
 */
edit.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\PatientController::edit
 * @see app/Http/Controllers/PatientController.php:250
 * @route '/patients/edit'
 */
edit.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\PatientController::edit
 * @see app/Http/Controllers/PatientController.php:250
 * @route '/patients/edit'
 */
    const editForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: edit.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\PatientController::edit
 * @see app/Http/Controllers/PatientController.php:250
 * @route '/patients/edit'
 */
        editForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: edit.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\PatientController::edit
 * @see app/Http/Controllers/PatientController.php:250
 * @route '/patients/edit'
 */
        editForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: edit.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    edit.form = editForm
/**
* @see \App\Http\Controllers\PatientController::update
 * @see app/Http/Controllers/PatientController.php:266
 * @route '/patients/update'
 */
export const update = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/patients/update',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\PatientController::update
 * @see app/Http/Controllers/PatientController.php:266
 * @route '/patients/update'
 */
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PatientController::update
 * @see app/Http/Controllers/PatientController.php:266
 * @route '/patients/update'
 */
update.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\PatientController::update
 * @see app/Http/Controllers/PatientController.php:266
 * @route '/patients/update'
 */
    const updateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url({
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\PatientController::update
 * @see app/Http/Controllers/PatientController.php:266
 * @route '/patients/update'
 */
        updateForm.put = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    update.form = updateForm
/**
* @see \App\Http\Controllers\PatientController::destroy
 * @see app/Http/Controllers/PatientController.php:305
 * @route '/patients/destroy'
 */
export const destroy = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/patients/destroy',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\PatientController::destroy
 * @see app/Http/Controllers/PatientController.php:305
 * @route '/patients/destroy'
 */
destroy.url = (options?: RouteQueryOptions) => {
    return destroy.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PatientController::destroy
 * @see app/Http/Controllers/PatientController.php:305
 * @route '/patients/destroy'
 */
destroy.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\PatientController::destroy
 * @see app/Http/Controllers/PatientController.php:305
 * @route '/patients/destroy'
 */
    const destroyForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url({
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\PatientController::destroy
 * @see app/Http/Controllers/PatientController.php:305
 * @route '/patients/destroy'
 */
        destroyForm.delete = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
/**
* @see \App\Http\Controllers\PatientController::generateReport
 * @see app/Http/Controllers/PatientController.php:319
 * @route '/patients/report'
 */
export const generateReport = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: generateReport.url(options),
    method: 'get',
})

generateReport.definition = {
    methods: ["get","head"],
    url: '/patients/report',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PatientController::generateReport
 * @see app/Http/Controllers/PatientController.php:319
 * @route '/patients/report'
 */
generateReport.url = (options?: RouteQueryOptions) => {
    return generateReport.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PatientController::generateReport
 * @see app/Http/Controllers/PatientController.php:319
 * @route '/patients/report'
 */
generateReport.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: generateReport.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\PatientController::generateReport
 * @see app/Http/Controllers/PatientController.php:319
 * @route '/patients/report'
 */
generateReport.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: generateReport.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\PatientController::generateReport
 * @see app/Http/Controllers/PatientController.php:319
 * @route '/patients/report'
 */
    const generateReportForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: generateReport.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\PatientController::generateReport
 * @see app/Http/Controllers/PatientController.php:319
 * @route '/patients/report'
 */
        generateReportForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: generateReport.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\PatientController::generateReport
 * @see app/Http/Controllers/PatientController.php:319
 * @route '/patients/report'
 */
        generateReportForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: generateReport.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    generateReport.form = generateReportForm
/**
* @see \App\Http\Controllers\PatientController::downloadReport
 * @see app/Http/Controllers/PatientController.php:334
 * @route '/patients/report/download'
 */
export const downloadReport = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: downloadReport.url(options),
    method: 'get',
})

downloadReport.definition = {
    methods: ["get","head"],
    url: '/patients/report/download',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PatientController::downloadReport
 * @see app/Http/Controllers/PatientController.php:334
 * @route '/patients/report/download'
 */
downloadReport.url = (options?: RouteQueryOptions) => {
    return downloadReport.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PatientController::downloadReport
 * @see app/Http/Controllers/PatientController.php:334
 * @route '/patients/report/download'
 */
downloadReport.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: downloadReport.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\PatientController::downloadReport
 * @see app/Http/Controllers/PatientController.php:334
 * @route '/patients/report/download'
 */
downloadReport.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: downloadReport.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\PatientController::downloadReport
 * @see app/Http/Controllers/PatientController.php:334
 * @route '/patients/report/download'
 */
    const downloadReportForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: downloadReport.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\PatientController::downloadReport
 * @see app/Http/Controllers/PatientController.php:334
 * @route '/patients/report/download'
 */
        downloadReportForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: downloadReport.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\PatientController::downloadReport
 * @see app/Http/Controllers/PatientController.php:334
 * @route '/patients/report/download'
 */
        downloadReportForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: downloadReport.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    downloadReport.form = downloadReportForm
/**
* @see \App\Http\Controllers\PatientController::generateSticker
 * @see app/Http/Controllers/PatientController.php:471
 * @route '/patients/sticker'
 */
export const generateSticker = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: generateSticker.url(options),
    method: 'get',
})

generateSticker.definition = {
    methods: ["get","head"],
    url: '/patients/sticker',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PatientController::generateSticker
 * @see app/Http/Controllers/PatientController.php:471
 * @route '/patients/sticker'
 */
generateSticker.url = (options?: RouteQueryOptions) => {
    return generateSticker.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PatientController::generateSticker
 * @see app/Http/Controllers/PatientController.php:471
 * @route '/patients/sticker'
 */
generateSticker.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: generateSticker.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\PatientController::generateSticker
 * @see app/Http/Controllers/PatientController.php:471
 * @route '/patients/sticker'
 */
generateSticker.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: generateSticker.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\PatientController::generateSticker
 * @see app/Http/Controllers/PatientController.php:471
 * @route '/patients/sticker'
 */
    const generateStickerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: generateSticker.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\PatientController::generateSticker
 * @see app/Http/Controllers/PatientController.php:471
 * @route '/patients/sticker'
 */
        generateStickerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: generateSticker.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\PatientController::generateSticker
 * @see app/Http/Controllers/PatientController.php:471
 * @route '/patients/sticker'
 */
        generateStickerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: generateSticker.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    generateSticker.form = generateStickerForm
/**
* @see \App\Http\Controllers\PatientController::generateLabel
 * @see app/Http/Controllers/PatientController.php:610
 * @route '/patients/label'
 */
export const generateLabel = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: generateLabel.url(options),
    method: 'get',
})

generateLabel.definition = {
    methods: ["get","head"],
    url: '/patients/label',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PatientController::generateLabel
 * @see app/Http/Controllers/PatientController.php:610
 * @route '/patients/label'
 */
generateLabel.url = (options?: RouteQueryOptions) => {
    return generateLabel.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PatientController::generateLabel
 * @see app/Http/Controllers/PatientController.php:610
 * @route '/patients/label'
 */
generateLabel.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: generateLabel.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\PatientController::generateLabel
 * @see app/Http/Controllers/PatientController.php:610
 * @route '/patients/label'
 */
generateLabel.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: generateLabel.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\PatientController::generateLabel
 * @see app/Http/Controllers/PatientController.php:610
 * @route '/patients/label'
 */
    const generateLabelForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: generateLabel.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\PatientController::generateLabel
 * @see app/Http/Controllers/PatientController.php:610
 * @route '/patients/label'
 */
        generateLabelForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: generateLabel.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\PatientController::generateLabel
 * @see app/Http/Controllers/PatientController.php:610
 * @route '/patients/label'
 */
        generateLabelForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: generateLabel.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    generateLabel.form = generateLabelForm
const PatientController = { index, create, store, show, edit, update, destroy, generateReport, downloadReport, generateSticker, generateLabel }

export default PatientController