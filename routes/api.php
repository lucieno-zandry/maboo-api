<?php

use App\Http\Controllers\Article\ImageController;
use App\Http\Controllers\Article\ParagraphController;
use App\Http\Controllers\Article\SectionController;
use App\Http\Controllers\Article\SubsectionController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductColorController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\ProfessionalDataController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WstokenController;
use Illuminate\Support\Facades\Route;

Route::prefix('sales')
    ->controller(SalesController::class)
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('total', 'total');
    });

Route::prefix('chat')
    ->controller(ChatController::class)
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('chats', 'chats');
        Route::get('users', 'users');
    });

Route::prefix('wstoken')
    ->controller(WstokenController::class)
    ->middleware('auth:sanctum')
    ->group(function () {
        route::get('get', 'token');
    });

Route::prefix('notification')
    ->controller(NotificationController::class)
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('all', 'all');
        Route::get('unread', 'unreadList');
        Route::put('read/{notification_id}', 'read');
    });

Route::prefix('transaction')
    ->controller(TransactionController::class)
    ->group(function () {
        Route::prefix('order')
            ->group(function () {
                Route::post('make', 'purchaseOrder');
            });

        Route::prefix('status')
            ->group(function () {
                Route::post('orangemoney/{transactionnable_id}', 'orangeMoneyCallback');
                Route::post('vanillapay/{transactionnable_id}', 'vanillaPayCallback');
            });
    });

Route::prefix('search')
    ->controller(SearchController::class)
    ->group(function () {
        Route::get('/{query}', 'search');
    });

Route::prefix('order')
    ->controller(OrderController::class)
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('get/{id}', 'get');
        Route::get('grouped/{id}', 'grouped');
        Route::get('processing', 'processing');
        Route::get('delivered', 'delivered');
        Route::prefix('status')
            ->group(function () {
                Route::post('update', 'updateStatus');
            });
    });

Route::prefix('user')
    ->controller(UserController::class)
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::post('/update', 'update');
        Route::post('/change-password', 'changePassword');
    });

Route::prefix('category')
    ->controller(CategoryController::class)
    ->group(function () {
        Route::middleware('auth:sanctum')
            ->group(function () {
                Route::post('/create', 'create');
                Route::post('/update', 'update');
                Route::post('/delete', 'delete');
            });

        Route::get('/{id}/products', 'products');
        Route::get('hierarchy', 'getHierarchy');
        Route::get('all', 'all');
    });

Route::prefix('cart')
    ->controller(CartController::class)
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('get', 'get');
        Route::post('add', 'add');
        Route::put('update/{id}', 'update');
        Route::post('delete', 'delete');
    });

Route::prefix('product')
    ->group(function () {
        Route::controller(ProductsController::class)
            ->group(function () {
                Route::middleware('auth:sanctum')
                    ->group(function () {
                        Route::post('/create', 'create');
                        Route::post('/update', 'update');
                        Route::post('/cancel-update', 'cancelUpdate');
                        Route::post('/delete', 'delete');

                        Route::prefix('image')
                            ->group(function () {
                                Route::delete('/delete/{id}', 'deleteImage');
                            });
                    });

                Route::get('/get/{slug}', 'get');
                Route::get('/featured', 'featured');
            });

        Route::prefix('variant')
            ->controller(ProductVariantController::class)
            ->middleware('auth:sanctum')
            ->group(function () {
                Route::post('create', 'create');
                Route::post('delete', 'delete');
                Route::post('update/{id}', 'update');
            });

        Route::prefix('color')
            ->controller(ProductColorController::class)
            ->middleware('auth:sanctum')
            ->group(function () {
                Route::post('create', 'create');
                Route::post('delete', 'delete');
                Route::post('update/{id}', 'update');
            });
    });


Route::prefix('auth')
    ->controller(AuthController::class)
    ->group(function () {
        Route::middleware('auth:sanctum')
            ->group(function () {
                Route::get('/email/make_confirmation', 'makeEmailConfirmation');
                Route::post('/email/match_code', 'matchConfirmationCode');
            });

        Route::post('/forgotten-password', 'forgottenPassword');
        Route::post('/verify-email-conformity', 'verifyEmailConformity');
        Route::post('/reset-password', 'resetPassword');
    });

