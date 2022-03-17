<?php

namespace App\Models;

use App\Models\Traits\DateFormat;
use Illuminate\Database\Eloquent\Model;

class MyzapSession extends Model
{
    protected $fillable = [
        'user_id',
        'session',
        'session_key',
        'state',
        'status',
        'number'
    ];

    public function updateSession(array $wookData, $userId)
    {
        if (empty($userId)) {
            $session = MyzapSession::where('session', $wookData['session'])->firstOrFail();
        } else {
            $session = MyzapSession::where('user_id', $userId)->firstOrFail();
        }
        
        $arrUpdate = [];
        $arrUpdate['state'] = $wookData['state'];
        $arrUpdate['status'] = $wookData['status'];

        if (!empty($wookData['number'])) {
            $arrUpdate['number'] = $wookData['number'];
        }
        if (!empty($wookData['qrcode'])) {
            $arrUpdate['qrcode'] = str_replace('data:image/png;base64,', '', $wookData['qrcode']);
        }

        $session->update($arrUpdate);
    }
}
