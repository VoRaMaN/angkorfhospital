import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\MedicineReportController::index
 * @see app/Http/Controllers/MedicineReportController.php:17
 * @route '/medicine-report'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/medicine-report',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MedicineReportController::index
 * @see app/Http/Controllers/MedicineReportController.php:17
 * @route '/medicine-report'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicineReportController::index
 * @see app/Http/Controllers/MedicineReportController.php:17
 * @route '/medicine-report'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MedicineReportController::index
 * @see app/Http/Controllers/MedicineReportController.php:17
 * @route '/medicine-report'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\MedicineReportController::index
 * @see app/Http/Controllers/MedicineReportController.php:17
 * @route '/medicine-report'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\MedicineReportController::index
 * @see app/Http/Controllers/MedicineReportController.php:17
 * @route '/medicine-report'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\MedicineReportController::index
 * @see app/Http/Controllers/MedicineReportController.php:17
 * @route '/medicine-report'
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
* @see \App\Http\Controllers\MedicineReportController::exportMethod
 * @see app/Http/Controllers/MedicineReportController.php:147
 * @route '/medicine-report/export'
 */
export const exportMethod = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})

exportMethod.definition = {
    methods: ["get","head"],
    url: '/medicine-report/export',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MedicineReportController::exportMethod
 * @see app/Http/Controllers/MedicineReportController.php:147
 * @route '/medicine-report/export'
 */
exportMethod.url = (options?: RouteQueryOptions) => {
    return exportMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicineReportController::exportMethod
 * @see app/Http/Controllers/MedicineReportController.php:147
 * @route '/medicine-report/export'
 */
exportMethod.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MedicineReportController::exportMethod
 * @see app/Http/Controllers/MedicineReportController.php:147
 * @route '/medicine-report/export'
 */
exportMethod.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportMethod.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\MedicineReportController::exportMethod
 * @see app/Http/Controllers/MedicineReportController.php:147
 * @route '/medicine-report/export'
 */
    const exportMethodForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: exportMethod.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\MedicineReportController::exportMethod
 * @see app/Http/Controllers/MedicineReportController.php:147
 * @route '/medicine-report/export'
 */
        exportMethodForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: exportMethod.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\MedicineReportController::exportMethod
 * @see app/Http/Controllers/MedicineReportController.php:147
 * @route '/medicine-report/export'
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
/**
* @see \App\Http\Controllers\MedicineReportController::finish
 * @see app/Http/Controllers/MedicineReportController.php:105
 * @route '/medicine-report/finish/{medical_order}'
 */
export const finish = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: finish.url(args, options),
    method: 'patch',
})

finish.definition = {
    methods: ["patch"],
    url: '/medicine-report/finish/{medical_order}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\MedicineReportController::finish
 * @see app/Http/Controllers/MedicineReportController.php:105
 * @route '/medicine-report/finish/{medical_order}'
 */
finish.url = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return finish.definition.url
            .replace('{medical_order}', parsedArgs.medical_order.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MedicineReportController::finish
 * @see app/Http/Controllers/MedicineReportController.php:105
 * @route '/medicine-report/finish/{medical_order}'
 */
finish.patch = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: finish.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\MedicineReportController::finish
 * @see app/Http/Controllers/MedicineReportController.php:105
 * @route '/medicine-report/finish/{medical_order}'
 */
    const finishForm = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: finish.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PATCH',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\MedicineReportController::finish
 * @see app/Http/Controllers/MedicineReportController.php:105
 * @route '/medicine-report/finish/{medical_order}'
 */
        finishForm.patch = (args: { medical_order: string | number } | [medical_order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: finish.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PATCH',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    finish.form = finishForm
const MedicineReportController = { index, exportMethod, finish, export: exportMethod }

export default MedicineReportController