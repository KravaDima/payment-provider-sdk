<?php

declare(strict_types=1);

namespace Checkvin\PaymentProviderSdk\ServiceProviders;

use Checkvin\PaymentProviderSdk\Callbacks\LiqPayCallbackHandler;
use Checkvin\PaymentProviderSdk\Callbacks\WayForPayCallbackHandler;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use Checkvin\PaymentProviderSdk\Clients\LiqPayClient;
use Checkvin\PaymentProviderSdk\Clients\WayForPayClient;
use Checkvin\PaymentProviderSdk\DTO\PaymentConfig;
use Checkvin\PaymentProviderSdk\PaymentManager;
use Checkvin\PaymentProviderSdk\Providers\LiqPay;
use Checkvin\PaymentProviderSdk\Providers\WayForPay;

class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/payment.php', 'payment'
        );

        // LiqPay Configuration
        $this->app->singleton('payment.liqpay.config', function ($app) {
            return new PaymentConfig(
                publicKey: config('payment.liqpay.public_key'),
                privateKey: config('payment.liqpay.private_key'),
                merchantDomain: config('payment.liqpay.merchant_domain'),
                callbackUrl: config('app.url') . config('payment.liqpay.callback_url'),
                redirectUrl: config('app.url') . config('payment.liqpay.redirect_url'),
                isDebug: config('payment.liqpay.debug', false)
            );
        });

        $this->app->singleton(LiqPayClient::class, function ($app) {
            $options = [
                'base_uri' => config('payment.liqpay.merchant_domain'),
                'allow_redirects' => [
                    'track_redirects' => true
                ],
            ];

            return new LiqPayClient(
                $app->make('payment.liqpay.config'),
                new Client($options)
            );
        });

        // WayForPay Configuration
        $this->app->singleton('payment.wayforpay.config', function ($app) {
            return new PaymentConfig(
                publicKey: config('payment.wayforpay.merchant_account'),
                privateKey: config('payment.wayforpay.merchant_secret_key'),
                merchantDomain: config('payment.wayforpay.merchant_domain'),
                callbackUrl: config('app.url') . config('payment.wayforpay.callback_url'),
                redirectUrl: config('app.url') . config('payment.wayforpay.redirect_url'),
                isDebug: config('payment.wayforpay.debug', false)
            );
        });

        $this->app->singleton(WayForPayClient::class, function ($app) {
            $options = [
                'base_uri' => config('payment.wayforpay.merchant_domain'),
            ];

            return new WayForPayClient(
                $app->make('payment.wayforpay.config'),
                new Client($options)
            );
        });

        // Register Payment Providers
        $this->app->singleton(LiqPay::class, function ($app) {
            return new LiqPay($app->make(LiqPayClient::class));
        });

        $this->app->singleton(WayForPay::class, function ($app) {
            return new WayForPay($app->make(WayForPayClient::class));
        });

        // Register Payment Manager
        $this->app->singleton(PaymentManager::class, function ($app) {
            return new PaymentManager([
                $app->make(LiqPay::class),
                $app->make(WayForPay::class)
            ]);
        });

        $this->app->singleton(LiqPayCallbackHandler::class, function ($app) {
            return new LiqPayCallbackHandler(
                config('payment.liqpay.private_key')
            );
        });

        $this->app->singleton(WayForPayCallbackHandler::class, function ($app) {
            return new WayForPayCallbackHandler(
                config('payment.wayforpay.merchant_secret_key')
            );
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/payment.php' => config_path('payment.php'),
        ], 'payment-config');
    }
} 