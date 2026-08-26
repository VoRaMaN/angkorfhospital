import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\DoctorController::myAppointments
 * @see app/Http/Controllers/DoctorController.php:14
 * @route '/doctors/my-appointments'
 */
export const myAppointments = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: myAppointments.url(options),
    method: 'get',
})

myAppointments.definition = {
    methods: ["get","head"],
    url: '/doctors/my-appointments',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DoctorController::myAppointments
 * @see app/Http/Controllers/DoctorController.php:14
 * @route '/doctors/my-appointments'
 */
myAppointments.url = (options?: RouteQueryOptions) => {
    return myAppointments.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DoctorController::myAppointments
 * @see app/Http/Controllers/DoctorController.php:14
 * @route '/doctors/my-appointments'
 */
myAppointments.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: myAppointments.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\DoctorController::myAppointments
 * @see app/Http/Controllers/DoctorController.php:14
 * @route '/doctors/my-appointments'
 */
myAppointments.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: myAppointments.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\DoctorController::myAppointments
 * @see app/Http/Controllers/DoctorController.php:14
 * @route '/doctors/my-appointments'
 */
    const myAppointmentsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: myAppointments.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\DoctorController::myAppointments
 * @see app/Http/Controllers/DoctorController.php:14
 * @route '/doctors/my-appointments'
 */
        myAppointmentsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: myAppointments.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\DoctorController::myAppointments
 * @see app/Http/Controllers/DoctorController.php:14
 * @route '/doctors/my-appointments'
 */
        myAppointmentsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: myAppointments.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    myAppointments.form = myAppointmentsForm
/**
* @see \App\Http\Controllers\DoctorController::myPatients
 * @see app/Http/Controllers/DoctorController.php:51
 * @route '/doctors/my-patients'
 */
export const myPatients = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: myPatients.url(options),
    method: 'get',
})

myPatients.definition = {
    methods: ["get","head"],
    url: '/doctors/my-patients',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DoctorController::myPatients
 * @see app/Http/Controllers/DoctorController.php:51
 * @route '/doctors/my-patients'
 */
myPatients.url = (options?: RouteQueryOptions) => {
    return myPatients.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DoctorController::myPatients
 * @see app/Http/Controllers/DoctorController.php:51
 * @route '/doctors/my-patients'
 */
myPatients.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: myPatients.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\DoctorController::myPatients
 * @see app/Http/Controllers/DoctorController.php:51
 * @route '/doctors/my-patients'
 */
myPatients.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: myPatients.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\DoctorController::myPatients
 * @see app/Http/Controllers/DoctorController.php:51
 * @route '/doctors/my-patients'
 */
    const myPatientsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: myPatients.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\DoctorController::myPatients
 * @see app/Http/Controllers/DoctorController.php:51
 * @route '/doctors/my-patients'
 */
        myPatientsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: myPatients.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\DoctorController::myPatients
 * @see app/Http/Controllers/DoctorController.php:51
 * @route '/doctors/my-patients'
 */
        myPatientsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: myPatients.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    myPatients.form = myPatientsForm
/**
* @see \App\Http\Controllers\VisitController::myVisits
 * @see app/Http/Controllers/VisitController.php:420
 * @route '/my-visits'
 */
export const myVisits = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: myVisits.url(options),
    method: 'get',
})

myVisits.definition = {
    methods: ["get","head"],
    url: '/my-visits',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\VisitController::myVisits
 * @see app/Http/Controllers/VisitController.php:420
 * @route '/my-visits'
 */
myVisits.url = (options?: RouteQueryOptions) => {
    return myVisits.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\VisitController::myVisits
 * @see app/Http/Controllers/VisitController.php:420
 * @route '/my-visits'
 */
myVisits.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: myVisits.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\VisitController::myVisits
 * @see app/Http/Controllers/VisitController.php:420
 * @route '/my-visits'
 */
myVisits.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: myVisits.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\VisitController::myVisits
 * @see app/Http/Controllers/VisitController.php:420
 * @route '/my-visits'
 */
    const myVisitsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: myVisits.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\VisitController::myVisits
 * @see app/Http/Controllers/VisitController.php:420
 * @route '/my-visits'
 */
        myVisitsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: myVisits.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\VisitController::myVisits
 * @see app/Http/Controllers/VisitController.php:420
 * @route '/my-visits'
 */
        myVisitsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: myVisits.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    myVisits.form = myVisitsForm
/**
* @see \App\Http\Controllers\VisitController::myToBeProcessVisits
 * @see app/Http/Controllers/VisitController.php:470
 * @route '/my-to-be-process-visits'
 */
export const myToBeProcessVisits = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: myToBeProcessVisits.url(options),
    method: 'get',
})

myToBeProcessVisits.definition = {
    methods: ["get","head"],
    url: '/my-to-be-process-visits',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\VisitController::myToBeProcessVisits
 * @see app/Http/Controllers/VisitController.php:470
 * @route '/my-to-be-process-visits'
 */
myToBeProcessVisits.url = (options?: RouteQueryOptions) => {
    return myToBeProcessVisits.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\VisitController::myToBeProcessVisits
 * @see app/Http/Controllers/VisitController.php:470
 * @route '/my-to-be-process-visits'
 */
myToBeProcessVisits.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: myToBeProcessVisits.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\VisitController::myToBeProcessVisits
 * @see app/Http/Controllers/VisitController.php:470
 * @route '/my-to-be-process-visits'
 */
myToBeProcessVisits.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: myToBeProcessVisits.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\VisitController::myToBeProcessVisits
 * @see app/Http/Controllers/VisitController.php:470
 * @route '/my-to-be-process-visits'
 */
    const myToBeProcessVisitsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: myToBeProcessVisits.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\VisitController::myToBeProcessVisits
 * @see app/Http/Controllers/VisitController.php:470
 * @route '/my-to-be-process-visits'
 */
        myToBeProcessVisitsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: myToBeProcessVisits.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\VisitController::myToBeProcessVisits
 * @see app/Http/Controllers/VisitController.php:470
 * @route '/my-to-be-process-visits'
 */
        myToBeProcessVisitsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: myToBeProcessVisits.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    myToBeProcessVisits.form = myToBeProcessVisitsForm
const doctors = {
    myAppointments: Object.assign(myAppointments, myAppointments),
myPatients: Object.assign(myPatients, myPatients),
myVisits: Object.assign(myVisits, myVisits),
myToBeProcessVisits: Object.assign(myToBeProcessVisits, myToBeProcessVisits),
}

export default doctors