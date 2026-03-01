<?php

namespace App\Presentation\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Application\Shared\Bus\CommandBus;
use App\Application\Shared\Bus\QueryBus;
use App\Application\User\Commands\RegisterUser\RegisterUserCommand;
use App\Application\User\Queries\GetUserById\GetUserByIdQuery;
use App\Presentation\Http\Requests\RegisterUserRequest;
use App\Application\User\Queries\ListUsers\ListUsersQuery;
use App\Application\User\Commands\UpdateUser\UpdateUserCommand;
use App\Application\User\Commands\DeleteUser\DeleteUserCommand;
use App\Presentation\Http\Requests\UpdateUserRequest;

class UserApiController
{
    public function index(Request $request, QueryBus $queryBus)
    {
        $page = max(1, (int) $request->query('page', 1));
        $perPage = min(max((int) $request->query('per_page', 15), 1), 100);
        $search = $request->query('search');
        $sortBy = $request->query('sort_by', 'id');
        $sortDir = $request->query('sort_dir', 'desc');

        return response()->json(
            $queryBus->ask(new ListUsersQuery(
                page: $page,
                perPage: $perPage,
                search: is_string($search) ? $search : null,
                sortBy: is_string($sortBy) ? $sortBy : 'id',
            sortDir: is_string($sortDir) ? $sortDir : 'desc'
            ))
        );
    }

    public function store(RegisterUserRequest $request, CommandBus $commandBus)
    {
        $user = $commandBus->dispatch(new RegisterUserCommand(
            name: $request->string('name'),
            email: $request->string('email'),
            password: $request->string('password'),
        ));

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ], 201);
    }

    public function show(int $id, QueryBus $queryBus)
    {
        $data = $queryBus->ask(new GetUserByIdQuery($id));
        return response()->json($data);
    }

    public function update(int $id, UpdateUserRequest $request, CommandBus $commandBus)
    {
        $user = $commandBus->dispatch(new UpdateUserCommand(
            id: $id,
            name: $request->string('name'),
            email: $request->string('email'),
            password: $request->filled('password') ? $request->string('password') : null
        ));

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    public function destroy(int $id, CommandBus $commandBus)
    {
        $commandBus->dispatch(new DeleteUserCommand($id));

        return response()->json([
            'message' => 'User deleted',
        ]);
    }
}