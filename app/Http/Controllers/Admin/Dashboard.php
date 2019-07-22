<?php
namespace App\Http\Controllers\Admin; use App\Library\CurlRequest; use App\Library\Response; use App\Order; use Illuminate\Http\Request; use App\Http\Controllers\Controller; class Dashboard extends Controller { function index(Request $spf066f3) { $spf832df = array('today' => array('count' => 0, 'paid' => 0, 'profit' => 0), 'yesterday' => array('count' => 0, 'paid' => 0, 'profit' => 0)); $sp698ac8 = Order::whereUserId(\Auth::Id())->whereDate('paid_at', \Carbon\Carbon::now()->toDateString())->where(function ($sp5044a7) { $sp5044a7->where('status', Order::STATUS_PAID)->orWhere('status', Order::STATUS_SUCCESS); })->selectRaw('COUNT(*) as `count`,SUM(`paid`) as `paid`,SUM(`paid`-`sms_price`-`cost`-`fee`) as `profit`')->get()->toArray(); $sp7a6714 = Order::whereUserId(\Auth::Id())->whereDate('paid_at', \Carbon\Carbon::yesterday()->toDateString())->where(function ($sp5044a7) { $sp5044a7->where('status', Order::STATUS_PAID)->orWhere('status', Order::STATUS_SUCCESS); })->selectRaw('COUNT(*) as `count`,SUM(`paid`) as `paid`,SUM(`paid`-`sms_price`-`cost`-`fee`) as `profit`')->get()->toArray(); if (isset($sp698ac8[0]) && isset($sp698ac8[0]['count'])) { $spf832df['today'] = array('count' => (int) $sp698ac8[0]['count'], 'paid' => (int) $sp698ac8[0]['paid'], 'profit' => (int) $sp698ac8[0]['profit']); } if (isset($sp7a6714[0]) && isset($sp7a6714[0]['count'])) { $spf832df['yesterday'] = array('count' => (int) $sp7a6714[0]['count'], 'paid' => (int) $sp7a6714[0]['paid'], 'profit' => (int) $sp7a6714[0]['profit']); } $spf832df['need_ship_count'] = Order::whereUserId(\Auth::Id())->where('status', Order::STATUS_PAID)->count(); $spf832df['login'] = \App\Log::where('action', \App\Log::ACTION_LOGIN)->latest()->firstOrFail(); return Response::success($spf832df); } function clearCache() { if (function_exists('opcache_reset')) { opcache_reset(); } try { \Artisan::call('cache:clear'); \Artisan::call('route:cache'); \Artisan::call('config:cache'); } catch (\Throwable $sp3f4aab) { return Response::fail($sp3f4aab->getMessage()); } return Response::success(); } function version() { return Response::success(array('version' => config('app.version'))); } function checkUpdate() { return Response::success(@json_decode(CurlRequest::get('https://raw.githubusercontent.com/Tai7sy/card-system/master/.version'), true)); } }