<?php

namespace App\Http\Controllers;

use App\Models\SensorSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    /**
     * Show the settings page.
     */
    public function index()
    {
        $settings   = SensorSetting::current();
        $talkbackOk = $this->talkbackConfigured();
        return view('settings', compact('settings', 'talkbackOk'));
    }

    /**
     * Save updated settings and push commands to TalkBack.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'temperature_enabled' => 'nullable|boolean',
            'humidity_enabled'    => 'nullable|boolean',
            'refresh_delay'       => 'required|integer|min:5|max:3600',
        ]);

        $settings = SensorSetting::current();

        $newTempEnabled = $request->boolean('temperature_enabled');
        $newHumEnabled  = $request->boolean('humidity_enabled');
        $newDelay       = (int) $validated['refresh_delay'];

        // Queue TalkBack commands for changes
        $commands = [];

        if ($newTempEnabled !== (bool) $settings->temperature_enabled) {
            $commands[] = $newTempEnabled ? 'TEMP_ON' : 'TEMP_OFF';
        }

        if ($newHumEnabled !== (bool) $settings->humidity_enabled) {
            $commands[] = $newHumEnabled ? 'HUM_ON' : 'HUM_OFF';
        }

        // Always send the delay command so the device stays in sync
        // (minimum 15s enforced by the device; we send dashboard value)
        $commands[] = 'DELAY_' . $newDelay;

        // Save to database
        $settings->update([
            'temperature_enabled' => $newTempEnabled,
            'humidity_enabled'    => $newHumEnabled,
            'refresh_delay'       => $newDelay,
        ]);

        // Push commands to ThingSpeak TalkBack
        $sent  = 0;
        $total = count($commands);

        if ($this->talkbackConfigured()) {
            foreach ($commands as $cmd) {
                if ($this->queueTalkBackCommand($cmd)) {
                    $sent++;
                }
            }
        }

        // Build response message
        if (!$this->talkbackConfigured()) {
            $message = 'Settings saved (dashboard only). Add TalkBack credentials to .env to control the device.';
        } elseif ($sent === $total) {
            $message = 'Settings saved! ' . $total . ' command(s) queued for the IoT device.';
        } else {
            $message = 'Settings saved. ' . $sent . ' of ' . $total . ' TalkBack command(s) sent (check logs).';
        }

        return redirect()->route('settings')->with('success', $message);
    }

    // ───────────────────────────────────────────────────────────
    // Queue a single command string to ThingSpeak TalkBack
    // ───────────────────────────────────────────────────────────
    private function queueTalkBackCommand(string $command): bool
    {
        $appId  = env('TALKBACK_APP_ID');
        $apiKey = env('TALKBACK_API_KEY');

        $url = "https://api.thingspeak.com/talkbacks/{$appId}/commands.json";

        try {
            $response = Http::asForm()->post($url, [
                'api_key'        => $apiKey,
                'command_string' => $command,
            ]);

            if ($response->successful()) {
                Log::info("[TalkBack] Queued: {$command}");
                return true;
            }

            Log::error("[TalkBack] Failed to queue '{$command}'. HTTP {$response->status()}: {$response->body()}");
            return false;

        } catch (\Throwable $e) {
            Log::error("[TalkBack] Exception for '{$command}': " . $e->getMessage());
            return false;
        }
    }

    // ───────────────────────────────────────────────────────────
    // Check if TalkBack credentials are set
    // ───────────────────────────────────────────────────────────
    private function talkbackConfigured(): bool
    {
        $appId  = env('TALKBACK_APP_ID',  '');
        $apiKey = env('TALKBACK_API_KEY', '');
        return !empty($appId)
            && $appId  !== 'YOUR_TALKBACK_APP_ID'
            && !empty($apiKey)
            && $apiKey !== 'YOUR_TALKBACK_API_KEY';
    }
}
