<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use App\Packet;
use App\User;
use App\Http\Resources\Packet as PacketResource;
use App\Exceptions\Handler;
use Illuminate\Support\Facades\Hash;
use Mail;
use Illuminate\Support\Facades\URL;

class PacketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($pagesize, Request $request)
    {
        try
        {
            if($request->has('search'))
            {
                $searchString = $request->input('search');
                $packets = Packet::where('title', 'LIKE', "%{$searchString}%")
                ->where('show_hash', "notset")
                ->orWhere(function($q) use($searchString){
                    $q->where('postcode_a', 'LIKE', "%{$searchString}%");
                    $q->where('show_hash', "notset");
                })
                ->orWhere(function($q) use($searchString){
                    $q->where('postcode_b', 'LIKE', "%{$searchString}%");
                    $q->where('show_hash', "notset");
                })->paginate(15);
            }
            else
            {
                $packets = Packet::where('show_hash', "notset")->paginate($pagesize);
            }
            //om te filteren, moeten afpsreken wat
            // if($request->has('test'))
            // {
            //     echo "Test";
            // }

            return PacketResource::collection($packets);
        }
        catch (Exception $error)
        {
            report($error);
            return response()->json(['message' => $error], 500);
        }
    }

    //krijg alle pakketten die bij de persoon zijn
    public function userPackets($pagesize, Request $request)
    {
        try
        {
            $user_id = $request->user()->id;
            $packets = Packet::where('user_id', $user_id)->paginate($pagesize);
            return PacketResource::collection($packets);
        }
        catch (Exception $error)
        {
            report($error);
            return response()->json(['message' => $error,], 500);
        }
    }

    //krijg alleen de userinfo voor in de paketten
    public function userInfo($id)
    {
        try
        {
            $user = User::where('id', $id)->get('name');
            return response()->json($user, 200);
        }
        catch (Exception $error)
        {
            report($error);
            return response()->json(['message' => $error], 500);
        }
    }

    //Uitnodiging naar pakket sturen om te bezorgen
    public function sendInvite(Request $request)
    {
        try
        {
            //CHECKEN OF IDS NIET HETZELFDE ZIJN ZODAT JE NIET JEZELF KAN BEZORGEN

            $packetOwnerId = $request->input('user_id');
            $packetId = $request->input('id');
            $inviteMessage = $request->input('message');

            $userId = $request->user()->id;
            $userName = $request->user()->name;

            $packetHash = Packet::where("id", $packetId)->get('show_hash');

            if($packetHash[0]->show_hash != "notset")
            {
                return response()->json(['message' => "Oops something went wrong."], 404);
            }

            $sendEmail = User::where('id', $packetOwnerId)->get('email');
            $signedRoute = URL::signedRoute('accept', ['id' => $userId.'-'.time()]);
            $deniedRoute = URL::signedRoute('deny', ['id' => $userId.'-'.time().'deny']);

            $packet = Packet::where("id", $packetId)->update([
                "show_hash" => $signedRoute,
                "deny_hash" => $deniedRoute,
            ]);

            return [
                "packetOwnerID" => $packetOwnerId,
                "packetId" => $packetId,
                "inviteMessage" => $inviteMessage,
                "userId" => $userId,
                "userName" => $userName,
                "email" => $sendEmail[0]->email,
                "signedRoute" => $signedRoute,
                "deniedRoute" => $deniedRoute,
                "packet" => $packet,
                "request" => $request
            ];

            //TODO: mail sturen

        }
        catch (Exception $error)
        {
            report($error);
            return response()->json(['message' => $error], 500);
        }
    }

    public function DeniedUrl($id, Request $request)
    {
        try
        {
            if (! $request->hasValidSignature())
            {
                return response()->json(['message' => "Oops not found"], 404);
            }

            $trimId = explode('-', $id)[0];

            $packet = Packet::where('deny_hash', $request->fullUrl())->first();

            if(!$packet)
            {
                return response()->json(['message' => "Oops not found"], 404);
            }

            $sendEmailUserData = User::where('id', $trimId)->first();

            $packet = Packet::where('deny_hash', $request->fullUrl())->update([
                "show_hash" => "notset",
                "deny_hash" => "notset",
            ]);

            return [
                "emailTo" => $sendEmailUserData->email,
                "name" => $sendEmailUserData->name,
            ];

             //Mail hier

        }
        catch (Exception $error)
        {
            report($error);
            return response()->json(['message' => $error], 500);
        }
    }

    public function AcceptedUrl($id, Request $request)
    {
        try
        {
            if (! $request->hasValidSignature())
            {
                return response()->json(['message' => "Oops not found"], 404);
            }

            $trimId = explode('-', $id)[0];

            $packet = Packet::where('show_hash', $request->fullUrl())->first();

            if(!$packet)
            {
                return response()->json(['message' => "Oops not found"], 404);
            }

            $sendEmailUserData = User::where('id', $trimId)->first();

            $secretData = [
                "contact" => $packet->contact,
                "adres_a" => $packet->adres_a,
                "adres_b" => $packet->aders_b,
                "postcode_a" => $packet->postcode_a,
                "postcode_b" => $packet->postcode_b,
            ];

            $packet = Packet::where('show_hash', $request->fullUrl())->update([
                "deliverer_id" => $trimId,
                "show_hash" => "used",
                "deny_hash" => "notset",
            ]);

            return [
                "emailTo" => $sendEmailUserData->email,
                "name" => $sendEmailUserData->name,
                "contact" => $secretData['contact'],
                "straat A" => $secretData['adres_a'],
                "postcode A" => $secretData['postcode_a'],
                "straat B" => $secretData['adres_b'],
                "postcode B" => $secretData['postcode_b'],
            ];

             //Mail hier
        }
        catch (Exception $error)
        {
            report($error);
            return response()->json(['message' => $error], 500);
        }
    }

    //NOT NIET ACCEPTEER KNOP EN ROUTE EN ALLES

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return [
        //     "title" => $request->title,
        //     'description' => $request->description,
        //     'height' => $request->height,
        //     'width' => $request->input('width'),
        //     'length' => $request->input('length'),
        //     'weight' => $request->input('weight'),
        //     'photo' => $request->input('photo'),
        //     'contact' => $request->input('contact'),
        //     'postcode_a' => $request->input('postcode_a'),
        //     'postcode_b' => $request->input('postcode_b'),
        //     'adres_a' => $request->input('adres_a'),
        //     'adres_b' => $request->input('adres_b'),
        //     'avg_confirmed' => $request->input('avg_confirmed'),
        //     'WHOLE ARRAY' => $request->all()
        // ];

        try
        {
            $user_id = $request->user()->id;
            //$packet = $request->isMethod('put') ? Packet::findOrFail($request->package_id) : new Packet;

            //check of put request or not, if put request, check if packet is yours
            if($request->isMethod('put'))
            {
                $packet = Packet::findOrFail($request->package_id);

                if($packet->user_id != $user_id)
                {
                    return response()->json(['message' => 'Package not found.'], 404);
                }
            }
            else
            {
                $packet = new Packet;
            }

            $request->validate([
                'title' => 'required|string|min:5|max:50',
                'description' => 'required|string|min:5|max:500',
                'height' => 'required|integer',
                'width' => 'required|integer',
                'length' => 'required|integer',
                'weight' => 'required|integer',
                // 'photo' => 'required|image|mimes:jpeg,png,jpg,giv,svg|max:5048',
                'contact' => 'required|max:255',
                'postcode_a' => 'required|string|min:6|max:7',
                'postcode_b' => 'required|string|min:6|max:7',
                'adres_a' => 'required|string|max:65',
                'adres_b' => 'required|string|max:65',
                'avg_confirmed' => 'required',
            ]);

            //image naam
            //$imageName = time().$user_id.'.'.$request->photo->extension();

            //image opslaan
            if(/*$request->photo->move(public_path('images'), $imageName)*/true)
            {
                //spaties, white spaces, tabs etc allemaal weghalen (dit werkte eerste en nu niet meer, fck me)
                $postA = preg_replace('~\x{00a0}~','',$request->input('postcode_a'));
                $postB = preg_replace('~\x{00a0}~','',$request->input('postcode_b'));

                $packet->user_id = $user_id;
                $packet->deliverer_id = null;
                $packet->title = $request->input('title');
                $packet->description = $request->input('description');
                $packet->height = $request->input('height');
                $packet->width = $request->input('width');
                $packet->length = $request->input('length');
                $packet->weight = $request->input('weight');
               // $packet->photo = $imageName;
                $packet->photo = "photoNaam";
                $packet->contact = $request->input('contact');
                $packet->postcode_a = $postA;
                $packet->postcode_b = $postB;
                $packet->adres_a = $request->input('adres_a');
                $packet->aders_b = $request->input('adres_b');
                $packet->avg_confirmed = $request->input('avg_confirmed');
                $packet->show_hash = "notset";
                $packet->deny_hash = "notset";

                if($packet->save())
                {
                    return new PacketResource($packet);
                }
                else
                {
                    return response()->json(['message' => "Oops something went wrong."], 500);
                }
            }
            else
            {
                return response()->json(['message' => "Oops something went wrong with uploading the image."], 500);
            }
        }
        catch (Exception $error)
        {
            report($error);
            return response()->json(['message' => $error], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try
        {
            $packet = Packet::findOrFail($id);
            return new PacketResource($packet);
        }
        catch (Exception $error)
        {
            report($error);
            return response()->json(['message' => $error], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try
        {
            $packet = Packet::findOrFail($id);
            $user_id = Auth::user()->id;

            if($packet->user_id != $user_id)
            {
                return response()->json(['message' => 'Package not found.'], 404);
            }
            else
            {
                if($packet->delete())
                {
                    return new PacketResource($packet);
                }
                else
                {
                    return response()->json(['message' => 'Oops something went wrong.'], 500);
                }
            }
        }
        catch (Exception $error)
        {
            report($error);
            return response()->json(['message' => $error], 500);
        }
    }
}
