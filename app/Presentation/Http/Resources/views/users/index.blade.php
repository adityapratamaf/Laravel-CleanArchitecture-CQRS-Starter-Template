<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Users</title>
</head>
<body>
    <h1>Users</h1>

    @auth
    <div class="row">
        <div class="col">
            <div>
            Login as: <strong>{{ auth()->user()->name }}</strong>
        </div>
        </div>
        <div class="col">
            <form method="POST" action="{{ url('/logout') }}">
            @csrf
            <button type="submit">Logout</button>
        </form>
        </div>
    </div>
    @endauth

    <form method="GET" action="{{ url('/users') }}">
        <input
            type="text"
            name="search"
            placeholder="Search"
            value="{{ $filters['search'] }}"
        />

        <select name="per_page">
            @foreach([10, 15, 25, 50, 100] as $pp)
                <option value="{{ $pp }}" @selected((int)$filters['per_page'] === $pp)>{{ $pp }}</option>
            @endforeach
        </select>

        <button type="submit">Filter</button>
    </form>

    <p style="margin-top: 10px;">
        View {{ $meta['range_start'] }} - {{ $meta['range_end'] }} from {{ $meta['total'] }}
    </p>

    <a href="{{ url('/users/create') }}">+ Create User</a>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
        <tr>
            <th>ID</th><th>Name</th><th>Email</th><th>Action</th>
        </tr>
        </thead>
        <tbody>
        @forelse($users as $u)
            <tr>
                <td>{{ $u->id ?? $u['id'] }}</td>
                <td>{{ $u->name ?? $u['name'] }}</td>
                <td>{{ $u->email ?? $u['email'] }}</td>
                <td>
                    <a href="{{ url('/users/' . ($u->id ?? $u['id'])) }}">Detail</a>
                    <a href="{{ url('/users/' . ($u->id ?? $u['id']) . '/edit') }}">Edit</a>
                    <form method="POST" action="{{ url('/users/' . ($u->id ?? $u['id'])) }}" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete this user?')">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="4">No data</td></tr>
        @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    <div style="margin-top: 12px;">
        @php
            $current = $meta['current_page'];
            $last = $meta['last_page'];
            $qs = request()->query();
        @endphp

        @if($current > 1)
            <a href="{{ url('/users') . '?' . http_build_query(array_merge($qs, ['page' => $current - 1])) }}">Prev</a>
        @endif

        <span style="margin: 0 8px;">
            Page {{ $current }} / {{ $last }}
        </span>

        @if($current < $last)
            <a href="{{ url('/users') . '?' . http_build_query(array_merge($qs, ['page' => $current + 1])) }}">Next</a>
        @endif
    </div>
</body>
</html>