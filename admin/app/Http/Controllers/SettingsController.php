<?php

namespace App\Http\Controllers;


use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function social()
    {
        return view("settings.app.social");
    }

    public function globals()
    {
        return view("settings.app.global");
    }

    public function notifications()
    {
        return view("settings.app.notification");
    }





    public function mobileGlobals()
    {
        return view('settings.mobile.globals');
    }



    public function adminCommission()
    {
        return view("settings.app.adminCommission");
    }

    public function radiosConfiguration()
    {
        return view("settings.app.radiosConfiguration");
    }



    public function bookTable()
    {
        return view('settings.app.bookTable');
    }


    public function paystack()
    {
        return view('settings.app.paystack');
    }





    public function deliveryCharge()
    {
        return view("settings.app.deliveryCharge");
    }

    public function languages()
    {
        return view('settings.languages.index');
    }

    public function languagesedit($id)
    {
        return view('settings.languages.edit')->with('id', $id);
    }

    public function languagescreate()
    {
        return view('settings.languages.create');
    }

    public function specialOffer()
    {
        return view('settings.app.specialDiscountOffer');
    }

    public function menuItems()
    {
        return view('settings.menu_items.index');
        
    }

    public function menuItemsCreate()
    {
        return view('settings.menu_items.create');

    }

    public function menuItemsEdit($id)
    {
        return view('settings.menu_items.edit')->with('id', $id);

    }

    public function story()
    {
        return view('settings.app.story');

    }

    public function footerTemplate()
    {
        return view('footerTemplate.index');
    }

    public function homepageTemplate()
    {
        return view('homepage_Template.index');
    }

    public function emailTemplatesIndex()
    {
        return view('email_templates.index');        
    }

    public function emailTemplatesSave($id = '')
    {

        return view('email_templates.save')->with('id', $id);
    }
    public function documentVerification()
    {
        return view('settings.app.documentVerificationSetting');
    }
    public function scheduleOrderNotification()
    {
        return view('settings.app.schedule_notification');
    }


    public function sendWebhookTest(Request $request)
    {
        if ($request->webhookUrl && $request->order_data) {
            try {
                $webhookUrl = trim((string) $request->webhookUrl);
                if (!$this->isSafeWebhookUrl($webhookUrl)) {
                    return response()->json(['success' => false, 'error' => 'Invalid webhook URL'], 400);
                }

                $orderData = $this->normalizeOrderData($request->order_data);
                if (!$orderData) {
                    return response()->json(['success' => false, 'error' => 'Invalid order data'], 400);
                }

                $payload = json_encode($orderData);
                if ($payload === false) {
                    return response()->json(['success' => false, 'error' => 'Failed to encode order data'], 400);
                }

                $headers = [
                    'Content-Type' => 'application/json',
                ];
                $secret = env('WEBHOOK_SECRET');
                if ($secret) {
                    $headers['X-Webhook-Signature'] = hash_hmac('sha256', $payload, $secret);
                }

                $client = new Client([
                    'timeout' => 5,
                    'connect_timeout' => 3,
                ]);
                $client->post($webhookUrl, [
                    'headers' => $headers,
                    'body' => $payload,
                ]);
                return response()->json(['success' => true]);
            } catch (\Exception $e) {
                Log::warning('Webhook test failed.', [
                    'webhook_url' => $request->webhookUrl,
                    'error' => $e->getMessage(),
                ]);
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
        }
        return response()->json(['success' => false, 'error' => 'Missing data'], 400);
    }

    private function normalizeOrderData($orderData): ?array
    {
        if (is_array($orderData)) {
            return $orderData;
        }
        if (is_string($orderData)) {
            $decoded = json_decode($orderData, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }
        return null;
    }

    private function isSafeWebhookUrl(string $webhookUrl): bool
    {
        $parts = parse_url($webhookUrl);
        if (!$parts || empty($parts['scheme']) || empty($parts['host'])) {
            return false;
        }
        $scheme = strtolower($parts['scheme']);
        if (!in_array($scheme, ['http', 'https'], true)) {
            return false;
        }

        $host = $parts['host'];
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return $this->isPublicIp($host);
        }

        $resolvedIps = gethostbynamel($host);
        if ($resolvedIps === false) {
            return false;
        }
        foreach ($resolvedIps as $ip) {
            if (!$this->isPublicIp($ip)) {
                return false;
            }
        }
        return true;
    }

    private function isPublicIp(string $ip): bool
    {
        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) !== false;
    }
}