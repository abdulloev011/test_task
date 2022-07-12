<?php

namespace App\Http\Controllers;

use App\Http\Resources\CertificateResource;
use App\Mail\NotifyMail;
use App\Models\Certificate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    /**
     * @OA\Post(
     ** path="/api/certificate/buy",
     *  tags={"Certificate"},
     *  summary="Active order",
     *  @OA\RequestBody(
     *      @OA\JsonContent(),
     *      @OA\MediaType(
     *         mediaType="multipart/form-data",
     *         @OA\Schema(
     *            type="object",
     *            required={"last_name","name","email","number_of_tree","currency","plantation_year"},
     *            @OA\Property(property="last_name", type="string"),
     *            @OA\Property(property="name", type="string"),
     *            @OA\Property(property="email", type="string"),
     *            @OA\Property(property="number_of_tree", type="integer"),
     *            @OA\Property(property="currency", type="string"),
     *            @OA\Property(property="plantation_year", type="string"),
     *         ),
     *        ),
     *  ),
     *  @OA\Response(response=200, description="Success",@OA\MediaType(mediaType="application/json",)),
     *  @OA\Response(response=401,description="Unauthenticated"),
     *  @OA\Response(response=400,description="Bad Request"),
     *  @OA\Response(response=404,description="not found"),
     *  @OA\Response(response=403,description="Forbidden")
     *)
     **/
    public function buy_certificate(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => ['required','string'],
            'last_name' => ['required','string'],
            'email' => ['required','email'],
            'number_of_tree' => ['required','integer'],
            'currency' => ['required','string','in:евро'],
            'plantation_year' => ['required','string']
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        $user = User::updateOrCreate([
            'email' => $request->email,
            'name' => $request->name,
            'last_name' => $request->last_name,
        ]);
        $user_balance = $user->balance;
        $price = $request->number_of_tree * 39;
        if($user_balance >= $price){
            $uniqueId = mt_rand(1000000000, 9999999999);
            $check = Certificate::where('unique_code',$uniqueId)->exists();
            $unique_code = $uniqueId;
            $user = User::find($user->id);
            if($user){
                $user->update([
                    'balance' => $user_balance - $price
                ]);
            }
            if (!$check){
                $unique_code = $uniqueId;
            }else{
                $flag = true;
                while($flag){
                    $uniqueId = mt_rand(1000, 9999999999);
                    $check = Certificate::where('unique_code',$uniqueId)->exists();
                    if ($check){
                        $uniqueId = mt_rand(1000, 9999999999);
                    }else{
                        $unique_code = $uniqueId;
                        $flag = false;
                    }
                }
            }
            Certificate::create([
                'user_id' => $user->id,
                'plantation_year'=> $request->plantation_year,
                'number_of_tree' => $request->number_of_tree,
                'currency' => $request->currency,
                'price' => $request->number_of_tree * 39,
                'unique_code' => $unique_code
            ]);
            $details = [
                'unique_code' => $unique_code
            ];
            Mail::to($user->email)->send(new NotifyMail($details));
 
            if (Mail::failures()) {
                return response()->Fail('Sorry! Please try again latter');
            }else{
                return response()->success('Great! Successfully send in your mail');
            }
            return response()->json([
                'message' => 'Успешно куплено сертификат'
            ]);
        }else{
            return response()->json([
                'message' => 'В вашем балансе не достаточно денег'
            ]);
        }
    }
    /**
     * @OA\Post(
     ** path="/api/certificate/activate",
     *  tags={"Certificate"},
     *  summary="Active order",
     *  @OA\RequestBody(
     *      @OA\JsonContent(),
     *      @OA\MediaType(
     *         mediaType="multipart/form-data",
     *         @OA\Schema(
     *            type="object",
     *            required={"code"},
     *            @OA\Property(property="code", type="number"),
     *         ),
     *        ),
     *  ),
     *  @OA\Response(response=200, description="Success",@OA\MediaType(mediaType="application/json",)),
     *  @OA\Response(response=401,description="Unauthenticated"),
     *  @OA\Response(response=400,description="Bad Request"),
     *  @OA\Response(response=404,description="not found"),
     *  @OA\Response(response=403,description="Forbidden")
     *)
     **/
    public function activate_certificate(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'code' => ['required','numeric']
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        $certificate = Certificate::where('unique_code',$request->code)->exists();
        if($certificate){
            Certificate::where('unique_code',$request->code)->first()->update([
                'is_active' => 1
            ]);
            return response()->json([
                "message" => "Сертификат с кодом $request->code актировирован." 
            ]);
        }else{
            return response()->json([
                "message" => "Сертификат с кодом $request->code не найдён."
            ]);
        }
    }
    /**
     * @OA\Get(
     *     summary="Activate  certificates",
     *     path="/api/certificate/list",
     *     tags={"Certificate"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="Display a listing of addresses."),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Not Found"),
     * )
     */
    public function activated_certificate()
    {
        $certificates = Certificate::where('is_active',1)->with('user')->get();
        if ($certificates){
            return response()->json(CertificateResource::collection($certificates));
        }else{
            return response()->json([
                'message' => 'Активированных сертификатов нет!'
            ]);
        }
    }

}
