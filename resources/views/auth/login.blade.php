<h1>Login</h1>

@if ($errors->any())
  <ul>
    @foreach ($errors->all() as $e)
      <li>{{ $e }}</li>
    @endforeach
  </ul>
@endif

<form method="POST" action="{{ url('/login') }}">
  @csrf

  <div>
    <label>Email</label>
    <input name="email" value="{{ old('email') }}" required>
  </div>

  <div>
    <label>Password</label>
    <input type="password" name="password" required>
  </div>

  <div>
    <label>
      <input type="checkbox" name="remember" value="1">
      Remember me
    </label>
  </div>

  <button type="submit">Login</button>
</form>