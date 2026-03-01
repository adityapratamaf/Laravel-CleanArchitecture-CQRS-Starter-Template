<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Users</title>
</head>
<body>

<h1>Edit User</h1>

@if ($errors->any())
  <ul>
    @foreach ($errors->all() as $e)
      <li>{{ $e }}</li>
    @endforeach
  </ul>
@endif

<form method="POST" action="{{ url('/users/' . $user['id']) }}">
  @csrf
  @method('PUT')

  <div>
    <label>Name</label>
    <input name="name" value="{{ old('name', $user['name']) }}">
  </div>

  <div>
    <label>Email</label>
    <input name="email" value="{{ old('email', $user['email']) }}">
  </div>

  <div>
    <label>New Password (optional)</label>
    <input type="password" name="password">
  </div>

  <button type="submit">Update</button>
</form>

<a href="{{ url('/users/' . $user['id']) }}">Back</a>

</body>
</html>