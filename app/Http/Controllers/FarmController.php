<?php

namespace App\Http\Controllers;

use App\Models\{Farm, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
class FarmController extends Controller
{

    public function index(Request $request)
    {
        $data = Farm::latest()->get();
        if ($request->ajax()) {
            $data = Farm::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $id = Crypt::encrypt($row->id);
                    $btn = '<div class="d-flex" style="gap:5px;">';
                    $btn .= '
                    <button type="button" title="EDIT" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#updateData"
                    data-name="' . $row->name . '"
                    data-address="' . $row->address . '"
                    data-user_id="' . (string) $row->user_id . '"
                    data-url="' . route('farm.update', ['id' => $id]) . '"
                    >
                        Edit
                    </button>';
                    $btn .= '
                    <form id="deleteForm" action="' . route('farm.delete', ['id' => $id]) . '" method="POST">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                                <button type="button" title="DELETE" class="btn btn-sm btn-danger btn-delete" onclick="confirmDelete(event)">
                                    Delete
                                </button>
                            </form>
                    </div>';
                    return $btn;
                })
                
                ->addColumn('image', function ($row) {
                    if ($row->image != null) {
                        $image = '<img src="' . asset('storage/farm/' . $row->image) . '" style="width: 100px; border-radius:20px; height: 100px; object-fit: cover;">';
                    } else {
                        $image = '<img src="' . url('assets/img/noimage.jpg') . '" style="width: 100px; border-radius:20px; height: 100px; object-fit: cover;">';
                    }
                    return $image;
                })
                ->addColumn('owner', function ($row) {

                    return $row->owner->name;
                })
                ->rawColumns(['action', 'image'])
                ->make(true);
        }
        return view('admin.farm', [
            'data' => Farm::all(),
            'owners' => User::all()
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'user_id' => 'required',
        ]);

        Farm::create([
            'name' => $request->name,
            'address' => $request->address,
            'user_id' => $request->user_id,
        ]);

        return redirect()->back()->with(['message' => 'Farm berhasil ditambahkan', 'status' => 'success']);
    }



    // public function update(Request $request, $id)
    // {
    //     $id = Crypt::decrypt($id);
    //     $farm = Farm::where('id', new ($id))->first();


    //     $request->validate([
    //         'name' => 'required',
    //         'address' => 'required',
    //         'user_id' => 'required',
    //     ]);

    //     $farm->update([
    //         'name' => $request->name,
    //         'address' => $request->address,
    //         'user_id' => new ($request->user_id),
    //     ]);

    //     return redirect()->route('farm.index')->with(['message' => 'Farm berhasil di update', 'status' => 'success']);

    // }

    public function update(Request $request, $id)
{
    // Decrypt the ID
    $id = Crypt::decrypt($id);

    // Find the farm by ID
    $farm = Farm::where('id', $id)->first();

    // Validate the request data
    $request->validate([
        'name' => 'required',
        'address' => 'required',
        'user_id' => 'required|exists:users,id',  // Ensure user_id exists in the users table
    ]);

    // Update the farm information
    $farm->update([
        'name' => $request->name,
        'address' => $request->address,
        'user_id' => $request->user_id,  // No need for 'new' here
    ]);

    // Redirect back to the farm index with a success message
    return redirect()->route('farm.index')->with(['message' => 'Farm berhasil di update', 'status' => 'success']);
}


    public function destroy($id)
    {
    //     $id = Crypt::decrypt($id);
    //     Farm::where('id', new ($id))->delete();
    //     return redirect()->route('farm.index')->with(['message' => 'Farm berhasil di delete', 'status' => 'success']);
    // }
    // Decrypt the ID
        $id = Crypt::decrypt($id);
        
        // Delete the farm with the decrypted ID
        Farm::where('id', $id)->delete();
        
        // Redirect with a success message
        return redirect()->route('farm.index')->with(['message' => 'Farm berhasil di delete', 'status' => 'success']);
    }
}
