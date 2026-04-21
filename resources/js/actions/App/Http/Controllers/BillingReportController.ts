import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\BillingReportController::index
 * @see app/Http/Controllers/BillingReportController.php:12
 * @route '/billing-report'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/billing-report',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BillingReportController::index
 * @see app/Http/Controllers/BillingReportController.php:12
 * @route '/billing-report'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BillingReportController::index
 * @see app/Http/Controllers/BillingReportController.php:12
 * @route '/billing-report'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\BillingReportController::index
 * @see app/Http/Controllers/BillingReportController.php:12
 * @route '/billing-report'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\BillingReportController::index
 * @see app/Http/Controllers/BillingReportController.php:12
 * @route '/billing-report'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\BillingReportController::index
 * @see app/Http/Controllers/BillingReportController.php:12
 * @route '/billing-report'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\BillingReportController::index
 * @see app/Http/Controllers/BillingReportController.php:12
 * @route '/billing-report'
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
* @see \App\Http\Controllers\BillingReportController::exportMethod
 * @see app/Http/Controllers/BillingReportController.php:75
 * @route '/billing-report/export'
 */
export const exportMethod = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})

exportMethod.definition = {
    methods: ["get","head"],
    url: '/billing-report/export',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BillingReportController::exportMethod
 * @see app/Http/Controllers/BillingReportController.php:75
 * @route '/billing-report/export'
 */
exportMethod.url = (options?: RouteQueryOptions) => {
    return exportMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BillingReportController::exportMethod
 * @see app/Http/Controllers/BillingReportController.php:75
 * @route '/billing-report/export'
 */
exportMethod.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\BillingReportController::exportMethod
 * @see app/Http/Controllers/BillingReportController.php:75
 * @route '/billing-report/export'
 */
exportMethod.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportMethod.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\BillingReportController::exportMethod
 * @see app/Http/Controllers/BillingReportController.php:75
 * @route '/billing-report/export'
 */
    const exportMethodForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: exportMethod.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\BillingReportController::exportMethod
 * @see app/Http/Controllers/BillingReportController.php:75
 * @route '/billing-report/export'
 */
        exportMethodForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: exportMethod.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\BillingReportController::exportMethod
 * @see app/Http/Controllers/BillingReportController.php:75
 * @route '/billing-report/export'
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
const BillingReportController = { index, exportMethod, export: exportMethod }

export default BillingReportController