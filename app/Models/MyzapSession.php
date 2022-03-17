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
        'number',
        'qrcode'
    ];

    public function updateSession(array $wookData, $userId)
    {
        if (empty($userId)) {
            $session = MyzapSession::where('session', $wookData['session'])->firstOrFail();
        } else {
            $session = MyzapSession::where('user_id', $userId)->firstOrFail();
        }
        
        $arrUpdate = [];
        if (!empty($wookData['state'])) {
            $arrUpdate['state'] = $wookData['state'];
        }
        if (!empty($wookData['status'])) {
            $arrUpdate['status'] = $wookData['status'];
        }
        if (!empty($wookData['number'])) {
            $arrUpdate['number'] = $wookData['number'];
        }
        if (!empty($wookData['qrcode'])) {
            $arrUpdate['qrcode'] = $wookData['qrcode'];
        }

        // Em caso de fechamento da sessão
        if(!empty($wookData['state']) && $wookData['state'] == 'browserClose') {
            $arrUpdate['qrcode'] = null;
            $arrUpdate['number'] = $wookData['number'];
        }

        $session->update($arrUpdate);
    }
}
