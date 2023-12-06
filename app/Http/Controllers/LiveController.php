<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLiveRequest;
use App\Http\Requests\UpdateLiveRequest;
use App\Models\Live;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\AgoraTokenService;
use App\Services\AgoraApiService;

class LiveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $liveStreams = Live::where('deleted_at', null)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Live/Index', [
            'liveStreams' => $liveStreams,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();

        $agoraApi = new AgoraApiService();
        $mediaGateway = $agoraApi->createMediaGateway($user->id, 'na', config('agora.app_id'));

        $live = Live::withTrashed()
            ->where('user_id', $user->id)
            ->first();

        if ($live) {
            if ($live->trashed()) {
                $live->restore();
            }
        
        $rtcToken = AgoraTokenService::buildRtcToken($live->uuid, $user->id, 3600);
        $rtmToken = AgoraTokenService::buildRtmToken($user->id, 3600);

        return Inertia::render('Live/Show', [
            'live' => $live,
            'channelId' => $live->uuid,
            'appId' => config('agora.app_id'),
            'rtcToken' => $rtcToken,
            'rtmToken' => $rtmToken,
        ]);
        }

        return Inertia::render('Live/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLiveRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Live $live)
    {
        $user = auth()->user();

        $live = Live::where('uuid', $live->uuid)
            ->with('user')
            ->firstOrFail();
        
        if (!$live) {
            return redirect()->route('live.index');
        }

        $rtcToken = AgoraTokenService::buildRtcToken($live->uuid, $user->id, 3600);
        $rtmToken = AgoraTokenService::buildRtmToken($user->id, 3600);

        return Inertia::render('Live/Show', [
            'channelId' => $live->uuid,
            'appId' => config('agora.app_id'),
            'rtcToken' => $rtcToken,
            'rtmToken' => $rtmToken,
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Live $live)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLiveRequest $request, Live $live)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Live $live)
    {
        //
    }

    //private functions
    private function createMediaGatewayToken(Request $request)
    {
        $userId = $request->userId;
        $region = $request->region;
        $appId = $request->appId;

        $agoraApi = new AgoraApiService();
        $mediaGateway = $agoraApi->createMediaGateway($userId, $region, $appId);

        return $mediaGateway;
    }
}
