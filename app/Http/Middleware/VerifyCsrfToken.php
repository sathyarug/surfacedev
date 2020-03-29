<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'test-auth',
        'sub-category-save',
		'sub-category-save',
		'payment-term.save',
    'Mainlocation.postdata',
    'MainSubLocation.postdata',
    'cost-center.save',
    'payment-method.save'
    ];
}
