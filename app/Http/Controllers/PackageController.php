<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Package;
use App\Http\Resources\Package as PackageResource;
use App\Exceptions\Handler;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $packages = Package::paginate(15);
        return PackageResource::collection($packages);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $package = $request->isMethod('put') ? Package::findOrFail($request->package_id) : new Package;

        $package->title = $request->input('title');
        $package->name = $request->input('name');
        $package->description = $request->input('description');
        $package->height = $request->input('height');
        $package->width = $request->input('width');
        $package->length = $request->input('length');
        $package->weight = $request->input('weight');
        $package->photo = $request->input('photo');
        $package->email = $request->input('email');
        $package->phone_number = $request->input('phone_number');
        $package->postcode_a = $request->input('postcode_a');
        $package->postcode_b = $request->input('postcode_b');
        $package->avg_confirmed = $request->input('avg_confirmed');
        $package->show_hash = $request->input('show_hash');

        if($package->save())
        {
            return new PackageResource($package);
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
            $package = Package::findOrFail($id);
            return new PackageResource($package);
        }
        catch (Exception $error)
        {
            report($error);
            return response()->json(['message' => $error], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $package = Package::findOrFail($id);

        if($package->delete())
        {
            return new PackageResource($package);
        }
    }
}
