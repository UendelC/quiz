<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Choice;
use App\Models\Exam;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Revolution\Google\Sheets\Facades\Sheets;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->type) {
            return User::where('type', $request->type)->get();
        }
        return User::all();
    }

    public function indexTeacher()
    {
        $user = auth()->user();

        $participants = $user
            ->lecture
            ->exams()
            ->with(['users:id,name'])
            ->get()
            ->map(
                function ($exam) {
                    return $exam->users;
                }
            )
            ->flatten()
            ->unique('id');

        return UserResource::collection($participants);
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|string|email|unique:users,email',
                'name' => 'required|string|max:255',
                'type' => 'required',
                'password' => 'required|string|min:6',
            ]
        );

        $user = User::create(
            [
                'name' => $request->name,
                'password' => bcrypt($request->password),
                'type' => $request->type,
                'email' => $request->email,
            ]
        );

        $token = $user->createToken('api-token')->plainTextToken;

        $user->token = $token;

        return response(
            [
                'name' => $user->name,
                'email' => $user->email,
                'token' => $user->token,
            ],
            201
        );
    }

    public function show(User $user)
    {
        return new UserResource(auth()->user());
    }

    public function login(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|string|email|',
                'password' => 'required|string|min:6'
            ]
        );

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['errors' => 'The provided credentials are incorrect.'], 404);
        }

        $token = $user->createToken('app-token')->plainTextToken;

        $user->token = $token;

        return response(
            [
                'name' => $user->name,
                'email' => $user->email,
                'token' => $user->token,
            ],
            201
        );
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Tokens Revoked'
        ];
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|string|email'
            ]
        );

        try {
            $mail = Password::sendResetLink($request->only('email'));

            switch ($mail) {
            case Password::RESET_LINK_SENT:
                return response()->json(['errors' => 'Reset password link sent on your email id.'], 201);
            case Password::INVALID_USER:
                return response()->json(['errors' => 'We can\'t find a user with that email address.'], 404);
            }
            
        } catch (\Swift_TransportException $ex) {
            return response()->json(['errors' => $ex->getMessage(), 500]);
        } catch (Exception $ex) {
            return response()->json(['errors' => $ex->getMessage(), 500]);
        }
    }

    public function updatePassword(Request $request)
    {
        $request->validate(
            [
                'current_password' => 'required|string|email',
                'password' => 'required|string|min:6',
            ]
        );

        if (!Hash::check($request->get('current_password'), $request->user()->password)) {
            return response()->json(['errors' => 'The provided password does not match your current password.'], 404);
        }

        $request->user()->forceFill(
            [
                'password' => Hash::make($request->get('password')),
            ]
        )->save();

        return response(['data' => 'Password set successfully.'], 201);
    }

    public function takeExam(Request $request)
    {
        $request->validate(
            [
                'exam_id' => 'required',
                'answers' => 'required',
            ]
        );

        $answers = $request->get('answers');
        $exam_id = $request->get('exam_id');

        $user = auth()->user();

        if ($user->exams()->find($exam_id)) {
            return response()->json(
                [
                    'status' => 'exame já enviado',
                ]
            );
        }

        $score = Choice::whereIn('id', $answers)->where('is_right', true)->count();
        $amount_of_questions = Exam::find($exam_id)->questions()->count();

        $grade = ($score / $amount_of_questions) * 10;

        $user->exams()->attach(
            $exam_id,
            [
                'score' => $grade,
            ]
        );

        $exam = Exam::find($exam_id);

        $data[] = [
            'Topico' => $exam->category->name,
            'participante' => $user->name,
            'Nota' => $grade,
            'date' => $exam->created_at->format('d/m/Y'),
        ];

        Sheets::spreadsheet('1SRGZH4PaHn-w52GI1ZwdTCJsjVLundz4rPN4A66k4Yg')
            ->sheet('Página3')
            ->append($data);

        return response()->json(
            [
                'status' => 'ok',
                'score' => $grade,
            ]
        );
    }

    public function grades()
    {
        $user = auth()->user();

        $exams = $user
            ->exams()
            ->with('category')
            ->get()
            ->map(
                function ($exam) {
                    $exam->score = $exam->pivot->score;
                    $exam->date = $exam->created_at->format('d/m/Y');
                    $exam->category_name = $exam->category->name;
                    unset($exam->pivot);
                    return $exam;
                }
            );

        return response()->json(
            [
                'exams' => $exams,
            ]
        );
    }
}
