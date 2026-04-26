import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\AppointmentController::index
 * @see app/Http/Controllers/AppointmentController.php:18
 * @route '/appointments'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/appointments',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AppointmentController::index
 * @see app/Http/Controllers/AppointmentController.php:18
 * @route '/appointments'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AppointmentController::index
 * @see app/Http/Controllers/AppointmentController.php:18
 * @route '/appointments'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\AppointmentController::index
 * @see app/Http/Controllers/AppointmentController.php:18
 * @route '/appointments'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\AppointmentController::index
 * @see app/Http/Controllers/AppointmentController.php:18
 * @route '/appointments'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\AppointmentController::index
 * @see app/Http/Controllers/AppointmentController.php:18
 * @route '/appointments'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\AppointmentController::index
 * @see app/Http/Controllers/AppointmentController.php:18
 * @route '/appointments'
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
* @see \App\Http\Controllers\AppointmentController::create
 * @see app/Http/Controllers/AppointmentController.php:169
 * @route '/appointments/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/appointments/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AppointmentController::create
 * @see app/Http/Controllers/AppointmentController.php:169
 * @route '/appointments/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AppointmentController::create
 * @see app/Http/Controllers/AppointmentController.php:169
 * @route '/appointments/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\AppointmentController::create
 * @see app/Http/Controllers/AppointmentController.php:169
 * @route '/appointments/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\AppointmentController::create
 * @see app/Http/Controllers/AppointmentController.php:169
 * @route '/appointments/create'
 */
    const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: create.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\AppointmentController::create
 * @see app/Http/Controllers/AppointmentController.php:169
 * @route '/appointments/create'
 */
        createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: create.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\AppointmentController::create
 * @see app/Http/Controllers/AppointmentController.php:169
 * @route '/appointments/create'
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
* @see \App\Http\Controllers\AppointmentController::store
 * @see app/Http/Controllers/AppointmentController.php:196
 * @route '/appointments'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/appointments',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AppointmentController::store
 * @see app/Http/Controllers/AppointmentController.php:196
 * @route '/appointments'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AppointmentController::store
 * @see app/Http/Controllers/AppointmentController.php:196
 * @route '/appointments'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\AppointmentController::store
 * @see app/Http/Controllers/AppointmentController.php:196
 * @route '/appointments'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AppointmentController::store
 * @see app/Http/Controllers/AppointmentController.php:196
 * @route '/appointments'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\AppointmentController::show
 * @see app/Http/Controllers/AppointmentController.php:210
 * @route '/appointments/{appointment}'
 */
export const show = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/appointments/{appointment}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AppointmentController::show
 * @see app/Http/Controllers/AppointmentController.php:210
 * @route '/appointments/{appointment}'
 */
