<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLiveRequest;
use App\Http\Requests\UpdateLiveRequest;
use App\Models\Live;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\AgoraTokenService;
use App\Services\AgoraApiService;

use Ramsey\Uuid\Uuid;

class LiveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //Create new RTM token for Vue to access global index channel
        // for pub/sub information about new channels being created
        $user = auth()->user();
        // Agora Api service for querying "get all channels": 
        $agoraApi = new AgoraApiService();
        // new RTM token with scope on index page: 
        $rtmToken = AgoraTokenService::buildRtmToken($user->id, 3600);
        $APPID = config('agora.app_id');

        //Query DB Index Live
        $liveStreamsDB = Live::where('deleted_at', null)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        //make API call to get channel list
        $agoraLiveChannels = $agoraApi->getAllActiveStreams($APPID);

        //filter DB array for matching livestreams from Agora REST API:
        $liveStreams = $this->filterLiveStreams($liveStreamsDB, $agoraLiveChannels);

        //TODO: add "user count" from API res to $liveStreams + add User count to UI:
        //TODO: Add local map as a cache or implement DB Cache (Redis)

        return Inertia::render('Live/Index', [
            'liveStreams' => $liveStreams,
            'appId' => config('agora.app_id'),
            'rtmToken' => $rtmToken,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();

        $agoraApi = new AgoraApiService();

        $live = Live::withTrashed()
            ->where('user_id', $user->id)
            ->first();

        if ($live) {
            if ($live->trashed()) {
                $live->restore();
            }
        
        $rtcToken = AgoraTokenService::buildRtcToken($live->uuid, $user->id, 3600);
        $rtmToken = AgoraTokenService::buildRtmToken($user->id, 3600);

        return Inertia::render('Live/Host', [
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
        $user = auth()->user();
        $uuid = Uuid::uuid4();
        $privacy = strtolower($request->privacy);
        $tipOptions = [
            ["id" => 1, "name" => "Tip", "price" => 5],
            ["id" => 2, "name" => "Tip", "price" => 10],
            ["id" => 3, "name" => "Tip", "price" => 25],
            ["id" => 4, "name" => "Tip", "price" => 50],
            ["id" => 5, "name" => "Custom Tip", "price" => "Custom"]
        ];
        $tipsEncoded = json_encode($tipOptions);

        // $coverPhoto = $this->storeCoverPhoto($request, $uuid);

        $userLive = live::create([
            'uuid' => $uuid,
            'user_id' => auth()->user()->id,
            'description' => $request->description,
            'privacy' => $privacy,
            'cover' => 'https://picsum.photos/seed/' . $uuid . '/200/300',
            'tip_options' => $tipsEncoded,

        ]);

        return redirect()->route('live.create');
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $user = auth()->user();

        $live = Live::where('uuid', $uuid)
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

    private function filterLiveStreams($databaseArray, $channelsObject)
    {
        // Decode the JSON strings:
        $databaseArray = json_decode($databaseArray, true);
        $channelsObject = json_decode($channelsObject, true);

        // Extract channels array from the channelsObject:
        $channelsArray = $channelsObject['data']['channels'];

        // Initialize an array to store matching UUIDs:
        $matchingUUIDs = [];

        // Iterate over the database array to find matching UUIDs:
        foreach ($databaseArray as $key => $item) {
            // Check if the 'uuid' key exists in the item:
            if (isset($item['uuid'])) {
                $uuid = $item['uuid'];
                // Check if the UUID exists in the channels array:
                foreach ($channelsArray as $channel) {
                    if ($channel['channel_name'] == $uuid) {
                        $matchingUUIDs[] = $uuid;
                    }
                }
            }
        }

        // Filter the database array to keep only the items with matching UUIDs
        $databaseArray = array_filter($databaseArray, function ($item) use ($matchingUUIDs) {
            return in_array($item['uuid'], $matchingUUIDs);
        });

        //return modified/filtered DB array:
        return $databaseArray;
    }
}