Route::prefix('article')->group(function () {
    Route::controller(ArticleController::class)->group(function () {
        Route::get('all', 'index');

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('create', 'store');
            Route::put('update/{article}', 'update');
            Route::delete('delete', 'destroy');
        });
    });

    # FIX: Ajouter middleware auth:sanctum pour toutes les routes CUD
    Route::prefix('section')->controller(SectionController::class)->group(function () {
        Route::get('all', 'index');
        
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('create', 'store');
            Route::put('update/{section}', 'update');
            Route::delete('delete', 'destroy');
        });
    });

    Route::prefix('subsection')->controller(SubsectionController::class)->group(function () {
        Route::get('all', 'index');
        
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('create', 'store');
            Route::put('update/{subsection}', 'update');
            Route::delete('delete', 'destroy');
        });
    });

    Route::prefix('paragraph')->controller(ParagraphController::class)->group(function () {
        Route::get('all', 'index');
        
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('create', 'store');
            Route::put('update/{paragraph}', 'update');
            Route::delete('delete', 'destroy');
        });
    });

    Route::prefix('image')->controller(ImageController::class)->group(function () {
        Route::get('all', 'index');
        
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('create', 'store');
            Route::delete('delete', 'destroy');
        });
    });
});

Route::prefix('customer')
    ->group(function () {
        Route::prefix('auth')
            ->controller(\App\Http\Controllers\Customer\AuthController::class)
            ->group(function () {
                Route::post('/login', 'login');
                Route::post('/signup', 'signup');
                Route::post('/reset-password', 'resetPassword');

                Route::middleware('auth:sanctum')
                    ->group(function () {
                        Route::get('/user', 'getAuth');
                    });
            });

        Route::prefix('order')
            ->middleware('auth:sanctum')
            ->controller(\App\Http\Controllers\Customer\OrderController::class)
            ->group(function () {
                Route::delete('delete/{order}', 'delete');
                Route::post('make', 'make');
                Route::get('all', 'all');
                Route::get('cancelled', 'cancelled');
                Route::get('processing', 'processing');
                Route::get('delivered', 'delivered');
            });
    });

Route::prefix('admin')
    ->group(function () {
        Route::prefix('auth')
            ->controller(\App\Http\Controllers\Admin\AuthController::class)->group(function () {
                Route::post('/login', 'login');
                Route::post('/signup', 'signup');
                Route::post('/reset-password', 'resetPassword');

                Route::middleware('auth:sanctum')
                    ->group(function () {
                        Route::get('/user', 'getAuth');
                    });
            });

        Route::prefix('user')
            ->controller(\App\Http\Controllers\Admin\UserController::class)
            ->middleware('auth:sanctum')
            ->group(function () {
                Route::post('/validate', 'validate');
                Route::get('/count', 'count');
                Route::get('/users', 'users');
            });

        Route::prefix('seller')
            ->controller(\App\Http\Controllers\Admin\SellerController::class)
            ->middleware('auth:sanctum')
            ->group(function () {
                Route::get('/requests', 'requests');
            });

        Route::prefix('admin')
            ->controller(\App\Http\Controllers\Admin\AdminController::class)
            ->middleware('auth:sanctum')
            ->group(function () {
                Route::get('/requests', 'requests');
            });

        Route::prefix('product')
            ->controller(\App\Http\Controllers\Admin\ProductsController::class)
            ->middleware('auth:sanctum')
            ->group(function () {
                Route::get('get', 'get');
            });

        Route::prefix('order')
            ->controller(\App\Http\Controllers\Admin\OrderController::class)
            ->middleware('auth:sanctum')
            ->group(function () {
                Route::get('closed', 'closed');
                Route::get('unchecked', 'unchecked');
                Route::put('update-transaction/{id}', 'updateTransaction');
            });
    });

Route::prefix('seller')
    ->group(function () {
        Route::prefix('auth')
            ->controller(\App\Http\Controllers\Seller\AuthController::class)
            ->group(function () {
                Route::post('/login', 'login');
                Route::post('/signup', 'signup');
                Route::post('/reset-password', 'resetPassword');

                Route::middleware('auth:sanctum')
                    ->group(function () {
                        Route::get('/user', 'getAuth');
                    });
            });

        Route::prefix('product')
            ->controller(\App\Http\Controllers\Seller\ProductsController::class)
            ->middleware('auth:sanctum')
            ->group(function () {
                Route::get('get', 'get');
            });
    });

Route::prefix('professional')
    ->group(function () {
        Route::prefix('auth')
            ->controller(\App\Http\Controllers\Professional\AuthController::class)
            ->group(function () {
                Route::post('/login', 'login');
                Route::post('/signup', 'signup');
            });

        Route::get('get/{id}', [ProfessionalDataController::class, 'show']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('data/update', [ProfessionalDataController::class, 'update']);
        });
    });