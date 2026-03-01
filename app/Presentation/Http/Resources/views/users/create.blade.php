<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Users</title>
</head>
<body>

<h1>Create User</h1>

@if ($errors->any())
  <ul>
    @foreach ($errors->all() as $e)
      <li>{{ $e }}</li>
    @endforeach
  </ul>
@endif

<form method="POST" action="{{ url('/users') }}">
  @csrf

  <div>
    <label>Name</label>
    <input name="name" value="{{ old('name') }}">
  </div>

  <div>
    <label>Email</label>
    <input name="email" value="{{ old('email') }}">
  </div>

  <div>
    <label>Password</label>
    <input type="password" name="password">
  </div>

  <button type="submit">Save</button>
</form>

<a href="{{ url('/users') }}">Back</a>

</body>
</html>