show.url = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { appointment: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { appointment: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    appointment: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        appointment: typeof args.appointment === 'object'
                ? args.appointment.id
                : args.appointment,
                }

    return show.definition.url
            .replace('{appointment}', parsedArgs.appointment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AppointmentController::show
 * @see app/Http/Controllers/AppointmentController.php:210
 * @route '/appointments/{appointment}'
 */
show.get = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\AppointmentController::show
 * @see app/Http/Controllers/AppointmentController.php:210
 * @route '/appointments/{appointment}'
 */
show.head = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\AppointmentController::show
 * @see app/Http/Controllers/AppointmentController.php:210
 * @route '/appointments/{appointment}'
 */
    const showForm = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\AppointmentController::show
 * @see app/Http/Controllers/AppointmentController.php:210
 * @route '/appointments/{appointment}'
 */
        showForm.get = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\AppointmentController::show
 * @see app/Http/Controllers/AppointmentController.php:210
 * @route '/appointments/{appointment}'
 */
        showForm.head = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\AppointmentController::edit
 * @see app/Http/Controllers/AppointmentController.php:224
 * @route '/appointments/{appointment}/edit'
 */
export const edit = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/appointments/{appointment}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AppointmentController::edit
 * @see app/Http/Controllers/AppointmentController.php:224
 * @route '/appointments/{appointment}/edit'
 */
edit.url = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { appointment: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { appointment: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    appointment: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        appointment: typeof args.appointment === 'object'
                ? args.appointment.id
                : args.appointment,
                }

    return edit.definition.url
            .replace('{appointment}', parsedArgs.appointment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AppointmentController::edit
 * @see app/Http/Controllers/AppointmentController.php:224
 * @route '/appointments/{appointment}/edit'
 */
edit.get = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\AppointmentController::edit
 * @see app/Http/Controllers/AppointmentController.php:224
 * @route '/appointments/{appointment}/edit'
 */
edit.head = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\AppointmentController::edit
 * @see app/Http/Controllers/AppointmentController.php:224
 * @route '/appointments/{appointment}/edit'
 */
    const editForm = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: edit.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\AppointmentController::edit
 * @see app/Http/Controllers/AppointmentController.php:224
 * @route '/appointments/{appointment}/edit'
 */
        editForm.get = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: edit.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\AppointmentController::edit
 * @see app/Http/Controllers/AppointmentController.php:224
 * @route '/appointments/{appointment}/edit'
 */
        editForm.head = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\AppointmentController::update
 * @see app/Http/Controllers/AppointmentController.php:259
 * @route '/appointments/{appointment}'
 */
export const update = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/appointments/{appointment}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\AppointmentController::update
 * @see app/Http/Controllers/AppointmentController.php:259
 * @route '/appointments/{appointment}'
 */
update.url = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { appointment: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { appointment: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    appointment: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        appointment: typeof args.appointment === 'object'
                ? args.appointment.id
                : args.appointment,
                }

    return update.definition.url
            .replace('{appointment}', parsedArgs.appointment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AppointmentController::update
 * @see app/Http/Controllers/AppointmentController.php:259
 * @route '/appointments/{appointment}'
 */
update.put = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\AppointmentController::update
 * @see app/Http/Controllers/AppointmentController.php:259
 * @route '/appointments/{appointment}'
 */
update.patch = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\AppointmentController::update
 * @see app/Http/Controllers/AppointmentController.php:259
 * @route '/appointments/{appointment}'
 */
    const updateForm = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AppointmentController::update
 * @see app/Http/Controllers/AppointmentController.php:259
 * @route '/appointments/{appointment}'
 */
        updateForm.put = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
            /**
* @see \App\Http\Controllers\AppointmentController::update
 * @see app/Http/Controllers/AppointmentController.php:259
 * @route '/appointments/{appointment}'
 */
        updateForm.patch = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\AppointmentController::destroy
 * @see app/Http/Controllers/AppointmentController.php:321
 * @route '/appointments/{appointment}'
 */
export const destroy = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/appointments/{appointment}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\AppointmentController::destroy
 * @see app/Http/Controllers/AppointmentController.php:321
 * @route '/appointments/{appointment}'
 */
destroy.url = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { appointment: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { appointment: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    appointment: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        appointment: typeof args.appointment === 'object'
                ? args.appointment.id
                : args.appointment,
                }

    return destroy.definition.url
            .replace('{appointment}', parsedArgs.appointment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AppointmentController::destroy
 * @see app/Http/Controllers/AppointmentController.php:321
 * @route '/appointments/{appointment}'
 */
destroy.delete = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\AppointmentController::destroy
 * @see app/Http/Controllers/AppointmentController.php:321
 * @route '/appointments/{appointment}'
 */
    const destroyForm = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AppointmentController::destroy
 * @see app/Http/Controllers/AppointmentController.php:321
 * @route '/appointments/{appointment}'
 */
        destroyForm.delete = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\AppointmentController::calendar
 * @see app/Http/Controllers/AppointmentController.php:118
 * @route '/appointments-calendar'
 */
export const calendar = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: calendar.url(options),
    method: 'get',
})

calendar.definition = {
    methods: ["get","head"],
    url: '/appointments-calendar',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AppointmentController::calendar
 * @see app/Http/Controllers/AppointmentController.php:118
 * @route '/appointments-calendar'
 */
calendar.url = (options?: RouteQueryOptions) => {
    return calendar.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AppointmentController::calendar
 * @see app/Http/Controllers/AppointmentController.php:118
 * @route '/appointments-calendar'
 */
calendar.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: calendar.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\AppointmentController::calendar
 * @see app/Http/Controllers/AppointmentController.php:118
 * @route '/appointments-calendar'
 */
calendar.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: calendar.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\AppointmentController::calendar
 * @see app/Http/Controllers/AppointmentController.php:118
 * @route '/appointments-calendar'
 */
    const calendarForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: calendar.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\AppointmentController::calendar
 * @see app/Http/Controllers/AppointmentController.php:118
 * @route '/appointments-calendar'
 */
        calendarForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: calendar.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\AppointmentController::calendar
 * @see app/Http/Controllers/AppointmentController.php:118
 * @route '/appointments-calendar'
 */
        calendarForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: calendar.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    calendar.form = calendarForm
/**
* @see \App\Http\Controllers\AppointmentController::updateStatus
 * @see app/Http/Controllers/AppointmentController.php:302
 * @route '/appointments/{appointment}/status'
 */
export const updateStatus = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateStatus.url(args, options),
    method: 'patch',
})

updateStatus.definition = {
    methods: ["patch"],
    url: '/appointments/{appointment}/status',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\AppointmentController::updateStatus
 * @see app/Http/Controllers/AppointmentController.php:302
 * @route '/appointments/{appointment}/status'
 */
updateStatus.url = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { appointment: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { appointment: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    appointment: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        appointment: typeof args.appointment === 'object'
                ? args.appointment.id
                : args.appointment,
                }

    return updateStatus.definition.url
            .replace('{appointment}', parsedArgs.appointment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AppointmentController::updateStatus
 * @see app/Http/Controllers/AppointmentController.php:302
 * @route '/appointments/{appointment}/status'
 */
updateStatus.patch = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateStatus.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\AppointmentController::updateStatus
 * @see app/Http/Controllers/AppointmentController.php:302
 * @route '/appointments/{appointment}/status'
 */
    const updateStatusForm = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: updateStatus.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PATCH',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AppointmentController::updateStatus
 * @see app/Http/Controllers/AppointmentController.php:302
 * @route '/appointments/{appointment}/status'
 */
        updateStatusForm.patch = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: updateStatus.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PATCH',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    updateStatus.form = updateStatusForm
/**
* @see \App\Http\Controllers\AppointmentController::generateReport
 * @see app/Http/Controllers/AppointmentController.php:334
 * @route '/appointments/{appointment}/report'
 */
export const generateReport = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: generateReport.url(args, options),
    method: 'get',
})

generateReport.definition = {
    methods: ["get","head"],
    url: '/appointments/{appointment}/report',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AppointmentController::generateReport
 * @see app/Http/Controllers/AppointmentController.php:334
 * @route '/appointments/{appointment}/report'
 */
generateReport.url = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { appointment: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { appointment: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    appointment: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        appointment: typeof args.appointment === 'object'
                ? args.appointment.id
                : args.appointment,
                }

    return generateReport.definition.url
            .replace('{appointment}', parsedArgs.appointment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AppointmentController::generateReport
 * @see app/Http/Controllers/AppointmentController.php:334
 * @route '/appointments/{appointment}/report'
 */
generateReport.get = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: generateReport.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\AppointmentController::generateReport
 * @see app/Http/Controllers/AppointmentController.php:334
 * @route '/appointments/{appointment}/report'
 */
generateReport.head = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: generateReport.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\AppointmentController::generateReport
 * @see app/Http/Controllers/AppointmentController.php:334
 * @route '/appointments/{appointment}/report'
 */
    const generateReportForm = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: generateReport.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\AppointmentController::generateReport
 * @see app/Http/Controllers/AppointmentController.php:334
 * @route '/appointments/{appointment}/report'
 */
        generateReportForm.get = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: generateReport.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\AppointmentController::generateReport
 * @see app/Http/Controllers/AppointmentController.php:334
 * @route '/appointments/{appointment}/report'
 */
        generateReportForm.head = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\AppointmentController::generateLetter
 * @see app/Http/Controllers/AppointmentController.php:465
 * @route '/appointments/{appointment}/letter'
 */
export const generateLetter = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: generateLetter.url(args, options),
    method: 'get',
})

generateLetter.definition = {
    methods: ["get","head"],
    url: '/appointments/{appointment}/letter',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AppointmentController::generateLetter
 * @see app/Http/Controllers/AppointmentController.php:465
 * @route '/appointments/{appointment}/letter'
 */
generateLetter.url = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { appointment: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { appointment: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    appointment: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        appointment: typeof args.appointment === 'object'
                ? args.appointment.id
                : args.appointment,
                }

    return generateLetter.definition.url
            .replace('{appointment}', parsedArgs.appointment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AppointmentController::generateLetter
 * @see app/Http/Controllers/AppointmentController.php:465
 * @route '/appointments/{appointment}/letter'
 */
generateLetter.get = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: generateLetter.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\AppointmentController::generateLetter
 * @see app/Http/Controllers/AppointmentController.php:465
 * @route '/appointments/{appointment}/letter'
 */
generateLetter.head = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: generateLetter.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\AppointmentController::generateLetter
 * @see app/Http/Controllers/AppointmentController.php:465
 * @route '/appointments/{appointment}/letter'
 */
    const generateLetterForm = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: generateLetter.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\AppointmentController::generateLetter
 * @see app/Http/Controllers/AppointmentController.php:465
 * @route '/appointments/{appointment}/letter'
 */
        generateLetterForm.get = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: generateLetter.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\AppointmentController::generateLetter
 * @see app/Http/Controllers/AppointmentController.php:465
 * @route '/appointments/{appointment}/letter'
 */
        generateLetterForm.head = (args: { appointment: number | { id: number } } | [appointment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: generateLetter.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    generateLetter.form = generateLetterForm
/**
* @see \App\Http\Controllers\AppointmentController::exportMethod
 * @see app/Http/Controllers/AppointmentController.php:494
 * @route '/appointments-export'
 */
export const exportMethod = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})

exportMethod.definition = {
    methods: ["get","head"],
    url: '/appointments-export',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AppointmentController::exportMethod
 * @see app/Http/Controllers/AppointmentController.php:494
 * @route '/appointments-export'
 */
exportMethod.url = (options?: RouteQueryOptions) => {
    return exportMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AppointmentController::exportMethod
 * @see app/Http/Controllers/AppointmentController.php:494
 * @route '/appointments-export'
 */
exportMethod.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\AppointmentController::exportMethod
 * @see app/Http/Controllers/AppointmentController.php:494
 * @route '/appointments-export'
 */
exportMethod.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportMethod.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\AppointmentController::exportMethod
 * @see app/Http/Controllers/AppointmentController.php:494
 * @route '/appointments-export'
 */
    const exportMethodForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: exportMethod.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\AppointmentController::exportMethod
 * @see app/Http/Controllers/AppointmentController.php:494
 * @route '/appointments-export'
 */
        exportMethodForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: exportMethod.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\AppointmentController::exportMethod
 * @see app/Http/Controllers/AppointmentController.php:494
 * @route '/appointments-export'
 */
        exportMethodForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: exportMethod.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    exportMethod.form = exportMethodForm
const AppointmentController = { index, create, store, show, edit, update, destroy, calendar, updateStatus, generateReport, generateLetter, exportMethod, export: exportMethod }

export default AppointmentController