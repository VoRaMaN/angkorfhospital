import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
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
const DoctorController = { myAppointments, myPatients }

export default DoctorController