<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserAdminRequest;
use App\Http\Requests\ValidateUser;
use App\Order;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Usercontroller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $list = User::whereNotIn('status', [-1])->paginate(10);
        $data = ['list' => $list];
        return view('admin.user.list-user',$data);
    }

    public function index2()
    {
        $list = User::whereNotIn('status', [1]) ->paginate(10);
        $data = ['list' => $list];
        return view('admin.user.deleted-user', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.user.create-user');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateUserAdminRequest $request)
    {
        $request ->validated();
        $user = new User();
        $user->firstName = $request->get('firstName');
        $user->lastName = $request->get('lastName');
        $user->phone = $request->get('phone');
        $user->gender = $request->get('gender');
        $user->email = $request->get('email');
        $user->password = $request->get('password');
        $user->save();
        return redirect('admin/user');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        $data = ['user' => $user];
        return view('admin.user.list-user', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('admin/user/edit-user')->with('user', $user);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(ValidateUser $request, $id)
    {
        $user=User::find($id);
        $request->validated();
        $request->validate([
            'firstName' => 'required|max:20|min:2',
            'lastName' => 'required|max:20|min:2',
            'phone' => 'required',
            'email' => 'required|email|unique:users,email',
            'gender' => 'required',
            'password' => 'required|max:15|min:5',
        ],
            [
                'firstName.required'=>'Vui l??ng nh???p h??? c???a b???n',
                'firstName.min'=>'H??? c???a b???n qu?? ng???n, vui l??ng nh???p ??t nh???t 2 k?? t???',
                'firstName.max'=>'H??? c???a b???n qu?? d??i, vui l??ng nh???p nhi???u nh???t 20 k?? t???',
                'lastName.required'=>'Vui l??ng nh???p h??? c???a b???n',
                'lastName.min'=>'H??? c???a b???n qu?? ng???n, vui l??ng nh???p ??t nh???t 2 k?? t???',
                'lastName.max'=>'H??? c???a b???n qu?? d??i, vui l??ng nh???p nhi???u nh???t 20 k?? t???',
                'phone.required'=>'Vui l??ng nh???p s??? ??i???n tho???i c???a b???n',
                'email.required'=>'Vui l??ng nh???p email c???a b???n',
                'email.email'=>'Vui l??ng nh???p email c???a b???n theo ????ng ?????nh d???ng',
                'email.unique'=>'Email ???? ???????c s??? d???ng, vui l??ng ch???n email kh??c',
                'gender.required'=>'Vui l??ng l???a  ch???n gi???i t??nh c???a b???n',
                'password.required'=>'Vui l??ng nh???p password c???a b???n',
                'password.min'=>'Password c???a b???n qu?? ng???n, vui l??ng nh???p ??t nh???t 5 k?? t???',
                'password.max'=>'Password c???a b???n qu?? d??i, vui l??ng nh???p nhi???u nh???t 15 k?? t???',
            ]);
        $user->firstName = $request->get('firstName');
        $user->lastName = $request->get('lastName');
        $user->phone = $request->get('phone');
        $user->gender = $request->get('gender');
        $user->email = $request->get('email');
        $user->password = $request->get('password');
        $user->save();
        return redirect('/admin/user');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        error_log('Some message here.');
        $user = User::find($id);
        $user->status = -1;
        $user->save();
        return response()->json(['status' => '200', 'message' => 'Okie']);
    }

    public function changeStatus(Request $request)
    {
        $listItem = User::whereIn('id', $request->input('ids'));
        $listItem->update(array(
            'status' => (int)$request->input('status'),
            'updated_at' => date('Y-m-d H:i:s')));
        return response()->json(['status' => '200', 'message' => 'Good']);
    }

    public function getClassroom() {
        $orders = Order::all()->where('user_id', Auth::user()->id);
        return view('client/classroom')->with(compact('orders'));
    }
}
