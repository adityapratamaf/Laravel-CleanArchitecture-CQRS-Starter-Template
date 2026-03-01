<?php

namespace App\Presentation\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Application\Shared\Bus\QueryBus;
use App\Application\Shared\Bus\CommandBus;

use App\Application\User\Queries\ListUsers\ListUsersQuery;
use App\Application\User\Queries\GetUserById\GetUserByIdQuery;
use App\Application\User\Commands\RegisterUser\RegisterUserCommand;
use App\Application\User\Commands\UpdateUser\UpdateUserCommand;
use App\Application\User\Commands\DeleteUser\DeleteUserCommand;
use App\Presentation\Http\Requests\StoreUserRequest;
use App\Presentation\Http\Requests\UpdateUserRequest;

class UserPageController
{
    public function index(Request $request, QueryBus $queryBus)
    {
        $page = max(1, (int) $request->query('page', 1));
        $perPage = min(max((int) $request->query('per_page', 15), 1), 100);
        $search = $request->query('search');

        $result = $queryBus->ask(new ListUsersQuery(
            page: $page,
            perPage: $perPage,
            search: is_string($search) ? $search : null
        ));

        return view('users.index', [
            'users' => $result['data'],
            'meta' => $result['meta'],
            'filters' => [
                'search' => is_string($search) ? $search : '',
                'per_page' => $perPage,
            ],
        ]);
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(StoreUserRequest $request, CommandBus $commandBus)
    {
        $commandBus->dispatch(new RegisterUserCommand(
            name: $request->string('name'),
            email: $request->string('email'),
            password: $request->string('password'),
        ));

        return redirect('/users')->with('success', 'User created');
    }

    public function show(int $id, QueryBus $queryBus)
    {
        $user = $queryBus->ask(new GetUserByIdQuery($id));
        return view('users.show', compact('user'));
    }

    public function edit(int $id, QueryBus $queryBus)
    {
        $user = $queryBus->ask(new GetUserByIdQuery($id));
        return view('users.edit', compact('user'));
    }

    public function update(int $id, UpdateUserRequest $request, CommandBus $commandBus)
    {
        $commandBus->dispatch(new UpdateUserCommand(
            id: $id,
            name: $request->string('name'),
            email: $request->string('email'),
            password: $request->filled('password') ? $request->string('password') : null
        ));

        return redirect('/users/'.$id)->with('success', 'User updated');
    }

    public function destroy(int $id, CommandBus $commandBus)
    {
        $commandBus->dispatch(new DeleteUserCommand($id));
        return redirect('/users')->with('success', 'User deleted');
    }
}