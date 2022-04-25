<?php

namespace App\Http\Controllers\Template;

use Illuminate\Http\Request;
use App\Models\Template\Home;
use App\Models\template\Slider;
use App\Models\template\Submission;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\TemplateResource;
use App\Http\Controllers\Template\HomeRepository;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $homeRepository;

    public function __construct(HomeRepository $homeRepository)
    {
        $this->homeRepository = $homeRepository;
    }

    public function getSubmissionDescription()
    {
        $description = Submission::first();
        return new TemplateResource($description);
    }

    public function updateSubmissionDescription(Request $request, $id)
    {
        $description = Submission::find($id);
        $is_success = $description->update([
            'content' => $request->content,
        ]);
        return $is_success ? response()->json(['message' => 'Successfully updated'], 200) : response()->json(['message' => 'Failed to update'], 400);
    }

    public function getImages()
    {
        $images = Slider::get();
        return response()->json(['data' => $images], 200);
        # code...
    }

    public function showImages($id)
    {
        $image = Slider::find($id);
        return response()->json(['data' => $image], 200);
        # code...
    }

    public function deleteImage($id)
    {
        $image = Slider::find($id);
        $is_success = $image->delete();
        return $is_success ? response()->json(['message' => 'Successfully deleted'], 200) : response()->json(['message' => 'Failed to delete'], 400);
        # code...
    }
   

    public function updateImages(Request $request)
    {
        // $request->validate([
        //     'path' => 'image|mimes:jpg,png,jpeg|max:2048|dimensions:min_width=100,min_height=100',
        // ]);
        $image = $request->file('path');
       
       $slider= Slider::where('id', $request->id)->first();
        if ($slider->exists()) {
        

            if ($request->path != '') {
                // $image = $request->file('path');
                // dd($image);
                $path = public_path() . '/storage/uploads/file_upload';
                $image->move($path, $image->getClientOriginalName());
             

                $slider->update([
                    'path' => $path.'/'.$image->getClientOriginalName(),
                    // 'doc_file' => $file //new file path updated
                ]);

              
            }

            $slider->update([
                'content' => $request->content,
                // 'doc_file' => $file //new file path updated
            ]);
        
    }
}

    public function storeImages(Request $request)
    {

        $request->validate([
            'path' => 'required|image|mimes:jpg,png,jpeg|max:2048|dimensions:min_width=100,min_height=100',
        ]);
        $image = $request->file('path');
        $slider = new Slider();
        $path = public_path() . '/storage/uploads/file_upload';
        $image->move($path, $image->getClientOriginalName());



        $slider->path = $path . '/' . $image->getClientOriginalName();
        $slider->content = $request->content;
        $slider->save();
    }


    public function index()
    {
        return TemplateResource::collection(Home::where('content_type_id', '!=', 4)->get());
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->homeRepository->getDataByContentId($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $data = Home::findOrFail($id);
        $data->update([
            'content' => $request->content,
        ]);

        return $data ? response()->json(['data' => $data], 200) : response()->json(['data' => 'error'], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
